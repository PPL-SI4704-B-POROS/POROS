<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            return $this->redirectUserByRole();
        }
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            return $this->redirectUserByRole();
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    protected function redirectUserByRole()
    {
        $role = Auth::user()->role->nama_role;

        return match ($role) {
            'super admin' => redirect()->intended('/dashboard/superadmin'),
            'dapur' => redirect()->intended('/dashboard/dapur'),
            'sekolah' => redirect()->intended('/dashboard/sekolah'),
            default => redirect('/login'),
        };
    }
}
