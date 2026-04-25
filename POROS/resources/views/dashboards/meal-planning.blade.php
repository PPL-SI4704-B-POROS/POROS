@extends('layouts.app')

@section('title', 'Meal Planning')

@section('styles')
<style>
    .week-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 1rem; }
    .day-card { border: 2px dashed #d1d5db; border-radius: 12px; padding: 1rem; min-height: 160px; display: flex; flex-direction: column; transition: all 0.2s; position: relative; }
    .day-card.has-menu { border: 2px solid var(--primary); background: #fff5ed; }
    .day-card .day-name { font-weight: 700; color: #0c1e35; font-size: 0.9rem; }
    .day-card .day-date { font-size: 0.75rem; color: #7b8ea3; margin-bottom: 0.75rem; }
    .day-card .day-icon { position: absolute; top: 0.75rem; right: 0.75rem; color: #c0c8d4; }
    .day-card.has-menu .day-icon { color: var(--primary); }
    .day-card .menu-name { font-weight: 700; font-size: 0.85rem; color: #0c1e35; margin-top: auto; }
    .day-card .menu-portions { font-size: 0.7rem; color: #7b8ea3; }
    .day-card .change-link { color: var(--primary); font-size: 0.75rem; font-weight: 700; text-decoration: none; cursor: pointer; }
    .add-menu-link { color: #7b8ea3; font-size: 0.8rem; font-weight: 600; cursor: pointer; margin-top: auto; background: none; border: none; }
    .day-actions { display: flex; gap: 0.3rem; margin-top: auto; }
    .day-actions button { background: none; border: none; cursor: pointer; font-size: 0.65rem; font-weight: 700; padding: 0.2rem 0.35rem; border-radius: 6px; transition: all 0.15s; }
    .day-actions .btn-view { color: #2563eb; }
    .day-actions .btn-view:hover { background: #eff6ff; }
    .day-actions .btn-edit { color: var(--primary); }
    .day-actions .btn-edit:hover { background: #fff7ed; }
    .day-actions .btn-del { color: #ef4444; }
    .day-actions .btn-del:hover { background: #fef2f2; }

    .view-modal-box { background: white; border-radius: 20px; padding: 2rem; width: 520px; max-width: 92%; max-height: 88vh; overflow-y: auto; box-shadow: 0 25px 50px rgba(0,0,0,0.15); animation: popIn 0.2s ease; }
    .view-section { margin-bottom: 1.25rem; }
    .view-section-title { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #9ca3af; letter-spacing: 0.05em; margin-bottom: 0.5rem; }
    .view-table { width: 100%; border-collapse: collapse; font-size: 0.82rem; }
    .view-table th { text-align: left; padding: 0.5rem 0.6rem; background: #f8fafc; color: #6b7280; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; border-bottom: 1px solid #e5e7eb; }
    .view-table td { padding: 0.5rem 0.6rem; border-bottom: 1px solid #f3f4f6; color: #374151; }
    .view-table tr:last-child td { border-bottom: none; }
    .view-table .row-total { background: #fff7ed; font-weight: 700; }
    .view-table .row-total td { color: #0c1e35; border-top: 2px solid var(--primary); }

    .menu-lib-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 1.5rem; }
    .menu-lib-card { background: white; border: 1px solid #e5e7eb; border-radius: 16px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04); position: relative; }
    .menu-lib-card .food-icon { position: absolute; top: 1.25rem; right: 1.25rem; color: var(--primary); }
    .menu-lib-card .menu-title { font-size: 1.1rem; font-weight: 700; color: #0c1e35; margin-bottom: 0.25rem; padding-right: 2rem; }
    .menu-lib-card .menu-kcal { font-size: 0.85rem; color: #6b7280; margin-bottom: 1rem; }
    .nutrient-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem; margin-bottom: 1rem; }
    .nutrient-box { padding: 0.6rem 0.75rem; border-radius: 10px; }
    .nutrient-box.protein { background: #dbeafe; }
    .nutrient-box.carbs { background: #fff7ed; }
    .nutrient-box.fat { background: #fef9c3; }
    .nutrient-box .n-label { font-size: 0.65rem; font-weight: 600; text-transform: uppercase; }
    .nutrient-box .n-value { font-size: 1.1rem; font-weight: 800; }
    .nutrient-box.protein .n-label, .nutrient-box.protein .n-value { color: #2563eb; }
    .nutrient-box.carbs .n-label, .nutrient-box.carbs .n-value { color: #ea580c; }
    .nutrient-box.fat .n-label, .nutrient-box.fat .n-value { color: #ca8a04; }

    .portion-info { background: #f8fafc; border-radius: 10px; padding: 0.75rem; font-size: 0.8rem; }
    .portion-info .p-row { display: flex; justify-content: space-between; padding: 0.2rem 0; color: #4b5563; }
    .portion-info .p-row span:last-child { font-weight: 700; color: #0c1e35; }

    .btn-outline { background: white; border: 2px solid var(--primary); color: var(--primary); padding: 0.6rem 1.25rem; border-radius: 10px; font-weight: 700; font-size: 0.85rem; cursor: pointer; transition: all 0.2s; }
    .btn-outline:hover { background: #fff5ed; }

    .modal-form-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; }
    .modal-form-overlay.visible { display: flex; }
    .modal-form-box { background: white; border-radius: 20px; padding: 2rem; width: 500px; max-width: 92%; max-height: 88vh; overflow-y: auto; box-shadow: 0 25px 50px rgba(0,0,0,0.15); }
    .modal-form-box h3 { font-size: 1.2rem; font-weight: 800; color: #0c1e35; }
    .f-label { display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem; color: #374151; }
    .f-input, .f-select { width: 100%; padding: 0.65rem 0.75rem; border: 1.5px solid #d1d5db; border-radius: 10px; font-size: 0.9rem; font-family: inherit; }
    .f-input:focus, .f-select:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(255,107,0,0.1); }


    .menu-actions { display: flex; gap: 0.5rem; margin-top: 1rem; }
    .menu-actions .btn-edit-menu { flex: 1; display: flex; align-items: center; justify-content: center; gap: 0.4rem; padding: 0.5rem; border-radius: 8px; font-size: 0.75rem; font-weight: 700; cursor: pointer; transition: all 0.2s; background: #f0f9ff; border: 1px solid #bfdbfe; color: #2563eb; }
    .menu-actions .btn-edit-menu:hover { background: #dbeafe; }
    .menu-actions .btn-delete-menu { flex: 1; display: flex; align-items: center; justify-content: center; gap: 0.4rem; padding: 0.5rem; border-radius: 8px; font-size: 0.75rem; font-weight: 700; cursor: pointer; transition: all 0.2s; background: #fef2f2; border: 1px solid #fecaca; color: #ef4444; }
    .menu-actions .btn-delete-menu:hover { background: #fee2e2; }

    .confirm-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 2000; justify-content: center; align-items: center; }
    .confirm-overlay.visible { display: flex; }
    .confirm-box { background: white; border-radius: 20px; padding: 2rem; width: 380px; max-width: 90%; text-align: center; box-shadow: 0 25px 50px rgba(0,0,0,0.2); animation: popIn 0.2s ease; }
    .confirm-box .confirm-icon { font-size: 2.5rem; margin-bottom: 0.75rem; }
    .confirm-box h4 { font-size: 1.1rem; font-weight: 800; color: #0c1e35; margin-bottom: 0.5rem; }
    .confirm-box p { font-size: 0.85rem; color: #6b7280; margin-bottom: 1.5rem; }
    .confirm-box .confirm-actions { display: flex; gap: 0.75rem; }
    .confirm-box .btn-cancel { flex: 1; padding: 0.7rem; border: 1.5px solid #d1d5db; border-radius: 10px; background: white; font-weight: 700; font-size: 0.85rem; cursor: pointer; color: #374151; transition: all 0.2s; }
    .confirm-box .btn-cancel:hover { background: #f3f4f6; }
    .confirm-box .btn-confirm-delete { flex: 1; padding: 0.7rem; border: none; border-radius: 10px; background: #ef4444; color: white; font-weight: 700; font-size: 0.85rem; cursor: pointer; transition: all 0.2s; }
    .confirm-box .btn-confirm-delete:hover { background: #dc2626; }
    @keyframes popIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }

    @media (max-width: 900px) { .week-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 600px) { .week-grid { grid-template-columns: repeat(2, 1fr); } }
</style>
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
                            </div>
                            @endif
                            @endforeach
                            @php $firstSch = $dayItems->first(); @endphp
                            <div class="day-actions">
                                @if($firstSch->menu)
                                <button type="button" class="btn-view" onclick='openViewScheduleModal(@json([
                                    "menu_name" => $firstSch->menu->nama_menu,
                                    "porsi" => $firstSch->total_target_porsi,
                                    "kalori" => $firstSch->menu->total_kalori,
                                    "protein" => $firstSch->menu->total_protein,
                                    "karbohidrat" => $firstSch->menu->total_karbohidrat,
                                    "lemak" => $firstSch->menu->total_lemak,
                                    "ingredients" => $firstSch->menu->reseps->map(fn($r) => [
                                        "nama" => $r->bahanBaku->nama_bahan,
                                        "gram" => $r->gramasi_per_porsi,
                                    ])
                                ]))'>👁 View</button>
                                @endif
                                <button type="button" class="btn-edit" onclick="openEditScheduleModal('{{ $firstSch->id }}', '{{ $key }}', '{{ $firstSch->menu_id }}', '{{ $firstSch->total_target_porsi }}')">✏️ Edit</button>
                                <form action="{{ route('schedule.destroy', $firstSch->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus jadwal ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-del">🗑️ Hapus</button>
                                </form>
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
                        <div class="p-row"><span>Berat / 1 porsi</span><span>{{ number_format($beratPerPorsi, 0) }} g</span></div>
                        @foreach($menu->reseps as $r)
                        <div class="p-row" style="font-size:0.75rem;"><span>&nbsp;&bull; {{ $r->bahanBaku->nama_bahan }}</span><span>{{ number_format($r->gramasi_per_porsi, 0) }} g</span></div>
                        @endforeach
                    </div>

                    <div class="menu-actions">
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
                        <select name="ingredients[0][bahan_id]" class="f-select" required style="flex:2;min-width:180px;">
                            <option value="">Pilih Bahan</option>
                            @foreach($bahanBakus as $b)
                            <option value="{{ $b->id }}">{{ $b->nama_bahan }}</option>
                            @endforeach
                        </select>
                        <input type="number" step="0.01" name="ingredients[0][gramasi]" class="f-input" placeholder="gram" required style="flex:1;min-width:80px;">
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
                    <option value="{{ $m->id }}"
                        data-berat="{{ $m->reseps->sum('gramasi_per_porsi') }}"
                        data-kcal="{{ round($m->total_kalori) }}"
                        data-protein="{{ round($m->total_protein) }}"
                        data-karbo="{{ round($m->total_karbohidrat) }}"
                        data-lemak="{{ round($m->total_lemak) }}">
                        {{ $m->nama_menu }}
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
                <div class="p-row" style="border-top:1px solid #e5e7eb;padding-top:0.4rem;margin-top:0.3rem;font-weight:700;">
                    <span>Total Berat</span><span id="pvTotal" style="color:var(--primary);font-size:1rem;">-</span>
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
                    <option value="{{ $m->id }}"
                        data-berat="{{ $m->reseps->sum('gramasi_per_porsi') }}"
                        data-kcal="{{ round($m->total_kalori) }}">
                        {{ $m->nama_menu }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div style="margin-bottom:1rem;">
                <label class="f-label">Jumlah Porsi</label>
                <input type="number" name="total_target_porsi" id="editPortionInput" class="f-input" min="1" required oninput="updateEditPreview()">
            </div>
            <div id="editPreview" class="portion-info" style="display:none;margin-bottom:1rem;">
                <div class="p-row" style="font-weight:700;"><span>Total Berat</span><span id="epvTotal" style="color:var(--primary);">-</span></div>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Simpan Perubahan</button>
        </form>
    </div>
</div>

{{-- ═══ MODAL: VIEW DETAIL JADWAL ═══ --}}
<div id="viewScheduleModal" class="modal-form-overlay">
    <div class="view-modal-box">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
            <h3 style="font-size:1.2rem;font-weight:800;color:#0c1e35;">📋 Detail Jadwal Menu</h3>
            <span onclick="closeModal('viewScheduleModal')" style="cursor:pointer;font-size:1.4rem;color:#6b7280;">&times;</span>
        </div>

        <div class="view-section">
            <div style="display:flex;justify-content:space-between;align-items:baseline;margin-bottom:0.75rem;">
                <div>
                    <div style="font-size:1.1rem;font-weight:800;color:#0c1e35;" id="viewMenuName"></div>
                    <div style="font-size:0.8rem;color:#6b7280;" id="viewMenuPorsi"></div>
                </div>
            </div>
        </div>

        <div class="view-section">
            <div class="view-section-title">Kebutuhan Bahan Baku</div>
            <table class="view-table">
                <thead><tr><th>Bahan</th><th style="text-align:right;">Per Porsi</th><th style="text-align:right;">Total</th></tr></thead>
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

        <button type="button" onclick="closeModal('viewScheduleModal')" class="btn btn-primary" style="width:100%;margin-top:0.5rem;">Tutup</button>
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
let ic = 1;

function addIngredientRow() {
    const c = document.getElementById('ingredientRows');
    const r = document.createElement('div');
    r.className = 'ingredient-row';
    r.style.cssText = 'display:flex;gap:0.5rem;margin-bottom:0.5rem;align-items:center;';
    r.innerHTML = `
        <select name="ingredients[${ic}][bahan_id]" class="f-select" required style="flex:2;min-width:180px;">
            <option value="">Pilih Bahan</option>
            @foreach($bahanBakus as $b)
            <option value="{{ $b->id }}">{{ $b->nama_bahan }}</option>
            @endforeach
        </select>
        <input type="number" step="0.01" name="ingredients[${ic}][gramasi]" class="f-input" placeholder="gram" required style="flex:1;min-width:80px;">
    `;
    c.appendChild(r);
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
const bahanOptions = `<option value="">Pilih Bahan</option>@foreach($bahanBakus as $b)<option value="{{ $b->id }}">{{ $b->nama_bahan }}</option>@endforeach`;

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
    r.innerHTML = `
        <select name="ingredients[${editIc}][bahan_id]" class="f-select" required style="flex:2;min-width:180px;">${bahanOptions}</select>
        <input type="number" step="0.01" name="ingredients[${editIc}][gramasi]" class="f-input" placeholder="gram" required style="flex:1;min-width:80px;" value="${gramasi || ''}">
        <button type="button" onclick="this.parentElement.remove()" style="background:none;border:none;color:#ef4444;cursor:pointer;font-size:1.2rem;padding:0.25rem;">&times;</button>
    `;
    if (bahanId) r.querySelector('select').value = bahanId;
    c.appendChild(r);
    editIc++;
}

function fmtG(g) {
    if (g >= 1000) return (g / 1000).toFixed(1).replace(/\.0$/, '') + ' kg';
    return Math.round(g) + ' g';
}

function openViewScheduleModal(data) {
    document.getElementById('viewMenuName').textContent = data.menu_name;
    document.getElementById('viewMenuPorsi').textContent = data.porsi + ' porsi';

    // --- Ingredients Table ---
    const ingBody = document.getElementById('viewIngredientsBody');
    let ingHtml = '';
    let totalGramPerPorsi = 0;
    let totalGramAll = 0;
    data.ingredients.forEach(function(ing) {
        const totalG = ing.gram * data.porsi;
        totalGramPerPorsi += ing.gram;
        totalGramAll += totalG;
        ingHtml += `<tr><td>${ing.nama}</td><td style="text-align:right;">${fmtG(ing.gram)}</td><td style="text-align:right;">${fmtG(totalG)}</td></tr>`;
    });
    ingHtml += `<tr class="row-total"><td>Total Bahan</td><td style="text-align:right;">${fmtG(totalGramPerPorsi)}</td><td style="text-align:right;">${fmtG(totalGramAll)}</td></tr>`;
    ingBody.innerHTML = ingHtml;

    // --- Nutrition Table ---
    const nutBody = document.getElementById('viewNutritionBody');
    const nutrients = [
        { label: 'Energi', val: data.kalori, unit: 'kcal' },
        { label: 'Protein', val: data.protein, unit: 'g' },
        { label: 'Karbohidrat', val: data.karbohidrat, unit: 'g' },
        { label: 'Lemak', val: data.lemak, unit: 'g' },
    ];
    let nutHtml = '';
    nutrients.forEach(function(n) {
        const perPorsi = Math.round(n.val * 10) / 10;
        const total = Math.round(n.val * data.porsi * 10) / 10;
        nutHtml += `<tr><td>${n.label}</td><td style="text-align:right;">${perPorsi} ${n.unit}</td><td style="text-align:right;">${total.toLocaleString()} ${n.unit}</td></tr>`;
    });
    nutBody.innerHTML = nutHtml;

    document.getElementById('viewScheduleModal').classList.add('visible');
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
    const totalBerat = berat * porsi;
    document.getElementById('pvBerat').textContent = berat + ' g';
    document.getElementById('pvKcal').textContent = kcal + ' kcal';
    document.getElementById('pvTotal').textContent = (totalBerat >= 1000 ? (totalBerat/1000).toFixed(1)+' kg' : totalBerat+' g') + ' (' + porsi + ' porsi)';
    preview.style.display = 'block';
}

function updateEditPreview() {
    const sel = document.getElementById('editMenuSelect');
    const opt = sel.options[sel.selectedIndex];
    const porsi = parseInt(document.getElementById('editPortionInput').value) || 0;
    const preview = document.getElementById('editPreview');
    if (!opt || !opt.value) { preview.style.display = 'none'; return; }
    const berat = parseFloat(opt.dataset.berat) || 0;
    const totalBerat = berat * porsi;
    document.getElementById('epvTotal').textContent = (totalBerat >= 1000 ? (totalBerat/1000).toFixed(1)+' kg' : totalBerat+' g') + ' (' + porsi + ' porsi)';
    preview.style.display = 'block';
}
</script>
@endsection
