@extends('layouts.app')

@section('title', 'Edit Pengumuman')

@section('content')
<div class="dashboard-container">
    <div class="page-header">
        <h1>Edit Pengumuman</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('pengumuman.update', $pengumuman) }}">
                @csrf
                @method('PUT')
                <div style="margin-bottom: 16px;">
                    <label style="display:block; margin-bottom: 6px; font-weight: 500;">Judul</label>
                    <input type="text" name="judul" class="form-control" value="{{ old('judul', $pengumuman->judul) }}" required>
                </div>
                <div style="margin-bottom: 16px;">
                    <label style="display:block; margin-bottom: 6px; font-weight: 500;">Isi Pengumuman</label>
                    <textarea name="isi" class="form-control" rows="6" required>{{ old('isi', $pengumuman->isi) }}</textarea>
                </div>
                <div style="display: flex; gap: 12px;">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('pengumuman.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection