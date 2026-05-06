@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="logo-container">
            <h1 class="logo-text">POROS</h1>
            <p style="color: var(--text-muted); font-size: 0.875rem; margin-top: 0.5rem;">Silakan masuk dengan akun Anda</p>
        </div>

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-input" placeholder="admin@poros.com" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" required>
            </div>

            <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 2rem;">
                <input type="checkbox" id="remember" name="remember" style="accent-color: var(--primary); width: 16px; height: 16px;">
                <label for="remember" style="font-size: 0.875rem; color: var(--text-muted); cursor: pointer;">Ingat saya</label>
            </div>

            <button type="submit" class="btn-primary">Masuk ke Dashboard</button>
        </form>

        <div class="auth-footer">
            <span>Lupa password?</span>
            <a href="#" style="color: var(--primary); text-decoration: none; font-weight: 600; margin-left: 0.25rem;">Hubungi Admin</a>
        </div>
    </div>
</div>
@endsection
