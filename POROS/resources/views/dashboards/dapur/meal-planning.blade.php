@extends('layouts.app')

@section('title', 'Meal Planning')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/meal-planning.css') }}">
@endsection

@section('content')
<div class="dashboard-layout">
    @include('partials.sidebar')

    <main class="main-content">
        @include('partials.header')

        <div class="planning-header">
            <div>
                <h1 style="font-size: 1.75rem; font-weight: 800; color: #0c1e35;">Meal Planning</h1>
                <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 0.25rem;">Rencanakan menu mingguan dan kelola resep secara cerdas.</p>
            </div>
        </div>

        {{-- ═══ KALENDER MINGGUAN ═══ --}}
        <section class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="font-weight: 700; color: #0c1e35;">Kalender Menu Mingguan</h3>
                <div style="font-size: 0.85rem; color: #0c1e35; font-weight: 600; display:flex; align-items:center; gap:0.75rem;">
                    <a href="{{ route('dashboard.meal_planning', ['week' => $weekOffset - 1]) }}" style="text-decoration:none; color: var(--primary); font-size:1.1rem; cursor:pointer;">&larr;</a>
                    <span>{{ $startOfWeek->translatedFormat('d F Y') }}</span>
                    <a href="{{ route('dashboard.meal_planning', ['week' => $weekOffset + 1]) }}" style="text-decoration:none; color: var(--primary); font-size:1.1rem; cursor:pointer;">&rarr;</a>
                </div>
            </div>
            <div class="week-grid">
                @for($i = 0; $i < 7; $i++)
                    @php
                        $d = $startOfWeek->copy()->addDays($i);
                        $key = $d->format('Y-m-d');
                        $dayItems = $schedules->get($key) ?? collect();
                    @endphp
                    <div class="day-card {{ $dayItems->count() ? 'has-menu' : '' }}">
                        <div class="day-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                        </div>
                        <div class="day-name">{{ $d->translatedFormat('l') }}</div>
                        <div class="day-date">{{ $d->translatedFormat('M d') }}</div>

                        @if($dayItems->count())
                            @foreach($dayItems as $sch)
                            @if($sch->menu)
                            <div style="margin-bottom: 0.4rem;">
                                <div class="menu-name">{{ $sch->menu->nama_menu }}</div>
                                <div class="menu-portions">{{ $sch->total_target_porsi }} porsi</div>
                                <div style="font-size:0.75rem; color:#059669; font-weight:700;">
                                    Modal: Rp {{ number_format($sch->harga_total_modal, 0, ',', '.') }}
                                </div>
                                @php
                                    $badgeBg = '#fef3c7'; $badgeColor = '#d97706';
                                    if ($sch->status_produksi === 'Memasak') {
                                        $badgeBg = '#ffedd5'; $badgeColor = '#ea580c';
                                    } elseif ($sch->status_produksi === 'Siap Kirim') {
                                        $badgeBg = '#d1fae5'; $badgeColor = '#059669';
                                    }
                                @endphp
                                <div style="display:inline-block; font-size:0.65rem; font-weight:700; padding:0.15rem 0.4rem; border-radius:6px; margin-top:0.25rem; background:{{ $badgeBg }}; color:{{ $badgeColor }}; text-transform:uppercase; letter-spacing:0.5px;">
                                    {{ $sch->status_produksi }}
                                </div>
                            </div>
                            @endif
                            @endforeach
                            @php
                                $firstSch = $dayItems->first();
                                if ($firstSch->menu) {
                                    $totalSerat = 0;
                                    $totalKalsium = 0;
                                    $totalBesi = 0;
                                    foreach($firstSch->menu->reseps as $r) {
                                        $totalSerat += ($r->bahanBaku->serat_per_gram ?? 0) * $r->gramasi_per_porsi;
                                        $totalKalsium += ($r->bahanBaku->kalsium_per_gram ?? 0) * $r->gramasi_per_porsi;
                                        $totalBesi += ($r->bahanBaku->besi_per_gram ?? 0) * $r->gramasi_per_porsi;
                                    }

                                    $viewData = json_encode([
                                        'id' => $firstSch->id,
                                        'status_produksi' => $firstSch->status_produksi,
                                        'menu_name' => $firstSch->menu->nama_menu,
                                        'porsi' => $firstSch->total_target_porsi,
                                        'kalori' => $firstSch->menu->total_kalori,
                                        'protein' => $firstSch->menu->total_protein,
                                        'karbohidrat' => $firstSch->menu->total_karbohidrat,
                                        'lemak' => $firstSch->menu->total_lemak,
                                        'serat' => $totalSerat,
                                        'kalsium' => $totalKalsium,
                                        'besi' => $totalBesi,
                                        'modal_per_porsi' => $firstSch->menu->harga_modal_per_porsi,
                                        'total_modal' => $firstSch->harga_total_modal,
                                        'ingredients' => $firstSch->menu->reseps->map(function($r) {
                                            return [
                                                'nama' => $r->bahanBaku ? ($r->bahanBaku->trashed() ? $r->bahanBaku->nama_bahan . ' (Terhapus)' : ($r->bahanBaku->stok <= 0 ? $r->bahanBaku->nama_bahan . ' (Habis)' : $r->bahanBaku->nama_bahan)) : 'Bahan Tidak Valid',
                                                'gram' => $r->gramasi_per_porsi,
                                                'harga_per_gram' => $r->bahanBaku->harga_terbaru ?? 0,
                                                'stok_tersedia' => $r->bahanBaku ? $r->bahanBaku->stok : 0
                                            ];
                                        })->values()->toArray(),
                                    ]);
                                }
                            @endphp
                            <div class="day-actions">
                                @if($firstSch->menu)
                                <button type="button" class="btn-view" onclick='openViewScheduleModal({!! htmlspecialchars($viewData, ENT_QUOTES, "UTF-8") !!})'>👁 View</button>
                                @endif
                                @if($firstSch->status_produksi === 'Menunggu')
                                    <button type="button" class="btn-edit" onclick="openEditScheduleModal('{{ $firstSch->id }}', '{{ $key }}', '{{ $firstSch->menu_id }}', '{{ $firstSch->total_target_porsi }}')">✏️ Edit</button>
                                    <form action="{{ route('schedule.destroy', $firstSch->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus jadwal ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-del">🗑️ Hapus</button>
                                    </form>
                                @else
                                    <button type="button" class="btn-edit" style="text-decoration: line-through; opacity: 0.5; cursor: not-allowed;" disabled>✏️ Edit</button>
                                    <button type="button" class="btn-del" style="text-decoration: line-through; opacity: 0.5; cursor: not-allowed;" disabled>🗑️ Hapus</button>
                                @endif
                            </div>
                        @else
                            <button class="add-menu-link" onclick="openScheduleModal('{{ $key }}')">+ Add Menu</button>
                        @endif
                    </div>
                @endfor
            </div>
        </section>

        {{-- ═══ MENU LIBRARY ═══ --}}
        <section style="margin-top: 0.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="font-weight: 700; color: #0c1e35; font-size: 1.25rem;">Menu Library</h3>
                <button class="btn-outline" onclick="openMenuModal()">+ Add New Menu</button>
            </div>

            <div class="menu-lib-grid">
                @foreach($menus as $menu)
                @php
                    $beratPerPorsi = $menu->reseps->sum('gramasi_per_porsi');
                @endphp
                <div class="menu-lib-card">
                    <div class="food-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 13.87A4 4 0 0 1 7.41 6a5.11 5.11 0 0 1 1.05-1.54 5 5 0 0 1 7.08 0A5.11 5.11 0 0 1 16.59 6 4 4 0 0 1 18 13.87V21H6Z"/><line x1="6" y1="17" x2="18" y2="17"/></svg>
                    </div>
                    <div class="menu-title">{{ $menu->nama_menu }}</div>
                    <div class="menu-kcal">{{ round($menu->total_kalori) }} kcal</div>
                    <div style="font-size:0.85rem; color:#059669; font-weight:700; text-align:center; margin-top:0.25rem;">
                        Modal: Rp {{ number_format($menu->harga_modal_per_porsi, 0, ',', '.') }} / porsi
                    </div>

                    <div class="nutrient-grid">
                        <div class="nutrient-box protein">
                            <div class="n-label">Protein</div>
                            <div class="n-value">{{ number_format($menu->total_protein, 0) }}g</div>
                        </div>
                        <div class="nutrient-box carbs">
                            <div class="n-label">Carbs</div>
                            <div class="n-value">{{ number_format($menu->total_karbohidrat, 0) }}g</div>
                        </div>
                        <div class="nutrient-box fat">
                            <div class="n-label">Fat</div>
                            <div class="n-value">{{ number_format($menu->total_lemak, 0) }}g</div>
                        </div>
                    </div>

                    <div class="portion-info">
                        <div class="p-row"><span>Berat / 1 porsi</span><span>{{ number_format($beratPerPorsi, 0) }} g/ml</span></div>
                        @foreach($menu->reseps as $r)
                        <div class="p-row" style="font-size:0.75rem;">
                            <span>
                                &nbsp;&bull; 
                                @if(!$r->bahanBaku)
                                    <span style="text-decoration: line-through; color: #9ca3af;">Bahan Tidak Valid</span>
                                    <span style="color: #ef4444; font-weight: 700; margin-left: 2px;">(Terhapus)</span>
                                @elseif($r->bahanBaku->trashed())
                                    <span style="text-decoration: line-through; color: #9ca3af;">{{ $r->bahanBaku->nama_bahan }}</span>
                                    <span style="color: #ef4444; font-weight: 700; margin-left: 2px;">(Terhapus)</span>
                                @elseif($r->bahanBaku->stok <= 0)
                                    <span style="text-decoration: line-through; color: #9ca3af;">{{ $r->bahanBaku->nama_bahan }}</span>
                                    <span style="color: #f59e0b; font-weight: 700; margin-left: 2px;">(Habis)</span>
                                @else
                                    {{ $r->bahanBaku->nama_bahan }}
                                @endif
                            </span>
                            <span>{{ number_format($r->gramasi_per_porsi, 0) }} g/ml</span>
                        </div>
                        @endforeach
                    </div>

                    @php
                        $totalSerat = 0;
                        $totalKalsium = 0;
                        $totalBesi = 0;
                        foreach($menu->reseps as $r) {
                            $totalSerat += ($r->bahanBaku->serat_per_gram ?? 0) * $r->gramasi_per_porsi;
                            $totalKalsium += ($r->bahanBaku->kalsium_per_gram ?? 0) * $r->gramasi_per_porsi;
                            $totalBesi += ($r->bahanBaku->besi_per_gram ?? 0) * $r->gramasi_per_porsi;
                        }

                        $menuData = [
                            "nama_menu" => $menu->nama_menu,
                            "kalori" => $menu->total_kalori,
                            "protein" => $menu->total_protein,
                            "karbohidrat" => $menu->total_karbohidrat,
                            "lemak" => $menu->total_lemak,
                            "serat" => $totalSerat,
                            "kalsium" => $totalKalsium,
                            "besi" => $totalBesi,
                            "modal_per_porsi" => $menu->harga_modal_per_porsi,
                            "ingredients" => $menu->reseps->map(function($r) {
                                return [
                                    "nama" => $r->bahanBaku ? ($r->bahanBaku->trashed() ? $r->bahanBaku->nama_bahan . ' (Terhapus)' : ($r->bahanBaku->stok <= 0 ? $r->bahanBaku->nama_bahan . ' (Habis)' : $r->bahanBaku->nama_bahan)) : 'Bahan Tidak Valid',
                                    "gram" => $r->gramasi_per_porsi,
                                    "harga_per_gram" => $r->bahanBaku->harga_terbaru ?? 0,
                                    "subtotal" => $r->gramasi_per_porsi * ($r->bahanBaku->harga_terbaru ?? 0)
                                ];
                            })->values()->toArray()
                        ];
                    @endphp

                    <div class="menu-actions">
                        <button type="button" class="btn-view-menu" style="background:var(--primary); color:#ffffff; padding:0.4rem 0.6rem; border-radius:6px; border:none; cursor:pointer; display:flex; align-items:center; gap:0.25rem; font-size:0.75rem; font-weight:600;" onclick='openViewMenuLibraryModal(@json($menuData))'>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            View
                        </button>
                        <button type="button" class="btn-edit-menu" onclick='openEditMenuModal(@json($menu->id), @json($menu->nama_menu), @json($menu->reseps->map(fn($r) => ["bahan_id" => $r->bahan_id, "gramasi" => $r->gramasi_per_porsi])))'>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Edit
                        </button>
                        <button type="button" class="btn-delete-menu" onclick="confirmDeleteMenu({{ $menu->id }}, '{{ addslashes($menu->nama_menu) }}')">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                            Hapus
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
    </main>
