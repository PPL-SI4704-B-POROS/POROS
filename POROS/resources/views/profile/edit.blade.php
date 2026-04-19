@extends('layouts.app')

@section('title', 'Edit Profil')

@section('styles')
<style>
    .profile-card { max-width: 800px; margin: 0 auto; }
</style>
@endsection

@section('content')
<div class="dashboard-layout">
    @include('partials.sidebar')

    <main class="main-content">
        @include('partials.header')

        <div style="margin-bottom: 2rem;">
            <h1 style="font-size: 1.75rem; font-weight: 800; color: #0c1e35;">Pengaturan Profil</h1>
            <p style="color: var(--text-muted);">Kelola informasi akun dan keamanan Anda di sini.</p>
        </div>

        @if(session('success'))
            <div class="success-alert">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="card profile-card">
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                
                <!-- Personal Information -->
                <div style="border-bottom: 1px solid #f3f4f6; margin-bottom: 2rem; padding-bottom: 2rem;">
                    <h3 style="font-size: 1.1rem; font-weight: 700; color: #0c1e35; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Informasi Pribadi
                    </h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="form-group" style="grid-column: span 2;">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-input" value="{{ old('nama_lengkap', $user->nama_lengkap) }}" required>
                            @error('nama_lengkap') <div class="error-msg" style="margin-top: 5px; margin-bottom: 0;">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label class="form-label">Alamat Email (Tidak dapat diubah)</label>
                            <input type="email" class="form-input" value="{{ $user->email }}" disabled>
                            <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">Hubungi admin jika Anda ingin mengganti alamat email.</p>
                        </div>
                    </div>
                </div>

                <!-- Security -->
                <div>
                    <h3 style="font-size: 1.1rem; font-weight: 700; color: #0c1e35; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Ganti Password
                    </h3>
                    <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.5rem;">Isi bagian ini hanya jika Anda ingin memperbarui password akun.</p>
                    
                    <div class="form-group">
                        <label class="form-label">Password Saat Ini</label>
                        <input type="password" name="current_password" class="form-input" placeholder="Masukkan password lama untuk konfirmasi">
                        @error('current_password') <div class="error-msg" style="margin-top: 5px; margin-bottom: 0;">{{ $message }}</div> @enderror
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="form-group">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password" class="form-input" placeholder="Minimal 8 karakter">
                            @error('password') <div class="error-msg" style="margin-top: 5px; margin-bottom: 0;">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" class="form-input" placeholder="Ulangi password baru">
                        </div>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem;">
                    <button type="button" class="btn" style="background: #f3f4f6; color: #4b5563; width: auto; padding: 0.75rem 2rem; border-radius: 12px; font-size: 0.875rem;" onclick="window.history.back()">Batal</button>
                    <button type="submit" class="btn btn-primary" style="width: auto; padding: 0.75rem 2.5rem;">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </main>
</div>
@endsection
