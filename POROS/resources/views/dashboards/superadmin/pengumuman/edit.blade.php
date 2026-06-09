@extends('layouts.app')
@section('title', 'Edit Pengumuman')
@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    .edit-root {
        font-family: 'Plus Jakarta Sans', sans-serif;
        padding: 32px 36px;
        background: #f8f9fc;
        min-height: 100vh;
    }

    .edit-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 28px;
    }

    .edit-header h1 {
        font-size: 1.75rem;
        font-weight: 800;
        color: #0c1e35;
        margin: 0 0 4px 0;
    }

    .edit-header p {
        font-size: 13.5px;
        color: #94a3b8;
        margin: 0;
        font-weight: 500;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        color: #64748b;
        font-size: 13.5px;
        font-weight: 600;
        background: white;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 9px 16px;
        transition: all 0.18s;
    }

    .btn-back:hover { border-color: #0c1e35; color: #0c1e35; }

    .edit-card {
        background: white;
        border-radius: 18px;
        border: 1.5px solid #f1f5f9;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        max-width: 720px;
    }

    .edit-card-header {
        padding: 18px 24px;
        border-bottom: 1.5px solid #f8fafc;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .icon-badge-orange {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: linear-gradient(135deg, #ff6b00, #ffaa5e);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .edit-form { padding: 24px; }

    .form-group { margin-bottom: 18px; }

    .form-group label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: #475569;
        margin-bottom: 7px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .form-group input[type="text"],
    .form-group textarea,
    .form-group select {
        width: 100%;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 13px;
        font-size: 13.5px;
        color: #0c1e35;
        outline: none;
        transition: all 0.18s;
        box-sizing: border-box;
        font-family: 'Plus Jakarta Sans', sans-serif;
        background: #fafbfc;
    }

    .form-group input[type="text"]:focus,
    .form-group textarea:focus,
    .form-group select:focus {
        border-color: #ff6b00;
        background: white;
        box-shadow: 0 0 0 3px rgba(255, 107, 0, 0.08);
    }

    .form-group textarea {
        resize: vertical;
        min-height: 120px;
    }

    .gambar-preview-lama {
        width: 100%;
        max-height: 200px;
        object-fit: cover;
        border-radius: 10px;
        margin-bottom: 8px;
        border: 1px solid #f1f5f9;
    }

    .file-upload-wrap {
        border: 1.5px dashed #e2e8f0;
        border-radius: 10px;
        padding: 14px;
        background: #fafbfc;
        text-align: center;
        cursor: pointer;
        transition: all 0.18s;
    }

    .file-upload-wrap:hover {
        border-color: #ff6b00;
        background: #fff8f3;
    }

    .file-upload-wrap input[type="file"] { display: none; }

    .file-upload-label {
        font-size: 13px;
        color: #64748b;
        font-weight: 500;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .file-upload-label span.highlight { color: #ff6b00; font-weight: 700; }

    #preview-img-edit {
        width: 100%;
        max-height: 160px;
        object-fit: cover;
        border-radius: 8px;
        margin-top: 10px;
        display: none;
        border: 1px solid #f1f5f9;
    }

    .btn-row { display: flex; gap: 12px; }

    .btn-save {
        flex: 1;
        padding: 12px;
        background: linear-gradient(135deg, #ff6b00 0%, #ff9a3c 100%);
        color: white;
        font-weight: 700;
        font-size: 14px;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        font-family: 'Plus Jakarta Sans', sans-serif;
        box-shadow: 0 4px 12px rgba(255, 107, 0, 0.25);
        transition: all 0.18s;
    }

    .btn-save:hover {
        box-shadow: 0 6px 18px rgba(255, 107, 0, 0.35);
        transform: translateY(-1px);
    }

    .btn-cancel {
        padding: 12px 20px;
        background: white;
        color: #64748b;
        font-weight: 700;
        font-size: 14px;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        font-family: 'Plus Jakarta Sans', sans-serif;
        transition: all 0.18s;
    }

    .btn-cancel:hover { border-color: #94a3b8; color: #0c1e35; }
</style>

<div class="edit-root">

    <div class="edit-header">
        <div>
            <h1>Edit Pengumuman</h1>
            <p>Perbarui judul, isi, gambar, atau target penerima pengumuman</p>
        </div>
        <a href="{{ route('pengumuman.index') }}" class="btn-back">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
            Kembali
        </a>
    </div>

    <div class="edit-card">
        <div class="edit-card-header">
            <div class="icon-badge-orange">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            </div>
            <h2 style="font-size:14.5px; font-weight:700; color:#0c1e35; margin:0;">Edit Pengumuman</h2>
        </div>

        <div class="edit-form">
            <form method="POST" action="{{ route('pengumuman.update', $pengumuman) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Judul</label>
                    <input type="text" name="judul" value="{{ old('judul', $pengumuman->judul) }}" required>
                </div>

                <div class="form-group">
                    <label>Isi Pengumuman</label>
                    <textarea name="isi" rows="6" required>{{ old('isi', $pengumuman->isi) }}</textarea>
                </div>

                <div class="form-group">
                    <label>Ditujukan Untuk</label>
                    <select name="target_role">
                        <option value="umum"    {{ old('target_role', $pengumuman->target_role) === 'umum'    ? 'selected' : '' }}>🌐 Semua Pengguna (Umum)</option>
                        <option value="sekolah" {{ old('target_role', $pengumuman->target_role) === 'sekolah' ? 'selected' : '' }}>🏫 Sekolah Saja</option>
                        <option value="dapur"   {{ old('target_role', $pengumuman->target_role) === 'dapur'   ? 'selected' : '' }}>🍳 Dapur Saja</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Gambar (Opsional)</label>

                    {{-- Preview gambar lama --}}
                    @if($pengumuman->gambar)
                        <img src="{{ asset('storage/' . $pengumuman->gambar) }}"
                             class="gambar-preview-lama"
                             alt="Gambar saat ini">
                        <p style="font-size:12px; color:#94a3b8; margin:0 0 10px 0;">
                            ⬆️ Gambar saat ini. Upload baru untuk menggantinya.
                        </p>
                    @endif

                    <div class="file-upload-wrap" onclick="document.getElementById('gambar-input-edit').click()">
                        <label class="file-upload-label">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            <span>Klik untuk ganti gambar atau <span class="highlight">Browse</span></span>
                        </label>
                        <input type="file" id="gambar-input-edit" name="gambar" accept="image/*" onchange="previewGambarEdit(event)">
                        <img id="preview-img-edit" src="" alt="preview baru">
                    </div>
                    <p style="font-size:11.5px; color:#94a3b8; margin:6px 0 0 0; font-weight:500;">Format: JPG, PNG, WEBP — Maks. 2MB</p>
                </div>

                <div class="btn-row">
                    <button type="submit" class="btn-save">Simpan Perubahan</button>
                    <a href="{{ route('pengumuman.index') }}" class="btn-cancel">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function previewGambarEdit(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('preview-img-edit');
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
        }
    }
</script>

@endsection