<?php

namespace App\Http\Controllers\Dapur;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SuppliersController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_supplier' => [
            'required',
            'string',
            'max:255',
            Rule::unique('suppliers', 'nama_supplier')->whereNull('deleted_at')
        ],
            'kontak' => 'required|string|max:50',
            'alamat' => 'required|string|max:255'
        ], [
            'nama_supplier.unique' => 'Nama supplier ini sudah terdaftar di sistem. Silakan input nama lain.',
            'nama_supplier.required' => 'Nama supplier tidak boleh kosong.'
         ]);

        Supplier::create($validated);
        return redirect()->route('inventory.index')->with('success', 'Supplier berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $suppliers = Supplier::with('bahanBakus')->get();
        $supplierEdit = Supplier::findOrFail($id);

        return view('dashboards.dapur.inventory', compact('suppliers', 'supplierEdit'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_supplier' => [
            'required',
            'string',
            'max:255',
            Rule::unique('suppliers', 'nama_supplier')
                ->whereNull('deleted_at')
                ->ignore($id)
        ],
            'kontak' => 'required|string|max:50',
            'alamat' => 'required|string|max:255'
        ]);

        $supplier = Supplier::findOrFail($id);
        $supplier->update($validated);

        return redirect()->route('inventory.index')->with('success', 'Data supplier berhasil diupdate!');
    }

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);

        $terpakaiDiStok = \App\Models\StokGudang::where('supplier_id', $id)->exists();

        if ($terpakaiDiStok) {
            return redirect()->route('inventory.index')->with('error', 'Gagal! Supplier tidak dapat dihapus karena masih memiliki bahan baku yang terdata di Stok Gudang.');
        }

        $supplier->delete();

        return redirect()->route('inventory.index')->with('success', 'Supplier berhasil dihapus!');
    }
}