<?php

namespace App\Http\Controllers;

use App\Models\LaporanMasalah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanMasalahController extends Controller
{
    /**
     * Tampilkan form + riwayat laporan milik user yang login.
     */
    public function index()
    {
        $laporan = LaporanMasalah::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboards.laporan-masalah.index', compact('laporan'));
    }

    /**
     * Simpan laporan masalah baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul_masalah' => 'required|string|max:255',
            'deskripsi'     => 'required|string',
            'foto_bukti'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto_bukti')) {
            $fotoPath = $request->file('foto_bukti')->store('laporan-bukti', 'public');
        }

        LaporanMasalah::create([
            'judul_masalah' => $request->judul_masalah,
            'deskripsi'     => $request->deskripsi,
            'foto_bukti'    => $fotoPath,
            'status'        => 'Open',
            'user_id'       => Auth::id(),
        ]);

        return back()->with('success', 'Laporan masalah berhasil dikirim.');
    }

    /**
     * Hapus laporan milik user sendiri (hanya jika masih Open).
     */
    public function destroy(LaporanMasalah $laporanMasalah)
    {
        if ($laporanMasalah->user_id !== Auth::id()) {
            abort(403);
        }

        if ($laporanMasalah->foto_bukti) {
            $path = public_path('storage/' . $laporanMasalah->foto_bukti);
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $laporanMasalah->delete();

        return back()->with('success', 'Laporan berhasil dihapus.');
    }
}