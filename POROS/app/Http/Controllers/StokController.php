<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StokGudang;
use App\Models\Supplier;

class StokController extends Controller
{
    public function index()
    {
        $stocks = StokGudang::latest()->get();
        $suppliers = Supplier::all();

        return view('dashboards.dapur.deliveries', compact('stocks', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_bahan' => 'required',
            'jumlah_masuk' => 'required|numeric',
            'satuan' => 'required',
            'supplier_id' => 'required',
            'batch_id' => 'required',
            'tanggal_terima' => 'required|date',
            'expired_date' => 'required|date',
        ]);

        StokGudang::create([
            'nama_bahan' => $request->nama_bahan,
            'jumlah_masuk' => $request->jumlah_masuk,
            'satuan' => $request->satuan,
            'supplier_id' => $request->supplier_id,
            'batch_id' => $request->batch_id,
            'tanggal_terima' => $request->tanggal_terima,
            'expired_date' => $request->expired_date,
        ]);

        return redirect()->back()->with('success', 'Stock berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_bahan' => 'required',
            'jumlah_masuk' => 'required|numeric',
            'satuan' => 'required',
            'supplier_id' => 'required',
            'batch_id' => 'required',
            'tanggal_terima' => 'required|date',
            'expired_date' => 'required|date',
        ]);

        $stok = StokGudang::findOrFail($id);

        $stok->update([
            'nama_bahan' => $request->nama_bahan,
            'jumlah_masuk' => $request->jumlah_masuk,
            'satuan' => $request->satuan,
            'supplier_id' => $request->supplier_id,
            'batch_id' => $request->batch_id,
            'tanggal_terima' => $request->tanggal_terima,
            'expired_date' => $request->expired_date,
        ]);

        return redirect()->back()->with('success', 'Stock berhasil diupdate!');
    }

    public function destroy($id)
    {
        $stok = StokGudang::findOrFail($id);

        $stok->delete();

        return redirect()->back()->with('success', 'Stock berhasil dihapus!');
    }
}