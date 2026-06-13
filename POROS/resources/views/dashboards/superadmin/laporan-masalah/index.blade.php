@extends('layouts.app')

@section('title', 'Laporan Masalah')

@section('content')
<div class="dashboard-layout">
    @include('partials.sidebar')

    <main class="main-content">
        @include('partials.header')

        @php
            $kategoriList = [
                'Bug Aplikasi'              => ['icon' => '🐛', 'color' => '#dc2626', 'bg' => '#fef2f2'],
                'Bahan Baku'                => ['icon' => '🥦', 'color' => '#16a34a', 'bg' => '#f0fdf4'],
                'Transportasi & Pengiriman' => ['icon' => '🚚', 'color' => '#2563eb', 'bg' => '#eff6ff'],
                'Menu & Produksi'           => ['icon' => '🍱', 'color' => '#d97706', 'bg' => '#fffbeb'],
                'Data Siswa'                => ['icon' => '👤', 'color' => '#7c3aed', 'bg' => '#f5f3ff'],
                'Keuangan'                  => ['icon' => '💰', 'color' => '#0891b2', 'bg' => '#ecfeff'],
                'Lainnya'                   => ['icon' => '📋', 'color' => '#64748b', 'bg' => '#f8fafc'],
            ];
        @endphp

        <div class="page-header" style="margin-bottom: 1.5rem;">
            <h1 style="font-size: 1.75rem; font-weight: 800; color: #0c1e35;">Laporan Masalah</h1>
            <p style="color: var(--text-muted); margin-top: 0.25rem;">Daftar laporan masalah dari Sekolah dan Dapur</p>
        </div>

        @if(session('success'))
            <div style="background: #d1fae5; border: 1px solid #6ee7b7; color: #065f46; padding: 0.75rem 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                {{ session('success') }}
            </div>
        @endif

        {{-- Filter --}}
        <div class="card" style="margin-bottom: 1.5rem; padding: 1.25rem 1.5rem;">
            <form method="GET" action="{{ route('superadmin.laporan-masalah.index') }}">
                <div style="display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap;">

                    {{-- Filter Status --}}
                    <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                        <label style="font-size: 0.8rem; font-weight: 600; color: #0c1e35;">Status</label>
                        <select name="status" style="height: 38px; padding: 0 0.875rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; color: #0c1e35; background: white; min-width: 150px;">
                            <option value="">Semua Status</option>
                            <option value="Open"        {{ request('status') == 'Open'        ? 'selected' : '' }}>Open</option>
                            <option value="In Progress" {{ request('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="Resolved"    {{ request('status') == 'Resolved'    ? 'selected' : '' }}>Resolved</option>
                        </select>
                    </div>

                    {{-- Filter Dari --}}
                    <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                        <label style="font-size: 0.8rem; font-weight: 600; color: #0c1e35;">Dari</label>
                        <select name="role" style="height: 38px; padding: 0 0.875rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; color: #0c1e35; background: white; min-width: 150px;">
                            <option value="">Semua Pengguna</option>
                            <option value="sekolah" {{ request('role') == 'sekolah' ? 'selected' : '' }}>Sekolah</option>
                            <option value="dapur"   {{ request('role') == 'dapur'   ? 'selected' : '' }}>Dapur</option>
                        </select>
                    </div>

                    {{-- Filter Kategori --}}
                    <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                        <label style="font-size: 0.8rem; font-weight: 600; color: #0c1e35;">Kategori</label>
                        <select name="kategori" style="height: 38px; padding: 0 0.875rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; color: #0c1e35; background: white; min-width: 200px;">
                            <option value="">Semua Kategori</option>
                            @foreach($kategoriList as $nama => $style)
                            <option value="{{ $nama }}" {{ request('kategori') == $nama ? 'selected' : '' }}>
                                {{ $style['icon'] }} {{ $nama }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div style="display: flex; gap: 0.5rem; align-items: flex-end;">
                        <button type="submit" class="btn btn-primary" style="height: 38px; width: auto; padding: 0 1.25rem;">Filter</button>
                        <a href="{{ route('superadmin.laporan-masalah.index') }}" class="btn" style="height: 38px; width: auto; padding: 0 1.25rem; background: #f1f5f9; color: #0c1e35; display: flex; align-items: center;">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Tabel Laporan --}}
        <div class="card" style="padding: 0; overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <th style="padding: 0.875rem 1.25rem; text-align: left; font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">ID</th>
                        <th style="padding: 0.875rem 1.25rem; text-align: left; font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Judul Masalah</th>
                        <th style="padding: 0.875rem 1.25rem; text-align: left; font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Kategori</th>
                        <th style="padding: 0.875rem 1.25rem; text-align: left; font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Dilaporkan Oleh</th>
                        <th style="padding: 0.875rem 1.25rem; text-align: left; font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Status</th>
                        <th style="padding: 0.875rem 1.25rem; text-align: left; font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Tanggal</th>
                        <th style="padding: 0.875rem 1.25rem; text-align: left; font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporan as $item)
                    <!-- <tr style="border-bottom: 1px solid #f1f5f9;" class="table-row-hover"> -->
                    <tr style="border-bottom: 1px solid #f1f5f9; {{ $item->status === 'Resolved' ? 'opacity: 0.4;' : '' }}" class="table-row-hover">    
                        <td style="padding: 1rem 1.25rem; font-size: 0.85rem; font-weight: 600; color: #64748b;">{{ $item->formatted_id }}</td>

                        <td style="padding: 1rem 1.25rem;">
                            <div style="font-weight: 600; color: #0c1e35; font-size: 0.9rem;">{{ $item->judul_masalah }}</div>
                            <div style="color: var(--text-muted); font-size: 0.8rem; margin-top: 0.2rem; max-width: 260px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $item->deskripsi }}</div>
                        </td>

                        {{-- Kolom Kategori --}}
                        <td style="padding: 1rem 1.25rem;">
                            @if($item->kategori && isset($kategoriList[$item->kategori]))
                                <span style="display:inline-flex; align-items:center; gap:5px; background:{{ $kategoriList[$item->kategori]['bg'] }}; color:{{ $kategoriList[$item->kategori]['color'] }}; padding:0.2rem 0.65rem; border-radius:999px; font-size:0.78rem; font-weight:600; white-space:nowrap;">
                                    {{ $kategoriList[$item->kategori]['icon'] }} {{ $item->kategori }}
                                </span>
                            @else
                                <span style="color: var(--text-muted); font-size: 0.8rem;">-</span>
                            @endif
                        </td>

                        <td style="padding: 1rem 1.25rem;">
                            <div style="font-weight: 600; color: #0c1e35; font-size: 0.875rem;">{{ $item->user->nama_lengkap ?? '-' }}</div>
                            <span style="font-size: 0.75rem; background: #e0f2fe; color: #0369a1; padding: 0.15rem 0.5rem; border-radius: 999px; font-weight: 600; text-transform: capitalize;">
                                {{ $item->user->role->nama_role ?? '-' }}
                            </span>
                        </td>

                        <td style="padding: 1rem 1.25rem;">
                            @php
                                $statusColor = match($item->status) {
                                    'Open'        => ['bg' => '#fee2e2', 'text' => '#dc2626'],
                                    'In Progress' => ['bg' => '#fef9c3', 'text' => '#ca8a04'],
                                    'Resolved'    => ['bg' => '#d1fae5', 'text' => '#059669'],
                                    default       => ['bg' => '#f1f5f9', 'text' => '#64748b'],
                                };
                            @endphp
                            <span style="background: {{ $statusColor['bg'] }}; color: {{ $statusColor['text'] }}; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.8rem; font-weight: 600;">
                                {{ $item->status }}
                            </span>
                        </td>

                        <td style="padding: 1rem 1.25rem; font-size: 0.85rem; color: var(--text-muted);">
                            {{ $item->created_at->format('d M Y') }}
                        </td>

                        <td style="padding: 1rem 1.25rem;">
                            <div style="display: flex; gap: 0.5rem; align-items: center;">
                                <button onclick="openDetailModal({{ $item->id }})"
                                    style="background: #f1f5f9; color: #0c1e35; border: none; padding: 0.4rem 0.75rem; border-radius: 0.375rem; font-size: 0.8rem; font-weight: 600; cursor: pointer;">
                                    Detail
                                </button>
                                <form action="{{ route('superadmin.laporan-masalah.updateStatus', $item) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()"
                                        style="font-size: 0.8rem; padding: 0.35rem 0.5rem; border: 1px solid #e2e8f0; border-radius: 0.375rem; background: white; color: #0c1e35; cursor: pointer;">
                                        <option value="Open"        {{ $item->status == 'Open'        ? 'selected' : '' }}>Open</option>
                                        <option value="In Progress" {{ $item->status == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="Resolved"    {{ $item->status == 'Resolved'    ? 'selected' : '' }}>Resolved</option>
                                    </select>
                                </form>
                                <form action="{{ route('superadmin.laporan-masalah.destroy', $item) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus laporan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        style="background: #fee2e2; color: #dc2626; border: none; padding: 0.4rem 0.75rem; border-radius: 0.375rem; font-size: 0.8rem; font-weight: 600; cursor: pointer;">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    {{-- Detail row --}}
                    <tr id="detail-{{ $item->id }}" style="display: none; background: #f8fafc;">
                        <td colspan="7" style="padding: 1.25rem 1.5rem;">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                                <div>
                                    <p style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Deskripsi Lengkap</p>
                                    <p style="color: #0c1e35; font-size: 0.875rem; line-height: 1.6;">{{ $item->deskripsi }}</p>
                                </div>
                                <div>
                                    <p style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Foto Bukti</p>
                                    @if($item->foto_bukti)
                                        <img src="{{ asset('storage/' . $item->foto_bukti) }}" alt="Foto Bukti"
                                            style="max-width: 300px; border-radius: 0.5rem; border: 1px solid #e2e8f0;">
                                    @else
                                        <p style="color: var(--text-muted); font-size: 0.875rem;">Tidak ada foto bukti.</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="padding: 3rem; text-align: center; color: var(--text-muted);">
                            <div style="font-size: 2.5rem; margin-bottom: 0.75rem;">📋</div>
                            <p style="font-weight: 600;">Belum ada laporan masalah</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            @if($laporan->hasPages())
            <div style="padding: 1rem 1.25rem; border-top: 1px solid #f1f5f9;">
                {{ $laporan->links() }}
            </div>
            @endif
        </div>

    </main>
</div>

<script>
function openDetailModal(id) {
    const row = document.getElementById('detail-' + id);
    row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
}
</script>
@endsection