</div>

{{-- ═══ MODAL: TAMBAH MENU BARU ═══ --}}
<div id="addMenuModal" class="modal-form-overlay">
    <div class="modal-form-box">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
            <h3>Tambah Menu Baru</h3>
            <span onclick="closeModal('addMenuModal')" style="cursor:pointer;font-size:1.4rem;color:#6b7280;">&times;</span>
        </div>
        <form action="{{ route('menu.store') }}" method="POST">
            @csrf
            <div style="margin-bottom:1rem;">
                <label class="f-label">Nama Menu</label>
                <input type="text" name="nama_menu" class="f-input" placeholder="Contoh: Nasi Ayam Bakar + Sayur" required>
            </div>
            <div style="margin-bottom:1rem;">
                <label class="f-label">Bahan Baku & Gramasi per Porsi</label>
                <div id="ingredientRows">
                    <div class="ingredient-row" style="display:flex;gap:0.5rem;margin-bottom:0.5rem;align-items:center;">
                        <div class="searchable-select" data-name="ingredients[0][bahan_id]"></div>
                        <input type="number" step="0.01" name="ingredients[0][gramasi]" class="f-input" placeholder="gram / ml" required style="flex:1;min-width:80px;">
                        <button type="button" onclick="this.parentElement.remove()" style="background:none;border:none;color:#ef4444;cursor:pointer;font-size:1.2rem;padding:0.25rem;">&times;</button>
                    </div>
                </div>
                <button type="button" onclick="addIngredientRow()" style="background:#f3f4f6;border:none;padding:0.5rem 0.75rem;border-radius:8px;font-size:0.75rem;cursor:pointer;font-weight:600;color:#374151;">+ Tambah Bahan</button>
            </div>
            <small style="display:block;color:#7b8ea3;margin-bottom:1rem;">*Kalori & nutrisi akan dihitung otomatis berdasarkan Tabel Komposisi Pangan Indonesia.</small>
            <button type="submit" class="btn btn-primary" style="width:100%;">Simpan Menu</button>
        </form>
    </div>
