<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Pengiriman;
use App\Models\PlateWaste;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PengirimanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pengiriman::with(['produksi.menu', 'sekolah', 'kurir', 'plateWastes']);

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
            'ompreng_kembali' => 'nullable|integer|min:0',
            'menu_tersisa' => 'nullable|string|max:255',
            'tanggal_sisa' => 'nullable|date',
            'wastes' => 'nullable|array',
            'wastes.*' => 'nullable|integer|min:0',
        ]);

        $pengiriman = Pengiriman::findOrFail($id);
        $pengiriman->nama_penerima = $request->nama_penerima;
        $pengiriman->ompreng_kembali = $request->ompreng_kembali;
        $pengiriman->menu_tersisa = $request->menu_tersisa;
        $pengiriman->tanggal_sisa = $request->tanggal_sisa;

        // Hapus data plate wastes lama untuk pengiriman ini agar bisa di-update bersih
        PlateWaste::where('pengiriman_id', $pengiriman->id)->delete();

        $wastes = $request->input('wastes', []);
        $totalSisaOmpreng = 0;
        $primaryReason = null;
        $maxReasonPorsi = -1;

        $tanggal = $request->tanggal_sisa ?: Carbon::today()->format('Y-m-d');

        foreach ($wastes as $keterangan => $porsi) {
            if ($porsi > 0) {
                $totalSisaOmpreng += $porsi;

                PlateWaste::create([
                    'jumlah_waste' => $porsi,
                    'tanggal' => $tanggal,
                    'keterangan' => $keterangan,
                    'sekolah_id' => $pengiriman->sekolah_id,
                    'pengiriman_id' => $pengiriman->id,
                ]);

                if ($porsi > $maxReasonPorsi) {
                    $maxReasonPorsi = $porsi;
                    $primaryReason = $keterangan;
                }
            }
        }

        $pengiriman->jumlah_sisa_ompreng = $totalSisaOmpreng;
        $pengiriman->keterangan = $primaryReason; // Simpan alasan utama (porsi terbanyak) untuk kompatibilitas

        // If handover is done, status should be 'Sampai'
        $pengiriman->status_kirim = 'Sampai';
        if (! $pengiriman->waktu_sampai) {
            $pengiriman->waktu_sampai = Carbon::now();
        }

        $pengiriman->save();

        return redirect()->back()->with('success', 'Bukti serah terima berhasil disimpan.');
    }
}
