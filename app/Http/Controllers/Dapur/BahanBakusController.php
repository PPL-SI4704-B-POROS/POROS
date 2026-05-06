<?php

namespace App\Http\Controllers\Dapur;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use App\Models\Supplier;
use Illuminate\Http\Request;

class BahanBakusController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::with('bahanBakus')->get(); 

        return view('dashboards.dapur.inventory', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_bahan' => 'required|string|max:255',
            'stok' => 'required|numeric',
            'satuan' => 'required|string|max:50',
            'stok_minimal' => 'required|numeric',
            'supplier_id' => 'required|exists:suppliers,id'
        ]);

        BahanBaku::create($validated);

        return redirect()->route('inventory.index')->with('success', 'Bahan baku berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $suppliers = Supplier::with('bahanBakus')->get(); 
        $bahanBaku = BahanBaku::findOrFail($id); // Ambil data 1 bahan baku yang diklik

        // Return ke view yang sama, tapi kali ini bawa variabel $bahanBaku
        return view('dashboards.dapur.inventory', compact('suppliers', 'bahanBaku'));
    }

    // UPDATE: Menyimpan perubahan data dari form edit
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_bahan' => 'required|string|max:255',
            'stok' => 'required|numeric',
            'satuan' => 'required|string|max:50',
            'stok_minimal' => 'required|numeric',
            'supplier_id' => 'required|exists:suppliers,id'
        ]);

        $bahanBaku = BahanBaku::findOrFail($id);
        $bahanBaku->update($validated);

        // Setelah update, kembalikan ke halaman index agar form kembali kosong
        return redirect()->route('inventory.index')->with('success', 'Data bahan baku berhasil diupdate!');
    }

    // DELETE: Menghapus data bahan baku
    public function destroy($id)
    {
        $bahanBaku = BahanBaku::findOrFail($id);
        $bahanBaku->delete();

        return redirect()->route('inventory.index')->with('success', 'Data bahan baku berhasil dihapus!');
    }
}