</div>

{{-- ═══ MODAL: EDIT MENU ═══ --}}
<div id="editMenuModal" class="modal-form-overlay">
    <div class="modal-form-box">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
            <h3>Edit Menu</h3>
            <span onclick="closeModal('editMenuModal')" style="cursor:pointer;font-size:1.4rem;color:#6b7280;">&times;</span>
        </div>
        <form id="editMenuForm" method="POST">
            @csrf
            @method('PUT')
            <div style="margin-bottom:1rem;">
                <label class="f-label">Nama Menu</label>
                <input type="text" name="nama_menu" id="editMenuName" class="f-input" required>
            </div>
            <div style="margin-bottom:1rem;">
                <label class="f-label">Bahan Baku & Gramasi per Porsi</label>
                <div id="editIngredientRows"></div>
                <button type="button" onclick="addEditIngredientRow()" style="background:#f3f4f6;border:none;padding:0.5rem 0.75rem;border-radius:8px;font-size:0.75rem;cursor:pointer;font-weight:600;color:#374151;">+ Tambah Bahan</button>
            </div>
            <small style="display:block;color:#7b8ea3;margin-bottom:1rem;">*Kalori & nutrisi akan dihitung ulang otomatis.</small>
            <button type="submit" class="btn btn-primary" style="width:100%;">Simpan Perubahan</button>
        </form>
    </div>
</div>

{{-- ═══ MODAL: VIEW MENU LIBRARY ═══ --}}
<div id="viewMenuLibraryModal" class="modal-form-overlay">
    <div class="modal-form-box" style="width: 520px; padding: 2rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
            <h3 id="vmlName" style="color:var(--primary);">Nama Menu</h3>
            <span onclick="closeModal('viewMenuLibraryModal')" style="cursor:pointer;font-size:1.4rem;color:#6b7280;">&times;</span>
        </div>
        
        <div style="background:var(--bg); border-radius:12px; padding:1.5rem; margin-bottom:1.5rem;">
            <h4 style="font-size:0.85rem; color:#6b7280; text-transform:uppercase; letter-spacing:1px; margin-bottom:1rem; border-bottom:1px solid #e5e7eb; padding-bottom:0.5rem;">Informasi Gizi & Modal / Porsi</h4>
            <table style="width:100%; font-size:0.9rem; border-collapse:collapse;">
                <tbody id="vmlNutritionBody">
                    <!-- Injected via JS -->
                </tbody>
            </table>
        </div>

        <div>
            <h4 style="font-size:0.85rem; color:#6b7280; text-transform:uppercase; letter-spacing:1px; margin-bottom:1rem; border-bottom:1px solid #e5e7eb; padding-bottom:0.5rem;">Komposisi Bahan & Biaya / Porsi</h4>
            <table style="width:100%; font-size:0.9rem; border-collapse:collapse;">
                <thead>
                    <tr style="text-align:left; color:#9ca3af; font-weight:600; font-size:0.8rem;">
                        <th style="padding-bottom:0.5rem;">Bahan Baku</th>
                        <th style="text-align:right; padding-bottom:0.5rem;">Gramasi</th>
                        <th style="text-align:right; padding-bottom:0.5rem;">Harga/g</th>
                        <th style="text-align:right; padding-bottom:0.5rem;">Biaya</th>
                    </tr>
                </thead>
                <tbody id="vmlIngredientsBody">
                    <!-- Injected via JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ═══ MODAL: JADWALKAN MENU ═══ --}}
