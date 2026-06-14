<?php

namespace App\Http\Controllers\Dapur;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use App\Models\Supplier;
use App\Models\FormHarga;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
            'nama_bahan' => [
                'required',
                'string',
                'max:255',
                Rule::unique('bahan_bakus', 'nama_bahan')
                    ->where('supplier_id', $request->supplier_id)
                    ->whereNull('deleted_at') 
            ],
            'stok' => 'required|numeric',
            'satuan' => 'required|in:kg,gram,liter,ml',
            'stok_minimal' => 'required|numeric',
            'supplier_id' => 'required|exists:suppliers,id',
            'harga' => 'required|numeric|min:0'
            ], [
                'nama_bahan.unique' => 'Bahan baku dengan nama tersebut sudah terdaftar pada supplier ini.'
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
            'nama_bahan' => [
                'required',
                'string',
                'max:255',
                Rule::unique('bahan_bakus', 'nama_bahan')
                    ->where('supplier_id', $request->supplier_id)
                    ->whereNull('deleted_at')
                    ->ignore($id)      
            ],
            'stok' => 'required|numeric',
            'satuan' => 'required|in:kg,gram,liter,ml',
            'stok_minimal' => 'required|numeric',
            'supplier_id' => 'required|exists:suppliers,id',
            'harga' => 'required|numeric|min:0'
        ], [
            'nama_bahan.unique' => 'Bahan baku dengan nama tersebut sudah terdaftar pada supplier ini.'
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

    public function destroy($id)
    {
        $bahanBaku = BahanBaku::findOrFail($id);

        // Cukup cek dari bahan_baku_id karena struktur tabel sudah rapi
        $terpakaiDiStok = \App\Models\StokGudang::where('bahan_baku_id', $id)->exists();

        if ($terpakaiDiStok) {
            return redirect()->route('inventory.index')->with('error', 'Gagal! Bahan baku tidak dapat dihapus karena sedang terdata di Stok Gudang.');
        }

        $bahanBaku->delete();

        return redirect()->route('inventory.index')->with('success', 'Data bahan baku berhasil dihapus!');
    }
}