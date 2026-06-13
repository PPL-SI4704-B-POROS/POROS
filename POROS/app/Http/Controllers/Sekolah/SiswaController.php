<?php

namespace App\Http\Controllers\Sekolah;

use App\Http\Controllers\Controller;
use App\Models\Antropometri;
use App\Models\Siswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $sekolah_id = Auth::user()->sekolah_id;

        $query = Siswa::with(['antropometris' => function ($q) {
            $q->orderBy('tanggal_ukur', 'desc');
        }])->where('sekolah_id', $sekolah_id);

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama_siswa', 'like', "%$search%")
                    ->orWhere('nisn', 'like', "%$search%");
            });
        }

        $siswas = $query->latest()->paginate(10)->withQueryString();

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
            'sekolah_id' => Auth::user()->sekolah_id,
        ]));

        return redirect()->route('sekolah.siswas.index')->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function update(Request $request, Siswa $siswa)
    {
        $request->validate([
            'nama_siswa' => 'required|string|max:255',
            'nisn' => 'required|string|max:20|unique:siswas,nisn,'.$siswa->id,
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
            'berat_badan' => 'required|numeric|min:0.1|max:500',
            'tinggi_badan' => 'required|numeric|min:0.1|max:300',
            'tanggal_ukur' => 'required|date',
        ]);

        $imt = $request->berat_badan / (($request->tinggi_badan / 100) ** 2);

        // Disamakan dengan visual badge di riwayat_kesehatan.blade.php
        $status_gizi = 'Normal';
        if ($imt < 18.5) {
            $status_gizi = 'Kurus';
        } elseif ($imt >= 25 && $imt < 30) {
            $status_gizi = 'Gemuk';
        } elseif ($imt >= 30) {
            $status_gizi = 'Obesitas';
        }

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

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file_csv' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file_csv');
        $filePath = $file->getRealPath();

        $firstLine = fgets(fopen($filePath, 'r'));
        $delimiter = ',';
        if ($firstLine !== false) {
            $numCommas = substr_count($firstLine, ',');
            $numSemicolons = substr_count($firstLine, ';');
            if ($numSemicolons > $numCommas) {
                $delimiter = ';';
            }
        }

        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            return redirect()->back()->with('error', 'Gagal membuka file CSV.');
        }

        $header = fgetcsv($handle, 1000, $delimiter);
        if (! $header) {
            fclose($handle);

            return redirect()->back()->with('error', 'File CSV kosong.');
        }

        $header = array_map(function (string $col): string {
            $col = strtolower(trim($col));
            if (in_array($col, ['nama', 'name', 'nama siswa', 'nama_siswa'])) {
                return 'nama_siswa';
            }
            if (in_array($col, ['nisn'])) {
                return 'nisn';
            }
            if (in_array($col, ['kelas', 'class'])) {
                return 'kelas';
            }
            if (in_array($col, ['kontak', 'contact', 'no_telp', 'no_hp', 'telepon'])) {
                return 'contact';
            }
            if (in_array($col, ['alergi', 'allergy', 'allergi'])) {
                return 'alergi';
            }
            if (in_array($col, ['status'])) {
                return 'status';
            }

            return $col;
        }, $header);

        if (! in_array('nama_siswa', $header) || ! in_array('nisn', $header)) {
            fclose($handle);

            return redirect()->back()->with('error', 'File CSV harus memiliki kolom nama dan nisn.');
        }

        $sekolah_id = Auth::user()->sekolah_id;
        $successCount = 0;
        $failCount = 0;
        $errors = [];
        $rowNum = 1;

        while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
            $rowNum++;

            if (count(array_filter($row)) === 0) {
                continue;
            }

            if (count($row) < count($header)) {
                $row = array_pad($row, count($header), null);
            }
            $data = array_combine($header, array_slice($row, 0, count($header)));

            $nama_siswa = trim($data['nama_siswa'] ?? '');
            $nisn = trim($data['nisn'] ?? '');
            $kelas = trim($data['kelas'] ?? '');
            $contact = trim($data['contact'] ?? '');
            $alergi = trim($data['alergi'] ?? '');
            $statusRaw = trim($data['status'] ?? 'Active');

            if (empty($nama_siswa)) {
                $failCount++;
                $errors[] = "Baris $rowNum: Nama siswa tidak boleh kosong.";

                continue;
            }

            if (empty($nisn)) {
                $failCount++;
                $errors[] = "Baris $rowNum: NISN tidak boleh kosong.";

                continue;
            }

            $existing = Siswa::withTrashed()->where('nisn', $nisn)->first();
            if ($existing) {
                $failCount++;
                $errors[] = "Baris $rowNum: NISN '$nisn' sudah terdaftar.";

                continue;
            }

            $status = 'Active';
            if (strtolower($statusRaw) === 'inactive' || strtolower($statusRaw) === 'tidak aktif') {
                $status = 'Inactive';
            }

            Siswa::create([
                'nama_siswa' => $nama_siswa,
                'nisn' => $nisn,
                'kelas' => $kelas ?: null,
                'sekolah_id' => $sekolah_id,
                'contact' => $contact ?: null,
                'alergi' => $alergi ?: null,
                'status' => $status,
            ]);

            $successCount++;
        }

        fclose($handle);

        $message = "Berhasil mengimpor $successCount siswa.";
        if ($failCount > 0) {
            $message .= " Gagal mengimpor $failCount baris.";

            return redirect()->route('sekolah.siswas.index')
                ->with('success', $message)
                ->with('import_errors', $errors);
        }

        return redirect()->route('sekolah.siswas.index')->with('success', $message);
    }

    public function riwayatKesehatan(Request $request): View
    {
        $sekolah_id = Auth::user()->sekolah_id;

        $query = Antropometri::with('siswa')
            ->whereHas('siswa', function ($q) use ($sekolah_id) {
                $q->where('sekolah_id', $sekolah_id);
            });

        if ($request->has('search') && ! empty($request->get('search'))) {
            $search = $request->get('search');
            $query->whereHas('siswa', function ($q) use ($search) {
                $q->where('nama_siswa', 'like', "%$search%")
                    ->orWhere('nisn', 'like', "%$search%");
            });
        }

        if ($request->has('kelas') && ! empty($request->get('kelas'))) {
            $kelas = $request->get('kelas');
            $query->whereHas('siswa', function ($q) use ($kelas) {
                $q->where('kelas', $kelas);
            });
        }

        if ($request->has('tanggal_mulai') && ! empty($request->get('tanggal_mulai'))) {
            $query->where('tanggal_ukur', '>=', $request->get('tanggal_mulai'));
        }

        if ($request->has('tanggal_selesai') && ! empty($request->get('tanggal_selesai'))) {
            $query->where('tanggal_ukur', '<=', $request->get('tanggal_selesai'));
        }

        $riwayat = $query->orderBy('tanggal_ukur', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $classes = Siswa::where('sekolah_id', $sekolah_id)
            ->whereNotNull('kelas')
            ->distinct()
            ->pluck('kelas');

        return view('dashboards.sekolah.siswas.riwayat_kesehatan', compact('riwayat', 'classes'));
    }

    public function importAntropometri(Request $request): RedirectResponse
    {
        $request->validate([
            'file_csv' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file_csv');
        $filePath = $file->getRealPath();

        $firstLine = fgets(fopen($filePath, 'r'));
        $delimiter = ',';
        if ($firstLine !== false) {
            $numCommas = substr_count($firstLine, ',');
            $numSemicolons = substr_count($firstLine, ';');
            if ($numSemicolons > $numCommas) {
                $delimiter = ';';
            }
        }

        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            return redirect()->back()->with('error', 'Gagal membuka file CSV.');
        }

        $header = fgetcsv($handle, 1000, $delimiter);
        if (! $header) {
            fclose($handle);

            return redirect()->back()->with('error', 'File CSV kosong.');
        }

        $header = array_map(function (string $col): string {
            $col = strtolower(trim($col));
            if ($col === 'nisn') {
                return 'nisn';
            }
            if (in_array($col, ['berat_badan', 'berat badan', 'bb', 'weight'])) {
                return 'berat_badan';
            }
            if (in_array($col, ['tinggi_badan', 'tinggi badan', 'tb', 'height'])) {
                return 'tinggi_badan';
            }
            if (in_array($col, ['tanggal_ukur', 'tanggal ukur', 'tanggal', 'date'])) {
                return 'tanggal_ukur';
            }

            return $col;
        }, $header);

        if (! in_array('nisn', $header) || ! in_array('berat_badan', $header) || ! in_array('tinggi_badan', $header) || ! in_array('tanggal_ukur', $header)) {
            fclose($handle);

            return redirect()->back()->with('error', 'File CSV harus memiliki kolom nisn, berat_badan, tinggi_badan, dan tanggal_ukur.');
        }

        $sekolah_id = Auth::user()->sekolah_id;
        $successCount = 0;
        $failCount = 0;
        $errors = [];
        $rowNum = 1;

        while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
            $rowNum++;

            if (count(array_filter($row)) === 0) {
                continue;
            }

            if (count($row) < count($header)) {
                $row = array_pad($row, count($header), null);
            }
            $data = array_combine($header, array_slice($row, 0, count($header)));

            $nisn = trim($data['nisn'] ?? '');
            $berat_badan = trim($data['berat_badan'] ?? '');
            $tinggi_badan = trim($data['tinggi_badan'] ?? '');
            $tanggal_ukur = trim($data['tanggal_ukur'] ?? '');

            if (empty($nisn)) {
                $failCount++;
                $errors[] = "Baris $rowNum: NISN tidak boleh kosong.";

                continue;
            }

            if (empty($berat_badan) || ! is_numeric($berat_badan) || floatval($berat_badan) <= 0 || floatval($berat_badan) > 500) {
                $failCount++;
                $errors[] = "Baris $rowNum: Berat badan harus berupa angka positif tidak melebihi 500 kg.";

                continue;
            }

            if (empty($tinggi_badan) || ! is_numeric($tinggi_badan) || floatval($tinggi_badan) <= 0 || floatval($tinggi_badan) > 300) {
                $failCount++;
                $errors[] = "Baris $rowNum: Tinggi badan harus berupa angka positif tidak melebihi 300 cm.";

                continue;
            }

            if (empty($tanggal_ukur) || ! strtotime($tanggal_ukur)) {
                $failCount++;
                $errors[] = "Baris $rowNum: Tanggal ukur tidak valid.";

                continue;
            }

            $siswa = Siswa::where('nisn', $nisn)
                ->where('sekolah_id', $sekolah_id)
                ->first();

            if (! $siswa) {
                $failCount++;
                $errors[] = "Baris $rowNum: Siswa dengan NISN '$nisn' tidak ditemukan di sekolah Anda.";

                continue;
            }

            $bb = floatval($berat_badan);
            $tb = floatval($tinggi_badan);
            $imt = $bb / (($tb / 100) ** 2);

            $status_gizi = 'Normal';
            if ($imt < 18.5) {
                $status_gizi = 'Kurus';
            } elseif ($imt >= 25 && $imt < 30) {
                $status_gizi = 'Gemuk';
            } elseif ($imt >= 30) {
                $status_gizi = 'Obesitas';
            }

            Antropometri::create([
                'siswa_id' => $siswa->id,
                'berat_badan' => $bb,
                'tinggi_badan' => $tb,
                'imt' => round($imt, 2),
                'status_gizi' => $status_gizi,
                'tanggal_ukur' => date('Y-m-d', strtotime($tanggal_ukur)),
            ]);

            $successCount++;
        }

        fclose($handle);

        $message = "Berhasil mengimpor $successCount data antropometri.";
        if ($failCount > 0) {
            $message .= " Gagal mengimpor $failCount baris.";

            return redirect()->route('sekolah.riwayat-kesehatan.index')
                ->with('success', $message)
                ->with('import_errors', $errors);
        }

        return redirect()->route('sekolah.riwayat-kesehatan.index')->with('success', $message);
    }

    public function destroyAntropometri(Antropometri $antropometri): RedirectResponse
    {
        $sekolah_id = Auth::user()->sekolah_id;

        if ($antropometri->siswa->sekolah_id !== $sekolah_id) {
            abort(403, 'Unauthorized action.');
        }

        $antropometri->delete();

        return redirect()->route('sekolah.riwayat-kesehatan.index')->with('success', 'Data riwayat kesehatan berhasil dihapus.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:siswas,id',
        ]);

        $sekolah_id = Auth::user()->sekolah_id;

        $count = Siswa::whereIn('id', $request->ids)
            ->where('sekolah_id', $sekolah_id)
            ->delete();

        return redirect()->route('sekolah.siswas.index')
            ->with('success', "$count siswa berhasil dihapus.");
    }

    public function bulkDestroyAntropometri(Request $request): RedirectResponse
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:antropometris,id',
        ]);

        $sekolah_id = Auth::user()->sekolah_id;

        $ids = Antropometri::whereIn('id', $request->ids)
            ->whereHas('siswa', fn ($q) => $q->where('sekolah_id', $sekolah_id))
            ->pluck('id');

        $count = $ids->count();
        Antropometri::whereIn('id', $ids)->delete();

        return redirect()->route('sekolah.riwayat-kesehatan.index')
            ->with('success', "$count data riwayat kesehatan berhasil dihapus.");
    }
}
