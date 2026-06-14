<?php

namespace App\Http\Controllers\Dapur;

use App\Models\ProduksiHarian;
use App\Models\BahanBaku;
use App\Models\StokGudang;
use App\Models\StockHistory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProduksiHarianController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_production' => 'nullable|date', // Antisipasi jika ada typo di form HTML temen lu
            'tanggal_produksi' => 'required_without:tanggal_production|date',
            'menu_id' => 'required|exists:menus,id',
            'total_target_porsi' => 'required|integer|min:1',
        ]);

        $tanggal = $request->tanggal_produksi ?? $request->tanggal_production;
        $this->validateAllergy($request->menu_id);

        ProduksiHarian::create([
            'tanggal_produksi' => $tanggal,
            'menu_id' => $request->menu_id,
            'total_target_porsi' => $request->total_target_porsi,
            'status_produksi' => 'Menunggu',
        ]);

        return redirect()->back()->with('success', 'Jadwal menu berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'total_target_porsi' => 'required|integer|min:1',
        ]);

        $this->validateAllergy($request->menu_id);

        $schedule = ProduksiHarian::findOrFail($id);
        $schedule->update([
            'menu_id' => $request->menu_id,
            'total_target_porsi' => $request->total_target_porsi,
        ]);

        return redirect()->back()->with('success', 'Jadwal menu berhasil diperbarui.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_produksi' => 'required|in:Menunggu,Memasak,Siap Kirim',
        ]);

        $schedule = ProduksiHarian::findOrFail($id);
        $newStatus = $request->status_produksi;
        $oldStatus = $schedule->status_produksi;

        if ($newStatus === $oldStatus) {
            return redirect()->back()->with('info', 'Status produksi tidak berubah.');
        }

        // ── KONDISI 1: JIKA STATUS BERUBAH MENJADI 'Memasak' (POTONG STOK GUDANG) ──
        if ($newStatus === 'Memasak' && $oldStatus === 'Menunggu') {
            try {
                DB::transaction(function () use ($schedule, $newStatus) {
                    $menu = $schedule->menu;
                    $porsi = $schedule->total_target_porsi;

                    // 1. Validasi kecukupan fisik barang di StokGudang internal lu
                    foreach ($menu->reseps as $resep) {
                        $stokGudang = StokGudang::where('bahan_baku_id', $resep->bahan_id)->first();
                        
                        if (!$stokGudang) {
                            throw new \Exception("Bahan baku '{$resep->bahanBaku->nama_bahan}' belum didaftarkan di Stok Gudang Inventaris!");
                        }

                        $kebutuhanGram = $resep->gramasi_per_porsi * $porsi;
                        
                        // Konversi hitungan gram resep ke satuan gudang (kg/liter atau gram asli)
                        $satuanGudang = strtolower(trim($stokGudang->satuan));
                        $kebutuhanFinal = ($satuanGudang === 'kg' || $satuanGudang === 'liter') ? $kebutuhanGram / 1000 : $kebutuhanGram;

                        if ($stokGudang->quantity < $kebutuhanFinal) {
                            $kebutuhanStr = $kebutuhanFinal . ' ' . $stokGudang->satuan;
                            $tersediaStr = $stokGudang->quantity . ' ' . $stokGudang->satuan;
                            throw new \Exception("Stok internal '{$resep->bahanBaku->nama_bahan}' tidak cukup untuk memasak! Butuh: {$kebutuhanStr}, Tersedia: {$tersediaStr}.");
                        }
                    }

                    // 2. Jika semua bahan lolos kualifikasi, eksekusi pemotongan real-time
                    foreach ($menu->reseps as $resep) {
                        $stokGudang = StokGudang::where('bahan_baku_id', $resep->bahan_id)->first();
                        $kebutuhanGram = $resep->gramasi_per_porsi * $porsi;
                        
                        $satuanGudang = strtolower(trim($stokGudang->satuan));
                        $kebutuhanFinal = ($satuanGudang === 'kg' || $satuanGudang === 'liter') ? $kebutuhanGram / 1000 : $kebutuhanGram;

                        // Potong fisik stok gudang
                        $stokGudang->decrement('quantity', (float)$kebutuhanFinal);

                        // Potong juga stok master bahan baku (menjaga keutuhan relasi lama milik temen lu)
                        if ($resep->bahanBaku) {
                            $resep->bahanBaku->decrement('stok', $kebutuhanGram);
                        }

                        // Catat log pengeluaran bahan ke riwayat digital deliveries lu
                        StockHistory::create([
                            'stok_gudang_id' => $stokGudang->id,
                            'status'         => 'adjustment', // Masuk sebagai penyesuaian pemakaian masakan
                            'quantity'       => -$kebutuhanFinal, // Nilai minus karena keluar dari gudang
                            'incoming_date'  => now()->toDateString(),
                            'batch_id'       => 'PRODUKSI - Memasak ' . $menu->nama_menu . ' (' . $porsi . ' Porsi)',
                            'expired_date'   => null,
                        ]);
                    }

                    $schedule->update([
                        'status_produksi' => $newStatus,
                    ]);
                });
            } catch (\Exception $e) {
                return redirect()->back()->with('error_toast', $e->getMessage());
            }
        } else {
            // Perpindahan status selain dari Menunggu ke Memasak
            $schedule->update([
                'status_produksi' => $newStatus,
            ]);
        }

        return redirect()->back()->with('success', 'Status produksi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $schedule = ProduksiHarian::findOrFail($id);

        // ── KONDISI 2: JIKA JADWAL DIHAPUS SAAT SEDANG MEMASAK (KEMBALIKAN STOK GUDANG) ──
        if ($schedule->status_produksi === 'Memasak' || $schedule->status_produksi === 'Siap Kirim') {
            DB::transaction(function () use ($schedule) {
                $menu = $schedule->menu;
                $porsi = $schedule->total_target_porsi;

                foreach ($menu->reseps as $resep) {
                    $stokGudang = StokGudang::where('bahan_baku_id', $resep->bahan_id)->first();
                    $kebutuhanGram = $resep->gramasi_per_porsi * $porsi;

                    if ($stokGudang) {
                        $satuanGudang = strtolower(trim($stokGudang->satuan));
                        $kebutuhanFinal = ($satuanGudang === 'kg' || $satuanGudang === 'liter') ? $kebutuhanGram / 1000 : $kebutuhanGram;

                        // Kembalikan kuantitas stok gudang yang batal dimasak
                        $stokGudang->increment('quantity', (float)$kebutuhanFinal);

                        // Buat riwayat pembatalan stok masuk kembali
                        StockHistory::create([
                            'stok_gudang_id' => $stokGudang->id,
                            'status'         => 'adjustment',
                            'quantity'       => $kebutuhanFinal,
                            'incoming_date'  => now()->toDateString(),
                            'batch_id'       => 'BATAL MASAK - ' . $menu->nama_menu,
                            'expired_date'   => null,
                        ]);
                    }

                    // Kembalikan ke stok master milik temanmu
                    if ($resep->bahanBaku) {
                        $resep->bahanBaku->increment('stok', $kebutuhanGram);
                    }
                }
            });
        }

        $schedule->delete();
        return redirect()->back()->with('success', 'Jadwal menu berhasil dihapus.');
    }

    private function validateAllergy($menuId)
    {
        $activeAlergiRaw = \App\Models\Siswa::where('status', 'Active')->whereNotNull('alergi')->pluck('alergi');
        $activeAllergies = [];
        foreach ($activeAlergiRaw as $alergiStr) {
            $items = array_map('trim', explode(',', $alergiStr));
            foreach ($items as $item) {
                if (!empty($item) && !in_array(strtolower($item), array_map('strtolower', $activeAllergies))) {
                    $activeAllergies[] = $item;
                }
            }
        }

        if (empty($activeAllergies)) {
            return;
        }

        $menu = \App\Models\Menu::with('reseps.bahanBaku')->findOrFail($menuId);
        foreach ($menu->reseps as $resep) {
            if ($resep->bahanBaku) {
                $namaBahan = strtolower($resep->bahanBaku->nama_bahan);
                foreach ($activeAllergies as $alergi) {
                    if (stripos($namaBahan, strtolower($alergi)) !== false) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'menu_id' => "Menu ini tidak dapat dijadwalkan karena mengandung bahan alergen '{$resep->bahanBaku->nama_bahan}' (Alergi aktif: {$alergi})."
                        ]);
                    }
                }
            }
        }
    }
}