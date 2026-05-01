@extends('layouts.app')

@section('title', 'User Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/users.css') }}">
@endsection

@section('content')
<div class="dashboard-layout">
    @include('partials.sidebar')

    <main class="main-content">
        @include('partials.header')

        <div class="planning-header">
            <div>
                <h1 style="font-size: 1.75rem; font-weight: 800; color: #0c1e35;">User Management</h1>
                <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 0.25rem;">Manage system users and permissions</p>
            </div>
            <button onclick="openAddModal('{{ $tab }}')" class="btn btn-primary" style="width: auto; padding: 0.75rem 1.5rem; display: flex; align-items: center; gap: 0.5rem; border-radius: 12px; transition: 0.3s; cursor: pointer;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                {{ $tab == 'users' ? 'Add New User' : 'Add New Student' }}
            </button>
        </div>

        <!-- Stats Cards -->
        <div class="grid" style="margin-bottom: 2.5rem;">
            <div class="stats-card">
                <div>
                    <p style="color: #64748b; font-size: 0.85rem; font-weight: 600;">Student</p>
                    <h2 style="font-size: 1.85rem; font-weight: 800; color: #10b981; margin-top: 0.25rem;">{{ $stats['student'] }}</h2>
                </div>
                <div class="stats-icon icon-student">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                </div>
            </div>
            <div class="stats-card">
                <div>
                    <p style="color: #64748b; font-size: 0.85rem; font-weight: 600;">Admin</p>
                    <h2 style="font-size: 1.85rem; font-weight: 800; color: #9333ea; margin-top: 0.25rem;">{{ $stats['admin'] }}</h2>
                </div>
                <div class="stats-icon icon-admin">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a10 10 0 1 0 10 10H12V2z"/><path d="M12 12L2.69 6.5"/><path d="M12 12V21.5"/><path d="M12 12h9.5"/></svg>
                </div>
            </div>
            <div class="stats-card">
                <div>
                    <p style="color: #64748b; font-size: 0.85rem; font-weight: 600;">Dapur</p>
                    <h2 style="font-size: 1.85rem; font-weight: 800; color: #ea580c; margin-top: 0.25rem;">{{ $stats['dapur'] }}</h2>
                </div>
                <div class="stats-icon icon-dapur">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 13.87A4 4 0 0 1 7.41 6a5.11 5.11 0 0 1 1.05-1.54 5 5 0 0 1 7.08 0A5.11 5.11 0 0 1 16.59 6 4 4 0 0 1 18 13.87V21H6Z"/><line x1="6" y1="17" x2="18" y2="17"/></svg>
                </div>
            </div>
            <div class="stats-card">
                <div>
                    <p style="color: #64748b; font-size: 0.85rem; font-weight: 600;">Petugas Sekolah</p>
                    <h2 style="font-size: 1.85rem; font-weight: 800; color: #2563eb; margin-top: 0.25rem;">{{ $stats['petugas_sekolah'] }}</h2>
                </div>
                <div class="stats-icon icon-sekolah">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M3 10l9-7 9 7v11H3V10z"/><path d="M9 21V11h6v10"/></svg>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div style="margin-bottom: 1.5rem; display: flex; gap: 1rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 0;">
            <a href="{{ route('users.index', ['tab' => 'users']) }}" style="padding: 0.75rem 1.5rem; font-weight: 700; color: {{ $tab == 'users' ? '#ff6b00' : '#64748b' }}; border-bottom: 3px solid {{ $tab == 'users' ? '#ff6b00' : 'transparent' }}; margin-bottom: -2px; text-decoration: none; transition: 0.3s;">System Users</a>
            <a href="{{ route('users.index', ['tab' => 'siswa']) }}" style="padding: 0.75rem 1.5rem; font-weight: 700; color: {{ $tab == 'siswa' ? '#ff6b00' : '#64748b' }}; border-bottom: 3px solid {{ $tab == 'siswa' ? '#ff6b00' : 'transparent' }}; margin-bottom: -2px; text-decoration: none; transition: 0.3s;">Students</a>
        </div>

        <!-- Table Section -->
        <div class="card" style="border: none; box-shadow: none; padding: 0; background: transparent;">
            @if(session('success'))
                <div class="success-alert" style="margin-bottom: 1rem; padding: 1rem; background: #dcfce7; color: #15803d; border-radius: 12px; display: flex; align-items: center; gap: 0.5rem; font-weight: 600;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="error-alert" style="margin-bottom: 1rem; padding: 1rem; background: #fee2e2; color: #b91c1c; border-radius: 12px; display: flex; align-items: center; gap: 0.5rem; font-weight: 600;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    {{ session('error') }}
                </div>
            @endif

            @if($tab == 'users')
            <div class="search-container">
                <form action="{{ route('users.index') }}" method="GET" style="flex: 1; position: relative; display: flex; align-items: center;">
                    <input type="hidden" name="tab" value="users">
                    <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" name="search" class="search-input" placeholder="Search users..." value="{{ request('search') }}">
                </form>
                <form action="{{ route('users.index') }}" method="GET" id="roleFilterForm">
                    <input type="hidden" name="tab" value="users">
                    <select name="role" class="form-input" style="width: auto; padding: 0.65rem 2rem; font-size: 0.9rem;" onchange="this.form.submit()">
                        <option value="">All Roles</option>
                        <option value="super admin" {{ request('role') == 'super admin' ? 'selected' : '' }}>Admin</option>
                        <option value="dapur" {{ request('role') == 'dapur' ? 'selected' : '' }}>Dapur</option>
                        <option value="sekolah" {{ request('role') == 'sekolah' ? 'selected' : '' }}>Petugas Sekolah</option>
                    </select>
                </form>
            </div>

            <table class="user-table">
                <thead>
                    <tr>
                        <th style="width: 250px;">User</th>
                        <th style="width: 250px;">Contact</th>
                        <th>Role</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th style="text-align: right; width: 140px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center;">
                                    @php
                                        $initials = collect(explode(' ', $user->nama_lengkap))->map(fn($n) => substr($n, 0, 1))->take(2)->join('');
                                        $colors = ['#ff6b00', '#9333ea', '#2563eb', '#10b981', '#f59e0b'];
                                        $color = $colors[$user->id % count($colors)];
                                    @endphp
                                    <div class="avatar" style="background: {{ $color }};">{{ $initials }}</div>
                                    <div>
                                        <div style="font-weight: 700; color: #0c1e35;">{{ $user->nama_lengkap }}</div>
                                        <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 600;">{{ $user->formatted_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                                    <div style="font-size: 0.85rem; color: #475569; display: flex; align-items: center; gap: 0.4rem;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                        {{ $user->email }}
                                    </div>
                                    <div style="font-size: 0.85rem; color: #94a3b8; display: flex; align-items: center; gap: 0.4rem;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                        {{ $user->no_telp ?? '-' }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $roleName = $user->role->nama_role;
                                    $badgeClass = 'badge-admin';
                                    $label = 'Admin';
                                    if($roleName == 'dapur') { $badgeClass = 'badge-dapur'; $label = 'Dapur'; }
                                    if($roleName == 'sekolah') { $badgeClass = 'badge-sekolah'; $label = 'Petugas Sekolah'; }
                                @endphp
                                <span class="role-badge {{ $badgeClass }}">{{ $label }}</span>
                            </td>
                            <td style="color: #475569; font-size: 0.85rem; font-weight: 500;">
                                {{ $user->lokasi ?? '-' }}
                            </td>
                            <td>
                                <span class="status-pill {{ $user->status == 'Active' ? 'status-active' : 'status-inactive' }}">
                                    {{ $user->status }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                    <button onclick="openEditModal({{ $user->id }}, '{{ addslashes($user->nama_lengkap) }}', '{{ addslashes($user->email) }}', '{{ addslashes($user->no_telp) }}', '{{ addslashes($user->lokasi) }}', {{ $user->role_id }}, '{{ $user->status }}')" style="color: #2563eb; background: #eff6ff; border: none; padding: 0.5rem; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center;" title="Edit">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </button>
                                    @if($user->role->nama_role != 'super admin')
                                        <button onclick="openDeleteModal('user', {{ $user->id }}, '{{ addslashes($user->nama_lengkap) }}')" style="color: #ef4444; background: #fef2f2; border: none; padding: 0.5rem; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center;" title="Hapus">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                        </button>
                                    @else
                                        <button disabled style="color: #94a3b8; background: #f1f5f9; border: none; padding: 0.5rem; border-radius: 8px; cursor: not-allowed; display: flex; align-items: center; justify-content: center;" title="Super Admin tidak dapat dihapus">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="margin-top: 1.5rem;">
                {{ $users->links() }}
            </div>
            
            @elseif($tab == 'siswa')
            <div class="search-container">
                <form action="{{ route('users.index') }}" method="GET" style="flex: 1; position: relative; display: flex; align-items: center;">
                    <input type="hidden" name="tab" value="siswa">
                    <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" name="search" class="search-input" placeholder="Search students..." value="{{ request('search') }}">
                </form>
            </div>

            <table class="user-table">
                <thead>
                    <tr>
                        <th style="width: 250px;">Student Name</th>
                        <th>NISN & Kelas</th>
                        <th>Alergi</th>
                        <th>Contact</th>
                        <th>Role</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th style="text-align: right; width: 180px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($siswas as $siswa)
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
                            <td><span class="role-badge badge-dapur" style="background: #fef3c7; color: #d97706;">Siswa</span></td>
                            <td style="color: #475569; font-size: 0.85rem; font-weight: 500;">{{ $siswa->sekolah->nama_sekolah ?? '-' }}</td>
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
                                    <button onclick="openViewSiswaModal('{{ addslashes($siswa->nama_siswa) }}', '{{ $siswa->nisn }}', '{{ $siswa->kelas }}', '{{ addslashes($siswa->alergi) }}', '{{ addslashes($siswa->contact) }}', '{{ addslashes($siswa->sekolah->nama_sekolah ?? '') }}', '{{ $siswa->status }}', '{{ $tb }}', '{{ $bb }}', '{{ $tgl }}')" style="color: #059669; background: #d1fae5; border: none; padding: 0.5rem; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center;" title="View">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                    <button onclick="openEditSiswaModal({{ $siswa->id }}, '{{ addslashes($siswa->nama_siswa) }}', '{{ $siswa->nisn }}', '{{ $siswa->kelas }}', '{{ addslashes($siswa->alergi) }}', '{{ addslashes($siswa->contact) }}', '{{ $siswa->status }}')" style="color: #2563eb; background: #eff6ff; border: none; padding: 0.5rem; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center;" title="Edit">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </button>
                                    <button onclick="openDeleteModal('siswa', {{ $siswa->id }}, '{{ addslashes($siswa->nama_siswa) }}')" style="color: #ef4444; background: #fef2f2; border: none; padding: 0.5rem; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center;" title="Hapus">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="margin-top: 1.5rem;">
                {{ $siswas->links() }}
            </div>
            @endif
        </div>
    </main>
</div>

<!-- Add User Modal -->
<div id="addUserModal" class="modal-form-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div class="modal-form-box" style="background: white; border-radius: 20px; padding: 2rem; width: 500px; max-width: 92%; box-shadow: 0 25px 50px rgba(0,0,0,0.15);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
            <h3 style="font-size: 1.2rem; font-weight: 800; color: #0c1e35;">Add System User</h3>
            <span onclick="closeModal('addUserModal')" style="cursor:pointer;font-size:1.4rem;color:#6b7280;">&times;</span>
        </div>
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div style="margin-bottom:1rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem; color: #374151;">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" class="form-input" required style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
            </div>
            <div style="margin-bottom:1rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem; color: #374151;">Email</label>
                <input type="email" name="email" class="form-input" required style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
            </div>
            <div style="margin-bottom:1rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem; color: #374151;">Password</label>
                    <input type="password" name="password" class="form-input" required style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem; color: #374151;">No. Telepon</label>
                    <input type="text" name="no_telp" class="form-input" style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
                </div>
            </div>
            <div style="margin-bottom:1rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem; color: #374151;">Lokasi</label>
                <input type="text" name="lokasi" class="form-input" style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
            </div>
            <div style="margin-bottom:1.5rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem; color: #374151;">Role</label>
                    <select name="role_id" class="form-input" required style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ ucwords($role->nama_role) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem; color: #374151;">Status</label>
                    <select name="status" class="form-input" required style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Create System User</button>
        </form>
    </div>
</div>

<!-- Add Siswa Modal -->
<div id="addSiswaModal" class="modal-form-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div class="modal-form-box" style="background: white; border-radius: 20px; padding: 2rem; width: 500px; max-width: 92%; box-shadow: 0 25px 50px rgba(0,0,0,0.15);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
            <h3 style="font-size: 1.2rem; font-weight: 800; color: #0c1e35;">Add Student</h3>
            <span onclick="closeModal('addSiswaModal')" style="cursor:pointer;font-size:1.4rem;color:#6b7280;">&times;</span>
        </div>
        <form action="{{ route('siswas.store') }}" method="POST">
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
            <div style="margin-bottom:1.5rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem; color: #374151;">Sekolah</label>
                    <select name="sekolah_id" class="form-input" required style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
                        @foreach($sekolahs as $sek)
                            <option value="{{ $sek->id }}">{{ $sek->nama_sekolah }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem; color: #374151;">Status</label>
                    <select name="status" class="form-input" required style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Create Student</button>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="modal-form-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div class="modal-form-box" style="background: white; border-radius: 20px; padding: 2rem; width: 500px; max-width: 92%; box-shadow: 0 25px 50px rgba(0,0,0,0.15);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
            <h3 style="font-size: 1.2rem; font-weight: 800; color: #0c1e35;">Edit User</h3>
            <span onclick="closeModal('editUserModal')" style="cursor:pointer;font-size:1.4rem;color:#6b7280;">&times;</span>
        </div>
        <form id="editUserForm" method="POST">
            @csrf
            @method('PUT')
            <div style="margin-bottom:1rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem; color: #374151;">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" id="edit_nama_lengkap" class="form-input" required style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
            </div>
            <div style="margin-bottom:1rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem; color: #374151;">Email</label>
                <input type="email" name="email" id="edit_email" class="form-input" required style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
            </div>
            <div style="margin-bottom:1rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem; color: #374151;">No. Telepon</label>
                    <input type="text" name="no_telp" id="edit_no_telp" class="form-input" style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem; color: #374151;">Lokasi</label>
                    <input type="text" name="lokasi" id="edit_lokasi" class="form-input" style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
                </div>
            </div>
            <div style="margin-bottom:1rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem; color: #374151;">Role</label>
                    <select name="role_id" id="edit_role_id" class="form-input" required style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ ucwords($role->nama_role) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem; color: #374151;">Status</label>
                    <select name="status" id="edit_status" class="form-input" required style="width: 100%; padding: 0.65rem; border: 1px solid #d1d5db; border-radius: 8px;">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%; margin-top: 1rem;">Simpan Perubahan</button>
        </form>
    </div>
</div>

<!-- Delete Confirm Modal -->
<div id="deleteUserModal" class="confirm-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 2000; justify-content: center; align-items: center;">
    <div class="confirm-box" style="background: white; border-radius: 20px; padding: 2rem; width: 380px; max-width: 90%; text-align: center; box-shadow: 0 25px 50px rgba(0,0,0,0.2);">
        <div style="font-size: 2.5rem; margin-bottom: 0.75rem;">🗑️</div>
        <h4 style="font-size: 1.1rem; font-weight: 800; color: #0c1e35; margin-bottom: 0.5rem;">Hapus User?</h4>
        <p id="deleteConfirmText" style="font-size: 0.85rem; color: #6b7280; margin-bottom: 1.5rem;">User akan dihapus secara permanen.</p>
        <div style="display: flex; gap: 0.75rem;">
            <button type="button" onclick="closeModal('deleteUserModal')" style="flex: 1; padding: 0.7rem; border: 1.5px solid #d1d5db; border-radius: 10px; background: white; font-weight: 700; font-size: 0.85rem; cursor: pointer; color: #374151;">Batal</button>
            <form id="deleteUserForm" method="POST" style="flex:1;display:flex;">
                @csrf
                @method('DELETE')
                <button type="submit" style="width:100%; padding: 0.7rem; border: none; border-radius: 10px; background: #ef4444; color: white; font-weight: 700; font-size: 0.85rem; cursor: pointer;">Ya, Hapus</button>
            </form>
        </div>
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
            <!-- Biodata -->
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

            <!-- Antropometri -->
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

            <!-- Status -->
            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 0.25rem;">
                <span style="color: #64748b; font-size: 0.85rem; font-weight: 600;">Status Akun</span>
                <span id="view_siswa_status" class="status-pill status-active" style="width: max-content;"></span>
            </div>
        </div>

        <button type="button" class="btn" style="width:100%; margin-top: 1.5rem; background: #e2e8f0; color: #475569;" onclick="closeModal('viewSiswaModal')">Tutup</button>
    </div>
</div>

<script>
    function openAddModal(tab) {
        if (tab === 'siswa') {
            document.getElementById('addSiswaModal').style.display = 'flex';
        } else {
            document.getElementById('addUserModal').style.display = 'flex';
        }
    }

    function openEditModal(id, nama, email, telp, lokasi, roleId, status) {
        document.getElementById('editUserForm').action = '/dashboard/superadmin/users/' + id;
        document.getElementById('edit_nama_lengkap').value = nama;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_no_telp').value = telp;
        document.getElementById('edit_lokasi').value = lokasi;
        document.getElementById('edit_role_id').value = roleId;
        document.getElementById('edit_status').value = status;
        document.getElementById('editUserModal').style.display = 'flex';
    }

    function openEditSiswaModal(id, nama, nisn, kelas, alergi, contact, status) {
        document.getElementById('editSiswaForm').action = '/dashboard/superadmin/siswas/' + id;
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
        if(status === 'Active') {
            statusEl.className = 'status-pill status-active';
        } else {
            statusEl.className = 'status-pill status-inactive';
        }

        document.getElementById('viewSiswaModal').style.display = 'flex';
    }

    function openDeleteModal(type, id, nama) {
        document.getElementById('deleteConfirmText').textContent = (type === 'user' ? 'User' : 'Siswa') + ' "' + nama + '" akan dihapus secara permanen.';
        if(type === 'user') {
            document.getElementById('deleteUserForm').action = '/dashboard/superadmin/users/' + id;
        } else {
            document.getElementById('deleteUserForm').action = '/dashboard/superadmin/siswas/' + id;
        }
        document.getElementById('deleteUserModal').style.display = 'flex';
    }

    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }
</script>
@endsection
