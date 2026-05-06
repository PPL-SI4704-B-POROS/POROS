<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\User;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\Sekolah;
use App\Models\Antropometri;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'users');
        $users = null;
        $siswas = null;

        if ($tab == 'users') {
            $query = User::with(['role', 'sekolah'])->latest();
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where(function($q) use ($search) {
                    $q->where('nama_lengkap', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }
            $users = $query->paginate(10)->withQueryString();
        } else {
            $siswaQuery = Siswa::with(['sekolah', 'antropometris'])->latest();
            if ($request->has('search')) {
                $search = $request->get('search');
                $siswaQuery->where(function($q) use ($search) {
                    $q->where('nama_siswa', 'like', "%{$search}%")
                                ->orWhere('nisn', 'like', "%{$search}%");
                });
            }
            $siswas = $siswaQuery->paginate(10)->withQueryString();
        }

        $stats = [
            'student' => Siswa::count(),
            'admin' => User::whereHas('role', function($q) { $q->where('nama_role', 'super admin'); })->count(),
            'dapur' => User::whereHas('role', function($q) { $q->where('nama_role', 'dapur'); })->count(),
            'petugas_sekolah' => User::whereHas('role', function($q) { $q->where('nama_role', 'sekolah'); })->count(),
        ];

        return view('dashboards.superadmin.users.index', [
            'users' => $users,
            'siswas' => $siswas,
            'stats' => $stats,
            'roles' => Role::all(),
            'sekolahs' => Sekolah::all(),
            'tab' => $tab
        ]);
    }

    public function indexSiswa(Request $request)
    {
        $search = $request->query('search');
        $siswas = Siswa::with(['antropometris' => function($q) {
                $q->latest('tanggal_ukur');
            }])
            ->latest() 
            ->when($search, function($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('nama_siswa', 'like', "%{$search}%")
                         ->orWhere('nisn', 'like', "%{$search}%");
                });
            })
            ->paginate(10);

        return view('dashboards.sekolah.siswas.index', compact('siswas'));
    }

    public function storeSiswa(Request $request)
    {
        $request->validate([
            'nama_siswa' => 'required|string|max:255',
            'nisn' => 'required|string|max:20|unique:siswas,nisn',
            'status' => 'required|in:Active,Inactive',
        ]);

        $data = $request->all();
        if (Auth::user()->role->nama_role == 'sekolah') {
            $data['sekolah_id'] = Auth::user()->sekolah_id; 
        }

        Siswa::create($data);
        if (Auth::user()->role->nama_role == 'sekolah') {
            return redirect()->route('sekolah.siswas.index')->with('success', 'Data siswa berhasil ditambahkan.');
        }
        return redirect()->route('users.index', ['tab' => 'siswa'])->with('success', 'Data siswa berhasil ditambahkan.');
    }

    /**
     * Update Siswa
     */
    public function updateSiswa(Request $request, Siswa $siswa)
    {
        $request->validate([
            'nama_siswa' => 'required|string|max:255',
            'nisn' => 'required|string|max:20|unique:siswas,nisn,' . $siswa->id,
            'status' => 'required|in:Active,Inactive',
        ]);

        $siswa->update($request->all());

        if (Auth::user()->role->nama_role == 'sekolah') {
            return redirect()->route('sekolah.siswas.index')->with('success', 'Data siswa berhasil diperbarui.');
        }
        return redirect()->route('users.index', ['tab' => 'siswa'])->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Destroy Siswa
     */
    public function destroySiswa(Siswa $siswa)
    {
        $siswa->delete();
        
        if (Auth::user()->role->nama_role == 'sekolah') {
            return redirect()->route('sekolah.siswas.index')->with('success', 'Siswa berhasil dihapus.');
        }
        return redirect()->route('users.index', ['tab' => 'siswa'])->with('success', 'Siswa berhasil dihapus.');
    }

    /**
     * Simpan Data Ukur (Antropometri)
     */
    public function storeAntropometri(Request $request, $id)
    {
        $request->validate([
            'berat_badan' => 'required|numeric',
            'tinggi_badan' => 'required|numeric',
            'tanggal_ukur' => 'required|date',
        ]);

        Antropometri::create([
            'siswa_id' => $id,
            'berat_badan' => $request->berat_badan,
            'tinggi_badan' => $request->tinggi_badan,
            'tanggal_ukur' => $request->tanggal_ukur,
        ]);

        return redirect()->back()->with('success', 'Data antropometri berhasil disimpan.');
    }

    // --- Method Dasar User (Super Admin) ---
    public function store(Request $request) {
        $request->validate(['email' => 'required|unique:users,email', 'password' => 'required|min:6']);
        User::create(array_merge($request->all(), ['password' => Hash::make($request->password)]));
        return redirect()->route('users.index')->with('success', 'User berhasil dibuat.');
    }

    public function update(Request $request, User $user) {
        $user->update($request->all());
        return redirect()->route('users.index')->with('success', 'User berhasil diupdate.');
    }

    public function destroy(User $user) {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}