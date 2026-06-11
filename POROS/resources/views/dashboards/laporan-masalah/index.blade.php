@extends('layouts.app')

@section('title', 'Laporan Masalah')

@section('content')
<div class="dashboard-layout">
    @include('partials.sidebar')

    <main class="main-content">
        @include('partials.header')

        @php
            $role = Auth::user()->role->nama_role;
            $storeRoute = $role == 'dapur' ? 'dapur.laporan-masalah.store' : 'sekolah.laporan-masalah.store';
            $destroyRouteName = $role == 'dapur' ? 'dapur.laporan-masalah.destroy' : 'sekolah.laporan-masalah.destroy';

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
            <p style="color: var(--text-muted); margin-top: 0.25rem;">Laporkan kendala atau masalah yang Anda temui</p>
        </div>

        @if(session('success'))
            <div style="background: #d1fae5; border: 1px solid #6ee7b7; color: #065f46; padding: 0.75rem 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                {{ session('success') }}
            </div>
        @endif

        <div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 1.5rem; align-items: start;">

            {{-- Form Laporan --}}
            <div class="card" style="padding: 1.5rem;">
                <h2 style="font-size: 1rem; font-weight: 700; color: #0c1e35; margin-bottom: 1.25rem;">Buat Laporan Baru</h2>

                <form action="{{ route($storeRoute) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- Judul --}}
                    <div style="margin-bottom: 1rem;">
                        <label style="display:block; font-size:0.85rem; font-weight:600; color:#0c1e35; margin-bottom:0.4rem;">
                            Judul Masalah <span style="color:#dc2626;">*</span>
                        </label>
                        <input type="text" name="judul_masalah" value="{{ old('judul_masalah') }}"
                            placeholder="Contoh: Stok bahan baku habis"
                            style="width:100%; padding:0.6rem 0.875rem; border:1px solid #e2e8f0; border-radius:0.5rem; font-size:0.875rem; color:#0c1e35; outline:none; box-sizing:border-box;">
                        @error('judul_masalah')
                            <p style="color:#dc2626; font-size:0.8rem; margin-top:0.25rem;">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kategori --}}
                    <div style="margin-bottom: 1rem;">
                        <label style="display:block; font-size:0.85rem; font-weight:600; color:#0c1e35; margin-bottom:0.4rem;">
                            Kategori <span style="color:#dc2626;">*</span>
                        </label>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                            @foreach($kategoriList as $nama => $style)
                            <label style="display:flex; align-items:center; gap:8px; padding:10px 12px; border:1.5px solid #e2e8f0; border-radius:10px; cursor:pointer; transition:all 0.15s;
                                {{ old('kategori') === $nama ? 'border-color:'.$style['color'].'; background:'.$style['bg'].';' : '' }}"
                                onclick="pilihKategori(this, '{{ addslashes($nama) }}', '{{ $style['color'] }}', '{{ $style['bg'] }}')">
                                <input type="radio" name="kategori" value="{{ $nama }}"
                                    {{ old('kategori') === $nama ? 'checked' : '' }}
                                    style="display:none;">
                                <span style="font-size:16px;">{{ $style['icon'] }}</span>
                                <span style="font-size:12px; font-weight:600; color:#0c1e35; line-height:1.3;">{{ $nama }}</span>
                            </label>
                            @endforeach
                        </div>
                        @error('kategori')
                            <p style="color:#dc2626; font-size:0.8rem; margin-top:0.25rem;">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div style="margin-bottom: 1rem;">
                        <label style="display:block; font-size:0.85rem; font-weight:600; color:#0c1e35; margin-bottom:0.4rem;">
                            Deskripsi <span style="color:#dc2626;">*</span>
                        </label>
                        <textarea name="deskripsi" rows="4" placeholder="Jelaskan masalah secara detail..."
                            style="width:100%; padding:0.6rem 0.875rem; border:1px solid #e2e8f0; border-radius:0.5rem; font-size:0.875rem; color:#0c1e35; outline:none; resize:vertical; box-sizing:border-box;">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <p style="color:#dc2626; font-size:0.8rem; margin-top:0.25rem;">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Foto Bukti --}}
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display:block; font-size:0.85rem; font-weight:600; color:#0c1e35; margin-bottom:0.4rem;">
                            Foto Bukti <span style="color:var(--text-muted); font-weight:400;">(opsional, maks. 2MB)</span>
                        </label>
                        <input type="file" name="foto_bukti" accept="image/jpg,image/jpeg,image/png"
                            style="width:100%; padding:0.5rem; border:1px dashed #cbd5e1; border-radius:0.5rem; font-size:0.875rem; background:#f8fafc; box-sizing:border-box;">
                        @error('foto_bukti')
                            <p style="color:#dc2626; font-size:0.8rem; margin-top:0.25rem;">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%;">
                        Kirim Laporan
                    </button>
                </form>
            </div>

            {{-- Riwayat Laporan --}}
            <div>
                <h2 style="font-size:1rem; font-weight:700; color:#0c1e35; margin-bottom:1rem;">Riwayat Laporan Saya</h2>

                @forelse($laporan as $item)
                <div class="card" style="padding:1.25rem; margin-bottom:1rem;">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:1rem;">
                        <div style="flex:1;">
                            <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.4rem; flex-wrap:wrap;">
                                <span style="font-size:0.75rem; color:var(--text-muted); font-weight:600;">{{ $item->formatted_id }}</span>

                                {{-- Badge Status --}}
                                @php
                                    $statusColor = match($item->status) {
                                        'Open'        => ['bg' => '#fee2e2', 'text' => '#dc2626'],
                                        'In Progress' => ['bg' => '#fef9c3', 'text' => '#ca8a04'],
                                        'Resolved'    => ['bg' => '#d1fae5', 'text' => '#059669'],
                                        default       => ['bg' => '#f1f5f9', 'text' => '#64748b'],
                                    };
                                @endphp
                                <span style="background:{{ $statusColor['bg'] }}; color:{{ $statusColor['text'] }}; padding:0.15rem 0.6rem; border-radius:999px; font-size:0.75rem; font-weight:600;">
                                    {{ $item->status }}
                                </span>

                                {{-- Badge Kategori --}}
                                @if($item->kategori && isset($kategoriList[$item->kategori]))
                                <span style="background:{{ $kategoriList[$item->kategori]['bg'] }}; color:{{ $kategoriList[$item->kategori]['color'] }}; padding:0.15rem 0.6rem; border-radius:999px; font-size:0.75rem; font-weight:600;">
                                    {{ $kategoriList[$item->kategori]['icon'] }} {{ $item->kategori }}
                                </span>
                                @endif
                            </div>

                            <p style="font-weight:700; color:#0c1e35; font-size:0.9rem; margin-bottom:0.3rem;">{{ $item->judul_masalah }}</p>
                            <p style="color:var(--text-muted); font-size:0.82rem; line-height:1.5;">{{ Str::limit($item->deskripsi, 120) }}</p>
                            <p style="color:var(--text-muted); font-size:0.78rem; margin-top:0.5rem;">{{ $item->created_at->format('d M Y, H:i') }}</p>
                        </div>

                        @if($item->status == 'Open')
                        <form action="{{ route($destroyRouteName, $item) }}" method="POST"
                            onsubmit="return confirm('Yakin ingin menghapus laporan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background:none; border:none; color:#dc2626; cursor:pointer; font-size:0.8rem; font-weight:600; white-space:nowrap;">
                                Hapus
                            </button>
                        </form>
                        @endif
                    </div>

                    @if($item->foto_bukti)
                    <div style="margin-top:0.75rem;">
                        <img src="{{ asset('storage/' . $item->foto_bukti) }}" alt="Foto Bukti"
                            style="max-width:200px; border-radius:0.375rem; border:1px solid #e2e8f0;">
                    </div>
                    @endif
                </div>
                @empty
                <div class="card" style="padding:2.5rem; text-align:center; color:var(--text-muted);">
                    <div style="font-size:2rem; margin-bottom:0.5rem;">📋</div>
                    <p>Belum ada laporan yang dibuat.</p>
                </div>
                @endforelse
            </div>

        </div>
    </main>
</div>

<script>
function pilihKategori(label, nama, color, bg) {
    // Reset semua
    document.querySelectorAll('input[name="kategori"]').forEach(function(radio) {
        var parentLabel = radio.closest('label');
        parentLabel.style.borderColor = '#e2e8f0';
        parentLabel.style.background = 'white';
        radio.checked = false;
    });
    // Aktifkan yang dipilih
    var radio = label.querySelector('input[type="radio"]');
    radio.checked = true;
    label.style.borderColor = color;
    label.style.background = bg;
}
</script>

@endsection