<div id="scheduleModal" class="modal-form-overlay">
    <div class="modal-form-box" style="width:420px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
            <h3>Jadwalkan Menu</h3>
            <span onclick="closeModal('scheduleModal')" style="cursor:pointer;font-size:1.4rem;color:#6b7280;">&times;</span>
        </div>
        <form action="{{ route('schedule.store') }}" method="POST">
            @csrf
            <input type="hidden" name="tanggal_produksi" id="scheduleDate">
            <div style="margin-bottom:1rem;">
                <label class="f-label">Pilih Menu</label>
                <select name="menu_id" id="scheduleMenuSelect" class="f-select" required onchange="updatePortionPreview()">
                    <option value="">-- Pilih Menu --</option>
                    @foreach($menus as $m)
                    @php
                        $isUnavailable = false;
                        foreach($m->reseps as $r) {
                            if(!$r->bahanBaku || $r->bahanBaku->trashed() || $r->bahanBaku->stok <= 0) { 
                                $isUnavailable = true; 
                                break; 
                            }
                        }
                    @endphp
                    <option value="{{ $m->id }}"
                        {{ $isUnavailable ? 'disabled' : '' }}
                        data-berat="{{ $m->reseps->sum('gramasi_per_porsi') }}"
                        data-kcal="{{ round($m->total_kalori) }}"
                        data-protein="{{ round($m->total_protein) }}"
                        data-karbo="{{ round($m->total_karbohidrat) }}"
                        data-lemak="{{ round($m->total_lemak) }}"
                        data-modal="{{ $m->harga_modal_per_porsi }}">
                        {{ $m->nama_menu }} {{ $isUnavailable ? '(Ada bahan yang habis)' : '' }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div style="margin-bottom:1rem;">
                <label class="f-label">Jumlah Porsi</label>
                <input type="number" name="total_target_porsi" id="schedulePortionInput" class="f-input" value="100" min="1" required oninput="updatePortionPreview()">
            </div>

            <div id="portionPreview" class="portion-info" style="display:none;margin-bottom:1rem;">
                <div class="p-row"><span>Berat / porsi</span><span id="pvBerat">-</span></div>
                <div class="p-row"><span>Kalori / porsi</span><span id="pvKcal">-</span></div>
                <div class="p-row"><span>Estimasi Modal / porsi</span><span id="pvModalUnit" style="color:#059669; font-weight:600;">-</span></div>
                <div class="p-row" style="border-top:1px solid #e5e7eb;padding-top:0.4rem;margin-top:0.3rem;font-weight:700;">
                    <span>Total Berat</span><span id="pvTotal">-</span>
                </div>
                <div class="p-row" style="font-weight:700;">
                    <span>Total Anggaran Modal</span><span id="pvTotalModal" style="color:#059669;font-size:1rem;">-</span>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;">Jadwalkan</button>
        </form>
    </div>
</div>

{{-- ═══ MODAL: EDIT JADWAL ═══ --}}
<div id="editScheduleModal" class="modal-form-overlay">
    <div class="modal-form-box" style="width:420px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
            <h3>Edit Jadwal Menu</h3>
            <span onclick="closeModal('editScheduleModal')" style="cursor:pointer;font-size:1.4rem;color:#6b7280;">&times;</span>
        </div>
        <form id="editScheduleForm" method="POST">
            @csrf
            @method('PUT')
            <div style="margin-bottom:1rem;">
                <label class="f-label">Pilih Menu</label>
                <select name="menu_id" id="editMenuSelect" class="f-select" required onchange="updateEditPreview()">
                    <option value="">-- Pilih Menu --</option>
                    @foreach($menus as $m)
                    @php
                        $isUnavailable = false;
                        foreach($m->reseps as $r) {
                            if(!$r->bahanBaku || $r->bahanBaku->trashed() || $r->bahanBaku->stok <= 0) { 
                                $isUnavailable = true; 
                                break; 
                            }
                        }
                    @endphp
                    <option value="{{ $m->id }}"
                        {{ $isUnavailable ? 'disabled' : '' }}
                        data-berat="{{ $m->reseps->sum('gramasi_per_porsi') }}"
                        data-kcal="{{ round($m->total_kalori) }}"
                        data-modal="{{ $m->harga_modal_per_porsi }}">
                        {{ $m->nama_menu }} {{ $isUnavailable ? '(Ada bahan yang habis)' : '' }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div style="margin-bottom:1rem;">
                <label class="f-label">Jumlah Porsi</label>
                <input type="number" name="total_target_porsi" id="editPortionInput" class="f-input" min="1" required oninput="updateEditPreview()">
            </div>
            <div id="editPreview" class="portion-info" style="display:none;margin-bottom:1rem;">
                <div class="p-row" style="font-weight:700;"><span>Total Berat</span><span id="epvTotal">-</span></div>
                <div class="p-row" style="font-weight:700;"><span>Total Anggaran Modal</span><span id="epvTotalModal" style="color:#059669;">-</span></div>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Simpan Perubahan</button>
        </form>
    </div>
</div>

{{-- ═══ MODAL: VIEW DETAIL JADWAL ═══ --}}
<div id="viewScheduleModal" class="modal-form-overlay">
    <div class="view-modal-box" style="max-width:650px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
            <h3 style="font-size:1.2rem;font-weight:800;color:#0c1e35;">Detail Jadwal Menu</h3>
            <span onclick="closeModal('viewScheduleModal')" style="cursor:pointer;font-size:1.4rem;color:#6b7280;">&times;</span>
        </div>

        <div class="view-section">
            <div style="display:flex;justify-content:space-between;align-items:baseline;margin-bottom:0.75rem;">
                <div>
                    <div style="font-size:1.1rem;font-weight:800;color:#0c1e35;" id="viewMenuName"></div>
                    <div style="display:flex; gap:0.5rem; align-items:center; margin-top:0.25rem;">
                        <span style="font-size:0.8rem;color:#6b7280;" id="viewMenuPorsi"></span>
                        <span id="viewMenuStatusBadge" style="font-size:0.65rem; font-weight:700; padding:0.15rem 0.4rem; border-radius:6px; text-transform:uppercase; letter-spacing:0.5px;">-</span>
                    </div>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:0.85rem;font-weight:700;color:#059669;" id="viewMenuModalUnit">Modal: Rp 0 / porsi</div>
                    <div style="font-size:1.05rem;font-weight:800;color:#059669;margin-top:0.1rem;" id="viewMenuModalTotal">Total Modal: Rp 0</div>
                </div>
            </div>
        </div>

        <div class="view-section">
            <div class="view-section-title">Kebutuhan & Biaya Bahan Baku</div>
            <table class="view-table">
                <thead>
                    <tr>
                        <th>Bahan</th>
                        <th style="text-align:right;">Per Porsi</th>
                        <th style="text-align:right;">Harga/g</th>
                        <th style="text-align:right;">Biaya/Porsi</th>
                        <th style="text-align:right;">Total Gram</th>
                        <th style="text-align:right;">Total Biaya</th>
                    </tr>
                </thead>
                <tbody id="viewIngredientsBody"></tbody>
            </table>
        </div>

        <div class="view-section">
            <div class="view-section-title">Informasi Gizi</div>
            <table class="view-table">
                <thead><tr><th>Nutrisi</th><th style="text-align:right;">Per Porsi</th><th style="text-align:right;">Total</th></tr></thead>
                <tbody id="viewNutritionBody"></tbody>
            </table>
        </div>

        <div id="viewScheduleActions" style="margin-top:1rem; margin-bottom:1rem; padding-top:1.5rem; border-top:1px solid #e5e7eb;">
            <!-- Injected dynamically via JavaScript -->
        </div>

        <button type="button" onclick="closeModal('viewScheduleModal')" class="btn btn-primary" style="width:100%;">Tutup</button>
    </div>
</div>

{{-- ═══ POPUP: KONFIRMASI HAPUS MENU ═══ --}}
<div id="deleteConfirmModal" class="confirm-overlay">
    <div class="confirm-box">
        <div class="confirm-icon">🗑️</div>
        <h4>Hapus Menu?</h4>
        <p id="deleteConfirmText">Menu ini akan dihapus secara permanen beserta resepnya.</p>
        <div class="confirm-actions">
            <button type="button" class="btn-cancel" onclick="closeDeleteConfirm()">Batal</button>
            <form id="deleteMenuForm" method="POST" style="flex:1;display:flex;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-confirm-delete" style="width:100%;">Ya, Hapus</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const csrfToken = '{{ csrf_token() }}';
let ic = 1;

// ── Bahan data (sorted A-Z from controller) ──
const bahanList = [
    @foreach($bahanBakus as $b)
    { id: {{ $b->id }}, nama: "{{ addslashes($b->nama_bahan) }}" },
    @endforeach
];

function createSearchableSelect(container, fieldName, selectedId) {
    const wrapper = container;
    wrapper.innerHTML = '';

    const hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = fieldName;
    hiddenInput.required = true;
    hiddenInput.value = selectedId || '';

    const display = document.createElement('div');
    display.className = 'ss-display placeholder';
    const selectedItem = bahanList.find(b => b.id == selectedId);
    display.innerHTML = `<span>${selectedItem ? selectedItem.nama : 'Pilih Bahan...'}</span><span class="ss-arrow">▼</span>`;
    if (selectedItem) display.classList.remove('placeholder');

    const dropdown = document.createElement('div');
    dropdown.className = 'ss-dropdown';
    dropdown.innerHTML = `<input type="text" class="ss-search" placeholder="Cari bahan...">
        <div class="ss-options"></div>`;

    wrapper.appendChild(hiddenInput);
    wrapper.appendChild(display);
    wrapper.appendChild(dropdown);

    const searchInput = dropdown.querySelector('.ss-search');
    const optionsContainer = dropdown.querySelector('.ss-options');

    function renderOptions(filter) {
        const keyword = (filter || '').toLowerCase();
        const filtered = bahanList.filter(b => b.nama.toLowerCase().includes(keyword));
        if (filtered.length === 0) {
            optionsContainer.innerHTML = '<div class="ss-empty">Tidak ditemukan</div>';
            return;
        }
        optionsContainer.innerHTML = filtered.map(b =>
            `<div class="ss-option${b.id == hiddenInput.value ? ' selected' : ''}" data-id="${b.id}">${b.nama}</div>`
        ).join('');
        optionsContainer.querySelectorAll('.ss-option').forEach(opt => {
            opt.addEventListener('click', function() {
                hiddenInput.value = this.dataset.id;
                display.innerHTML = `<span>${this.textContent}</span><span class="ss-arrow">▼</span>`;
                display.classList.remove('placeholder');
                wrapper.classList.remove('open');
            });
        });
    }

    display.addEventListener('click', function(e) {
        e.stopPropagation();
        // Close all other dropdowns
        document.querySelectorAll('.searchable-select.open').forEach(s => { if (s !== wrapper) s.classList.remove('open'); });
        wrapper.classList.toggle('open');
        if (wrapper.classList.contains('open')) {
            searchInput.value = '';
            renderOptions('');
            setTimeout(() => searchInput.focus(), 50);
        }
    });

    searchInput.addEventListener('input', function() {
        renderOptions(this.value);
    });

    searchInput.addEventListener('click', function(e) { e.stopPropagation(); });

    renderOptions('');
}

// Close dropdowns when clicking outside
document.addEventListener('click', function() {
    document.querySelectorAll('.searchable-select.open').forEach(s => s.classList.remove('open'));
});

// Initialize first ingredient row
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('#ingredientRows .searchable-select').forEach(el => {
        createSearchableSelect(el, el.dataset.name, '');
    });
});

