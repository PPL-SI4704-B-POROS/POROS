<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengumumanController extends Controller
{
    // Tampilkan semua pengumuman (filter sesuai role)
    public function index()
    {
        $userRole = auth()->user()->role->nama_role;

        if ($userRole === 'super admin') {
            // Super admin lihat semua
            $pengumuman = Pengumuman::with('pembuat')->latest()->get();
        } else {
            // Role lain hanya lihat: umum + yang ditujukan ke role mereka
            $pengumuman = Pengumuman::with('pembuat')
                ->where(function ($q) use ($userRole) {
                    $q->where('target_role', 'umum')
                      ->orWhere('target_role', $userRole);
                })
                ->latest()
                ->get();
        }

        return view('dashboards.superadmin.pengumuman.index', compact('pengumuman'));
    }

    // Simpan pengumuman baru
    public function store(Request $request)
    {
        $request->validate([
            'judul'       => 'required|string|max:255',
            'isi'         => 'required|string',
            'gambar'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'target_role' => 'required|in:umum,sekolah,dapur',
        ]);

        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('pengumuman', 'public');
        }

        Pengumuman::create([
            'judul'       => $request->judul,
            'isi'         => $request->isi,
            'gambar'      => $gambarPath,
            'target_role' => $request->target_role,
            'user_id'     => auth()->id(),
        ]);

        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil dibuat.');
    }

    // Tampilkan form edit
    public function edit(Pengumuman $pengumuman)
    {
        return view('dashboards.superadmin.pengumuman.edit', compact('pengumuman'));
    }

    // Simpan perubahan pengumuman
    public function update(Request $request, Pengumuman $pengumuman)
    {
        $request->validate([
            'judul'       => 'required|string|max:255',
            'isi'         => 'required|string',
            'gambar'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'target_role' => 'required|in:umum,sekolah,dapur',
        ]);

        $gambarPath = $pengumuman->gambar; // default pakai gambar lama

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama kalau ada
            if ($pengumuman->gambar) {
                Storage::disk('public')->delete($pengumuman->gambar);
            }
            $gambarPath = $request->file('gambar')->store('pengumuman', 'public');
        }

        $pengumuman->update([
            'judul'       => $request->judul,
            'isi'         => $request->isi,
            'gambar'      => $gambarPath,
            'target_role' => $request->target_role,
        ]);

        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    // Hapus pengumuman
    public function destroy(Pengumuman $pengumuman)
    {
        // Hapus gambar dari storage kalau ada
        if ($pengumuman->gambar) {
            Storage::disk('public')->delete($pengumuman->gambar);
        }

        $pengumuman->delete();

        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil dihapus.');
    }
}