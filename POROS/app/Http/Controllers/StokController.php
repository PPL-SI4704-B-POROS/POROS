<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StokGudang;

class StokController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama_bahan' => 'required',
            'jumlah_masuk' => 'required|numeric',
            'satuan' => 'required',
            'tanggal_terima' => 'required|date',
        ]);

        StokGudang::create($request->all());

        return redirect()->back()->with('success', 'Stok berhasil dicatat secara digital!');
    }
}