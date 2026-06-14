@extends('layouts.app')

@section('title', 'Logistics & Deliveries')

@section('content')
<div class="dashboard-layout">
    @include('partials.sidebar')

    <main class="main-content">
        @include('partials.header')

        {{-- HEADER HALAMAN --}}
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
            <div>
                <h1 style="font-size:2rem; font-weight:800; color:#0c1e35;">Logistics & Deliveries</h1>
                <p style="color:#6b7280; margin-top:0.5rem;">Ringkasan Stok Utama — pantau tingkat ketersediaan bahan</p>
            </div>
            <div style="display:flex; gap:0.75rem;">
                <button onclick="openAddItemModal()" style="background:#0c1e35; color:white; border:none; padding:1rem 1.5rem; border-radius:14px; font-weight:700; cursor:pointer;">
                    + Tambah Item
                </button>
            </div>
        </div>

        {{-- NOTIFIKASI FLASH --}}
        @if(session('success'))
            <div style="background:#dcfce7; color:#166534; padding:1rem; border-radius:12px; margin-bottom:1rem; font-weight:600;">
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div style="background:#fee2e2; color:#991b1b; padding:1rem; border-radius:12px; margin-bottom:1rem;">
                <ul style="margin-left:1rem;">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        {{-- KARTU RINGKASAN --}}
        <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:2rem;">
            <div class="card">
                <h3 style="color:#6b7280; margin-bottom:1rem;">Total Item</h3>
                <h1 style="font-size:2rem; color:#0c1e35;">{{ $stocks->count() }}</h1>
            </div>
            <div class="card">
                <h3 style="color:#6b7280; margin-bottom:1rem;">Stok Aman (Good)</h3>
                {{-- 🛠️ FIX: Panggil langsung variabel totalAman dari Controller --}}
                <h1 style="font-size:2rem; color:#22c55e;">{{ $totalAman }}</h1>
            </div>
            <div class="card">
                <h3 style="color:#6b7280; margin-bottom:1rem;">Stok Menipis (Low)</h3>
                {{-- 🛠️ FIX: Panggil langsung variabel totalMenipis dari Controller --}}
                <h1 style="font-size:2rem; color:#f59e0b;">{{ $totalMenipis }}</h1>
            </div>
            <div class="card">
                <h3 style="color:#6b7280; margin-bottom:1rem;">Kritis (Critical)</h3>
                {{-- 🛠️ FIX: Panggil langsung variabel totalKritis dari Controller --}}
                <h1 style="font-size:2rem; color:#ef4444;">{{ $totalKritis }}</h1>
            </div>
        </div>

        {{-- TABEL UTAMA --}}
        <div class="card" style="background:white; border-radius:20px; padding:2rem;">
            <h2 style="font-size:1.5rem; font-weight:700; color:#0c1e35; margin-bottom:2rem;">Stok Inventaris</h2>

            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid #e5e7eb; text-align:left;">
                        <th style="padding:1rem;">Status</th>
                        <th style="padding:1rem;">Item</th>
                        <th style="padding:1rem;">Kategori</th>
                        <th style="padding:1rem;">Total Kuantitas</th>
                        <th style="padding:1rem;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stocks as $bahanBakuId => $group)
                        @php
                            $namaBahan = $groupNames[$bahanBakuId] ?? 'Bahan Baku Tidak Diketahui';

                            $stock = $group->first();
                            $uniqueId = $stock->id;
                            $totalQty = $group->sum('quantity');
                            
                            // Menyesuaikan level tampilan baris tabel dengan logika fallback controller
                            $qtyFisik = (float) $totalQty;
                            $levelBawaan = strtolower($stock->stock_level ?? '');
                            $level = ($levelBawaan === 'good' || $qtyFisik >= 10000) ? 'good' : (($levelBawaan === 'low' || ($qtyFisik < 10000 && $qtyFisik >= 1000)) ? 'low' : 'critical');

                            $dotColor = match($level) {
                                'good' => '#22c55e',
                                'low' => '#f59e0b',
                                default => '#ef4444'
                            };

                            $qtyColor = match($level) {
                                'good' => '#22c55e',
                                'low' => '#f59e0b',
                                default => '#ef4444'
                            };

                            $badgeBg = match($level) {
                                'good' => '#dcfce7',
                                'low' => '#fef3c7',
                                default => '#fee2e2'
                            };

                            $badgeColor = match($level) {
                                'good' => '#166534',
                                'low' => '#92400e',
                                default => '#991b1b'
                            };
                        @endphp
                        <tr style="border-bottom:1px solid #f3f4f6;">

                            <td style="padding:1rem;">
                                <div style="width:12px; height:12px; border-radius:999px; background:{{ $dotColor }};"></div>
                            </td>

                            <td style="padding:1rem;">
                                <div
                                    onclick="toggleSupplierRow({{ $uniqueId }})"
                                    style="
                                        font-weight:700;
                                        color:#0c1e35;
                                        cursor:pointer;
                                        display:flex;
                                        align-items:center;
                                        gap:8px;
                                    "
                                >
                                    <span id="arrow-{{ $uniqueId }}">▶</span>
                                    {{ $namaBahan }}
                                </div>
                                <div style="font-size:0.85rem; color:#64748b; margin-top:0.3rem;">
                                    {{ $level === 'good' ? 'Aman 7 Hari' : ($level === 'low' ? 'Stok Menipis' : 'Perlu Kelola Restock') }}
                                </div>
                            </td>

                            <td style="padding:1rem;">
                                <span style="background:#dbeafe; color:#1d4ed8; padding:0.35rem 0.7rem; border-radius:8px; font-size:0.8rem;">
                                    {{ $stock->bahanBaku->katalogPangan->kategori ?? '-' }}
                                </span>
                            </td>

                            <td style="padding:1rem;">
                                <div style="font-weight:700; color:{{ $qtyColor }}; margin-bottom:0.4rem;">
                                    {{ $totalQty }} {{ $stock->satuan }}
                                </div>
                                <span style="background:{{ $badgeBg }}; color:{{ $badgeColor }}; padding:0.3rem 0.6rem; border-radius:999px; font-size:0.75rem; font-weight:600;">
                                    {{ $level === 'good' ? 'Aman 7 Hari' : ($level === 'low' ? 'Stok Menipis' : 'Kritis') }}
                                </span>
                            </td>

                            <td style="padding:1rem;">
                                <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                                    <button
                                        dusk="history-btn-{{ $stock->id }}"
                                        onclick="openHistoryModal({{ $stock->id }})"
                                        style="background:#dbeafe; color:#1d4ed8; border:none; padding:0.5rem 0.9rem; border-radius:10px; font-weight:600; font-size:0.8rem; cursor:pointer;"
                                    >Riwayat</button>

                                    <form method="POST" action="{{ route('stocks.destroy', $stock->id) }}" onsubmit="return confirm('Hapus seluruh grup inventaris item ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" style="background:#fee2e2; color:#991b1b; border:none; padding:0.5rem 0.9rem; border-radius:10px; font-weight:600; font-size:0.8rem; cursor:pointer;">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>

                        </tr>

                        {{-- DROPDOWN BARIS SUPPLIER --}}
                        <tr
                            id="supplier-row-{{ $uniqueId }}"
                            style="display:none;"
                        >
                            <td colspan="5" style="padding:1rem 2rem; background:#f8fafc;">

                                <table style="width:100%; border-collapse:collapse;">
                                    <thead>
                                        <tr style="text-align:left; color:#6b7280; font-size:0.9rem; border-bottom: 2px solid #e2e8f0;">
                                            <th style="padding:0.5rem 0;">Supplier</th>
                                            <th style="padding:0.5rem 0;">Stok</th>
                                            <th style="padding:0.5rem 0;">Harga Terbaru</th>
                                            <th style="padding:0.5rem 0;">Aksi</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                    @foreach($group as $supplierStock)
                                        @php
                                            $isSupplierDeleted  = !$supplierStock->supplier;
                                            $isBahanBakuDeleted = $supplierStock->bahanBaku?->trashed() ?? true;
                                            $isRowCritical      = $isSupplierDeleted || $isBahanBakuDeleted;
                                        @endphp
                                        <tr style="border-bottom: 1px solid #edf2f7; {{ $isRowCritical ? 'background-color: #fee2e2;' : '' }}">

                                            <td style="padding:0.75rem 0; padding-left: 0.5rem; color:{{ $isRowCritical ? '#991b1b' : '#334155' }}; font-weight:600;">
                                                @if($isSupplierDeleted)
                                                    ⚠️ Supplier Telah Dihapus
                                                @elseif($isBahanBakuDeleted)
                                                    ⚠️ Bahan Baku Dihapus dari Supplier
                                                @else
                                                    {{ $supplierStock->supplier->nama_supplier }}
                                                @endif
                                            </td>

                                            <td style="padding:0.75rem 0; color:{{ $isRowCritical ? '#991b1b' : '#334155' }};">
                                                {{ $supplierStock->quantity }} {{ $supplierStock->satuan }}
                                            </td>

                                            <td style="padding:0.75rem 0; color:{{ $isRowCritical ? '#991b1b' : '#334155' }};">
                                                Rp {{ number_format($supplierStock->bahanBaku->harga_terbaru ?? 0,0,',','.') }}
                                            </td>

                                            <td style="padding:0.75rem 0; display:flex; gap:10px; align-items:center;">
                                                @if($isRowCritical)
                                                    <button style="background:#cbd5e1;color:#94a3b8;border:none;padding:8px 12px;border-radius:8px;font-weight:600;cursor:not-allowed;" disabled>+ Stok</button>
                                                    <button style="background:#cbd5e1;color:#94a3b8;border:none;padding:8px 12px;border-radius:8px;font-weight:600;cursor:not-allowed;" disabled>Sesuaikan</button>
                                                    
                                                    <form method="POST" action="{{ route('stocks.destroy', $supplierStock->id) }}" onsubmit="return confirm('Hapus baris catatan stok lama ini?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" style="background:#ef4444;color:white;border:none;padding:8px 12px;border-radius:8px;font-weight:600;cursor:pointer;">Hapus</button>
                                                    </form>
                                                @else
                                                    <button
                                                        onclick="openIncomingModal(
                                                            {{ $supplierStock->id }},
                                                            '{{ addslashes($supplierStock->bahanBaku->nama_bahan ?? '') }}',
                                                            {{ $supplierStock->bahanBaku->harga_terbaru ?? 0 }},
                                                            '{{ $supplierStock->satuan }}'
                                                        )"
                                                        style="background:#ff6b00;color:white;border:none;padding:8px 12px;border-radius:8px;font-weight:600;cursor:pointer;"
                                                    >+ Stok</button>

                                                    <button
                                                        onclick="openAdjustModal(
                                                            {{ $supplierStock->id }},
                                                            '{{ addslashes($supplierStock->bahanBaku->nama_bahan ?? '') }}',
                                                            {{ $supplierStock->quantity }},
                                                            '{{ $supplierStock->satuan }}'
                                                        )"
                                                        style="background:#f3f4f6;color:#374151;border:none;padding:8px 12px;border-radius:8px;font-weight:600;cursor:pointer;"
                                                    >Sesuaikan</button>
                                                @endif
                                            </td>

                                        </tr>
                                    @endforeach
                                    </tbody>

                                </table>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding:2rem; text-align:center; color:#9ca3af;">
                                Belum ada item. Klik "+ Tambah Item" untuk memulai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </main>
