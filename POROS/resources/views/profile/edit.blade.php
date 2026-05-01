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
                            <label class="form-label">Role</label>
                            <div style="padding: 0.75rem 1rem; border-radius: 0.75rem; background: #f3f4f6; color: var(--text-dark); font-weight: 600; display: inline-block;">
                                {{ ucwords($user->role->nama_role) }}
                            </div>
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-input" value="{{ old('nama_lengkap', $user->nama_lengkap) }}">
                            @error('nama_lengkap') <div class="error-msg" style="margin-top: 5px; margin-bottom: 0; color: red; font-size: 0.8rem;">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="no_telp" class="form-input" value="{{ old('no_telp', $user->no_telp) }}">
                            @error('no_telp') <div class="error-msg" style="margin-top: 5px; margin-bottom: 0; color: red; font-size: 0.8rem;">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Lokasi</label>
                            <input type="text" name="lokasi" class="form-input" value="{{ old('lokasi', $user->lokasi) }}">
                            @error('lokasi') <div class="error-msg" style="margin-top: 5px; margin-bottom: 0; color: red; font-size: 0.8rem;">{{ $message }}</div> @enderror
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
