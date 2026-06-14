<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\BiayaBelanja;
use App\Models\StockHistory;
use App\Models\StokGudang;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StokController extends Controller
{
    // ── INDEX ────────────────────────────────────────────────────
    public function index()
    {
        // Ambil seluruh data stok gudang beserta log transaksinya
        $allStocks = StokGudang::with([
            'bahanBaku' => function($query) {
                $query->withTrashed()->with('katalogPangan');
            },
            'supplier',
        ])->get();

        // 🛠️ FIX INDIKATOR DENGAN FALLBACK BERDASARKAN KUANTITAS FISIK (SOLUSI DATA SEEDER)
        // Jika status prediksi model 'good' ATAU kuantitas fisik >= 10.000 gram/liter, masukkan ke totalAman
        $totalAman     = $allStocks->filter(function($s) {
            return $s->stock_level === 'good' || $s->quantity >= 10000;
        })->count();

        $totalMenipis  = $allStocks->filter(function($s) {
            return $s->stock_level === 'low' && $s->quantity < 10000;
        })->count();

        $totalKritis   = $allStocks->filter(function($s) {
            return $s->stock_level === 'critical' && $s->quantity < 1000;
        })->count();

        // Mengelompokkan berdasarkan bahan_baku_id (bukan nama) agar item yang bahan bakunya
        // sudah dihapus tetap masuk ke grup yang sama, bukan membuat baris baru
        $stocks = $allStocks->groupBy('bahan_baku_id');

        // Resolusi nama grup: cari nama bahan untuk setiap bahan_baku_id
        // Prioritas: (1) relasi langsung, (2) BiayaBelanja log, (3) fallback
        $groupNames = $stocks->mapWithKeys(function($group, $bahanBakuId) {
            $stockWithBahan = $group->first(fn($s) => $s->bahanBaku !== null);
            if ($stockWithBahan) {
                return [$bahanBakuId => $stockWithBahan->bahanBaku->nama_bahan];
            }
            // Coba lacak via log keuangan (BiayaBelanja punya relasi ke bahan_baku)
            $financialLog = BiayaBelanja::with(['bahanBaku' => fn($q) => $q->withTrashed()])
                ->where('bahan_baku_id', $bahanBakuId)
                ->first();
            $name = $financialLog?->bahanBaku?->nama_bahan ?? ('Bahan ID #' . $bahanBakuId);
            return [$bahanBakuId => $name];
        });

        // Mengambil array ID kombinasi bahan baku yang sudah eksis di gudang
        $existingBahanBakuIds = StokGudang::pluck('bahan_baku_id')->toArray();

        // Mengambil semua opsi bahan baku yang BELUM dimasukkan ke stok gudang
        $bahanBakus = BahanBaku::with([
            'supplier',
            'katalogPangan'
        ])
        ->whereNotIn('id', $existingBahanBakuIds)
        ->get();

        // Mengambil list stok mentah (tanpa grouping) untuk keperluan modal/list lain
        $stokList = StokGudang::with([
            'bahanBaku' => fn($q) => $q->withTrashed(),
            'supplier'
        ])->get();

        // Mengambil semua data supplier untuk kebutuhan dropdown modal Tambah Item
        $suppliers = Supplier::all();

        return view(
            'dashboards.dapur.deliveries',
            compact(
                'stocks',
                'groupNames',
                'bahanBakus',
                'stokList',
                'suppliers',
                'totalAman',     // Kirim ke Blade
                'totalMenipis',  // Kirim ke Blade
                'totalKritis'    // Kirim ke Blade
            )
        );
    }

    // ── ADD ITEM ──
    public function addItem(Request $request)
    {
        $request->validate([
            'supplier_id'   => 'required|exists:suppliers,id',
            'bahan_baku_id' => 'required|exists:bahan_bakus,id',
            'satuan'        => 'required|in:gram,liter',
        ]);

        $bahan = BahanBaku::findOrFail($request->bahan_baku_id);

        $exists = StokGudang::where('bahan_baku_id', $bahan->id)
                            ->where('supplier_id', $request->supplier_id)
                            ->exists();
                            
        if ($exists) {
            return back()->withErrors(['Item dari supplier ini sudah terdaftar di stok gudang.']);
        }

        StokGudang::create([
            'bahan_baku_id' => $bahan->id,
            'supplier_id'   => $request->supplier_id,
            'quantity'      => 0,
            'satuan'        => $request->satuan,
        ]);

        return back()->with('success', 'Item berhasil ditambahkan ke stok gudang.');
    }

    // ── ADD INCOMING STOCK ──
    public function addIncoming(Request $request, $id)
    {
        $request->validate([
            'quantity'      => 'required|numeric|min:0.01',
            'incoming_date' => 'required|date',
            'total_harga'   => 'nullable|numeric|min:0',
            'batch_id'      => 'nullable|string|max:100',
            'expired_date'  => 'nullable|date',
        ]);

        $stok      = StokGudang::findOrFail($id);
        $bahanBaku = BahanBaku::findOrFail($stok->bahan_baku_id);

        if ($bahanBaku->stok < $request->quantity) {
            return back()->withErrors(['quantity' => 'Stok supplier tidak cukup. Stok tersedia: ' . $bahanBaku->stok . ' ' . $stok->satuan]);
        }

        $qtyInBaseUnit = in_array(strtolower($stok->satuan), ['kg', 'liter']) ? $request->quantity * 1000 : $request->quantity;
        $harga_per_gram = $bahanBaku->harga_terbaru;
        $total_harga = $harga_per_gram > 0 ? ($harga_per_gram * $qtyInBaseUnit) : ($request->total_harga ?? 0);

        DB::transaction(function () use ($stok, $bahanBaku, $request, $total_harga) {
            $stok->increment('quantity', $request->quantity);
            $bahanBaku->decrement('stok', $request->quantity);

            StockHistory::create([
                'stok_gudang_id' => $stok->id,
                'status'         => 'incoming',
                'quantity'       => $request->quantity,
                'incoming_date'  => $request->incoming_date,
                'batch_id'       => $request->batch_id,
                'expired_date'   => $request->expired_date,
            ]);

            BiayaBelanja::create([
                'bahan_baku_id'   => $bahanBaku->id,
                'supplier_id'     => $bahanBaku->supplier_id,
                'dapur_id'        => auth()->id(),
                'jumlah_beli'     => $request->quantity,
                'total_harga'     => $total_harga,
                'tanggal_belanja' => $request->incoming_date,
            ]);
        });

        return back()->with('success', 'Stok berhasil diperbarui.');
    }

    // ── ADJUST STOCK ──
    public function adjustStock(Request $request, $id)
    {
        $request->validate([
            'adjustment_type'   => 'required|in:add,subtract',
            'adjustment_amount' => 'required|numeric|min:0.01',
            'reason'            => 'required|string|max:255',
        ]);

        $stok   = StokGudang::findOrFail($id);
        $amount = $request->adjustment_amount;
        $type   = $request->adjustment_type;

        if ($type === 'subtract' && $stok->quantity < $amount) {
            return back()->withErrors(['adjustment_amount' => 'Jumlah pengurangan melebihi stok saat ini (' . $stok->quantity . ' ' . $stok->satuan . ')']);
        }

        DB::transaction(function () use ($stok, $type, $amount, $request) {
            if ($type === 'add') {
                $stok->increment('quantity', $amount);
            } else {
                $stok->decrement('quantity', $amount);
            }

            StockHistory::create([
                'stok_gudang_id' => $stok->id,
                'status'         => 'adjustment',
                'quantity'       => $type === 'add' ? $amount : -$amount,
                'incoming_date'  => null,
                'batch_id'       => 'ADJ - ' . $request->reason,
                'expired_date'   => null,
            ]);
        });

        return back()->with('success', 'Stok berhasil dikoreksi.');
    }

    // ── DELETE ITEM ──
    public function destroy($id)
    {
        StokGudang::findOrFail($id)->delete();
        return back()->with('success', 'Item berhasil dihapus.');
    }

    // ── HISTORY ──
    public function history($id)
    {
        $stok = StokGudang::with([
            'bahanBaku' => fn($q) => $q->withTrashed()->with('katalogPangan'),
            'supplier',
            'histories',
        ])->findOrFail($id);

        return response()->json([
            'item'      => $stok->bahanBaku->nama_bahan ?? 'Bahan Terhapus',
            'supplier'  => $stok->supplier->nama_supplier ?? 'Supplier Terhapus',
            'kategori'  => $stok->bahanBaku->katalogPangan->kategori ?? '-',
            'satuan'    => $stok->satuan,
            'histories' => $stok->histories->map(fn($h) => [
                'status'         => $h->status,
                'quantity'       => $h->quantity,
                'incoming_date'  => $h->incoming_date?->format('d M Y') ?? '-',
                'batch_id'       => $h->batch_id ?? '-',
                'expired_date'   => $h->expired_date?->format('d M Y') ?? '-',
                'supplier_name'  => $stok->supplier->nama_supplier ?? 'Supplier Terhapus'
            ]),
        ]);
    }
}