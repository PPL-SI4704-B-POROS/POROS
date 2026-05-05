<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\Request;

class PengumumanController extends Controller
{
    // PBI #37 & #38 — Tampilkan semua pengumuman (Super Admin & User bisa lihat)
    public function index()
    {
        $pengumuman = Pengumuman::with('pembuat')->latest()->get();
        return view('dashboards.superadmin.pengumuman.index', compact('pengumuman'));
    }

    // PBI #37 — Simpan pengumuman baru
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi'   => 'required|string',
        ]);

        Pengumuman::create([
            'judul'   => $request->judul,
            'isi'     => $request->isi,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil dibuat.');
    }

    // PBI #39 — Tampilkan form edit
    public function edit(Pengumuman $pengumuman)
    {
        return view('dashboards.superadmin.pengumuman.edit', compact('pengumuman'));
    }

    // PBI #39 — Simpan perubahan pengumuman
    public function update(Request $request, Pengumuman $pengumuman)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi'   => 'required|string',
        ]);

        $pengumuman->update([
            'judul' => $request->judul,
            'isi'   => $request->isi,
        ]);

        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil diperbarui.');
    }
}