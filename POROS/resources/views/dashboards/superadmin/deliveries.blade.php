@extends('layouts.app')

@section('title', 'Logistics & Deliveries')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/users.css') }}">
<style>
    .delivery-status-select {
        padding: 0.4rem 0.6rem;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 700;
        cursor: pointer;
        border: 1px solid #e2e8f0;
        background-color: #f8fafc;
        color: #475569;
        outline: none;
        transition: 0.2s;
    }
    .delivery-status-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 2px rgba(var(--primary-rgb), 0.1);
    }
    .status-waiting { color: #64748b; background: #f1f5f9; }
    .status-way { color: #ea580c; background: #fff7ed; }
    .status-arrived { color: #15803d; background: #dcfce7; }
    
    .handover-btn {
        background: #ff6b00;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.85rem;
        border: none;
        cursor: pointer;
    }
</style>
@endsection

@section('content')
<div class="dashboard-layout">
    @include('partials.sidebar')

    <main class="main-content">
        @include('partials.header')

        <div class="planning-header">
            <div>
                <h1 style="font-size: 1.75rem; font-weight: 800; color: #0c1e35;">Logistics & Deliveries</h1>
                <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 0.25rem;">Monitor and update delivery status to schools</p>
            </div>
        </div>

        <!-- Table Section -->
        <div class="card" style="border: none; box-shadow: none; padding: 0; background: transparent;">
            @if(session('success'))
                <div class="success-alert" style="margin-bottom: 1rem; padding: 1rem; background: #dcfce7; color: #15803d; border-radius: 12px; display: flex; align-items: center; gap: 0.5rem; font-weight: 600;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    {{ session('success') }}
                </div>
            @endif

            <div class="search-container">
                <form action="{{ route('superadmin.deliveries.index') }}" method="GET" style="flex: 1; position: relative; display: flex; align-items: center;">
                    <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" name="search" class="search-input" placeholder="Search by school or courier..." value="{{ request('search') }}">
                </form>
                <form action="{{ route('superadmin.deliveries.index') }}" method="GET">
                    <select name="status" class="form-input" style="width: auto; padding: 0.65rem 2rem; font-size: 0.9rem;" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="Menunggu" {{ request('status') == 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                        <option value="Jalan" {{ request('status') == 'Jalan' ? 'selected' : '' }}>Jalan</option>
                        <option value="Sampai" {{ request('status') == 'Sampai' ? 'selected' : '' }}>Sampai</option>
                    </select>
                </form>
            </div>

            <table class="user-table">
                <thead>
                    <tr>
                        <th>Sekolah</th>
                        <th>Menu & Kurir</th>
                        <th>Waktu</th>
                        <th>Status Kirim</th>
                        <th>Penerima & Keterangan</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deliveries as $delivery)
                        <tr>
                            <td>
                                <div style="font-weight: 700; color: #0c1e35;">{{ $delivery->sekolah->nama_sekolah }}</div>
                                <div style="font-size: 0.75rem; color: #94a3b8;">{{ $delivery->sekolah->alamat }}</div>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #475569;">{{ $delivery->produksi->menu->nama_menu ?? 'N/A' }}</div>
                                <div style="font-size: 0.75rem; color: #64748b; display: flex; align-items: center; gap: 0.3rem;">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                    {{ $delivery->kurir->nama_kurir }} ({{ $delivery->kurir->no_plat }})
                                </div>
                            </td>
                            <td>
                                <div style="font-size: 0.85rem; color: #475569;">
                                    <strong>B:</strong> {{ $delivery->waktu_berangkat ? $delivery->waktu_berangkat->format('H:i') : '-' }}
                                </div>
                                <div style="font-size: 0.85rem; color: #475569;">
                                    <strong>S:</strong> {{ $delivery->waktu_sampai ? $delivery->waktu_sampai->format('H:i') : '-' }}
                                </div>
                            </td>
                            <td>
                                <form action="{{ route('superadmin.deliveries.updateStatus', $delivery->id) }}" method="POST">
                                    @csrf
                                    <select name="status_kirim" onchange="this.form.submit()" class="delivery-status-select @if($delivery->status_kirim == 'Menunggu') status-waiting @elseif($delivery->status_kirim == 'Jalan') status-way @else status-arrived @endif">
                                        <option value="Menunggu" {{ $delivery->status_kirim == 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                                        <option value="Jalan" {{ $delivery->status_kirim == 'Jalan' ? 'selected' : '' }}>Jalan</option>
                                        <option value="Sampai" {{ $delivery->status_kirim == 'Sampai' ? 'selected' : '' }}>Sampai</option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                @if($delivery->nama_penerima)
                                    <div style="font-weight: 700; color: #0c1e35;">{{ $delivery->nama_penerima }}</div>
                                    <div style="font-size: 0.75rem; color: #ea580c; font-weight: 600;">{{ $delivery->keterangan }}</div>
                                    @if($delivery->ompreng_kembali !== null)
                                        <div style="font-size: 0.75rem; color: #64748b;">Ompreng kembali: {{ $delivery->ompreng_kembali }}</div>
                                    @endif
                                @else
                                    <span style="color: #94a3b8; font-size: 0.85rem;">Belum diterima</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <button onclick="openHandoverModal({{ $delivery->id }}, '{{ addslashes($delivery->sekolah->nama_sekolah) }}')" class="handover-btn">
                                    Input Penerima
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem; color: #94a3b8;">No deliveries found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div style="margin-top: 1.5rem;">
                {{ $deliveries->links() }}
            </div>
        </div>
    </main>
</div>

<!-- Handover Modal -->
<div id="handoverModal" class="modal-form-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div class="modal-form-box" style="background: white; border-radius: 20px; padding: 2rem; width: 500px; max-width: 92%; box-shadow: 0 25px 50px rgba(0,0,0,0.15); max-height: 90vh; overflow-y: auto;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
            <div>
                <h3 style="font-size: 1.2rem; font-weight: 800; color: #0c1e35;">Bukti Serah Terima</h3>
                <p id="handover_school_name" style="font-size: 0.85rem; color: #64748b; margin-top: 0.1rem;"></p>
            </div>
            <span onclick="closeModal('handoverModal')" style="cursor:pointer;font-size:1.4rem;color:#6b7280;">&times;</span>
        </div>
        <form id="handoverForm" method="POST">
            @csrf
            <div style="margin-bottom:1rem;">
                <label style="display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.5rem; color: #374151;">Nama Penerima</label>
                <input type="text" name="nama_penerima" class="form-input" required placeholder="Masukkan nama petugas sekolah" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 10px;">
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.5rem; color: #374151;">Ompreng Kembali</label>
                    <input type="number" name="ompreng_kembali" class="form-input" placeholder="0" min="0" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 10px;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.5rem; color: #374151;">Keterangan Feedback</label>
                    <select name="keterangan" class="form-input" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 10px;">
                        <option value="" disabled selected>Pilih keterangan...</option>
                        <option value="rasa tidak enak">Rasa tidak enak</option>
                        <option value="porsi kebanyakan">Porsi kebanyakan</option>
                        <option value="menu ga menarik">Menu ga menarik</option>
                        <option value="siswa sedang sakit">Siswa sedang sakit</option>
                        <option value="kurang matang">Kurang matang</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom:1.5rem;">
                <label style="display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.5rem; color: #374151;">Detail Menu Tersisa</label>
                <div style="display: flex; flex-direction: column; gap: 0.8rem;">
                    <div>
                        <input type="text" name="menu_tersisa" class="form-input" placeholder="Nama Menu Tersisa" style="width: 100%; padding: 0.7rem; border: 1px solid #d1d5db; border-radius: 10px;">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 600; margin-bottom: 0.3rem; color: #6b7280;">Sisa (Ompreng)</label>
                            <input type="number" name="jumlah_sisa_ompreng" class="form-input" placeholder="0" min="0" style="width: 100%; padding: 0.7rem; border: 1px solid #d1d5db; border-radius: 10px;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 600; margin-bottom: 0.3rem; color: #6b7280;">Tanggal</label>
                            <input type="date" name="tanggal_sisa" class="form-input" style="width: 100%; padding: 0.7rem; border: 1px solid #d1d5db; border-radius: 10px;">
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%; padding: 0.85rem; border-radius: 12px; font-weight: 800;">Simpan Bukti Terima</button>
        </form>
    </div>
</div>

<script>
    function openHandoverModal(id, schoolName) {
        document.getElementById('handoverForm').action = '/dashboard/superadmin/deliveries/' + id + '/handover';
        document.getElementById('handover_school_name').textContent = schoolName;
        document.getElementById('handoverModal').style.display = 'flex';
    }

    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }
    
    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target.classList.contains('modal-form-overlay')) {
            event.target.style.display = 'none';
        }
    }
</script>
@endsection
