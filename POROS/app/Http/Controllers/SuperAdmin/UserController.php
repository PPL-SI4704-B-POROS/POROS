<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\User;
use App\Models\Role;
use App\Models\Siswa;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'users');

        $users = null;
        $siswas = null;

        if ($tab == 'users') {
            $query = User::with(['role', 'sekolah']);
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where(function($q) use ($search) {
                    $q->where('nama_lengkap', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }
            if ($request->filled('role')) {
                $query->whereHas('role', function($q) use ($request) {
                    $q->where('nama_role', $request->role);
                });
            }
            $users = $query->paginate(10)->withQueryString();
        } else {
            $siswaQuery = \App\Models\Siswa::with(['sekolah', 'antropometris' => function($q) {
                $q->orderBy('tanggal_ukur', 'desc');
            }]);
            if ($request->has('search')) {
                $search = $request->get('search');
                $siswaQuery->where(function($q) use ($search) {
                    $q->where('nama_siswa', 'like', "%{$search}%")
                      ->orWhere('nisn', 'like', "%{$search}%");
                });
            }
            $siswas = $siswaQuery->paginate(10)->withQueryString();
        }

        // Stats
        $stats = [
            'student' => \App\Models\Siswa::count(),
            'admin' => User::whereHas('role', function($q) { $q->where('nama_role', 'super admin'); })->count(),
            'dapur' => User::whereHas('role', function($q) { $q->where('nama_role', 'dapur'); })->count(),
            'petugas_sekolah' => User::whereHas('role', function($q) { $q->where('nama_role', 'sekolah'); })->count(),
        ];

        $roles = Role::all();
        $sekolahs = \App\Models\Sekolah::all();

        return view('dashboards.superadmin.users.index', compact('users', 'siswas', 'stats', 'roles', 'sekolahs', 'tab'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'no_telp' => 'nullable|string|max:20',
            'lokasi' => 'nullable|string|max:255',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:Active,Inactive',
        ]);

        User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'no_telp' => $request->no_telp,
            'lokasi' => $request->lokasi,
            'role_id' => $request->role_id,
            'status' => $request->status,
        ]);

        return redirect()->route('users.index', ['tab' => 'users'])->with('success', 'User baru berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'no_telp' => 'nullable|string|max:20',
            'lokasi' => 'nullable|string|max:255',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:Active,Inactive',
        ]);

        $user->update($request->only('nama_lengkap', 'email', 'no_telp', 'lokasi', 'role_id', 'status'));

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->role->nama_role == 'super admin') {
            return redirect()->route('users.index')->with('error', 'Super Admin tidak dapat dihapus.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }

    public function storeSiswa(Request $request)
    {
        $request->validate([
            'nama_siswa' => 'required|string|max:255',
            'nisn' => 'required|string|max:20|unique:siswas,nisn',
            'kelas' => 'nullable|string|max:20',
            'alergi' => 'nullable|string',
            'contact' => 'nullable|string|max:20',
            'status' => 'required|in:Active,Inactive',
            'sekolah_id' => 'required|exists:sekolahs,id',
        ]);

        \App\Models\Siswa::create($request->only('nama_siswa', 'nisn', 'kelas', 'alergi', 'contact', 'status', 'sekolah_id'));

        return redirect()->route('users.index', ['tab' => 'siswa'])->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function updateSiswa(Request $request, \App\Models\Siswa $siswa)
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

        return redirect()->route('users.index', ['tab' => 'siswa'])->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroySiswa(\App\Models\Siswa $siswa)
    {
        $siswa->delete();

        return redirect()->route('users.index', ['tab' => 'siswa'])->with('success', 'Siswa berhasil dihapus.');
    }
}
