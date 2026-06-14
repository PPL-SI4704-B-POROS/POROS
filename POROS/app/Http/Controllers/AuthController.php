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
            if (Auth::user()->status == 'Inactive') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return back()->withErrors([
                    'email' => 'Akun Anda dinonaktifkan. Silakan hubungi Administrator.',
                ])->onlyInput('email');
            }

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
        $role = Auth::user()->role?->nama_role;

        return match ($role) {
            'super admin' => redirect()->route('users.index'),
            'dapur' => redirect()->route('dashboard.meal_planning'),
            'sekolah' => redirect()->route('sekolah.siswas.index'),
            default => redirect()->route('login'),
        };
    }
}
