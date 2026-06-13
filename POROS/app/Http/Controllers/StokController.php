<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\BiayaBelanja;
use App\Models\StockHistory;
use App\Models\StokGudang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StokController extends Controller
{
    // ── INDEX ────────────────────────────────────────────────────
    public function index()
    {
        $stocks = StokGudang::with([
            'bahanBaku.katalogPangan',
            'supplier',
        ])->get();

        $existingIds = $stocks->pluck('bahan_baku_id')->toArray();
        $bahanBakus = BahanBaku::with(['supplier', 'katalogPangan'])
            ->whereNotIn('id', $existingIds)
            ->get();

        $stokList = $stocks;

        return view('dashboards.dapur.deliveries', compact(
            'stocks',
            'bahanBakus',
            'stokList',
        ));
    }

    // ── ADD ITEM (Menggunakan logika aman milikmu: qty awal = 0) ──
    public function addItem(Request $request)
    {
        $request->validate([
            'bahan_baku_id' => 'required|exists:bahan_bakus,id',
            'satuan'        => 'required|in:kg,gram,liter,ml',
        ]);

        $bahan = BahanBaku::findOrFail($request->bahan_baku_id);

        $exists = StokGudang::where('bahan_baku_id', $bahan->id)->exists();
        if ($exists) {
            return back()->withErrors(['Item ini sudah ada di stok gudang.']);
        }

        // Hanya mendaftarkan item baru ke inventaris dengan jumlah awal 0
        StokGudang::create([
            'bahan_baku_id' => $bahan->id,
            'supplier_id'   => $bahan->supplier_id,
            'quantity'      => 0,
            'satuan'        => $request->satuan,
        ]);

        return back()->with('success', 'Item berhasil ditambahkan ke stok gudang.');
    }

    // ── ADD INCOMING STOCK (Injeksi Hitung Harga Otomatis Temanmu) ──
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

        // Menggabungkan logika hitung harga otomatis milik temanmu ke dalam konversi satuan gramasi
        $qtyInBaseUnit = in_array(strtolower($stok->satuan), ['kg', 'liter']) ? $request->quantity * 1000 : $request->quantity;
        $harga_per_gram = $bahanBaku->harga_terbaru;
        $total_harga = $harga_per_gram > 0 ? ($harga_per_gram * $qtyInBaseUnit) : ($request->total_harga ?? 0);

        DB::transaction(function () use ($stok, $bahanBaku, $request, $total_harga) {
            // Update jumlah stok gudang dan kurangi stok supplier utama
            $stok->increment('quantity', $request->quantity);
            $bahanBaku->decrement('stok', $request->quantity);

            // Buat log mutasi barang masuk
            StockHistory::create([
                'stok_gudang_id' => $stok->id,
                'status'         => 'incoming',
                'quantity'       => $request->quantity,
                'incoming_date'  => $request->incoming_date,
                'batch_id'       => $request->batch_id,
                'expired_date'   => $request->expired_date,
            ]);

            // Catat transaksi belanja otomatis ke laporan keuangan
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

    // ── ADJUST STOCK ─────────────────────────────────────────────
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

    // ── DELETE ITEM ──────────────────────────────────────────────
    public function destroy($id)
    {
        StokGudang::findOrFail($id)->delete();
        return back()->with('success', 'Item berhasil dihapus.');
    }

    // ── HISTORY (JSON untuk modal) ───────────────────────────────
    public function history($id)
    {
        $stok = StokGudang::with([
            'bahanBaku.katalogPangan',
            'supplier',
            'histories',
        ])->findOrFail($id);

        return response()->json([
            'item'      => $stok->bahanBaku->nama_bahan ?? '-',
            'supplier'  => $stok->supplier->nama_supplier ?? '-',
            'kategori'  => $stok->bahanBaku->katalogPangan->kategori ?? '-',
            'satuan'    => $stok->satuan,
            'histories' => $stok->histories->map(fn($h) => [
                'status'         => $h->status,
                'quantity'       => $h->quantity,
                'incoming_date' => $h->incoming_date?->format('d M Y') ?? '-',
                'batch_id'       => $h->batch_id ?? '-',
                'expired_date'   => $h->expired_date?->format('d M Y') ?? '-',
            ]),
        ]);
    }
}