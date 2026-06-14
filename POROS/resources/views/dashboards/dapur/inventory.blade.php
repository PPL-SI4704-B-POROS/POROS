@extends('layouts.app')

@section('title', 'Manajemen Bahan Baku')

@section('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    .inv-root {
        font-family: 'Plus Jakarta Sans', sans-serif;
        padding: 32px 36px;
        background: #f8f9fc;
        min-height: 100vh;
    }

    /* ── Page Header ── */
    .inv-header {
        margin-bottom: 32px;
    }
    .inv-header h1 {
        font-size: 1.75rem;
        font-weight: 800;
        color: #0c1e35;
        margin: 0 0 4px 0;
        letter-spacing: -0.4px;
    }
    .inv-header p {
        font-size: 13.5px;
        color: #94a3b8;
        margin: 0;
        font-weight: 500;
    }

    /* ── Alerts ── */
    .alert-success {
        background: #f0fdf4;
        color: #16a34a;
        padding: 14px 20px;
        border-radius: 12px;
        border: 1.5px solid #bbf7d0;
        margin-bottom: 24px;
        font-size: 13.5px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* ── Card Base (Match Pengumuman) ── */
    .inv-card {
        background: white;
        border-radius: 18px;
        border: 1.5px solid #f1f5f9;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        overflow: hidden;
        margin-bottom: 24px;
    }
    .inv-card-header {
        padding: 18px 24px;
        border-bottom: 1.5px solid #f8fafc;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .icon-badge {
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
    .card-title {
        font-size: 15px;
        font-weight: 700;
        color: #0c1e35;
        margin: 0;
    }

    /* ── Grid Layout for Forms ── */
    .form-layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: 24px;
        margin-bottom: 32px;
        align-items: start;
    }
    @media(min-width: 992px) { .form-layout { grid-template-columns: 1fr 1fr; } }

    /* ── Form Elements ── */
    .inv-form-body { padding: 22px 24px; }
    .form-group { margin-bottom: 16px; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
    .form-group label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: #475569;
        margin-bottom: 7px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }
    .form-control {
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
    .form-control:focus {
        border-color: #ff6b00;
        background: white;
        box-shadow: 0 0 0 3px rgba(255, 107, 0, 0.08);
    }
    
    /* ── Buttons ── */
    .btn-submit {
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
        margin-top: 8px;
    }
    .btn-submit:hover { box-shadow: 0 6px 18px rgba(255, 107, 0, 0.35); transform: translateY(-1px); }
    .btn-submit.blue-variant { background: linear-gradient(135deg, #2563eb 0%, #60a5fa 100%); box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25); }
    .btn-submit.blue-variant:hover { box-shadow: 0 6px 18px rgba(37, 99, 235, 0.35); }
    
    .btn-cancel {
        display: block; text-align: center; margin-top: 12px;
        color: #94a3b8; font-size: 13px; font-weight: 600; text-decoration: none;
    }
    .btn-cancel:hover { color: #0c1e35; text-decoration: underline; }

    /* ── Supplier Accordion List ── */
    .supplier-header {
        padding: 20px 24px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        cursor: pointer;
        transition: background 0.2s;
        
        /* 1. WARNA DEFAULT SAAT BELUM DIKLIK (Biru Pastel) */
        background-color: #eff6ff; 
        border-bottom: 1.5px solid #dbeafe;
    }
    
    /* 2. WARNA SAAT DISOROT MOUSE (Biru sedikit lebih gelap) */
    .supplier-header:hover { 
        background-color: #dbeafe; 
    }
    
    /* 3. WARNA SAAT SUDAH DIKLIK / TERBUKA (Oranye Pastel) */
    .supplier-header.active { 
        border-bottom-color: #fed7aa; 
        background-color: #fff7ed; 
    }
    
    .supplier-info-wrapper {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        flex: 1;
    }
    
    /* Warna icon panah bawaan disesuaikan dengan tema biru */
    .toggle-icon {
        width: 24px; height: 24px;
        display: flex; align-items: center; justify-content: center;
        background: #bfdbfe; /* Background icon biru */
        border-radius: 6px; 
        color: #1e40af; /* Warna panah biru tua */
        transition: transform 0.3s ease, background 0.3s ease, color 0.3s ease; 
        flex-shrink: 0;
        margin-top: 2px;
    }
    
    /* Warna icon panah saat terbuka (berubah oranye) */
    .supplier-header.active .toggle-icon { 
        transform: rotate(180deg); 
        background: #ffedd5; 
        color: #ff6b00; 
    }
    
    .supplier-details h3 { font-size: 16px; font-weight: 700; color: #0c1e35; margin: 0 0 6px 0; }
    .supplier-meta {
        font-size: 12.5px; color: #64748b; font-weight: 500;
        display: flex; flex-direction: column; gap: 4px;
    }
    .meta-row { display: flex; align-items: center; gap: 6px; }
    .meta-icon { stroke: #94a3b8; width: 14px; height: 14px; }

    /* Modern Action Buttons */
    .action-group { display: flex; gap: 8px; flex-shrink: 0; z-index: 10; }
    .btn-action {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: 12px; font-weight: 700; border-radius: 8px; padding: 6px 12px;
        text-decoration: none; border: 1.5px solid transparent; transition: all 0.18s; font-family: inherit; cursor: pointer;
    }
    .btn-edit { color: #2563eb; background: #eff6ff; border-color: #dbeafe; }
    .btn-edit:hover { background: #dbeafe; border-color: #2563eb; }
    .btn-delete { color: #ef4444; background: #fef2f2; border-color: #fee2e2; }
    .btn-delete:hover { background: #fee2e2; border-color: #ef4444; }

    /* ── Table Styling ── */
    .table-wrapper { padding: 0; overflow-x: auto; display: none; }
    .modern-table { width: 100%; border-collapse: collapse; text-align: left; }
    
    .modern-table th {
        padding: 14px 24px; font-size: 12px; font-weight: 700; color: #475569;
        text-transform: uppercase; letter-spacing: 0.5px; 
        /* Warna latar Header Tabel */
        background: #e2e8f0; 
        border-bottom: 2px solid #cbd5e1;
    }
    
    .modern-table td {
        padding: 16px 24px; font-size: 13.5px; color: #0c1e35; font-weight: 500;
        border-bottom: 1px solid #f1f5f9;
    }
    
    /* Membuat warna baris tabel selang-seling (Zebra Striping) agar mudah dibaca */
    .modern-table tbody tr:nth-child(even) {
        background-color: #f8fafc;
    }
    .modern-table tbody tr:nth-child(odd) {
        background-color: #ffffff;
    }
    
    /* Efek hover pada baris tabel */
    .modern-table tbody tr:hover { 
        background-color: #eff6ff; /* Soft blue saat disorot */
    }

    .empty-state { text-align: center; padding: 40px 24px; color: #94a3b8; }
    .empty-state p { font-size: 13.5px; font-weight: 500; margin: 0; }

    select.form-control.dropdown-supplier {
        background-color: #fff5ed; /* Soft Orange */
        border-color: #fed7aa;
        color: #9a3412;
        font-weight: 600;
        cursor: pointer;
    }
    select.form-control.dropdown-supplier:focus {
        background-color: #fff;
        border-color: #ff6b00;
    }

    select.form-control.dropdown-satuan {
        background-color: #eff6ff; /* Soft Blue */
        border-color: #bfdbfe;
        color: #1e40af;
        font-weight: 600;
        cursor: pointer;
    }
    select.form-control.dropdown-satuan:focus {
        background-color: #fff;
        border-color: #2563eb;
    }
</style>
@endsection

@section('content')
<div class="dashboard-layout">
    @include('partials.sidebar')

    <main class="main-content" style="padding: 0;">

        <div class="inv-root">
            @include('partials.header')
            
            <div class="inv-header">
                <h1>Manajemen Bahan Baku & Supplier</h1>
                <p>Kelola data stok, harga, dan informasi lengkap supplier Anda.</p>
            </div>

            @if(session('success'))
                <div class="alert-success">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert-error" style="background: #fef2f2; color: #ef4444; padding: 14px 20px; border-radius: 12px; border: 1.5px solid #fee2e2; margin-bottom: 24px; font-size: 13.5px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            <div class="form-layout">
                
                <div class="inv-card" style="margin-bottom: 0;">
                    <div class="inv-card-header">
                        <div class="icon-badge orange">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        <h2 class="card-title">{{ isset($supplierEdit) ? 'Edit Profil Supplier' : 'Registrasi Supplier Baru' }}</h2>
                    </div>
                    
                    <div class="inv-form-body">
                        <form action="{{ isset($supplierEdit) ? route('suppliers.update', $supplierEdit->id) : route('suppliers.store') }}" method="POST">
                            @csrf
                            @if(isset($supplierEdit)) @method('PUT') @endif

                            <div class="form-group">
                                <label>Nama Supplier / Toko</label>
                                <input type="text" name="nama_supplier" class="form-control" value="{{ old('nama_supplier', $supplierEdit->nama_supplier ?? '') }}" placeholder="Contoh: PT. Sumber Pangan" required>
                            </div>

                            <div class="form-group">
                                <label>Nomor Kontak (HP/Telepon/Email)</label>
                                <input type="text" name="kontak" class="form-control" value="{{ old('kontak', $supplierEdit->kontak ?? '') }}" placeholder="Contoh: 08123456789" required>
                            </div>

                            <div class="form-group">
                                <label>Alamat Lengkap</label>
                                <textarea name="alamat" rows="3" class="form-control" placeholder="Tuliskan alamat lengkap supplier..." required>{{ old('alamat', $supplierEdit->alamat ?? '') }}</textarea>
                            </div>

                            <button type="submit" class="btn-submit">
                                {{ isset($supplierEdit) ? 'Simpan Perubahan Supplier' : 'Tambahkan Supplier' }}
                            </button>
                            @if(isset($supplierEdit))
                                <a href="{{ route('inventory.index') }}" class="btn-cancel">Batalkan Edit</a>
                            @endif
                        </form>
                    </div>
                </div>
                
                <div class="inv-card" style="margin-bottom: 0;">
                    <div class="inv-card-header">
                        <div class="icon-badge blue">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                        </div>
                        <h2 class="card-title">{{ isset($bahanBaku) ? 'Edit Data Bahan Baku' : 'Tambah Bahan Baku Baru' }}</h2>
                    </div>
                    
                    <div class="inv-form-body">
                        <form action="{{ isset($bahanBaku) ? route('bahan-bakus.update', $bahanBaku->id) : route('bahan-bakus.store') }}" method="POST">
                            @csrf
                            @if(isset($bahanBaku)) @method('PUT') @endif

                            <div class="form-group">
                                <label>Nama Bahan Baku</label>
                                <input type="text" name="nama_bahan" class="form-control" value="{{ old('nama_bahan', $bahanBaku->nama_bahan ?? '') }}" placeholder="Contoh: Beras Premium" required>
                            </div>

                            <div class="form-group">
                                <label>Pilih Supplier</label>
                                <select name="supplier_id" class="form-control dropdown-supplier" required>
                                    <option value="">-- Pilih Supplier yang Menyediakan --</option>
                                    @if(isset($suppliers))
                                        @foreach($suppliers as $sup)
                                            <option value="{{ $sup->id }}" {{ old('supplier_id', $bahanBaku->supplier_id ?? '') == $sup->id ? 'selected' : '' }}>
                                                {{ $sup->nama_supplier }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="form-row">
                                <div>
                                    <label>Jumlah Stok Awal</label>
                                    <input type="number" name="stok" class="form-control" value="{{ old('stok', $bahanBaku->stok ?? '') }}" placeholder="0" required>
                                </div>
                                <div>
                                    <label>Batas Stok Minimal</label>
                                    <input type="number" name="stok_minimal" class="form-control" value="{{ old('stok_minimal', $bahanBaku->stok_minimal ?? '') }}" placeholder="0" required>
                                </div>
                                <div>
                                    <label>Tanggal Barang Masuk</label>
                                    <input type="date" name="barang_masuk" class="form-control" value="{{ old('barang_masuk', isset($bahanBaku) && $bahanBaku->barang_masuk ? $bahanBaku->barang_masuk->format('Y-m-d') : '') }}" required>
                                </div>
                                <div>
                                    <label>Tanggal Barang Keluar (Opsional)</label>
                                    <input type="date" name="barang_keluar" class="form-control" value="{{ old('barang_keluar', isset($bahanBaku) && $bahanBaku->barang_keluar ? $bahanBaku->barang_keluar->format('Y-m-d') : '') }}">
                                </div>
                            </div>

                            <div class="form-row">
                                <div>
                                    <label>Satuan (Unit)</label>
                                    <select name="satuan" class="form-control dropdown-satuan" required>
                                        <option value="">-- Satuan --</option>
                                        <option value="kg" {{ (old('satuan', $bahanBaku->satuan ?? '') == 'kg') ? 'selected' : '' }}>Kilogram (kg)</option>
                                        <option value="gram" {{ (old('satuan', $bahanBaku->satuan ?? '') == 'gram') ? 'selected' : '' }}>Gram (g)</option>
                                        <option value="liter" {{ (old('satuan', $bahanBaku->satuan ?? '') == 'liter') ? 'selected' : '' }}>Liter (L)</option>
                                        <option value="ml" {{ (old('satuan', $bahanBaku->satuan ?? '') == 'ml') ? 'selected' : '' }}>Mililiter (ml)</option>
                                    </select>
                                </div>
                                <div>
                                    <label>Harga Per Satuan (Rp)</label>
                                    <input type="number" name="harga" class="form-control" value="{{ old('harga', isset($bahanBaku) ? $bahanBaku->harga_satuan_terbaru : '') }}" required min="0" step="1" placeholder="Contoh: 15000">
                                </div>
                            </div>

                            <button type="submit" class="btn-submit blue-variant">
                                {{ isset($bahanBaku) ? 'Simpan Perubahan Bahan' : 'Tambahkan Ke Inventory' }}
                            </button>
                            @if(isset($bahanBaku))
                                <a href="{{ route('inventory.index') }}" class="btn-cancel">Batalkan Edit</a>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

            @if(isset($suppliers))
                @forelse($suppliers as $supplier)
                    <div class="inv-card">
                        
                        <div class="supplier-header" id="header-{{ $supplier->id }}" onclick="toggleSupplier('supplier-{{ $supplier->id }}', 'header-{{ $supplier->id }}')">
                            <div class="supplier-info-wrapper">
                                <div class="toggle-icon">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                </div>
                                <div class="supplier-details">
                                    <h3>{{ $supplier->nama_supplier }}</h3>
                                    <div class="supplier-meta">
                                        <div class="meta-row">
                                            <svg class="meta-icon" viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                            {{ $supplier->kontak ?? 'Tidak ada kontak' }}
                                        </div>
                                        <div class="meta-row">
                                            <svg class="meta-icon" viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                            {{ $supplier->alamat ?? 'Alamat belum diatur' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="action-group" onclick="event.stopPropagation()">
                                <a href="{{ route('suppliers.edit', $supplier->id) }}" class="btn-action btn-edit">Edit</a>
                                <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Yakin ingin menghapus Supplier {{ $supplier->nama_supplier }} beserta SEMUA bahan bakunya?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action btn-delete">Hapus</button>
                                </form>
                            </div>
                        </div>
                        
                        <div id="supplier-{{ $supplier->id }}" class="table-wrapper">
                            <table class="modern-table">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">No</th>
                                        <th>Nama Bahan Baku</th>
                                        <th>Stok (Batas Min)</th>
                                        <th>Harga Update Terakhir</th>
                                        <th style="width: 15%; text-align: center;">Tindakan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($supplier->bahanBakus as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td style="font-weight: 700;">{{ $item->nama_bahan ?? 'Nama tidak ditemukan' }}</td>
                                            <td>
                                                <span style="color: {{ $item->stok <= $item->stok_minimal ? '#ef4444' : '#16a34a' }}; font-weight: 700;">
                                                    {{ $item->stok_formatted }}
                                                </span> 
                                                <span style="color: #94a3b8;">(Min: {{ $item->stok_minimal_formatted }})</span>
                                            </td>
                                            <td>
                                                <div style="font-weight: 700; color: #ff6b00;">Rp {{ number_format($item->harga_satuan_terbaru, 0, ',', '.') }}</div>
                                                <div style="font-size: 11.5px; color: #94a3b8;">per {{ $item->satuan_harga_terbaru }}</div>
                                            </td>
                                            <td style="text-align: center;">
                                                <div class="action-group" style="justify-content: center;">
                                                    <a href="{{ route('bahan-bakus.edit', $item->id) }}" class="btn-action btn-edit">Edit</a>
                                                    <form action="{{ route('bahan-bakus.destroy', $item->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus {{ $item->nama_bahan }}?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn-action btn-delete">Hapus</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5">
                                                <div class="empty-state">
                                                    <p>Belum ada data bahan baku yang didaftarkan dari supplier ini.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <div class="inv-card">
                        <div class="empty-state" style="padding: 60px 24px;">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" style="margin-bottom: 16px;">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <p style="font-size: 15px; color: #64748b;">Belum ada Data Supplier yang ditambahkan ke sistem.</p>
                        </div>
                    </div>
                @endforelse
            @endif
            
        </div>
    </main>
</div>

<script>
    function toggleSupplier(contentId, headerId) {
        var content = document.getElementById(contentId);
        var header = document.getElementById(headerId);
        
        if (content.style.display === "none" || content.style.display === "") {
            content.style.display = "block";
            header.classList.add("active");
        } else {
            content.style.display = "none";
            header.classList.remove("active");
        }
    }
</script>
@endsection