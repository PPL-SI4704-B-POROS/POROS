<?php

namespace App\Http\Controllers\Sekolah;

use App\Models\Siswa;
use App\Models\Antropometri;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $sekolah_id = Auth::user()->sekolah_id;

        $query = Siswa::with(['antropometris' => function($q) {
            $q->orderBy('tanggal_ukur', 'desc');
        }])->where('sekolah_id', $sekolah_id);

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('nama_siswa', 'like', "%$search%")
                  ->orWhere('nisn', 'like', "%$search%");
            });
        }

        $siswas = $query->paginate(10)->withQueryString();

        return view('dashboards.sekolah.siswas.index', compact('siswas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_siswa' => 'required|string|max:255',
            'nisn' => 'required|string|max:20|unique:siswas,nisn',
            'kelas' => 'nullable|string|max:20',
            'alergi' => 'nullable|string',
            'contact' => 'nullable|string|max:20',
            'status' => 'required|in:Active,Inactive',
        ]);

        Siswa::create(array_merge($request->only('nama_siswa', 'nisn', 'kelas', 'alergi', 'contact', 'status'), [
            'sekolah_id' => Auth::user()->sekolah_id
        ]));

        return redirect()->route('sekolah.siswas.index')->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function update(Request $request, Siswa $siswa)
    {
        $request->validate([
            'nama_siswa' => 'required|string|max:255',
            'nisn' => 'required|string|max:20|unique:siswas,nisn,' . $siswa->id,
            'kelas' => 'nullable|string|max:20',
            'alergi' => 'nullable|string',
            'contact' => 'nullable|string|max:20',
            'status' => 'required|in:Active,Inactive',
        ]);

        $siswa->update($request->only('nama_siswa', 'nisn', 'kelas', 'alergi', 'contact', 'status'));

        return redirect()->route('sekolah.siswas.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Siswa $siswa)
    {
        $siswa->delete();
        return redirect()->route('sekolah.siswas.index')->with('success', 'Siswa berhasil dihapus.');
    }

    public function storeAntropometri(Request $request, $siswa_id)
    {
        $request->validate([
            'berat_badan' => 'required|numeric',
            'tinggi_badan' => 'required|numeric',
            'tanggal_ukur' => 'required|date',
        ]);

        $imt = $request->berat_badan / (($request->tinggi_badan / 100) ** 2);
        
        // Basic status gizi logic
        $status_gizi = 'Normal';
        if ($imt < 18.5) $status_gizi = 'Kurus';
        elseif ($imt >= 25 && $imt < 30) $status_gizi = 'Gemuk';
        elseif ($imt >= 30) $status_gizi = 'Obesitas';

        Antropometri::create([
            'siswa_id' => $siswa_id,
            'berat_badan' => $request->berat_badan,
            'tinggi_badan' => $request->tinggi_badan,
            'imt' => round($imt, 2),
            'status_gizi' => $status_gizi,
            'tanggal_ukur' => $request->tanggal_ukur,
        ]);

        return redirect()->route('sekolah.siswas.index')->with('success', 'Data antropometri berhasil disimpan.');
    }
}
