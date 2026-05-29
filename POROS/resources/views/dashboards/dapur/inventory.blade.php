@extends('layouts.app')

@section('title', 'Manajemen Bahan Baku')

@section('content')
<div class="dashboard-layout">
    @include('partials.sidebar')

    <main class="main-content">
        @include('partials.header')

        <div style="padding: 2rem;">
            <!-- Header Halaman -->
            <div style="margin-bottom: 2rem;">
                <h1 style="font-size: 2rem; font-weight: 800; color: #0c1e35; margin-bottom: 0.5rem;">Manajemen Bahan Baku & Supplier</h1>
                <p style="color: var(--text-muted);">
                    Kelola data stok, satuan, dan master data supplier bahan baku di sini.
                </p>
            </div>

            <!-- Alert Success -->
            @if(session('success'))
                <div style="padding: 1rem; margin-bottom: 1.5rem; background-color: #dcfce7; color: #065f46; border: 1px solid #bbf7d0; border-radius: 8px;">
                    {{ session('success') }}
                </div>
            @endif

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
                
                <!-- KIRI: CARD FORM CREATE / UPDATE BAHAN BAKU -->
                <div class="card" style="padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                    <h2 style="font-size: 1.25rem; font-weight: 700; color: #0c1e35; margin-bottom: 1.5rem;">
                        {{ isset($bahanBaku) ? 'Edit Bahan Baku' : 'Tambah Bahan Baku' }}
                    </h2>
                    
                    <form action="{{ isset($bahanBaku) ? route('bahan-bakus.update', $bahanBaku->id) : route('bahan-bakus.store') }}" method="POST">
                        @csrf
                        @if(isset($bahanBaku)) @method('PUT') @endif

                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; margin-bottom: 0.5rem; color: #0c1e35; font-weight: 600;">Nama Bahan Baku</label>
                            <input type="text" name="nama_bahan" value="{{ old('nama_bahan', $bahanBaku->nama_bahan ?? '') }}" required 
                                style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 6px; outline: none;">
                        </div>

                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; margin-bottom: 0.5rem; color: #0c1e35; font-weight: 600;">Supplier</label>
                            <select name="supplier_id" required 
                                style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 6px; outline: none; background-color: white;">
                                <option value="">-- Pilih Supplier --</option>
                                @if(isset($suppliers))
                                    @foreach($suppliers as $sup)
                                        <option value="{{ $sup->id }}" {{ old('supplier_id', $bahanBaku->supplier_id ?? '') == $sup->id ? 'selected' : '' }}>
                                            {{ $sup->nama_supplier }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                            <div>
                                <label style="display: block; margin-bottom: 0.5rem; color: #0c1e35; font-weight: 600;">Jml Stok</label>
                                <input type="number" name="stok" value="{{ old('stok', $bahanBaku->stok ?? '') }}" required 
                                    style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 6px; outline: none;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 0.5rem; color: #0c1e35; font-weight: 600;">Stok Min</label>
                                <input type="number" name="stok_minimal" value="{{ old('stok_minimal', $bahanBaku->stok_minimal ?? '') }}" required 
                                    style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 6px; outline: none;">
                            </div>
                        </div>

                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; margin-bottom: 0.5rem; color: #0c1e35; font-weight: 600;">Satuan Berat/Volume (Wajib)</label>
                            <select name="satuan" required style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 6px; outline: none; background-color: white;">
                                <option value="">-- Pilih Satuan --</option>
                                <option value="kg" {{ (old('satuan', $bahanBaku->satuan ?? '') == 'kg') ? 'selected' : '' }}>kg</option>
                                <option value="gram" {{ (old('satuan', $bahanBaku->satuan ?? '') == 'gram') ? 'selected' : '' }}>gram</option>
                                <option value="liter" {{ (old('satuan', $bahanBaku->satuan ?? '') == 'liter') ? 'selected' : '' }}>liter</option>
                                <option value="ml" {{ (old('satuan', $bahanBaku->satuan ?? '') == 'ml') ? 'selected' : '' }}>ml</option>
                            </select>
                        </div>

                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; margin-bottom: 0.5rem; color: #0c1e35; font-weight: 600;">Harga per 1 Satuan (contoh: Harga 1 kg)</label>
                            <input type="number" name="harga" value="{{ old('harga', isset($bahanBaku) ? $bahanBaku->harga_satuan_terbaru : '') }}" required min="0" step="1" placeholder="Contoh: 40000"
                                style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 6px; outline: none;">
                        </div>

                        <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; border-radius: 6px; font-weight: 600; cursor: pointer;">
                            {{ isset($bahanBaku) ? 'Update' : 'Simpan' }}
                        </button>
                        @if(isset($bahanBaku))
                            <a href="{{ route('inventory.index') }}" style="margin-left: 1rem; color: var(--text-muted); text-decoration: none;">Batal</a>
                        @endif
                    </form>
                </div>

                <!-- KANAN: CARD FORM CREATE / UPDATE SUPPLIER -->
                <div class="card" style="padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); height: fit-content;">
                    <h2 style="font-size: 1.25rem; font-weight: 700; color: #0c1e35; margin-bottom: 1.5rem;">
                        {{ isset($supplierEdit) ? 'Edit Supplier' : 'Tambah Supplier Baru' }}
                    </h2>
                    
                    <form action="{{ isset($supplierEdit) ? route('suppliers.update', $supplierEdit->id) : route('suppliers.store') }}" method="POST">
                        @csrf
                        @if(isset($supplierEdit)) @method('PUT') @endif

                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; margin-bottom: 0.5rem; color: #0c1e35; font-weight: 600;">Nama Supplier</label>
                            <input type="text" name="nama_supplier" value="{{ old('nama_supplier', $supplierEdit->nama_supplier ?? '') }}" required 
                                style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 6px; outline: none;">
                        </div>

                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; margin-bottom: 0.5rem; color: #0c1e35; font-weight: 600;">Kontak</label>
                            <input type="text" name="kontak" value="{{ old('kontak', $supplierEdit->kontak ?? '') }}" required 
                                style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 6px; outline: none;">
                        </div>

                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; margin-bottom: 0.5rem; color: #0c1e35; font-weight: 600;">Alamat</label>
                            <textarea name="alamat" rows="3" style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 6px; outline: none;">{{ old('alamat', $supplierEdit->alamat ?? '') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; border-radius: 6px; font-weight: 600; cursor: pointer; background-color: #10b981; border-color: #10b981;">
                            {{ isset($supplierEdit) ? 'Update Supplier' : 'Simpan Supplier' }}
                        </button>
                        @if(isset($supplierEdit))
                            <a href="{{ route('inventory.index') }}" style="margin-left: 1rem; color: var(--text-muted); text-decoration: none;">Batal</a>
                        @endif
                    </form>
                </div>
            </div>

            <!-- DAFTAR INVENTORY DIKELOMPOKKAN PER SUPPLIER -->
            @if(isset($suppliers))
                @forelse($suppliers as $supplier)
                    <div class="card" style="margin-bottom: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); overflow: hidden;">
                        
                        <!-- Header Nama Supplier (Dipisah jadi 2 area: Kiri untuk toggle, Kanan untuk tombol Aksi) -->
                        <div style="background-color: #0c1e35; color: white; padding: 1rem 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                            
                            <!-- Area Kiri: Info Supplier & Icon Toggle -->
                            <div onclick="toggleSupplier('supplier-{{ $supplier->id }}', 'icon-{{ $supplier->id }}')" 
                                 style="cursor: pointer; user-select: none; display: flex; align-items: center; gap: 1rem; flex-grow: 1;">
                                <div id="icon-{{ $supplier->id }}" style="font-size: 1.2rem; transition: transform 0.2s; width: 20px;">
                                    ▼
                                </div>
                                <div>
                                    <h3 style="margin: 0; font-size: 1.2rem; font-weight: 600;">Supplier: {{ $supplier->nama_supplier }}</h3>
                                    <small style="opacity: 0.8;">Kontak: {{ $supplier->kontak ?? '-' }}</small>
                                </div>
                            </div>

                            <!-- Area Kanan: Tombol Edit/Hapus Supplier -->
                            <div style="display: flex; gap: 0.5rem;">
                                <a href="{{ route('suppliers.edit', $supplier->id) }}" style="padding: 0.4rem 0.8rem; background-color: #f59e0b; color: white; border-radius: 4px; font-size: 0.875rem; text-decoration: none;">
                                    Edit Supplier
                                </a>
                                <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Yakin ingin menghapus Supplier {{ $supplier->nama_supplier }} beserta SEMUA bahan bakunya?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" dusk="delete-supplier-{{ $supplier->id }}" style="padding: 0.4rem 0.8rem; background-color: #ef4444; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.875rem;">
                                        Hapus Supplier
                                    </button>
                                </form>
                            </div>

                        </div>
                        
                        <!-- Container Tabel (Secara default disembunyikan / display: none) -->
                        <div id="supplier-{{ $supplier->id }}" style="padding: 1.5rem; overflow-x: auto; display: none;">
                            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                                <thead>
                                    <tr style="border-bottom: 2px solid #e2e8f0;">
                                        <th style="padding: 0.75rem; color: #0c1e35; font-weight: 600; width: 5%;">No</th>
                                        <th style="padding: 0.75rem; color: #0c1e35; font-weight: 600;">Nama Bahan Baku</th>
                                        <th style="padding: 0.75rem; color: #0c1e35; font-weight: 600;">Stok & Satuan</th>
                                        <th style="padding: 0.75rem; color: #0c1e35; font-weight: 600;">Harga (Rp)</th>
                                        <th style="padding: 0.75rem; color: #0c1e35; font-weight: 600; width: 15%; text-align: center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($supplier->bahanBakus as $index => $item)
                                        <tr style="border-bottom: 1px solid #f1f5f9;">
                                            <td style="padding: 0.75rem;">{{ $index + 1 }}</td>
                                            <td style="padding: 0.75rem; font-weight: 500; color: #0c1e35;">{{ $item->nama_bahan ?? 'Nama tidak ditemukan' }}</td>
                                            <td style="padding: 0.75rem;">{{ $item->stok_formatted }} (Min: {{ $item->stok_minimal_formatted }})</td>
                                            <td style="padding: 0.75rem;">Rp {{ number_format($item->harga_satuan_terbaru, 0, ',', '.') }} / {{ $item->satuan_harga_terbaru }}</td>
                                            <td style="padding: 0.75rem; text-align: center;">
                                                <a href="{{ route('bahan-bakus.edit', $item->id) }}" style="display: inline-block; padding: 0.3rem 0.6rem; background-color: #f59e0b; color: white; border-radius: 4px; cursor: pointer; font-size: 0.8rem; margin-right: 0.25rem; text-decoration: none;">
                                                    Edit
                                                </a>
                                                <form action="{{ route('bahan-bakus.destroy', $item->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus {{ $item->nama_bahan }}?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" style="padding: 0.3rem 0.6rem; background-color: #ef4444; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" style="padding: 1.5rem; text-align: center; color: var(--text-muted);">
                                                Belum ada bahan baku dari supplier ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <div style="padding: 2rem; text-align: center; color: var(--text-muted); background: white; border-radius: 8px;">
                        Data Supplier dan Inventory masih kosong.
                    </div>
                @endforelse
            @endif
            
        </div>
    </main>
</div>

<script>
    function toggleSupplier(contentId, iconId) {
        var content = document.getElementById(contentId);
        var icon = document.getElementById(iconId);
        
        if (content.style.display === "none" || content.style.display === "") {
            content.style.display = "block";
            icon.innerHTML = "▲"; 
        } else {
            content.style.display = "none";
            icon.innerHTML = "▼";
        }
    }
</script>
@endsection