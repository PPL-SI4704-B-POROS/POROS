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

        // Jika berubah MENJADI 'Memasak' (Mulai masak) dari 'Menunggu', lakukan pemotongan stok!
        if ($newStatus === 'Memasak' && $oldStatus === 'Menunggu') {
            try {
                DB::transaction(function () use ($schedule, $newStatus) {
                    $menu = $schedule->menu;
                    $porsi = $schedule->total_target_porsi;

                    // 1. Validasi stok semua bahan baku mencukupi
                    foreach ($menu->reseps as $resep) {
                        $bahan = $resep->bahanBaku;
                        $kebutuhanG = $resep->gramasi_per_porsi * $porsi;

                        if ($bahan->stok < $kebutuhanG) {
                            $kebutuhanKgStr = ($kebutuhanG >= 1000) ? number_format($kebutuhanG / 1000, 2, ',', '.') . ' kg' : number_format($kebutuhanG, 0, ',', '.') . ' g';
                            $tersediaKgStr = ($bahan->stok >= 1000) ? number_format($bahan->stok / 1000, 2, ',', '.') . ' kg' : number_format($bahan->stok, 0, ',', '.') . ' g';
                            throw new \Exception("Stok bahan mentah '{$bahan->nama_bahan}' di gudang tidak mencukupi! Dibutuhkan: {$kebutuhanKgStr}, tersedia di inventori: {$tersediaKgStr}.");
                        }
                    }

                    // 2. Jika aman, potong stok bahan baku secara otomatis
                    foreach ($menu->reseps as $resep) {
                        $bahan = $resep->bahanBaku;
                        $kebutuhanG = $resep->gramasi_per_porsi * $porsi;
                        $bahan->decrement('stok', $kebutuhanG);
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

        // Jika sedang memasak atau sudah siap kirim, kembalikan stok bahan mentah saat jadwal dibatalkan/dihapus
        if ($schedule->status_produksi === 'Memasak' || $schedule->status_produksi === 'Siap Kirim') {
            DB::transaction(function () use ($schedule) {
                $menu = $schedule->menu;
                $porsi = $schedule->total_target_porsi;

                foreach ($menu->reseps as $resep) {
                    $bahan = $resep->bahanBaku;
                    $kebutuhanG = $resep->gramasi_per_porsi * $porsi;
                    $bahan->increment('stok', $kebutuhanG);
                }
            });
        }

        $schedule->delete();
        return redirect()->back()->with('success', 'Jadwal menu berhasil dihapus.');
    }
}
