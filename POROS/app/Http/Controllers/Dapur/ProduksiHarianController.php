<?php

namespace App\Http\Controllers\Dapur;

use App\Models\ProduksiHarian;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProduksiHarianController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_produksi' => 'required|date',
            'menu_id' => 'required|exists:menus,id',
            'total_target_porsi' => 'required|integer|min:1',
        ]);

        $this->validateAllergy($request->menu_id);

        ProduksiHarian::create([
            'tanggal_produksi' => $request->tanggal_produksi,
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

        // Jika berubah MENJADI 'Memasak' (Mulai masak) dari 'Menunggu', lakukan pemotongan stok gudang!
        if ($newStatus === 'Memasak' && $oldStatus === 'Menunggu') {
            try {
                DB::transaction(function () use ($schedule, $newStatus) {
                    $menu = $schedule->menu;
                    $porsi = $schedule->total_target_porsi;

                    // 1. Validasi stok semua bahan baku mencukupi di StokGudang
                    foreach ($menu->reseps as $resep) {
                        $stokGudang = \App\Models\StokGudang::where('bahan_baku_id', $resep->bahan_id)->first();
                        $kebutuhanG = $resep->gramasi_per_porsi * $porsi;

                        if (!$stokGudang) {
                            throw new \Exception("Stok gudang untuk {$resep->bahanBaku->nama_bahan} tidak ditemukan.");
                        }

                        if ($stokGudang->quantity < $kebutuhanG) {
                            $kebutuhanKgStr = ($kebutuhanG >= 1000) ? number_format($kebutuhanG / 1000, 2, ',', '.') . ' kg' : number_format($kebutuhanG, 0, ',', '.') . ' g';
                            $tersediaKgStr = ($stokGudang->quantity >= 1000) ? number_format($stokGudang->quantity / 1000, 2, ',', '.') . ' kg' : number_format($stokGudang->quantity, 0, ',', '.') . ' g';
                            
                            throw new \Exception("Stok gudang '{$resep->bahanBaku->nama_bahan}' tidak mencukupi! Dibutuhkan: {$kebutuhanKgStr}, tersedia di inventori gudang: {$tersediaKgStr}.");
                        }
                    }

                    // 2. Jika aman, potong stok_gudang.quantity secara otomatis
                    foreach ($menu->reseps as $resep) {
                        $stokGudang = \App\Models\StokGudang::where('bahan_baku_id', $resep->bahan_id)->first();
                        $kebutuhanG = $resep->gramasi_per_porsi * $porsi;
                        
                        $stokGudang->decrement('quantity', $kebutuhanG);
                    }

                    $schedule->update([
                        'status_produksi' => $newStatus,
                    ]);
                });
            } catch (\Exception $e) {
                return redirect()->back()->with('error_toast', $e->getMessage());
            }
        } else {
            // Perpindahan status lainnya
            $schedule->update([
                'status_produksi' => $newStatus,
            ]);
        }

        return redirect()->back()->with('success', 'Status produksi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $schedule = ProduksiHarian::findOrFail($id);

        // Jika sedang memasak atau sudah siap kirim, kembalikan stok_gudang.quantity saat jadwal dibatalkan/dihapus
        if ($schedule->status_produksi === 'Memasak' || $schedule->status_produksi === 'Siap Kirim') {
            DB::transaction(function () use ($schedule) {
                $menu = $schedule->menu;
                $porsi = $schedule->total_target_porsi;

                foreach ($menu->reseps as $resep) {
                    $stokGudang = \App\Models\StokGudang::where('bahan_baku_id', $resep->bahan_id)->first();
                    $kebutuhanG = $resep->gramasi_per_porsi * $porsi;

                    if ($stokGudang) {
                        $stokGudang->increment('quantity', $kebutuhanG);
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