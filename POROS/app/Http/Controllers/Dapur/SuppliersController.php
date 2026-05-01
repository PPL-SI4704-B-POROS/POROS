<?php

namespace App\Http\Controllers\Dapur;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SuppliersController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_supplier' => 'required|string|max:255',
            'kontak' => 'required|string|max:50',
            'alamat' => 'nullable|string'
        ]);

        Supplier::create($validated);
        return redirect()->route('inventory.index')->with('success', 'Supplier berhasil ditambahkan!');
    }

    public function edit($id)
    {
        // Ambil semua data untuk ditampilkan di tabel
        $suppliers = Supplier::with('bahanBakus')->get();
        // Ambil data supplier spesifik yang mau diedit
        $supplierEdit = Supplier::findOrFail($id);

        return view('dashboards.dapur.inventory', compact('suppliers', 'supplierEdit'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_supplier' => 'required|string|max:255',
            'kontak' => 'required|string|max:50',
            'alamat' => 'nullable|string'
        ]);

        $supplier = Supplier::findOrFail($id);
        $supplier->update($validated);

        return redirect()->route('inventory.index')->with('success', 'Data supplier berhasil diupdate!');
    }

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return redirect()->route('inventory.index')->with('success', 'Supplier berhasil dihapus!');
    }
}