function addIngredientRow() {
    const c = document.getElementById('ingredientRows');
    const r = document.createElement('div');
    r.className = 'ingredient-row';
    r.style.cssText = 'display:flex;gap:0.5rem;margin-bottom:0.5rem;align-items:center;';
    const ssDiv = document.createElement('div');
    ssDiv.className = 'searchable-select';
    const gramInput = document.createElement('input');
    gramInput.type = 'number';
    gramInput.step = '0.01';
    gramInput.name = `ingredients[${ic}][gramasi]`;
    gramInput.className = 'f-input';
    gramInput.placeholder = 'gram / ml';
    gramInput.required = true;
    gramInput.style.cssText = 'flex:1;min-width:80px;';
    
    const removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.innerHTML = '&times;';
    removeBtn.style.cssText = 'background:none;border:none;color:#ef4444;cursor:pointer;font-size:1.2rem;padding:0.25rem;';
    removeBtn.onclick = function() { r.remove(); };
    
    r.appendChild(ssDiv);
    r.appendChild(gramInput);
    r.appendChild(removeBtn);
    c.appendChild(r);
    createSearchableSelect(ssDiv, `ingredients[${ic}][bahan_id]`, '');
    ic++;
}

function closeModal(id) {
    document.getElementById(id).classList.remove('visible');
}

