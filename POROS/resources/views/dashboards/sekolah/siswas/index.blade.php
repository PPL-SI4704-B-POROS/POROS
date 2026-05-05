@extends('layouts.app')

@section('title', 'Data Siswa')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/users.css') }}">
<style>
    .link-ukur { color: #ff6b00; background: #fff5ed; border: none; padding: 0.5rem; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; }
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
            <button onclick="document.getElementById('addSiswaModal').style.display = 'flex'" class="btn btn-primary" style="width: auto; padding: 0.75rem 1.5rem; display: flex; align-items: center; gap: 0.5rem; border-radius: 12px; transition: 0.3s; cursor: pointer;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Siswa
            </button>
        </div>

        <div class="card" style="border: none; box-shadow: none; padding: 0; background: transparent;">
            @if(session('success'))
                <div class="success-alert" style="margin-bottom: 1rem; padding: 1rem; background: #dcfce7; color: #15803d; border-radius: 12px; display: flex; align-items: center; gap: 0.5rem; font-weight: 600;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    {{ session('success') }}
                </div>
            @endif

            <div class="search-container">
                <form action="{{ route('sekolah.siswas.index') }}" method="GET" style="flex: 1; position: relative; display: flex; align-items: center;">
                    <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" name="search" class="search-input" placeholder="Cari nama atau NISN siswa..." value="{{ request('search') }}">
                </form>
            </div>

            <table class="user-table">
                <thead>
                    <tr>
                        <th style="width: 250px;">Student Name</th>
                        <th>NISN & Kelas</th>
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
                                <div style="display: flex; align-items: center;">
                                    @php
                                        $initials = collect(explode(' ', $siswa->nama_siswa))->map(fn($n) => substr($n, 0, 1))->take(2)->join('');
                                        $color = '#f59e0b';
                                    @endphp
                                    <div class="avatar" style="background: {{ $color }};">{{ $initials }}</div>
                                    <div style="font-weight: 700; color: #0c1e35;">{{ $siswa->nama_siswa }}</div>
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
                                    $latestAntro = $siswa->antropometris->first();
                                    $tb = $latestAntro ? $latestAntro->tinggi_badan . ' cm' : '-';
                                    $bb = $latestAntro ? $latestAntro->berat_badan . ' kg' : '-';
                                    $tgl = $latestAntro ? \Carbon\Carbon::parse($latestAntro->tanggal_ukur)->format('d M Y') : '-';
                                @endphp
                                <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                    <button onclick="openUkurModal({{ $siswa->id }}, '{{ addslashes($siswa->nama_siswa) }}')" class="link-ukur" title="Ukur Antropometri">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"></path></svg>
                                    </button>
                                    <button onclick="openViewSiswaModal('{{ addslashes($siswa->nama_siswa) }}', '{{ $siswa->nisn }}', '{{ $siswa->kelas }}', '{{ addslashes($siswa->alergi) }}', '{{ addslashes($siswa->contact) }}', '{{ addslashes(Auth::user()->sekolah->nama_sekolah ?? '') }}', '{{ $siswa->status }}', '{{ $tb }}', '{{ $bb }}', '{{ $tgl }}')" style="color: #059669; background: #d1fae5; border: none; padding: 0.5rem; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center;" title="View">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                    <button onclick="openEditSiswaModal({{ $siswa->id }}, '{{ addslashes($siswa->nama_siswa) }}', '{{ $siswa->nisn }}', '{{ $siswa->kelas }}', '{{ addslashes($siswa->alergi) }}', '{{ addslashes($siswa->contact) }}', '{{ $siswa->status }}')" style="color: #2563eb; background: #eff6ff; border: none; padding: 0.5rem; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center;" title="Edit">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </button>
                                    <button onclick="openDeleteModal({{ $siswa->id }}, '{{ addslashes($siswa->nama_siswa) }}')" style="color: #ef4444; background: #fef2f2; border: none; padding: 0.5rem; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center;" title="Hapus">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 3rem; color: #64748b; font-weight: 600;">Tidak ada data siswa ditemukan.</td>
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

<!-- Add Siswa Modal -->
<div id="addSiswaModal" class="modal-form-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div class="modal-form-box" style="background: white; border-radius: 20px; padding: 2rem; width: 500px; max-width: 92%; box-shadow: 0 25px 50px rgba(0,0,0,0.15);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
            <h3 style="font-size: 1.2rem; font-weight: 800; color: #0c1e35;">Tambah Siswa</h3>
            <span onclick="closeModal('addSiswaModal')" style="cursor:pointer;font-size:1.4rem;color:#6b7280;">&times;</span>
        </div>
        <form action="{{ route('sekolah.siswas.store') }}" method="POST">
            @csrf
            <div style="margin-bottom:1rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem; color: #374151;">Nama Lengkap</label>
                    <input type="text" name="nama_siswa" class="form-input" required style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem; color: #374151;">NISN</label>
                    <input type="text" name="nisn" class="form-input" required style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
                </div>
            </div>
            <div style="margin-bottom:1rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem; color: #374151;">Kelas</label>
                    <input type="text" name="kelas" class="form-input" style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem; color: #374151;">Contact</label>
                    <input type="text" name="contact" class="form-input" style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
                </div>
            </div>
            <div style="margin-bottom:1rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem; color: #374151;">Alergi</label>
                <input type="text" name="alergi" class="form-input" placeholder="Kosongkan jika tidak ada" style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
            </div>
            <div style="margin-bottom:1.5rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem; color: #374151;">Status</label>
                <select name="status" class="form-input" required style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Tambah Siswa</button>
        </form>
    </div>
</div>

<!-- Edit Siswa Modal -->
<div id="editSiswaModal" class="modal-form-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div class="modal-form-box" style="background: white; border-radius: 20px; padding: 2rem; width: 500px; max-width: 92%; box-shadow: 0 25px 50px rgba(0,0,0,0.15);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
            <h3 style="font-size: 1.2rem; font-weight: 800; color: #0c1e35;">Edit Student</h3>
            <span onclick="closeModal('editSiswaModal')" style="cursor:pointer;font-size:1.4rem;color:#6b7280;">&times;</span>
        </div>
        <form id="editSiswaForm" method="POST">
            @csrf
            @method('PUT')
            <div style="margin-bottom:1rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem; color: #374151;">Nama Lengkap</label>
                    <input type="text" name="nama_siswa" id="edit_siswa_nama" class="form-input" required style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem; color: #374151;">NISN</label>
                    <input type="text" name="nisn" id="edit_siswa_nisn" class="form-input" required style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
                </div>
            </div>
            <div style="margin-bottom:1rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem; color: #374151;">Kelas</label>
                    <input type="text" name="kelas" id="edit_siswa_kelas" class="form-input" style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem; color: #374151;">Contact</label>
                    <input type="text" name="contact" id="edit_siswa_contact" class="form-input" style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
                </div>
            </div>
            <div style="margin-bottom:1rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem; color: #374151;">Alergi</label>
                <input type="text" name="alergi" id="edit_siswa_alergi" class="form-input" placeholder="Kosongkan jika tidak ada" style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
            </div>
            <div style="margin-bottom:1.5rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem; color: #374151;">Status</label>
                <select name="status" id="edit_siswa_status" class="form-input" required style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Simpan Perubahan</button>
        </form>
    </div>
</div>

<!-- View Siswa Modal -->
<div id="viewSiswaModal" class="modal-form-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div class="modal-form-box" style="background: white; border-radius: 20px; padding: 2.5rem; width: 450px; max-width: 92%; box-shadow: 0 25px 50px rgba(0,0,0,0.15);">
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <div class="avatar" style="background: #f59e0b; width: 80px; height: 80px; font-size: 2rem; margin: 0 auto 1rem auto; display: flex; align-items: center; justify-content: center;" id="view_siswa_initials"></div>
            <h3 style="font-size: 1.5rem; font-weight: 800; color: #0c1e35;" id="view_siswa_nama"></h3>
            <span class="role-badge badge-dapur" style="background: #fef3c7; color: #d97706; margin-top: 0.5rem; display: inline-block;">Siswa</span>
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

        <button type="button" class="btn" style="width:100%; margin-top: 1.5rem; background: #e2e8f0; color: #475569;" onclick="closeModal('viewSiswaModal')">Tutup</button>
    </div>
</div>

<!-- Ukur Modal -->
<div id="ukurModal" class="modal-form-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div class="modal-form-box" style="background: white; border-radius: 20px; padding: 2rem; width: 400px; max-width: 92%; box-shadow: 0 25px 50px rgba(0,0,0,0.15);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
            <h3 style="font-size: 1.2rem; font-weight: 800; color: #0c1e35;">Ukur Antropometri</h3>
            <span onclick="closeModal('ukurModal')" style="cursor:pointer;font-size:1.4rem;color:#6b7280;">&times;</span>
        </div>
        <p id="ukur_siswa_nama" style="font-weight: 700; color: #64748b; margin-bottom: 1rem;"></p>
        <form id="ukurForm" method="POST">
            @csrf
            <div style="margin-bottom:1rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem; color: #374151;">Berat Badan (kg)</label>
                <input type="number" step="0.01" name="berat_badan" class="form-input" required style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
            </div>
            <div style="margin-bottom:1rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem; color: #374151;">Tinggi Badan (cm)</label>
                <input type="number" step="0.01" name="tinggi_badan" class="form-input" required style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
            </div>
            <div style="margin-bottom:1.5rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem; color: #374151;">Tanggal Pengukuran</label>
                <input type="date" name="tanggal_ukur" class="form-input" required value="{{ date('Y-m-d') }}" style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Simpan Pengukuran</button>
        </form>
    </div>
</div>

<!-- Delete Confirm Modal -->
<div id="deleteModal" class="confirm-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 2000; justify-content: center; align-items: center;">
    <div class="confirm-box" style="background: white; border-radius: 20px; padding: 2rem; width: 380px; max-width: 90%; text-align: center; box-shadow: 0 25px 50px rgba(0,0,0,0.2);">
        <div style="font-size: 2.5rem; margin-bottom: 0.75rem;">🗑️</div>
        <h4 style="font-size: 1.1rem; font-weight: 800; color: #0c1e35; margin-bottom: 0.5rem;">Hapus Siswa?</h4>
        <p id="deleteConfirmText" style="font-size: 0.85rem; color: #6b7280; margin-bottom: 1.5rem;"></p>
        <div style="display: flex; gap: 0.75rem;">
            <button type="button" onclick="closeModal('deleteModal')" style="flex: 1; padding: 0.7rem; border: 1.5px solid #d1d5db; border-radius: 10px; background: white; font-weight: 700; font-size: 0.85rem; cursor: pointer; color: #374151;">Batal</button>
            <form id="deleteSiswaForm" method="POST" style="flex:1;display:flex;">
                @csrf
                @method('DELETE')
                <button type="submit" style="width:100%; padding: 0.7rem; border: none; border-radius: 10px; background: #ef4444; color: white; font-weight: 700; font-size: 0.85rem; cursor: pointer;">Ya, Hapus</button>
            </form>
        </div>
    </div>
</div>

<script>
    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    function openEditSiswaModal(id, nama, nisn, kelas, alergi, contact, status) {
        document.getElementById('editSiswaForm').action = '/dashboard/sekolah/siswas/' + id;
        document.getElementById('edit_siswa_nama').value = nama;
        document.getElementById('edit_siswa_nisn').value = nisn;
        document.getElementById('edit_siswa_kelas').value = kelas;
        document.getElementById('edit_siswa_alergi').value = alergi;
        document.getElementById('edit_siswa_contact').value = contact;
        document.getElementById('edit_siswa_status').value = status;
        document.getElementById('editSiswaModal').style.display = 'flex';
    }

    function openViewSiswaModal(nama, nisn, kelas, alergi, contact, lokasi, status, tb, bb, tgl) {
        document.getElementById('view_siswa_initials').textContent = nama.substring(0, 2).toUpperCase();
        document.getElementById('view_siswa_nama').textContent = nama;
        document.getElementById('view_siswa_nisn').textContent = nisn;
        document.getElementById('view_siswa_kelas').textContent = kelas || '-';
        document.getElementById('view_siswa_contact').textContent = contact || '-';
        document.getElementById('view_siswa_lokasi').textContent = lokasi || '-';
        document.getElementById('view_siswa_alergi').textContent = alergi || 'Tidak ada';
        document.getElementById('view_siswa_tb').textContent = tb;
        document.getElementById('view_siswa_bb').textContent = bb;
        document.getElementById('view_siswa_tgl').textContent = tgl;
        
        const statusEl = document.getElementById('view_siswa_status');
        statusEl.textContent = status;
        statusEl.className = 'status-pill ' + (status === 'Active' ? 'status-active' : 'status-inactive');

        document.getElementById('viewSiswaModal').style.display = 'flex';
    }

    function openUkurModal(id, nama) {
        document.getElementById('ukurForm').action = '/dashboard/sekolah/siswas/' + id + '/antropometri';
        document.getElementById('ukur_siswa_nama').textContent = 'Siswa: ' + nama;
        document.getElementById('ukurModal').style.display = 'flex';
    }

    function openDeleteModal(id, nama) {
        document.getElementById('deleteConfirmText').textContent = 'Siswa "' + nama + '" akan dihapus secara permanen.';
        document.getElementById('deleteSiswaForm').action = '/dashboard/sekolah/siswas/' + id;
        document.getElementById('deleteModal').style.display = 'flex';
    }

    window.onclick = function(event) {
        if (event.target.classList.contains('modal-form-overlay') || event.target.classList.contains('confirm-overlay')) {
            event.target.style.display = 'none';
        }
    }
</script>
@endsection
