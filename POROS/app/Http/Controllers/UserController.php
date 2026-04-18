<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Siswa;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['role', 'sekolah']);

        // Search Logic
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter Logic
        if ($request->filled('role')) {
            $query->whereHas('role', function($q) use ($request) {
                $q->where('nama_role', $request->role);
            });
        }

        $users = $query->paginate(10);

        // Stats
        $stats = [
            'student' => Siswa::count(),
            'admin' => User::whereHas('role', function($q) { $q->where('nama_role', 'super admin'); })->count(),
            'dapur' => User::whereHas('role', function($q) { $q->where('nama_role', 'dapur'); })->count(),
            'petugas_sekolah' => User::whereHas('role', function($q) { $q->where('nama_role', 'sekolah'); })->count(),
        ];

        return view('dashboards.superadmin.users.index', compact('users', 'stats'));
    }
}
