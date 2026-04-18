@extends('layouts.app')

@section('title', 'User Management')

@section('styles')
<style>
    .stats-card {
        background: white;
        border-radius: 1.25rem;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        border: 1px solid #f3f4f6;
    }
    .stats-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .icon-total { background: #eff6ff; color: #3b82f6; }
    .icon-student { background: #ecfdf5; color: #10b981; }
    .icon-admin { background: #f3e8ff; color: #9333ea; }
    .icon-dapur { background: #fff7ed; color: #ea580c; }
    .icon-sekolah { background: #eff6ff; color: #2563eb; }

    .user-table { width: 100%; border-collapse: separate; border-spacing: 0 0.75rem; }
    .user-table th { text-align: left; padding: 1rem; color: #64748b; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.025em; }
    .user-table td { padding: 1rem; background: white; vertical-align: middle; }
    .user-table tr td:first-child { border-radius: 12px 0 0 12px; }
    .user-table tr td:last-child { border-radius: 0 12px 12px 0; }
    
    .avatar { width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; color: white; margin-right: 1rem; }
    .role-badge { padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; }
    .badge-admin { background: #f3e8ff; color: #9333ea; }
    .badge-dapur { background: #fff7ed; color: #ea580c; }
    .badge-sekolah { background: #eff6ff; color: #2563eb; }
    
    .status-pill { padding: 0.25rem 0.6rem; border-radius: 12px; font-size: 0.7rem; font-weight: 700; }
    .status-active { background: #dcfce7; color: #15803d; }
    .status-inactive { background: #f3f4f6; color: #6b7280; }

    .search-container { display: flex; gap: 1rem; align-items: center; margin-bottom: 1.5rem; }
    .search-input { flex: 1; padding: 0.65rem 1rem 0.65rem 2.5rem; border: 1px solid #e2e8f0; border-radius: 10px; background: white; position: relative; }
    .search-icon { position: absolute; left: 1rem; color: #94a3b8; }
</style>
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
            <button class="btn btn-primary" style="width: auto; padding: 0.75rem 1.5rem; display: flex; align-items: center; gap: 0.5rem; border-radius: 12px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add New User
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

        <!-- Table Section -->
        <div class="card" style="border: none; box-shadow: none; padding: 0; background: transparent;">
            <div class="search-container">
                <form action="{{ route('users.index') }}" method="GET" style="flex: 1; position: relative; display: flex; align-items: center;">
                    <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" name="search" class="search-input" placeholder="Search users..." value="{{ request('search') }}">
                </form>
                <form action="{{ route('users.index') }}" method="GET" id="roleFilterForm">
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
                        <th>Last Login</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
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
                            <td style="color: #64748b; font-size: 0.85rem;">
                                {{ $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i') : 'Never' }}
                            </td>
                            <td>
                                <span class="status-pill {{ $user->status == 'Active' ? 'status-active' : 'status-inactive' }}">
                                    {{ $user->status }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <button style="color: #ef4444; background: none; border: none; font-weight: 700; font-size: 0.85rem; cursor: pointer;">Edit</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="margin-top: 1.5rem;">
                {{ $users->links() }}
            </div>
        </div>
    </main>
</div>
@endsection
