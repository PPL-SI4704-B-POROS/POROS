@extends('layouts.app')
@section('title', 'Pengumuman')
@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    .pgm-root {
        font-family: 'Plus Jakarta Sans', sans-serif;
        padding: 32px 36px;
        background: #f8f9fc;
        min-height: 100vh;
    }

    /* Page Header */
    .pgm-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 32px;
    }

    .pgm-header-left h1 {
        font-size: 1.75rem;
        font-weight: 800;
        color: #0c1e35;
        margin: 0 0 4px 0;
        letter-spacing: -0.4px;
    }

    .pgm-header-left p {
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
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    .btn-back:hover {
        border-color: #0c1e35;
        color: #0c1e35;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    /* Stats Row */
    .pgm-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 28px;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 20px 22px;
        border: 1.5px solid #f1f5f9;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .stat-icon.orange { background: #fff3e8; }
    .stat-icon.blue   { background: #eff6ff; }
    .stat-icon.green  { background: #f0fdf4; }

    .stat-info .label {
        font-size: 12px;
        font-weight: 600;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }

    .stat-info .value {
        font-size: 22px;
        font-weight: 800;
        color: #0c1e35;
        letter-spacing: -0.5px;
    }

    /* Main Layout */
    .pgm-layout {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 24px;
        align-items: start;
    }

    /* Card Base */
    .pgm-card {
        background: white;
        border-radius: 18px;
        border: 1.5px solid #f1f5f9;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        overflow: hidden;
    }

    .pgm-card-header {
        padding: 18px 24px;
        border-bottom: 1.5px solid #f8fafc;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .pgm-card-header .icon-badge {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .icon-badge.orange { background: linear-gradient(135deg, #ff6b00, #ffaa5e); }
    .icon-badge.blue   { background: linear-gradient(135deg, #2563eb, #60a5fa); }

    .pgm-card-header .card-title {
        font-size: 14.5px;
        font-weight: 700;
        color: #0c1e35;
        margin: 0;
    }

    /* Form */
    .pgm-form-body {
        padding: 22px 24px;
    }

    .form-group {
        margin-bottom: 16px;
    }

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
        min-height: 110px;
    }

    /* File input khusus */
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

    .file-upload-wrap input[type="file"] {
        display: none;
    }

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

    .file-upload-label span.highlight {
        color: #ff6b00;
        font-weight: 700;
    }

    #preview-img {
        width: 100%;
        max-height: 160px;
        object-fit: cover;
        border-radius: 8px;
        margin-top: 10px;
        display: none;
        border: 1px solid #f1f5f9;
    }

    .btn-publish {
        width: 100%;
        padding: 12px;
        background: linear-gradient(135deg, #ff6b00 0%, #ff9a3c 100%);
        color: white;
        font-weight: 700;
        font-size: 14px;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.18s;
        font-family: 'Plus Jakarta Sans', sans-serif;
        letter-spacing: 0.2px;
        box-shadow: 0 4px 12px rgba(255, 107, 0, 0.25);
    }

    .btn-publish:hover {
        box-shadow: 0 6px 18px rgba(255, 107, 0, 0.35);
        transform: translateY(-1px);
    }

    /* Daftar Pengumuman */
    .pgm-item {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 20px 24px;
        border-bottom: 1px solid #f8fafc;
        transition: background 0.15s;
    }

    .pgm-item:last-child { border-bottom: none; }
    .pgm-item:hover { background: #fafbfd; }

    .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #ff6b00, #ff9a3c);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 800;
        font-size: 12.5px;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(255,107,0,0.2);
    }

    .pgm-item-content { flex: 1; min-width: 0; }

    .pgm-item-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 5px;
    }

    .pgm-judul {
        font-size: 14.5px;
        font-weight: 700;
        color: #0c1e35;
        line-height: 1.3;
    }

    .pgm-meta {
        font-size: 11.5px;
        color: #94a3b8;
        font-weight: 500;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 5px;
        flex-wrap: wrap;
    }

    .pgm-meta .sep {
        width: 3px;
        height: 3px;
        border-radius: 50%;
        background: #cbd5e1;
        display: inline-block;
    }

    /* Badge target role */
    .badge-target {
        display: inline-block;
        font-size: 11px;
        font-weight: 700;
        padding: 2px 9px;
        border-radius: 20px;
        white-space: nowrap;
    }

    .badge-umum    { background: #eef2ff; color: #6366f1; }
    .badge-sekolah { background: #ecfeff; color: #0891b2; }
    .badge-dapur   { background: #fffbeb; color: #d97706; }

    .pgm-isi {
        font-size: 13px;
        color: #64748b;
        line-height: 1.65;
        margin: 0 0 10px 0;
    }

    /* Gambar di item pengumuman */
    .pgm-gambar {
        width: 100%;
        max-height: 220px;
        object-fit: cover;
        border-radius: 10px;
        margin-bottom: 4px;
        border: 1px solid #f1f5f9;
    }

    .pgm-item-actions {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-shrink: 0;
    }

    .btn-edit {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        text-decoration: none;
        color: #2563eb;
        font-size: 12.5px;
        font-weight: 700;
        background: #eff6ff;
        border: 1.5px solid #dbeafe;
        border-radius: 8px;
        padding: 5px 12px;
        white-space: nowrap;
        transition: all 0.18s;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .btn-edit:hover {
        background: #dbeafe;
        border-color: #2563eb;
    }

    .btn-delete {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        color: #dc2626;
        font-size: 12.5px;
        font-weight: 700;
        background: #fef2f2;
        border: 1.5px solid #fecaca;
        border-radius: 8px;
        padding: 5px 12px;
        white-space: nowrap;
        transition: all 0.18s;
        cursor: pointer;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .btn-delete:hover {
        background: #fee2e2;
        border-color: #dc2626;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 52px 24px;
        color: #94a3b8;
    }

    .empty-state .empty-icon {
        width: 56px;
        height: 56px;
        background: #f1f5f9;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 14px;
    }

    .empty-state p {
        font-size: 13.5px;
        font-weight: 500;
        margin: 0;
    }

    /* Tips Card */
    .tips-card {
        background: linear-gradient(135deg, #0c1e35 0%, #1e3a5f 100%);
        border-radius: 18px;
        padding: 22px;
        margin-top: 20px;
        color: white;
    }

    .tips-title {
        font-size: 12px;
        font-weight: 700;
        color: rgba(255,255,255,0.5);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 14px;
    }

    .tips-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 10px;
        font-size: 13px;
        color: rgba(255,255,255,0.82);
        font-weight: 500;
        line-height: 1.5;
    }

    .tips-item:last-child { margin-bottom: 0; }

    .tips-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #ff6b00;
        flex-shrink: 0;
        margin-top: 6px;
    }

    /* Alert success */
    .alert-success {
        background: #f0fdf4;
        border: 1.5px solid #bbf7d0;
        color: #166534;
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 13.5px;
        font-weight: 600;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
</style>

<div class="pgm-root">

    {{-- Alert success --}}
    @if(session('success'))
    <div class="alert-success">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Page Header --}}
    <div class="pgm-header">
        <div class="pgm-header-left">
            <h1>Pengumuman</h1>
            <p>Kelola dan publikasikan pengumuman untuk seluruh pengguna sistem</p>
        </div>
        <a href="{{ route('dashboard.index') }}" class="btn-back">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
            Kembali ke Dashboard
        </a>
    </div>

    {{-- Stats Row --}}
    @php
        $totalPengumuman    = $pengumuman->count();
        $pengumumanHariIni  = $pengumuman->filter(fn($p) => $p->created_at->isToday())->count();
        $pengumumanMingguIni = $pengumuman->filter(fn($p) => $p->created_at->isCurrentWeek())->count();
    @endphp
    <div class="pgm-stats">
        <div class="stat-card">
            <div class="stat-icon orange">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ff6b00" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                </svg>
            </div>
            <div class="stat-info">
                <div class="label">Total Pengumuman</div>
                <div class="value">{{ $totalPengumuman }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </div>
            <div class="stat-info">
                <div class="label">Hari Ini</div>
                <div class="value">{{ $pengumumanHariIni }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                </svg>
            </div>
            <div class="stat-info">
                <div class="label">Minggu Ini</div>
                <div class="value">{{ $pengumumanMingguIni }}</div>
            </div>
        </div>
    </div>

    {{-- Main Layout --}}
    <div class="pgm-layout">

        {{-- Kiri: Daftar Pengumuman --}}
        <div class="pgm-card">
            <div class="pgm-card-header">
                <div class="icon-badge blue">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                </div>
                <h2 class="card-title">Daftar Pengumuman</h2>
            </div>

            @forelse($pengumuman as $item)
            <div class="pgm-item">
                <div class="avatar">
                    {{ strtoupper(substr($item->pembuat->nama_lengkap, 0, 2)) }}
                </div>
                <div class="pgm-item-content">
                    <div class="pgm-item-top">
                        <div>
                            <div class="pgm-judul">{{ $item->judul }}</div>
                        </div>
                        @if(auth()->user()->role->nama_role === 'super admin')
                        <div class="pgm-item-actions">
                            <a href="{{ route('pengumuman.edit', $item) }}" class="btn-edit">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                Edit
                            </a>
                            <form method="POST" action="{{ route('pengumuman.destroy', $item) }}" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus pengumuman ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                                    Hapus
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>

                    {{-- Meta: pembuat + waktu + badge target --}}
                    <div class="pgm-meta">
                        <span>{{ $item->pembuat->nama_lengkap }}</span>
                        <span class="sep"></span>
                        <span>{{ $item->created_at->diffForHumans() }}</span>
                        <span class="sep"></span>
                        @php
                            $badgeClass = ['umum' => 'badge-umum', 'sekolah' => 'badge-sekolah', 'dapur' => 'badge-dapur'];
                            $badgeLabel = ['umum' => '🌐 Umum', 'sekolah' => '🏫 Sekolah', 'dapur' => '🍳 Dapur'];
                        @endphp
                        <span class="badge-target {{ $badgeClass[$item->target_role] ?? 'badge-umum' }}">
                            {{ $badgeLabel[$item->target_role] ?? 'Umum' }}
                        </span>
                    </div>

                    {{-- Gambar kalau ada --}}
                    @if($item->gambar)
                    <img src="{{ asset('storage/' . $item->gambar) }}"
                         alt="Gambar pengumuman"
                         class="pgm-gambar">
                    @endif

                    <p class="pgm-isi">{{ $item->isi }}</p>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <div class="empty-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.8">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                </div>
                <p>Belum ada pengumuman yang dipublikasikan.</p>
            </div>
            @endforelse
        </div>

        {{-- Kanan: Form + Tips --}}
        <div>
            @if(auth()->user()->role->nama_role === 'super admin')
            <div class="pgm-card">
                <div class="pgm-card-header">
                    <div class="icon-badge orange">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                    </div>
                    <h2 class="card-title">Buat Pengumuman Baru</h2>
                </div>
                <div class="pgm-form-body">
                    {{-- PENTING: enctype wajib ada untuk upload file --}}
                    <form method="POST" action="{{ route('pengumuman.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label>Judul</label>
                            <input type="text" name="judul" placeholder="Judul pengumuman..." required>
                        </div>

                        <div class="form-group">
                            <label>Isi Pengumuman</label>
                            <textarea name="isi" rows="4" placeholder="Tulis isi pengumuman di sini..." required></textarea>
                        </div>

                        {{-- Dropdown Target Role --}}
                        <div class="form-group">
                            <label>Ditujukan Untuk</label>
                            <select name="target_role">
                                <option value="umum">🌐 Semua Pengguna (Umum)</option>
                                <option value="sekolah">🏫 Sekolah Saja</option>
                                <option value="dapur">🍳 Dapur Saja</option>
                            </select>
                        </div>

                        {{-- Upload Gambar --}}
                        <div class="form-group">
                            <label>Gambar (Opsional)</label>
                            <div class="file-upload-wrap" onclick="document.getElementById('gambar-input').click()">
                                <label class="file-upload-label">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                    <span>Klik untuk pilih gambar atau <span class="highlight">Browse</span></span>
                                </label>
                                <input type="file" id="gambar-input" name="gambar" accept="image/*" onchange="previewGambar(event)">
                                <img id="preview-img" src="" alt="preview">
                            </div>
                            <p style="font-size:11.5px; color:#94a3b8; margin:6px 0 0 0; font-weight:500;">Format: JPG, PNG, WEBP — Maks. 2MB</p>
                        </div>

                        <button type="submit" class="btn-publish">Publikasikan Sekarang</button>
                    </form>
                </div>
            </div>
            @endif

            {{-- Tips Card --}}
            <div class="tips-card">
                <div class="tips-title">💡 Tips Pengumuman</div>
                <div class="tips-item">
                    <div class="tips-dot"></div>
                    <span>Gunakan judul yang singkat, jelas, dan mudah dipahami</span>
                </div>
                <div class="tips-item">
                    <div class="tips-dot"></div>
                    <span>Pilih target penerima agar informasi tepat sasaran</span>
                </div>
                <div class="tips-item">
                    <div class="tips-dot"></div>
                    <span>Tambahkan gambar agar pengumuman lebih menarik perhatian</span>
                </div>
                <div class="tips-item">
                    <div class="tips-dot"></div>
                    <span>Pengumuman "Umum" akan terlihat oleh semua pengguna sistem</span>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    // Preview gambar sebelum upload
    function previewGambar(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('preview-img');
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