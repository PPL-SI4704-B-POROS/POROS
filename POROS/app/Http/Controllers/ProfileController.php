<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', [
            'user' => Auth::user()
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'current_password' => [
                $request->filled('password') ? 'required' : 'nullable', 
                function ($attribute, $value, $fail) use ($user) {
                    if ($value && !Hash::check($value, $user->password)) {
                        $fail('Password saat ini tidak cocok.');
                    }
                }
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->nama_lengkap = $request->nama_lengkap;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Redirect to dashboard based on role
        $role = $user->role->nama_role;
        $routeName = 'dashboard.superadmin'; // default

        if ($role == 'dapur') {
            $routeName = 'dashboard.dapur';
        } elseif ($role == 'sekolah') {
            $routeName = 'dashboard.sekolah';
        }

        return redirect()->route($routeName)->with('success', 'Profil berhasil diperbarui!');
    }
}
