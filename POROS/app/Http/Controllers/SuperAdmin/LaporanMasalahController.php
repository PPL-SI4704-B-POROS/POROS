<?php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\LaporanMasalah;
use Illuminate\Http\Request;

class LaporanMasalahController extends Controller
{
    public function index(Request $request)
    {
        $query = LaporanMasalah::with('user.role')
            ->orderByRaw("FIELD(status, 'Open', 'In Progress', 'Resolved')")
            ->orderBy('created_at', 'asc');    

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('role')) {
            $query->whereHas('user.role', function ($q) use ($request) {
                $q->where('nama_role', $request->role);
            });
        }
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $laporan = $query->paginate(10)->withQueryString();
        return view('dashboards.superadmin.laporan-masalah.index', compact('laporan'));
    }

    public function updateStatus(Request $request, LaporanMasalah $laporanMasalah)
    {
        $request->validate([
            'status' => 'required|in:Open,In Progress,Resolved',
        ]);
        $laporanMasalah->update(['status' => $request->status]);
        return back()->with('success', 'Status laporan berhasil diperbarui.');
    }

    public function destroy(LaporanMasalah $laporanMasalah)
    {
        if ($laporanMasalah->foto_bukti) {
            $path = public_path('storage/' . $laporanMasalah->foto_bukti);
            if (file_exists($path)) unlink($path);
        }
        $laporanMasalah->delete();
        return back()->with('success', 'Laporan masalah berhasil dihapus.');
    }
}