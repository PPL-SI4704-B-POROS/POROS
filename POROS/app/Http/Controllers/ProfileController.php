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
            'nama_lengkap' => ['nullable', 'string', 'max:255'],
            'no_telp' => ['nullable', 'string', 'max:20'],
            'lokasi' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->filled('nama_lengkap')) {
            $user->nama_lengkap = $request->nama_lengkap;
        }
        if ($request->filled('no_telp')) {
            $user->no_telp = $request->no_telp;
        }
        if ($request->filled('lokasi')) {
            $user->lokasi = $request->lokasi;
        }

        $user->save();

        return redirect()->route('dashboard.index')->with('success', 'Profil berhasil diperbarui!');
    }
}