function confirmDeleteMenu(menuId, menuName) {
    document.getElementById('deleteConfirmText').textContent = `Menu "${menuName}" akan dihapus secara permanen beserta resepnya.`;
    document.getElementById('deleteMenuForm').action = '/menu/' + menuId;
    document.getElementById('deleteConfirmModal').classList.add('visible');
}

function closeDeleteConfirm() {
    document.getElementById('deleteConfirmModal').classList.remove('visible');
}

// ── Edit Menu Modal ──
let editIc = 0;

function openEditMenuModal(menuId, menuName, ingredients) {
    document.getElementById('editMenuForm').action = '/menu/' + menuId;
    document.getElementById('editMenuName').value = menuName;
    const container = document.getElementById('editIngredientRows');
    container.innerHTML = '';
    editIc = 0;
    ingredients.forEach(function(ing) {
        addEditIngredientRow(ing.bahan_id, ing.gramasi);
    });
    document.getElementById('editMenuModal').classList.add('visible');
}

function addEditIngredientRow(bahanId, gramasi) {
    const c = document.getElementById('editIngredientRows');
    const r = document.createElement('div');
    r.className = 'ingredient-row';
    r.style.cssText = 'display:flex;gap:0.5rem;margin-bottom:0.5rem;align-items:center;';
    const ssDiv = document.createElement('div');
    ssDiv.className = 'searchable-select';
    const gramInput = document.createElement('input');
    gramInput.type = 'number';
    gramInput.step = '0.01';
    gramInput.name = `ingredients[${editIc}][gramasi]`;
    gramInput.className = 'f-input';
    gramInput.placeholder = 'gram / ml';
    gramInput.required = true;
    gramInput.style.cssText = 'flex:1;min-width:80px;';
    gramInput.value = gramasi || '';
    const removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.innerHTML = '&times;';
    removeBtn.style.cssText = 'background:none;border:none;color:#ef4444;cursor:pointer;font-size:1.2rem;padding:0.25rem;';
    removeBtn.onclick = function() { r.remove(); };
    r.appendChild(ssDiv);
    r.appendChild(gramInput);
    r.appendChild(removeBtn);
    c.appendChild(r);
    createSearchableSelect(ssDiv, `ingredients[${editIc}][bahan_id]`, bahanId || '');
    editIc++;
}

function fmtG(g) {
    if (g >= 1000) return (g / 1000).toFixed(1).replace(/\.0$/, '') + ' kg/L';
    return Math.round(g) + ' g/ml';
}

