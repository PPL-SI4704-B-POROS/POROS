<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Pengiriman;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PengirimanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pengiriman::with(['produksi.menu', 'sekolah', 'kurir']);

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->whereHas('sekolah', function ($q) use ($search) {
                $q->where('nama_sekolah', 'like', "%{$search}%");
            })->orWhereHas('kurir', function ($q) use ($search) {
                $q->where('nama_kurir', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status_kirim', $request->status);
        }

        $deliveries = $query->latest()->paginate(10)->withQueryString();

        return view('dashboards.superadmin.deliveries', compact('deliveries'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_kirim' => 'required|in:Menunggu,Jalan,Sampai',
        ]);

        $pengiriman = Pengiriman::findOrFail($id);
        $pengiriman->status_kirim = $request->status_kirim;

        if ($request->status_kirim === 'Jalan' && ! $pengiriman->waktu_berangkat) {
            $pengiriman->waktu_berangkat = Carbon::now();
        }

        if ($request->status_kirim === 'Sampai' && ! $pengiriman->waktu_sampai) {
            $pengiriman->waktu_sampai = Carbon::now();
        }

        $pengiriman->save();

        return redirect()->back()->with('success', 'Status pengiriman berhasil diperbarui.');
    }

    public function updateHandover(Request $request, $id)
    {
        $request->validate([
            'nama_penerima' => 'required|string|max:255',
            'keterangan' => 'required|in:rasa tidak enak,porsi kebanyakan,menu ga menarik,siswa sedang sakit,kurang matang',
            'ompreng_kembali' => 'nullable|integer|min:0',
            'menu_tersisa' => 'nullable|string|max:255',
            'jumlah_sisa_ompreng' => 'nullable|integer|min:0',
            'tanggal_sisa' => 'nullable|date',
        ]);

        $pengiriman = Pengiriman::findOrFail($id);
        $pengiriman->nama_penerima = $request->nama_penerima;
        $pengiriman->keterangan = $request->keterangan;
        $pengiriman->ompreng_kembali = $request->ompreng_kembali;
        $pengiriman->menu_tersisa = $request->menu_tersisa;
        $pengiriman->jumlah_sisa_ompreng = $request->jumlah_sisa_ompreng;
        $pengiriman->tanggal_sisa = $request->tanggal_sisa;

        // If handover is done, status should be 'Sampai'
        $pengiriman->status_kirim = 'Sampai';
        if (! $pengiriman->waktu_sampai) {
            $pengiriman->waktu_sampai = Carbon::now();
        }

        $pengiriman->save();

        return redirect()->back()->with('success', 'Bukti serah terima berhasil disimpan.');
    }
}
