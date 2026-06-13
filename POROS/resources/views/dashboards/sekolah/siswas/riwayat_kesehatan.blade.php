@extends('layouts.app')

@section('title', 'Riwayat Kesehatan')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/users.css') }}">
<style>
    .avatar { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; margin-right: 12px; font-size: 0.9rem; }
    .status-gizi-pill { padding: 0.25rem 0.6rem; border-radius: 12px; font-size: 0.75rem; font-weight: 700; display: inline-block; }
    .gizi-normal { background: #dcfce7; color: #15803d; }
    .gizi-kurus { background: #fef3c7; color: #d97706; }
    .gizi-gemuk { background: #ffedd5; color: #ea580c; }
    .gizi-obesitas { background: #fee2e2; color: #ef4444; }
    .filter-card { background: white; border-radius: 16px; padding: 1.25rem; margin-bottom: 1.5rem; border: 1px solid #e2e8f0; }
    .filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: flex-end; }
    .form-group { display: flex; flex-direction: column; gap: 0.35rem; }
    .form-group label { font-size: 0.8rem; font-weight: 600; color: #475569; }
    .filter-input { padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.875rem; color: #334155; }
    .filter-btn-group { display: flex; gap: 0.5rem; justify-content: flex-end; }
</style>
@endsection

@section('content')
<div class="dashboard-layout">
    @include('partials.sidebar')

    <main class="main-content">
        @include('partials.header')

        <div class="planning-header">
            <div>
                <h1 style="font-size: 1.75rem; font-weight: 800; color: #0c1e35;">Riwayat Kesehatan</h1>
                <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 0.25rem;">Pantau histori pertumbuhan fisik dan antropometri siswa</p>
            </div>
            <div style="display: flex; gap: 0.75rem;">
                <button onclick="document.getElementById('importAntropometriModal').style.display = 'flex'" class="btn" style="width: auto; padding: 0.75rem 1.5rem; display: flex; align-items: center; gap: 0.5rem; border-radius: 12px; cursor: pointer; background: #f1f5f9; border: 1px solid #e2e8f0; color: #475569; font-weight: 600; transition: all 0.2s;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    Import BB/TB (CSV)
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

            <div class="filter-card">
                <form action="{{ route('sekolah.riwayat-kesehatan.index') }}" method="GET">
                    <div class="filter-grid">
                        <div class="form-group">
                            <label>Cari Siswa</label>
                            <input type="text" name="search" class="filter-input" placeholder="Nama atau NISN..." value="{{ request('search') }}">
                        </div>
                        <div class="form-group">
                            <label>Kelas</label>
                            <select name="kelas" class="filter-input">
                                <option value="">Semua Kelas</option>
                                @foreach($classes as $c)
                                    <option value="{{ $c }}" {{ request('kelas') == $c ? 'selected' : '' }}>Kelas {{ $c }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Dari Tanggal</label>
                            <input type="date" name="tanggal_mulai" class="filter-input" value="{{ request('tanggal_mulai') }}">
                        </div>
                        <div class="form-group">
                            <label>Sampai Tanggal</label>
                            <input type="date" name="tanggal_selesai" class="filter-input" value="{{ request('tanggal_selesai') }}">
                        </div>
                        <div class="filter-btn-group" style="grid-column: span 1; justify-content: flex-start;">
                            <button type="submit" class="btn btn-primary" style="padding: 0.6rem 1.25rem; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 0.85rem;">Filter</button>
                            <a href="{{ route('sekolah.riwayat-kesehatan.index') }}" class="btn" style="padding: 0.6rem 1.25rem; border-radius: 8px; font-weight: 600; background: #f1f5f9; border: 1px solid #e2e8f0; color: #475569; text-decoration: none; display: inline-flex; align-items: center; font-size: 0.85rem;">Reset</a>
                        </div>
                    </div>
                </form>
            </div>

            <table class="user-table" id="riwayatTable">
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" id="selectAllRiwayat" title="Pilih Semua" style="width: 16px; height: 16px; cursor: pointer; accent-color: #ff6b00;">
                        </th>
                        <th style="width: 220px;">Nama Siswa</th>
                        <th>NISN & Kelas</th>
                        <th>Berat Badan</th>
                        <th>Tinggi Badan</th>
                        <th>IMT (BMI)</th>
                        <th>Status Gizi</th>
                        <th>Tanggal Pengukuran</th>
                        <th style="text-align: right; width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $item)
                        <tr>
                            <td>
                                <input type="checkbox" class="riwayat-checkbox" value="{{ $item->id }}" style="width: 16px; height: 16px; cursor: pointer; accent-color: #ff6b00;">
                            </td>
                            <td>
                                <div style="display: flex; align-items: center;">
                                    @php
                                        $nama_bersih = $item->siswa->nama_siswa ?? 'Unknown';
                                        $initials = collect(explode(' ', $nama_bersih))->map(fn($n) => substr($n, 0, 1))->take(2)->join('');
                                    @endphp
                                    <div class="avatar" style="background: #10b981;">{{ strtoupper($initials) }}</div>
                                    <div style="font-weight: 700; color: #0c1e35;">{{ $nama_bersih }}</div>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #475569;">{{ $item->siswa->nisn ?? '-' }}</div>
                                <div style="font-size: 0.75rem; color: #94a3b8;">Kelas {{ $item->siswa->kelas ?? '-' }}</div>
                            </td>
                            <td style="font-weight: 600; color: #334155;">{{ $item->berat_badan }} kg</td>
                            <td style="font-weight: 600; color: #334155;">{{ $item->tinggi_badan }} cm</td>
                            <td style="font-weight: 600; color: #334155;">{{ $item->imt ?? '-' }}</td>
                            <td>
                                @php
                                    $status = $item->status_gizi ?? 'Normal';
                                    $badgeClass = 'gizi-normal';
                                    if ($status === 'Kurus' || $status === 'Under Weight') $badgeClass = 'gizi-kurus';
                                    elseif ($status === 'Gemuk' || $status === 'Overweight') $badgeClass = 'gizi-gemuk';
                                    elseif ($status === 'Obesitas' || $status === 'Obese') $badgeClass = 'gizi-obesitas';
                                @endphp
                                <span class="status-gizi-pill {{ $badgeClass }}">
                                    {{ $status }}
                                </span>
                            </td>
                            <td style="color: #64748b; font-weight: 500;">
                                {{ \Carbon\Carbon::parse($item->tanggal_ukur)->format('d M Y') }}
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                    <button onclick="openDeleteModal({{ $item->id }}, '{{ addslashes($nama_bersih) }}', '{{ \Carbon\Carbon::parse($item->tanggal_ukur)->format('d M Y') }}')" class="btn" style="padding: 0.5rem; background: #fee2e2; color: #ef4444; border: none; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center;" title="Hapus Catatan">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; color: #94a3b8; padding: 3rem 1rem;">
                                Tidak ada data riwayat kesehatan siswa ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div style="margin-top: 1.5rem;">
                {{ $riwayat->links() }}
            </div>
        </div>
    </main>
</div>

<div id="bulkBarRiwayat" style="display: none; position: fixed; bottom: 2rem; left: 50%; transform: translateX(-50%); z-index: 3000; background: #0c1e35; color: white; border-radius: 16px; padding: 1rem 1.5rem; align-items: center; gap: 1.5rem; box-shadow: 0 8px 32px rgba(0,0,0,0.25); min-width: 400px;">
    <span id="bulkCountRiwayat" style="font-weight: 700; font-size: 0.95rem;">0 data dipilih</span>
    <div style="display: flex; gap: 0.75rem; margin-left: auto;">
        <button onclick="clearSelectionRiwayat()" style="padding: 0.5rem 1rem; border-radius: 8px; border: 1px solid rgba(255,255,255,0.2); background: transparent; color: white; cursor: pointer; font-weight: 600; font-size: 0.85rem;">Batal</button>
        <button onclick="submitBulkDeleteRiwayat()" style="padding: 0.5rem 1rem; border-radius: 8px; background: #ef4444; color: white; border: none; cursor: pointer; font-weight: 700; font-size: 0.85rem; display: flex; align-items: center; gap: 0.5rem;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
            Hapus Terpilih
        </button>
    </div>
</div>

<form id="bulkDeleteRiwayatForm" method="POST" action="{{ route('sekolah.riwayat-kesehatan.bulk-destroy') }}" style="display: none;">
    @csrf
    @method('DELETE')
    <div id="bulkDeleteRiwayatIds"></div>
</form>

<div id="importAntropometriModal" class="modal-form-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 2000; justify-content: center; align-items: center;">
    <div class="modal-form-content" style="background: white; border-radius: 20px; padding: 2rem; width: 500px; max-width: 90%; max-height: 90vh; overflow-y: auto; position: relative;">
        <span onclick="closeModal('importAntropometriModal')" class="close-btn" style="position: absolute; right: 20px; top: 20px; font-size: 1.5rem; cursor: pointer; color: #94a3b8;">&times;</span>
        <h3 style="margin-bottom: 0.5rem; color: #0c1e35;">Import Hasil Timbangan BB/TB (CSV)</h3>
        <p style="color: #64748b; font-size: 0.875rem; margin-bottom: 1.5rem;">Unggah file CSV berisi data berat badan dan tinggi badan siswa. Format kolom harus berupa: <strong>nisn, berat_badan, tinggi_badan, tanggal_ukur</strong>.</p>
        
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1rem; margin-bottom: 1.5rem;">
            <div style="font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em;">Contoh Struktur CSV:</div>
            <pre style="font-family: monospace; font-size: 0.8rem; background: #e2e8f0; padding: 0.5rem; border-radius: 6px; overflow-x: auto; color: #334155; margin: 0;">nisn,berat_badan,tinggi_badan,tanggal_ukur
1234567890,45.5,150.0,2026-05-22
0987654321,50.2,155.4,2026-05-22</pre>

            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px dashed #cbd5e1; display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 0.8rem; color: #64748b;">Gunakan template yang sudah kami sediakan:</span>
                <a href="https://drive.google.com/drive/folders/1firj1YAQLQHm6ywuLLVo_GPjZfhkjGbp?usp=sharing" target="_blank" rel="noopener noreferrer" style="font-size: 0.8rem; font-weight: 700; color: #ff6b00; text-decoration: none; display: flex; align-items: center; gap: 0.4rem; padding: 0.4rem 0.8rem; background: #fff5ed; border-radius: 6px; transition: background 0.3s;" onmouseover="this.style.background='#ffede0'" onmouseout="this.style.background='#fff5ed'">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    Unduh Template
                </a>
            </div>
            </div>

        <form action="{{ route('sekolah.riwayat-kesehatan.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; color: #334155; margin-bottom: 0.5rem;">Pilih File CSV</label>
                <input type="file" name="file_csv" accept=".csv,.txt" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 10px; background: #f8fafc; cursor: pointer;">
            </div>
            
            <div style="display: flex; gap: 0.75rem; justify-content: flex-end; margin-top: 2rem;">
                <button type="button" onclick="closeModal('importAntropometriModal')" style="padding: 0.75rem 1.5rem; border-radius: 10px; border: 1px solid #d2d6dc; background: white; cursor: pointer; font-weight: 600; color: #475569;">Batal</button>
                <button type="submit" class="btn btn-primary" style="width: auto; padding: 0.75rem 1.5rem; border-radius: 10px; cursor: pointer; font-weight: 600;">Unggah & Import</button>
            </div>
        </form>
    </div>
</div>

<div id="deleteModal" class="confirm-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 2000; justify-content: center; align-items: center;">
    <div class="confirm-box" style="background: white; border-radius: 20px; padding: 2rem; width: 380px; text-align: center;">
        <h4 style="margin-bottom: 0.5rem; color: #0c1e35;">Hapus Catatan Pengukuran?</h4>
        <p id="deleteConfirmText" style="margin-bottom: 1.5rem; font-size: 0.9rem; color: #64748b;"></p>
        <div style="display: flex; gap: 0.75rem;">
            <button type="button" onclick="closeModal('deleteModal')" style="flex: 1; padding: 0.7rem; border-radius: 10px; border: 1px solid #d2d6dc; background: white; cursor: pointer; font-weight: 600; color: #475569;">Batal</button>
            <form id="deleteAntropometriForm" method="POST" style="flex:1;">
                @csrf
                @method('DELETE')
                <button type="submit" style="width:100%; padding: 0.7rem; background: #ef4444; color: white; border: none; border-radius: 10px; cursor: pointer; font-weight: 600;">Ya, Hapus</button>
            </form>
        </div>
    </div>
</div>

<script>
    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    function openDeleteModal(id, nama, tanggal) {
        document.getElementById('deleteConfirmText').innerHTML = 'Pengukuran siswa <strong>' + (nama || 'Siswa') + '</strong> pada tanggal <strong>' + tanggal + '</strong> akan dihapus.';
        document.getElementById('deleteAntropometriForm').action = '/dashboard/sekolah/riwayat-kesehatan/' + id;
        document.getElementById('deleteModal').style.display = 'flex';
    }

    window.onclick = function(event) {
        if (event.target.classList.contains('modal-form-overlay') || event.target.classList.contains('confirm-overlay')) {
            event.target.style.display = 'none';
        }
    }

    // ── Bulk Delete Riwayat ──
    const selectAllRiwayat = document.getElementById('selectAllRiwayat');
    const bulkBarRiwayat   = document.getElementById('bulkBarRiwayat');
    const bulkCountRiwayat = document.getElementById('bulkCountRiwayat');

    function getCheckedRiwayat() {
        return [...document.querySelectorAll('.riwayat-checkbox:checked')];
    }

    function updateBulkBarRiwayat() {
        const checked = getCheckedRiwayat();
        if (checked.length > 0) {
            bulkBarRiwayat.style.display = 'flex';
            bulkCountRiwayat.textContent = checked.length + ' data dipilih';
        } else {
            bulkBarRiwayat.style.display = 'none';
        }
        const all = document.querySelectorAll('.riwayat-checkbox');
        if(selectAllRiwayat) {
            selectAllRiwayat.checked = all.length > 0 && checked.length === all.length;
            selectAllRiwayat.indeterminate = checked.length > 0 && checked.length < all.length;
        }
    }

    if(selectAllRiwayat) {
        selectAllRiwayat.addEventListener('change', function () {
            document.querySelectorAll('.riwayat-checkbox').forEach(cb => cb.checked = this.checked);
            updateBulkBarRiwayat();
        });
    }

    document.querySelectorAll('.riwayat-checkbox').forEach(cb => {
        cb.addEventListener('change', updateBulkBarRiwayat);
    });

    function clearSelectionRiwayat() {
        document.querySelectorAll('.riwayat-checkbox').forEach(cb => cb.checked = false);
        if(selectAllRiwayat) {
            selectAllRiwayat.checked = false;
            selectAllRiwayat.indeterminate = false;
        }
        bulkBarRiwayat.style.display = 'none';
    }

    function submitBulkDeleteRiwayat() {
        const ids = getCheckedRiwayat().map(cb => cb.value);
        if (ids.length === 0) return;
        if (!confirm(ids.length + ' data riwayat kesehatan akan dihapus. Lanjutkan?')) return;

        const container = document.getElementById('bulkDeleteRiwayatIds');
        container.innerHTML = '';
        ids.forEach(id => {
            const input = document.createElement('input');
            input.type  = 'hidden';
            input.name  = 'ids[]';
            input.value = id;
            container.appendChild(input);
        });
        document.getElementById('bulkDeleteRiwayatForm').submit();
    }
</script>
@endsection