<?php

namespace App\Http\Controllers;

use App\Models\ProduksiHarian;
use Illuminate\Http\Request;

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

    public function destroy($id)
    {
        ProduksiHarian::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Jadwal menu berhasil dihapus.');
    }
}
