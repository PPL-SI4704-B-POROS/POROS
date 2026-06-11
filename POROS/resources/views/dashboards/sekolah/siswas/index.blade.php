@extends('layouts.app')

@section('title', 'Data Siswa')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/users.css') }}">
<style>
    .link-ukur { color: #ff6b00; background: #fff5ed; border: none; padding: 0.5rem; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.3s; }
    .link-ukur:hover { background: #ffede0; }
    .avatar { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; margin-right: 12px; font-size: 0.9rem; }
</style>
@endsection

@section('content')
<div class="dashboard-layout">
    @include('partials.sidebar')

    <main class="main-content">
        @include('partials.header')

        <div class="planning-header">
            <div>
                <h1 style="font-size: 1.75rem; font-weight: 800; color: #0c1e35;">Data Siswa</h1>
                <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 0.25rem;">Kelola profil siswa dan data kesehatan dasar</p>
            </div>
            <div style="display: flex; gap: 0.75rem;">
                <button onclick="document.getElementById('importSiswaModal').style.display = 'flex'" class="btn" style="width: auto; padding: 0.75rem 1.5rem; display: flex; align-items: center; gap: 0.5rem; border-radius: 12px; cursor: pointer; background: #f1f5f9; border: 1px solid #e2e8f0; color: #475569; font-weight: 600; transition: all 0.2s;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    Import CSV
                </button>
                <button onclick="document.getElementById('addSiswaModal').style.display = 'flex'" class="btn btn-primary" style="width: auto; padding: 0.75rem 1.5rem; display: flex; align-items: center; gap: 0.5rem; border-radius: 12px; cursor: pointer;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Tambah Siswa
                </button>
            </div>
        </div>

        <div class="card" style="border: none; box-shadow: none; padding: 0; background: transparent;">
            @if(session('success'))
                <div class="success-alert" style="margin-bottom: 1rem; padding: 1rem; background: #dcfce7; color: #15803d; border-radius: 12px; display: flex; align-items: center; gap: 0.5rem; font-weight: 600;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('import_errors'))
                <div class="error-alert" style="margin-bottom: 1rem; padding: 1rem; background: #fee2e2; color: #b91c1c; border-radius: 12px; font-weight: 500; border: 1px solid #fecaca;">
                    <div style="font-weight: 700; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        Beberapa baris gagal diimpor:
                    </div>
                    <ul style="margin-left: 1.5rem; margin-top: 0.25rem; list-style-type: disc; font-size: 0.9rem;">
                        @foreach(session('import_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="search-container">
                <form action="{{ route('sekolah.siswas.index') }}" method="GET" style="flex: 1; position: relative; display: flex; align-items: center;">
                    <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position: absolute; left: 15px; color: #94a3b8;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" name="search" class="search-input" placeholder="Cari nama atau NISN siswa..." value="{{ request('search') }}" style="padding-left: 45px;">
                </form>
            </div>

            <table class="user-table" id="siswaTable">
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" id="selectAllSiswa" title="Pilih Semua" style="width: 16px; height: 16px; cursor: pointer; accent-color: #ff6b00;">
                        </th>
                        <th style="width: 250px;">Student Name</th>
                        <th>NISN &amp; Kelas</th>
                        <th>Alergi</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th style="text-align: right; width: 180px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswas as $siswa)
                        <tr>
                            <td>
                                <input type="checkbox" class="siswa-checkbox" value="{{ $siswa->id }}" style="width: 16px; height: 16px; cursor: pointer; accent-color: #ff6b00;">
                            </td>
                            <td>
                                <div style="display: flex; align-items: center;">
                                    @php
                                        $nama_bersih = $siswa->nama_siswa ?? 'Unknown';
                                        $initials = collect(explode(' ', $nama_bersih))->map(fn($n) => substr($n, 0, 1))->take(2)->join('');
                                    @endphp
                                    <div class="avatar" style="background: #f59e0b;">{{ strtoupper($initials) }}</div>
                                    <div style="font-weight: 700; color: #0c1e35;">{{ $nama_bersih }}</div>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #475569;">{{ $siswa->nisn }}</div>
                                <div style="font-size: 0.75rem; color: #94a3b8;">Kelas {{ $siswa->kelas ?? '-' }}</div>
                            </td>
                            <td>
                                @if($siswa->alergi)
                                    <span style="background: #fee2e2; color: #ef4444; padding: 0.2rem 0.5rem; border-radius: 6px; font-size: 0.75rem; font-weight: 600;">{{ $siswa->alergi }}</span>
                                @else
                                    <span style="color: #94a3b8; font-size: 0.85rem;">-</span>
                                @endif
                            </td>
                            <td style="color: #475569; font-size: 0.85rem;">{{ $siswa->contact ?? '-' }}</td>
                            <td>
                                <span class="status-pill {{ $siswa->status == 'Active' ? 'status-active' : 'status-inactive' }}">
                                    {{ $siswa->status }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                @php
                                    $latestAntro = $siswa->antropometris->sortByDesc('tanggal_ukur')->first();
                                    $tb = $latestAntro ? $latestAntro->tinggi_badan . ' cm' : '-';
                                    $bb = $latestAntro ? $latestAntro->berat_badan . ' kg' : '-';
                                    $tgl = $latestAntro ? \Carbon\Carbon::parse($latestAntro->tanggal_ukur)->format('d M Y') : '-';
                                    $sekolah = Auth::user()->sekolah->nama_sekolah ?? 'Sekolah';
                                @endphp
                                <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                    {{-- Tombol Ukur --}}
                                    <button onclick="openUkurModal({{ $siswa->id }}, '{{ addslashes($nama_bersih) }}')" class="link-ukur" title="Ukur Antropometri">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"></path></svg>
                                    </button>

                                    {{-- Tombol View --}}
                                    <button onclick="openViewSiswaModal(
                                        '{{ addslashes($nama_bersih) }}', 
                                        '{{ $siswa->nisn }}', 
                                        '{{ $siswa->kelas ?? '-' }}', 
                                        '{{ addslashes($siswa->alergi ?? 'Tidak ada') }}', 
                                        '{{ addslashes($siswa->contact ?? '-') }}', 
                                        '{{ addslashes($sekolah) }}', 
                                        '{{ $siswa->status }}', 
                                        '{{ $tb }}', 
                                        '{{ $bb }}', 
                                        '{{ $tgl }}'
                                    )" style="color: #059669; background: #d1fae5; border: none; padding: 0.5rem; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center;" title="View">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>

                                    {{-- Tombol Edit --}}
                                    <button onclick="openEditSiswaModal({{ $siswa->id }}, '{{ addslashes($nama_bersih) }}', '{{ $siswa->nisn }}', '{{ $siswa->kelas }}', '{{ addslashes($siswa->alergi) }}', '{{ addslashes($siswa->contact) }}', '{{ $siswa->status }}')" style="color: #2563eb; background: #eff6ff; border: none; padding: 0.5rem; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center;" title="Edit">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </button>

                                    {{-- Tombol Delete --}}
                                    <button onclick="openDeleteModal({{ $siswa->id }}, '{{ addslashes($nama_bersih) }}')" style="color: #ef4444; background: #fef2f2; border: none; padding: 0.5rem; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center;" title="Hapus">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 3rem; color: #64748b; font-weight: 600;">Tidak ada data siswa ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div style="margin-top: 1.5rem;">
                {{ $siswas->links() }}
            </div>
        </div>
    </main>
</div>

<div id="bulkBarSiswa" style="display: none; position: fixed; bottom: 2rem; left: 50%; transform: translateX(-50%); z-index: 3000; background: #0c1e35; color: white; border-radius: 16px; padding: 1rem 1.5rem; align-items: center; gap: 1.5rem; box-shadow: 0 8px 32px rgba(0,0,0,0.25); min-width: 380px;">
    <span id="bulkCountSiswa" style="font-weight: 700; font-size: 0.95rem;">0 siswa dipilih</span>
    <div style="display: flex; gap: 0.75rem; margin-left: auto;">
        <button onclick="clearSelectionSiswa()" style="padding: 0.5rem 1rem; border-radius: 8px; border: 1px solid rgba(255,255,255,0.2); background: transparent; color: white; cursor: pointer; font-weight: 600; font-size: 0.85rem;">Batal</button>
        <button onclick="submitBulkDeleteSiswa()" style="padding: 0.5rem 1rem; border-radius: 8px; background: #ef4444; color: white; border: none; cursor: pointer; font-weight: 700; font-size: 0.85rem; display: flex; align-items: center; gap: 0.5rem;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
            Hapus Terpilih
        </button>
    </div>
</div>

<form id="bulkDeleteSiswaForm" method="POST" action="{{ route('sekolah.siswas.bulk-destroy') }}" style="display: none;">
    @csrf
    @method('DELETE')
    <div id="bulkDeleteSiswaIds"></div>
</form>

<div id="viewSiswaModal" class="modal-form-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div class="modal-form-box" style="background: white; border-radius: 20px; padding: 2.5rem; width: 450px; max-width: 92%; box-shadow: 0 25px 50px rgba(0,0,0,0.15);">
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <div class="avatar" style="background: #f59e0b; width: 80px; height: 80px; font-size: 2rem; margin: 0 auto 1rem auto; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: white;" id="view_siswa_initials"></div>
            <h3 style="font-size: 1.5rem; font-weight: 800; color: #0c1e35;" id="view_siswa_nama"></h3>
            <span class="status-pill status-active" style="margin-top: 0.5rem; display: inline-block;">Siswa</span>
        </div>
        
        <div style="background: #f8fafc; border-radius: 12px; padding: 1.5rem; display: flex; flex-direction: column; gap: 0.75rem;">
            <div style="font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Biodata & Kontak</div>
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.75rem;">
                <span style="color: #64748b; font-size: 0.85rem; font-weight: 600;">NISN</span>
                <span style="color: #0c1e35; font-size: 0.85rem; font-weight: 700; text-align: right;" id="view_siswa_nisn"></span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.75rem;">
                <span style="color: #64748b; font-size: 0.85rem; font-weight: 600;">Kelas</span>
                <span style="color: #0c1e35; font-size: 0.85rem; font-weight: 700; text-align: right;" id="view_siswa_kelas"></span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.75rem;">
                <span style="color: #64748b; font-size: 0.85rem; font-weight: 600;">Contact</span>
                <span style="color: #0c1e35; font-size: 0.85rem; font-weight: 700; text-align: right;" id="view_siswa_contact"></span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.75rem;">
                <span style="color: #64748b; font-size: 0.85rem; font-weight: 600;">Location</span>
                <span style="color: #0c1e35; font-size: 0.85rem; font-weight: 700; text-align: right;" id="view_siswa_lokasi"></span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.75rem;">
                <span style="color: #64748b; font-size: 0.85rem; font-weight: 600;">Alergi</span>
                <span style="color: #ef4444; font-size: 0.85rem; font-weight: 700; text-align: right;" id="view_siswa_alergi"></span>
            </div>

            <div style="font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 0.5rem; margin-bottom: 0.25rem;">Data Fisik (Terbaru)</div>
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.75rem;">
                <span style="color: #64748b; font-size: 0.85rem; font-weight: 600;">Tinggi Badan</span>
                <span style="color: #0c1e35; font-size: 0.85rem; font-weight: 700; text-align: right;" id="view_siswa_tb"></span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.75rem;">
                <span style="color: #64748b; font-size: 0.85rem; font-weight: 600;">Berat Badan</span>
                <span style="color: #0c1e35; font-size: 0.85rem; font-weight: 700; text-align: right;" id="view_siswa_bb"></span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.75rem;">
                <span style="color: #64748b; font-size: 0.85rem; font-weight: 600;">Tanggal Ukur</span>
                <span style="color: #0c1e35; font-size: 0.85rem; font-weight: 700; text-align: right;" id="view_siswa_tgl"></span>
            </div>
        </div>

        <button type="button" class="btn" style="width:100%; margin-top: 1.5rem; background: #e2e8f0; color: #475569; border: none; padding: 0.75rem; border-radius: 10px; cursor: pointer; font-weight: 700;" onclick="closeModal('viewSiswaModal')">Tutup</button>
    </div>
</div>

<div id="addSiswaModal" class="modal-form-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div class="modal-form-box" style="background: white; border-radius: 20px; padding: 2rem; width: 500px; max-width: 92%;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
            <h3 style="font-size: 1.2rem; font-weight: 800; color: #0c1e35;">Tambah Siswa</h3>
            <span onclick="closeModal('addSiswaModal')" style="cursor:pointer;font-size:1.4rem;color:#6b7280;">&times;</span>
        </div>
        <form action="{{ route('sekolah.siswas.store') }}" method="POST">
            @csrf
            <div style="margin-bottom:1rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem;">Nama Lengkap</label>
                    <input type="text" name="nama_siswa" class="form-input" required style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem;">NISN</label>
                    <input type="text" name="nisn" class="form-input" required style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
                </div>
            </div>
            <div style="margin-bottom:1rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem;">Kelas</label>
                    <input type="text" name="kelas" class="form-input" style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem;">Contact</label>
                    <input type="text" name="contact" class="form-input" style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
                </div>
            </div>
            <div style="margin-bottom:1rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem;">Alergi</label>
                <input type="text" name="alergi" class="form-input" placeholder="Kosongkan jika tidak ada" style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
            </div>
            <div style="margin-bottom:1.5rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem;">Status</label>
                <select name="status" class="form-input" required style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Tambah Siswa</button>
        </form>
    </div>
</div>

<div id="editSiswaModal" class="modal-form-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div class="modal-form-box" style="background: white; border-radius: 20px; padding: 2rem; width: 500px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
            <h3 style="font-size: 1.2rem; font-weight: 800; color: #0c1e35;">Edit Student</h3>
            <span onclick="closeModal('editSiswaModal')" style="cursor:pointer;font-size:1.4rem;color:#6b7280;">&times;</span>
        </div>
        <form id="editSiswaForm" method="POST">
            @csrf
            @method('PUT')
            <div style="margin-bottom:1rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama_siswa" id="edit_siswa_nama" class="form-input" required style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
                </div>
                <div>
                    <label>NISN</label>
                    <input type="text" name="nisn" id="edit_siswa_nisn" class="form-input" required style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
                </div>
            </div>
            <div style="margin-bottom:1rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label>Kelas</label>
                    <input type="text" name="kelas" id="edit_siswa_kelas" class="form-input" style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
                </div>
                <div>
                    <label>Contact</label>
                    <input type="text" name="contact" id="edit_siswa_contact" class="form-input" style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
                </div>
            </div>
            <div style="margin-bottom:1rem;">
                <label>Alergi</label>
                <input type="text" name="alergi" id="edit_siswa_alergi" class="form-input" style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
            </div>
            <div style="margin-bottom:1.5rem;">
                <label>Status</label>
                <select name="status" id="edit_siswa_status" class="form-input" required style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Simpan Perubahan</button>
        </form>
    </div>
</div>

<div id="ukurModal" class="modal-form-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div class="modal-form-box" style="background: white; border-radius: 20px; padding: 2rem; width: 400px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
            <h3 style="font-size: 1.2rem; font-weight: 800; color: #0c1e35;">Ukur Antropometri</h3>
            <span onclick="closeModal('ukurModal')" style="cursor:pointer;font-size:1.4rem;color:#6b7280;">&times;</span>
        </div>
        <p id="ukur_siswa_nama" style="font-weight: 700; color: #64748b; margin-bottom: 1rem;"></p>
        <form id="ukurForm" method="POST">
            @csrf
            <div style="margin-bottom:1rem;">
                <label>Berat Badan (kg)</label>
                <input type="number" step="0.01" name="berat_badan" class="form-input" required style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
            </div>
            <div style="margin-bottom:1rem;">
                <label>Tinggi Badan (cm)</label>
                <input type="number" step="0.01" name="tinggi_badan" class="form-input" required style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
            </div>
            <div style="margin-bottom:1.5rem;">
                <label>Tanggal Pengukuran</label>
                <input type="date" name="tanggal_ukur" class="form-input" required value="{{ date('Y-m-d') }}" style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Simpan Pengukuran</button>
        </form>
    </div>
</div>

<div id="deleteModal" class="confirm-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 2000; justify-content: center; align-items: center;">
    <div class="confirm-box" style="background: white; border-radius: 20px; padding: 2rem; width: 380px; text-align: center;">
        <h4 style="margin-bottom: 0.5rem;">Hapus Siswa?</h4>
        <p id="deleteConfirmText" style="margin-bottom: 1.5rem;"></p>
        <div style="display: flex; gap: 0.75rem;">
            <button type="button" onclick="closeModal('deleteModal')" style="flex: 1; padding: 0.7rem; cursor: pointer; border-radius:10px; border:1px solid #ccc; background:#fff;">Batal</button>
            <form id="deleteSiswaForm" method="POST" style="flex:1;">
                @csrf
                @method('DELETE')
                <button type="submit" style="width:100%; padding: 0.7rem; background: #ef4444; color: white; border: none; border-radius: 10px; cursor: pointer; font-weight:600;">Ya, Hapus</button>
            </form>
        </div>
    </div>
</div>

<div id="importSiswaModal" class="modal-form-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 2000; justify-content: center; align-items: center;">
    <div class="modal-form-content" style="background: white; border-radius: 20px; padding: 2rem; width: 500px; max-width: 90%; max-height: 90vh; overflow-y: auto; position: relative;">
        <span onclick="closeModal('importSiswaModal')" class="close-btn" style="position: absolute; right: 20px; top: 20px; font-size: 1.5rem; cursor: pointer; color: #94a3b8;">&times;</span>
        <h3 style="margin-bottom: 0.5rem; color: #0c1e35;">Import Data Siswa (CSV)</h3>
        <p style="color: #64748b; font-size: 0.875rem; margin-bottom: 1.5rem;">Unggah file CSV yang berisi data profil siswa. Format kolom harus berupa: <strong>nama, nisn, kelas, kontak, alergi, status</strong>.</p>
        
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1rem; margin-bottom: 1.5rem;">
            <div style="font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em;">Contoh Struktur CSV:</div>
            <pre style="font-family: monospace; font-size: 0.8rem; background: #e2e8f0; padding: 0.5rem; border-radius: 6px; overflow-x: auto; color: #334155; margin: 0;">nama,nisn,kelas,kontak,alergi,status
Budi Santoso,1234567890,7A,08123456789,,Active
Siti Aminah,0987654321,7B,08234567890,Kacang,Active</pre>
        </div>

        <form action="{{ route('sekolah.siswas.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; color: #334155; margin-bottom: 0.5rem;">Pilih File CSV</label>
                <input type="file" name="file_csv" accept=".csv,.txt" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 10px; background: #f8fafc; cursor: pointer;">
            </div>
            
            <div style="display: flex; gap: 0.75rem; justify-content: flex-end; margin-top: 2rem;">
                <button type="button" onclick="closeModal('importSiswaModal')" style="padding: 0.75rem 1.5rem; border-radius: 10px; border: 1px solid #d2d6dc; background: white; cursor: pointer; font-weight: 600; color: #475569;">Batal</button>
                <button type="submit" class="btn btn-primary" style="width: auto; padding: 0.75rem 1.5rem; border-radius: 10px; cursor: pointer; font-weight: 600;">Unggah & Import</button>
            </div>
        </form>
    </div>
</div>

<script>
    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    function openEditSiswaModal(id, nama, nisn, kelas, alergi, contact, status) {
        document.getElementById('editSiswaForm').action = '/dashboard/sekolah/siswas/' + id;
        document.getElementById('edit_siswa_nama').value = nama || '';
        document.getElementById('edit_siswa_nisn').value = nisn || '';
        document.getElementById('edit_siswa_kelas').value = kelas || '';
        document.getElementById('edit_siswa_alergi').value = alergi || '';
        document.getElementById('edit_siswa_contact').value = contact || '';
        document.getElementById('edit_siswa_status').value = status || 'Active';
        document.getElementById('editSiswaModal').style.display = 'flex';
    }

    function openViewSiswaModal(nama, nisn, kelas, alergi, contact, lokasi, status, tb, bb, tgl) {
        const initials = (nama || '??').substring(0, 2).toUpperCase();
        document.getElementById('view_siswa_initials').textContent = initials;
        
        document.getElementById('view_siswa_nama').textContent = nama || '-';
        document.getElementById('view_siswa_nisn').textContent = nisn || '-';
        document.getElementById('view_siswa_kelas').textContent = kelas || '-';
        document.getElementById('view_siswa_contact').textContent = contact || '-';
        document.getElementById('view_siswa_lokasi').textContent = lokasi || '-';
        document.getElementById('view_siswa_alergi').textContent = alergi || 'Tidak ada';
        document.getElementById('view_siswa_tb').textContent = tb || '-';
        document.getElementById('view_siswa_bb').textContent = bb || '-';
        document.getElementById('view_siswa_tgl').textContent = tgl || '-';

        document.getElementById('viewSiswaModal').style.display = 'flex';
    }

    function openUkurModal(id, nama) {
        document.getElementById('ukurForm').action = '/dashboard/sekolah/siswas/' + id + '/antropometri';
        document.getElementById('ukur_siswa_nama').textContent = 'Siswa: ' + (nama || 'Siswa');
        document.getElementById('ukurModal').style.display = 'flex';
    }

    function openDeleteModal(id, nama) {
        document.getElementById('deleteConfirmText').textContent = 'Siswa "' + (nama || 'ini') + '" akan dihapus secara permanen.';
        document.getElementById('deleteSiswaForm').action = '/dashboard/sekolah/siswas/' + id;
        document.getElementById('deleteModal').style.display = 'flex';
    }

    window.onclick = function(event) {
        if (event.target.classList.contains('modal-form-overlay') || event.target.classList.contains('confirm-overlay')) {
            event.target.style.display = 'none';
        }
    }

    // ── Bulk Delete Siswa ──
    const selectAllSiswa = document.getElementById('selectAllSiswa');
    const bulkBarSiswa   = document.getElementById('bulkBarSiswa');
    const bulkCountSiswa = document.getElementById('bulkCountSiswa');

    function getCheckedSiswa() {
        return [...document.querySelectorAll('.siswa-checkbox:checked')];
    }

    function updateBulkBarSiswa() {
        const checked = getCheckedSiswa();
        if (checked.length > 0) {
            bulkBarSiswa.style.display = 'flex';
            bulkCountSiswa.textContent = checked.length + ' siswa dipilih';
        } else {
            bulkBarSiswa.style.display = 'none';
        }
        const all = document.querySelectorAll('.siswa-checkbox');
        if(selectAllSiswa) {
            selectAllSiswa.checked = all.length > 0 && checked.length === all.length;
            selectAllSiswa.indeterminate = checked.length > 0 && checked.length < all.length;
        }
    }

    if(selectAllSiswa) {
        selectAllSiswa.addEventListener('change', function () {
            document.querySelectorAll('.siswa-checkbox').forEach(cb => cb.checked = this.checked);
            updateBulkBarSiswa();
        });
    }

    document.querySelectorAll('.siswa-checkbox').forEach(cb => {
        cb.addEventListener('change', updateBulkBarSiswa);
    });

    function clearSelectionSiswa() {
        document.querySelectorAll('.siswa-checkbox').forEach(cb => cb.checked = false);
        if(selectAllSiswa) {
            selectAllSiswa.checked = false;
            selectAllSiswa.indeterminate = false;
        }
        bulkBarSiswa.style.display = 'none';
    }

    function submitBulkDeleteSiswa() {
        const ids = getCheckedSiswa().map(cb => cb.value);
        if (ids.length === 0) return;
        if (!confirm(ids.length + ' siswa akan dihapus. Lanjutkan?')) return;

        const container = document.getElementById('bulkDeleteSiswaIds');
        container.innerHTML = '';
        ids.forEach(id => {
            const input = document.createElement('input');
            input.type  = 'hidden';
            input.name  = 'ids[]';
            input.value = id;
            container.appendChild(input);
        });
        document.getElementById('bulkDeleteSiswaForm').submit();
    }
</script>
@endsection