</div>


{{-- ═══════════════════════════════════════════ --}}
{{-- MODAL: TAMBAH ITEM                          --}}
{{-- ═══════════════════════════════════════════ --}}
<div id="addItemModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); justify-content:center; align-items:center; z-index:999;">
    <div style="background:white; width:460px; border-radius:24px; padding:2rem;">
        <h2 style="font-size:1.75rem; font-weight:800; color:#0c1e35; margin-bottom:1.75rem;">Tambah Item ke Stok</h2>

        <form action="{{ route('stocks.addItem') }}" method="POST">
            @csrf
            <div style="display:flex; flex-direction:column; gap:1rem;">

                <div>
                    <label style="display:block; margin-bottom:8px; font-weight:600;">Supplier</label>
                    <select
                        name="supplier_id"
                        id="modal_supplier_id"
                        required
                        onchange="filterBahanBakuBySupplier()"
                        style="width:100%; padding:12px; border:1px solid #d1d5db; border-radius:12px; margin-bottom:20px;"
                    >
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->nama_supplier }}</option>
                        @endforeach
                    </select>

                    <label style="font-size:0.85rem; color:#6b7280; font-weight:600; display:block; margin-bottom:0.4rem;">Bahan Baku</label>
                    <select 
                        name="bahan_baku_id" 
                        id="modal_bahan_baku_id" 
                        required 
                        disabled
                        style="width:100%; padding:1rem; border:1px solid #d1d5db; border-radius:12px; font-size:0.95rem;"
                    >
                        <option value="" data-supplier="">-- Pilih Supplier Dahulu --</option>
                        @foreach($bahanBakus as $bahan)
                            <option value="{{ $bahan->id }}" data-supplier="{{ $bahan->supplier_id }}">
                                {{ $bahan->nama_bahan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size:0.85rem; color:#6b7280; font-weight:600; display:block; margin-bottom:0.4rem;">Satuan</label>
                    <select name="satuan" required style="width:100%; padding:1rem; border:1px solid #d1d5db; border-radius:12px;">
                        <option value="">-- Pilih Satuan --</option>
                        <option value="gram">gram</option>
                        <option value="liter">liter</option>
                    </select>
                </div>

            </div>
            <div style="display:flex; gap:1rem; margin-top:2rem;">
                <button type="submit" style="flex:1; background:#0c1e35; color:white; border:none; padding:1rem; border-radius:14px; font-weight:700; cursor:pointer;">Tambah Item</button>
                <button type="button" onclick="closeAddItemModal()" style="flex:1; background:#e5e7eb; color:#111827; border:none; padding:1rem; border-radius:14px; font-weight:700; cursor:pointer;">Batal</button>
            </div>
        </form>
    </div>
</div>


{{-- ═══════════════════════════════════════════ --}}
{{-- MODAL: TAMBAH STOK MASUK                   --}}
{{-- ═══════════════════════════════════════════ --}}
<div id="incomingModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); justify-content:center; align-items:center; z-index:999;">
    <div style="background:white; width:460px; border-radius:24px; padding:2rem;">
        <h2 style="font-size:1.75rem; font-weight:800; color:#0c1e35; margin-bottom:1.75rem;">Tambah Stok Masuk</h2>

        <form id="incomingForm" method="POST">
            @csrf
            <div style="display:flex; flex-direction:column; gap:1rem;">

                <div id="incomingSelectWrapper">
                    <label style="font-size:0.85rem; color:#6b7280; font-weight:600; display:block; margin-bottom:0.4rem;">Item</label>
                    <select id="incomingSelectItem" style="width:100%; padding:1rem; border:1px solid #d1d5db; border-radius:12px;" onchange="onIncomingSelectChange(this)">
                        <option value="" data-harga-per-gram="0" data-satuan="">-- Pilih Item --</option>
                        @foreach($stokList as $s)
                            <option value="{{ $s->id }}" data-harga-per-gram="{{ $s->bahanBaku->harga_terbaru ?? 0 }}" data-satuan="{{ $s->satuan }}">
                                {{ $s->bahanBaku->nama_bahan ?? 'Bahan Terhapus' }} — {{ $s->supplier->nama_supplier ?? 'Supplier Terhapus' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="incomingLabelWrapper" style="display:none;">
                    <label style="font-size:0.85rem; color:#6b7280; font-weight:600; display:block; margin-bottom:0.4rem;">Item</label>
                    <div id="incomingItemLabel" style="padding:1rem; background:#f3f4f6; border-radius:12px; font-weight:600; color:#0c1e35;"></div>
                </div>

                <div>
                    <label style="font-size:0.85rem; color:#6b7280; font-weight:600; display:block; margin-bottom:0.4rem;">Jumlah yang Ditambahkan</label>
                    <input type="number" name="quantity" id="incomingQty" required min="0.01" step="0.01" oninput="calculateIncomingPrice()" placeholder="contoh: 50" style="width:100%; padding:1rem; border:1px solid #d1d5db; border-radius:12px;">
                </div>

                <div>
                    <label style="font-size:0.85rem; color:#6b7280; font-weight:600; display:block; margin-bottom:0.4rem;">Total Harga Belanja (Rp)</label>
                    <input type="number" name="total_harga" id="incomingTotal" required readonly min="0" placeholder="0" style="width:100%; padding:1rem; border:1px solid #d1d5db; border-radius:12px; background-color: #f3f4f6; color: #475569;">
                </div>

                <div>
                    <label style="font-size:0.85rem; color:#6b7280; font-weight:600; display:block; margin-bottom:0.4rem;">Tanggal Masuk</label>
                    <input type="date" name="incoming_date" required style="width:100%; padding:1rem; border:1px solid #d1d5db; border-radius:12px;">
                </div>

                <div>
                    <label style="font-size:0.85rem; color:#6b7280; font-weight:600; display:block; margin-bottom:0.4rem;">ID Batch <span style="color:#9ca3af; font-weight:400;">(opsional)</span></label>
                    <input type="text" name="batch_id" placeholder="contoh: BATCH-001" style="width:100%; padding:1rem; border:1px solid #d1d5db; border-radius:12px;">
                </div>

                <div>
                    <label style="font-size:0.85rem; color:#6b7280; font-weight:600; display:block; margin-bottom:0.4rem;">Tanggal Kedaluwarsa <span style="color:#9ca3af; font-weight:400;">(opsional)</span></label>
                    <input type="date" name="expired_date" style="width:100%; padding:1rem; border:1px solid #d1d5db; border-radius:12px;">
                </div>

            </div>

            <div style="display:flex; gap:1rem; margin-top:2rem;">
                <button type="submit" style="flex:1; background:#ff6b00; color:white; border:none; padding:1rem; border-radius:14px; font-weight:700; cursor:pointer;">Tambah Stok</button>
                <button type="button" onclick="closeIncomingModal()" style="flex:1; background:#e5e7eb; color:#111827; border:none; padding:1rem; border-radius:14px; font-weight:700; cursor:pointer;">Batal</button>
            </div>
        </form>
    </div>
</div>


{{-- ═══════════════════════════════════════════ --}}
{{-- MODAL: PENYESUAIAN STOK                     --}}
{{-- ═══════════════════════════════════════════ --}}
<div id="adjustModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); justify-content:center; align-items:center; z-index:999;">
    <div style="background:white; width:460px; border-radius:24px; padding:2rem;">

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.75rem;">
            <h2 style="font-size:1.75rem; font-weight:800; color:#0c1e35;">Sesuaikan Stok</h2>
            <button onclick="closeAdjustModal()" style="background:#e5e7eb; border:none; width:36px; height:36px; border-radius:999px; font-size:1rem; cursor:pointer;">✕</button>
        </div>

        <div id="adjustItemInfo" style="background:#f8fafc; border-radius:12px; padding:1rem; margin-bottom:1.5rem;">
            <div id="adjustItemName" style="font-weight:700; color:#0c1e35; font-size:1rem;"></div>
            <div id="adjustItemQty" style="color:#6b7280; font-size:0.85rem; margin-top:0.25rem;"></div>
        </div>

        <form id="adjustForm" method="POST">
            @csrf
            <div style="display:flex; flex-direction:column; gap:1rem;">

                <div>
                    <label style="font-size:0.85rem; color:#6b7280; font-weight:600; display:block; margin-bottom:0.4rem;">Tipe Koreksi</label>
                    <div style="display:flex; gap:0.75rem;">
                        <label style="flex:1; display:flex; align-items:center; gap:0.5rem; padding:0.9rem 1rem; border:2px solid #e5e7eb; border-radius:12px; cursor:pointer;" id="labelAdd">
                            <input type="radio" name="adjustment_type" value="add" onchange="onAdjustTypeChange()" style="accent-color:#22c55e;">
                            <span style="font-weight:600; color:#166534;">+ Tambah</span>
                        </label>
                        <label style="flex:1; display:flex; align-items:center; gap:0.5rem; padding:0.9rem 1rem; border:2px solid #e5e7eb; border-radius:12px; cursor:pointer;" id="labelSubtract">
                            <input type="radio" name="adjustment_type" value="subtract" onchange="onAdjustTypeChange()" style="accent-color:#ef4444;">
                            <span style="font-weight:600; color:#991b1b;">− Kurangi</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label style="font-size:0.85rem; color:#6b7280; font-weight:600; display:block; margin-bottom:0.4rem;">Jumlah</label>
                    <input type="number" name="adjustment_amount" required min="0.01" step="0.01" placeholder="contoh: 10" style="width:100%; padding:1rem; border:1px solid #d1d5db; border-radius:12px;">
                </div>

                <div>
                    <label style="font-size:0.85rem; color:#6b7280; font-weight:600; display:block; margin-bottom:0.4rem;">Alasan Koreksi</label>
                    <input type="text" name="reason" required placeholder="contoh: Barang rusak / Salah input sebelumnya" style="width:100%; padding:1rem; border:1px solid #d1d5db; border-radius:12px;">
                </div>

            </div>

            <div style="display:flex; gap:1rem; margin-top:2rem;">
                <button type="submit" id="adjustSubmitBtn" style="flex:1; background:#0c1e35; color:white; border:none; padding:1rem; border-radius:14px; font-weight:700; cursor:pointer;">Simpan Koreksi</button>
                <button type="button" onclick="closeAdjustModal()" style="flex:1; background:#e5e7eb; color:#111827; border:none; padding:1rem; border-radius:14px; font-weight:700; cursor:pointer;">Batal</button>
            </div>
        </form>
    </div>
</div>


{{-- ═══════════════════════════════════════════ --}}
{{-- MODAL: RIWAYAT                              --}}
{{-- ═══════════════════════════════════════════ --}}
<div id="historyModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); justify-content:center; align-items:center; z-index:999;">
    <div style="background:white; width:800px; max-width:95vw; border-radius:24px; padding:2rem; max-height:85vh; overflow-y:auto;">

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
            <div>
                <h2 id="historyTitle" style="font-size:1.75rem; font-weight:800; color:#0c1e35;">Riwayat Stok</h2>
                <p id="historySubtitle" style="color:#6b7280; margin-top:0.25rem; font-size:0.9rem;"></p>
            </div>
            <button onclick="closeHistoryModal()" style="background:#e5e7eb; border:none; width:36px; height:36px; border-radius:999px; font-size:1rem; cursor:pointer;">✕</button>
        </div>

        <div id="historyLoading" style="text-align:center; padding:2rem; color:#9ca3af;">Memuat...</div>

        <div id="historyTableWrapper" style="display:none;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid #e5e7eb; text-align:left;">
                        <th style="padding:0.75rem; font-size:0.85rem; color:#6b7280;">Status</th>
                        <th style="padding:0.75rem; font-size:0.85rem; color:#6b7280;">Supplier Asal</th>
                        <th style="padding:0.75rem; font-size:0.85rem; color:#6b7280;">Kuantitas</th>
                        <th style="padding:0.75rem; font-size:0.85rem; color:#6b7280;">Tanggal Masuk</th>
                        <th style="padding:0.75rem; font-size:0.85rem; color:#6b7280;">ID Batch / Alasan</th>
                        <th style="padding:0.75rem; font-size:0.85rem; color:#6b7280;">Tanggal Kedaluwarsa</th>
                    </tr>
                </thead>
                <tbody id="historyTableBody"></tbody>
            </table>
            <div id="historyEmpty" style="display:none; text-align:center; padding:2rem; color:#9ca3af;">
                Belum ada riwayat untuk item ini.
            </div>
        </div>

    </div>
</div>


{{-- ═══════════════════════════════════════════ --}}
{{-- JAVASCRIPT                                  --}}
{{-- ═══════════════════════════════════════════ --}}
<script>
    const BASE_URL = "{{ url('/dashboard/dapur/deliveries') }}";

    function openAddItemModal() {
        document.getElementById('addItemModal').style.display = 'flex';
    }
    function closeAddItemModal() {
        document.getElementById('addItemModal').style.display = 'none';
    }

    let currentIncomingHargaPerGram = 0;
    let currentIncomingSatuan = '';

    function filterBahanBakuBySupplier() {
        const supplierSelect = document.getElementById('modal_supplier_id');
        const bahanSelect = document.getElementById('modal_bahan_baku_id');
        const selectedSupplierId = supplierSelect.value;

        const options = bahanSelect.querySelectorAll('option');

        if (!selectedSupplierId) {
            bahanSelect.value = "";
            bahanSelect.disabled = true;
            options[0].textContent = "-- Pilih Supplier Dahulu --";
            return;
        }

        bahanSelect.disabled = false;
        options[0].textContent = "-- Pilih Bahan Baku --";

        let hasVisibleBahan = false;

        options.forEach((option, index) => {
            if (index === 0) return;

            const supplierIdBahan = option.getAttribute('data-supplier');

            if (supplierIdBahan === selectedSupplierId) {
                option.style.display = 'block';
                hasVisibleBahan = true;
            } else {
                option.style.display = 'none';
            }
        });

        bahanSelect.value = "";
        
        if (!hasVisibleBahan) {
            options[0].textContent = "-- Supplier ini tidak memiliki bahan baku tersedia --";
        }
    }

    function toggleSupplierRow(id)
    {
        const row = document.getElementById('supplier-row-' + id);
        const arrow = document.getElementById('arrow-' + id);

        if(row.style.display === 'none')
        {
            row.style.display = 'table-row';
            arrow.textContent = '▼';
        }
        else
        {
            row.style.display = 'none';
            arrow.textContent = '▶';
        }
    }

    function openIncomingModal(stockId, itemName, hargaPerGram, satuan) {
        const form          = document.getElementById('incomingForm');
        const selectWrapper = document.getElementById('incomingSelectWrapper');
        const labelWrapper  = document.getElementById('incomingLabelWrapper');
        const labelEl       = document.getElementById('incomingItemLabel');
        const selectEl      = document.getElementById('incomingSelectItem');

        form.reset();

        if (stockId) {
            form.action                 = `${BASE_URL}/${stockId}/incoming`;
            selectWrapper.style.display = 'none';
            labelWrapper.style.display  = 'block';
            labelEl.textContent         = itemName + ` (${satuan})`;
            selectEl.required           = false;
            currentIncomingHargaPerGram = parseFloat(hargaPerGram || 0);
            currentIncomingSatuan       = satuan;
        } else {
            form.action                 = '';
            selectWrapper.style.display = 'block';
            labelWrapper.style.display  = 'none';
            selectEl.required           = true;
            selectEl.value              = '';
            currentIncomingHargaPerGram = 0;
            currentIncomingSatuan       = '';
        }

        calculateIncomingPrice();
        document.getElementById('incomingModal').style.display = 'flex';
    }

    function onIncomingSelectChange(select) {
        if (select.value) {
            document.getElementById('incomingForm').action = `${BASE_URL}/${select.value}/incoming`;
            const selectedOption = select.options[select.selectedIndex];
            currentIncomingHargaPerGram = parseFloat(selectedOption?.getAttribute('data-harga-per-gram') || 0);
            currentIncomingSatuan       = selectedOption?.getAttribute('data-satuan') || '';
        } else {
            currentIncomingHargaPerGram = 0;
            currentIncomingSatuan       = '';
        }
        calculateIncomingPrice();
    }

    function closeIncomingModal() {
        document.getElementById('incomingModal').style.display = 'none';
    }

    function calculateIncomingPrice() {
        const qtyInput = document.getElementById('incomingQty');
        const totalInput = document.getElementById('incomingTotal');
        if (!qtyInput || !totalInput) return;
        
        const qty = parseFloat(qtyInput.value || 0);
        let calculatedTotal = 0;
        if (currentIncomingSatuan === 'kg' || currentIncomingSatuan === 'liter') {
            calculatedTotal = qty * 1000 * currentIncomingHargaPerGram;
        } else {
            calculatedTotal = qty * currentIncomingHargaPerGram;
        }
        totalInput.value = Math.round(calculatedTotal);
    }

    // Modal adjustment dan riwayat tetap sama
    function openAdjustModal(stockId, itemName, currentQty, satuan) {
        const form = document.getElementById('adjustForm');
        form.reset();
        form.action = `${BASE_URL}/${stockId}/adjust`;

        document.getElementById('adjustItemName').textContent = itemName;
        document.getElementById('adjustItemQty').textContent  = 'Stok saat ini: ' + currentQty + ' ' + satuan;
        document.getElementById('adjustSubmitBtn').style.background = '#0c1e35';

        document.getElementById('adjustModal').style.display = 'flex';
    }

    function onAdjustTypeChange() {
        const selected = document.querySelector('input[name="adjustment_type"]:checked');
        const btn      = document.getElementById('adjustSubmitBtn');
        if (selected && selected.value === 'subtract') {
            btn.style.background = '#ef4444';
        } else {
            btn.style.background = '#22c55e';
        }
    }

    function closeAdjustModal() {
        document.getElementById('adjustModal').style.display = 'none';
    }

    function openHistoryModal(stockId) {
        const modal        = document.getElementById('historyModal');
        const loading      = document.getElementById('historyLoading');
        const tableWrapper = document.getElementById('historyTableWrapper');
        const tbody        = document.getElementById('historyTableBody');
        const emptyEl      = document.getElementById('historyEmpty');

        loading.style.display      = 'block';
        loading.textContent        = 'Memuat...';
        tableWrapper.style.display = 'none';
        modal.style.display        = 'flex';

        fetch(`${BASE_URL}/${stockId}/history`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('historyTitle').textContent    = data.item;
            document.getElementById('historySubtitle').textContent = `${data.kategori} · Satuan: ${data.satuan}`;

            tbody.innerHTML = '';

            if (data.histories.length === 0) {
                emptyEl.style.display = 'block';
            } else {
                emptyEl.style.display = 'none';
                data.histories.forEach(h => {
                    let statusBg, statusColor;
                    if (h.status === 'incoming') {
                        statusBg    = '#dcfce7';
                        statusColor = '#166534';
                    } else if (h.status === 'adjustment') {
                        statusBg    = '#fef3c7';
                        statusColor = '#92400e';
                    } else {
                        statusBg    = '#f3f4f6';
                        statusColor = '#374151';
                    }

                    const qtyDisplay = h.status === 'adjustment'
                        ? (h.quantity > 0 ? '+' + h.quantity : h.quantity)
                        : h.quantity;

                    tbody.innerHTML += `
                        <tr style="border-bottom:1px solid #f3f4f6;">
                            <td style="padding:0.75rem;">
                                <span style="background:${statusBg}; color:${statusColor}; padding:0.3rem 0.6rem; border-radius:999px; font-size:0.8rem; font-weight:600;">
                                    ${h.status}
                                </span>
                            </td>
                            <td style="padding:0.75rem; color:#1e293b; font-weight:600;">${h.supplier_name}</td>
                            <td style="padding:0.75rem; font-weight:700; color:#0c1e35;">${qtyDisplay} ${data.satuan}</td>
                            <td style="padding:0.75rem; color:#374151;">${h.incoming_date}</td>
                            <td style="padding:0.75rem; color:#374151;">${h.batch_id}</td>
                            <td style="padding:0.75rem; color:#374151;">${h.expired_date}</td>
                        </tr>
                    `;
                });
            }

            loading.style.display      = 'none';
            tableWrapper.style.display = 'block';
        })
        .catch(() => {
            loading.textContent = 'Gagal memuat riwayat.';
        });
    }

    function closeHistoryModal() {
        document.getElementById('historyModal').style.display = 'none';
    }
</script>
@endsection