function openViewScheduleModal(data) {
    document.getElementById('viewMenuName').textContent = data.menu_name;
    document.getElementById('viewMenuPorsi').textContent = data.porsi + ' porsi';
    
    // Set costing in header
    document.getElementById('viewMenuModalUnit').textContent = 'Modal: Rp ' + Math.round(data.modal_per_porsi || 0).toLocaleString('id-ID') + ' / porsi';
    document.getElementById('viewMenuModalTotal').textContent = 'Total Modal: Rp ' + Math.round(data.total_modal || 0).toLocaleString('id-ID');

    // Set status badge styling
    const status = data.status_produksi || 'Menunggu';
    const badge = document.getElementById('viewMenuStatusBadge');
    badge.textContent = status;
    if (status === 'Memasak') {
        badge.style.background = '#ffedd5';
        badge.style.color = '#ea580c';
    } else if (status === 'Siap Kirim') {
        badge.style.background = '#d1fae5';
        badge.style.color = '#059669';
    } else { // Menunggu
        badge.style.background = '#fef3c7';
        badge.style.color = '#d97706';
    }

    // Set action buttons dynamically
    const actionsDiv = document.getElementById('viewScheduleActions');
    actionsDiv.innerHTML = '';
    
    if (status === 'Menunggu') {
        actionsDiv.innerHTML = `
            <form action="/dashboard/schedule/${data.id}/update-status" method="POST">
                <input type="hidden" name="_token" value="${csrfToken}">
                <input type="hidden" name="status_produksi" value="Memasak">
                <button type="submit" class="btn" style="width:100%; background:#10b981; color:white; border:none; padding:0.75rem; border-radius:8px; font-weight:700; cursor:pointer; font-size:0.9rem; transition: background 0.2s;" onmouseover="this.style.background='#059669'" onmouseout="this.style.background='#10b981'">
                    Mulai Memasak
                </button>
            </form>
        `;
    } else if (status === 'Memasak') {
        actionsDiv.innerHTML = `
            <form action="/dashboard/schedule/${data.id}/update-status" method="POST">
                <input type="hidden" name="_token" value="${csrfToken}">
                <input type="hidden" name="status_produksi" value="Siap Kirim">
                <button type="submit" class="btn" style="width:100%; background:#10b981; color:white; border:none; padding:0.75rem; border-radius:8px; font-weight:700; cursor:pointer; font-size:0.9rem; transition: background 0.2s;" onmouseover="this.style.background='#059669'" onmouseout="this.style.background='#10b981'">
                    Siap Kirim
                </button>
            </form>
        `;
    } else if (status === 'Siap Kirim') {
        actionsDiv.innerHTML = `
            <div style="text-align:center; color:#059669; font-weight:700; font-size:0.85rem; background:#ecfdf5; padding:0.75rem; border-radius:8px; border: 1px solid #a7f3d0; letter-spacing:0.5px;">
                Menu telah selesai di masak dan siap di kirim
            </div>
        `;
    }

    // --- Ingredients Table ---
    const ingBody = document.getElementById('viewIngredientsBody');
    let ingHtml = '';
    let totalGramPerPorsi = 0;
    let totalGramAll = 0;
    let totalBiayaPerPorsi = 0;
    let totalBiayaAll = 0;
    let isStockSufficient = true;
    
    data.ingredients.forEach(function(ing) {
        const gram = parseFloat(ing.gram) || 0;
        const totalG = gram * data.porsi;
        const hargaPerGram = parseFloat(ing.harga_per_gram) || 0;
        const biayaPerPorsi = gram * hargaPerGram;
        const totalBiaya = totalG * hargaPerGram;
        const stokTersedia = parseFloat(ing.stok_tersedia) || 0;
        
        const isShort = totalG > stokTersedia;
        if (isShort) isStockSufficient = false;

        totalGramPerPorsi += gram;
        totalGramAll += totalG;
        totalBiayaPerPorsi += biayaPerPorsi;
        totalBiayaAll += totalBiaya;
        
        ingHtml += `<tr>
            <td>${ing.nama} ${isShort ? '<br><span style="color:#ef4444; font-size:0.7rem; font-weight:700;">(Stok Kurang)</span>' : ''}</td>
            <td style="text-align:right;">${fmtG(gram)}</td>
            <td style="text-align:right;">Rp ${parseFloat(hargaPerGram).toFixed(2).replace(/\.00$/, '')} /g</td>
            <td style="text-align:right;">Rp ${Math.round(biayaPerPorsi).toLocaleString('id-ID')}</td>
            <td style="text-align:right; ${isShort ? 'color:#ef4444; font-weight:700;' : ''}">${fmtG(totalG)}</td>
            <td style="text-align:right; font-weight:600; color:#059669;">Rp ${Math.round(totalBiaya).toLocaleString('id-ID')}</td>
        </tr>`;
    });
    ingHtml += `<tr class="row-total">
        <td>Total</td>
        <td style="text-align:right;">${fmtG(totalGramPerPorsi)}</td>
        <td style="text-align:right;">-</td>
        <td style="text-align:right;">Rp ${Math.round(totalBiayaPerPorsi).toLocaleString('id-ID')}</td>
        <td style="text-align:right;">${fmtG(totalGramAll)}</td>
        <td style="text-align:right; color:#059669; font-weight:700;">Rp ${Math.round(totalBiayaAll).toLocaleString('id-ID')}</td>
    </tr>`;
    ingBody.innerHTML = ingHtml;

    // Update the actions block based on stock availability
    if (status === 'Menunggu') {
        if (isStockSufficient) {
            actionsDiv.innerHTML = `
                <form action="/dashboard/schedule/${data.id}/update-status" method="POST">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <input type="hidden" name="status_produksi" value="Memasak">
                    <button type="submit" class="btn" style="width:100%; background:#10b981; color:white; border:none; padding:0.75rem; border-radius:8px; font-weight:700; cursor:pointer; font-size:0.9rem; transition: background 0.2s;" onmouseover="this.style.background='#059669'" onmouseout="this.style.background='#10b981'">
                        Mulai Memasak
                    </button>
                </form>
            `;
        } else {
            actionsDiv.innerHTML = `
                <div style="text-align:center; color:#ef4444; font-weight:600; font-size:0.85rem; background:#fef2f2; padding:0.75rem; border-radius:8px; border: 1px solid #fecaca; margin-bottom: 0.75rem;">
                    Stok bahan baku tidak mencukupi untuk mulai memasak menu ini.
                </div>
                <button type="button" class="btn" style="width:100%; background:#9ca3af; color:white; border:none; padding:0.75rem; border-radius:8px; font-weight:700; cursor:not-allowed; font-size:0.9rem;" disabled>
                    Mulai Memasak
                </button>
            `;
        }
    }

    // --- Nutrition Table ---
    const nutBody = document.getElementById('viewNutritionBody');
    const nutrients = [
        { label: 'Energi', val: data.kalori, unit: 'kcal' },
        { label: 'Protein', val: data.protein, unit: 'g' },
        { label: 'Karbohidrat', val: data.karbohidrat, unit: 'g' },
        { label: 'Lemak', val: data.lemak, unit: 'g' },
        { label: 'Serat Pangan', val: data.serat, unit: 'g' },
        { label: 'Kalsium', val: data.kalsium, unit: 'mg' },
        { label: 'Zat Besi', val: data.besi, unit: 'mg' },
    ];
    let nutHtml = '';
    nutrients.forEach(function(n) {
        if (n.val >= 0) {
            const perPorsi = Math.round((n.val || 0) * 10) / 10;
            const total = Math.round((n.val || 0) * data.porsi * 10) / 10;
            nutHtml += `<tr><td>${n.label}</td><td style="text-align:right;">${perPorsi} ${n.unit}</td><td style="text-align:right;">${total.toLocaleString()} ${n.unit}</td></tr>`;
        }
    });
    nutBody.innerHTML = nutHtml;

    document.getElementById('viewScheduleModal').classList.add('visible');
}

