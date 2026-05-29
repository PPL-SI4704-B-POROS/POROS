<?php

namespace App\Http\Controllers\Dapur;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use App\Models\Supplier;
use App\Models\FormHarga;
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
            'satuan' => 'required|in:kg,gram,liter,ml',
            'stok_minimal' => 'required|numeric',
            'supplier_id' => 'required|exists:suppliers,id',
            'harga' => 'required|numeric|min:0'
        ]);

        $bahan = BahanBaku::create($validated);

        FormHarga::create([
            'harga_satuan' => $request->harga,
            'satuan_harga' => $request->satuan,
            'tanggal_update' => now()->toDateString(),
            'supplier_id' => $request->supplier_id,
            'bahan_id' => $bahan->id,
        ]);

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
            'satuan' => 'required|in:kg,gram,liter,ml',
            'stok_minimal' => 'required|numeric',
            'supplier_id' => 'required|exists:suppliers,id',
            'harga' => 'required|numeric|min:0'
        ]);

        $bahanBaku = BahanBaku::findOrFail($id);
        $bahanBaku->update($validated);

        $latestFormHarga = FormHarga::where('bahan_id', $bahanBaku->id)
            ->where('supplier_id', $request->supplier_id)
            ->orderBy('tanggal_update', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        if ($latestFormHarga && $latestFormHarga->tanggal_update->format('Y-m-d') === now()->toDateString()) {
             $latestFormHarga->update([
                 'harga_satuan' => $request->harga,
                 'satuan_harga' => $request->satuan,
             ]);
        } else {
             FormHarga::create([
                'harga_satuan' => $request->harga,
                'satuan_harga' => $request->satuan,
                'tanggal_update' => now()->toDateString(),
                'supplier_id' => $request->supplier_id,
                'bahan_id' => $bahanBaku->id,
            ]);
        }

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