function openViewMenuLibraryModal(data) {
    document.getElementById('vmlName').textContent = data.nama_menu;

    // --- Nutrition & Cost Table ---
    const nutBody = document.getElementById('vmlNutritionBody');
    const nutrients = [
        { label: 'Energi', val: data.kalori, unit: 'kcal' },
        { label: 'Protein', val: data.protein, unit: 'g' },
        { label: 'Karbohidrat', val: data.karbohidrat, unit: 'g' },
        { label: 'Lemak', val: data.lemak, unit: 'g' },
        { label: 'Serat Pangan', val: data.serat, unit: 'g' },
        { label: 'Kalsium', val: data.kalsium, unit: 'mg' },
        { label: 'Zat Besi', val: data.besi, unit: 'mg' },
    ];
    let nutHtml = '';
    nutrients.forEach(function(n) {
        if (n.val >= 0) {
            const perPorsi = Math.round((n.val || 0) * 10) / 10;
            nutHtml += `<tr><td style="padding:0.4rem 0;">${n.label}</td><td style="text-align:right; font-weight:600; color:#111827;">${perPorsi} ${n.unit}</td></tr>`;
        }
    });
    nutHtml += `<tr style="border-top: 1px solid #e5e7eb; font-weight:700;"><td style="padding:0.4rem 0; color:#059669;">Harga Modal</td><td style="text-align:right; font-weight:700; color:#059669;">Rp ${Math.round(data.modal_per_porsi || 0).toLocaleString('id-ID')} / porsi</td></tr>`;
    nutBody.innerHTML = nutHtml;

    // --- Ingredients Table ---
    const ingBody = document.getElementById('vmlIngredientsBody');
    let ingHtml = '';
    let totalGram = 0;
    let totalModal = 0;
    
    data.ingredients.forEach(function(ing) {
        const gram = parseFloat(ing.gram) || 0;
        const hargaPerGram = parseFloat(ing.harga_per_gram) || 0;
        const subtotal = parseFloat(ing.subtotal) || (gram * hargaPerGram);
        totalGram += gram;
        totalModal += subtotal;
        
        ingHtml += `<tr>
            <td style="padding:0.4rem 0;">${ing.nama}</td>
            <td style="text-align:right;">${fmtG(gram)}</td>
            <td style="text-align:right;">Rp ${parseFloat(hargaPerGram).toFixed(2).replace(/\.00$/, '')} /g</td>
            <td style="text-align:right; font-weight:600; color:#111827;">Rp ${Math.round(subtotal).toLocaleString('id-ID')}</td>
        </tr>`;
    });
    ingHtml += `<tr style="border-top:1px solid #e5e7eb; font-weight:700;">
        <td style="padding-top:0.6rem; margin-top:0.4rem;">Total per Porsi</td>
        <td style="text-align:right; padding-top:0.6rem;">${fmtG(totalGram)}</td>
        <td style="text-align:right; padding-top:0.6rem;">-</td>
        <td style="text-align:right; padding-top:0.6rem; color:#059669;">Rp ${Math.round(totalModal).toLocaleString('id-ID')}</td>
    </tr>`;
    ingBody.innerHTML = ingHtml;

    document.getElementById('viewMenuLibraryModal').classList.add('visible');
}

function openMenuModal() {
    document.getElementById('addMenuModal').classList.add('visible');
}

function openScheduleModal(date) {
    document.getElementById('scheduleDate').value = date;
    document.getElementById('scheduleMenuSelect').value = '';
    document.getElementById('portionPreview').style.display = 'none';
    document.getElementById('scheduleModal').classList.add('visible');
}

function openEditScheduleModal(id, date, menuId, porsi) {
    document.getElementById('editScheduleForm').action = '/dashboard/schedule/' + id;
    document.getElementById('editMenuSelect').value = menuId;
    document.getElementById('editPortionInput').value = porsi;
    updateEditPreview();
    document.getElementById('editScheduleModal').classList.add('visible');
}

function updatePortionPreview() {
    const sel = document.getElementById('scheduleMenuSelect');
    const opt = sel.options[sel.selectedIndex];
    const porsi = parseInt(document.getElementById('schedulePortionInput').value) || 0;
    const preview = document.getElementById('portionPreview');
    if (!opt || !opt.value) { preview.style.display = 'none'; return; }
    
    const berat = parseFloat(opt.dataset.berat) || 0;
    const kcal = parseFloat(opt.dataset.kcal) || 0;
    const modalUnit = parseFloat(opt.dataset.modal) || 0;
    
    const totalBerat = berat * porsi;
    const totalModal = modalUnit * porsi;
    
    document.getElementById('pvBerat').textContent = berat + ' g';
    document.getElementById('pvKcal').textContent = kcal + ' kcal';
    document.getElementById('pvModalUnit').textContent = 'Rp ' + Math.round(modalUnit).toLocaleString('id-ID') + ' / porsi';
    document.getElementById('pvTotal').textContent = (totalBerat >= 1000 ? (totalBerat/1000).toFixed(1)+' kg' : totalBerat+' g') + ' (' + porsi + ' porsi)';
    document.getElementById('pvTotalModal').textContent = 'Rp ' + Math.round(totalModal).toLocaleString('id-ID');
    preview.style.display = 'block';
}

function updateEditPreview() {
    const sel = document.getElementById('editMenuSelect');
    const opt = sel.options[sel.selectedIndex];
    const porsi = parseInt(document.getElementById('editPortionInput').value) || 0;
    const preview = document.getElementById('editPreview');
    if (!opt || !opt.value) { preview.style.display = 'none'; return; }
    
    const berat = parseFloat(opt.dataset.berat) || 0;
    const modalUnit = parseFloat(opt.dataset.modal) || 0;
    
    const totalBerat = berat * porsi;
    const totalModal = modalUnit * porsi;
    
    document.getElementById('epvTotal').textContent = (totalBerat >= 1000 ? (totalBerat/1000).toFixed(1)+' kg' : totalBerat+' g') + ' (' + porsi + ' porsi)';
    document.getElementById('epvTotalModal').textContent = 'Rp ' + Math.round(totalModal).toLocaleString('id-ID');
    preview.style.display = 'block';
}
</script>
@endsection
