@extends('layouts.app')

@section('title', 'Meal Planning')

@section('styles')
<style>
    .menu-card-highlight { border: 2px solid var(--primary); background: #fff5ed; }
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
                <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 0.25rem;">Rencanakan menu mingguan dan kalkulasi kebutuhan bahan baku secara cerdas.</p>
            </div>
            <button class="btn btn-primary" style="width: auto; padding: 0.75rem 1.5rem;">Hitung Kebutuhan</button>
        </div>

        <section class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h3 style="font-weight: 700; color: #0c1e35; font-size: 1.15rem;">Kalender Menu Mingguan</h3>
                <div style="font-size: 0.9rem; color: #0c1e35; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                    <span>&larr;</span> Minggu, 7 April 2026 <span>&rarr;</span>
                </div>
            </div>
            <div class="grid">
                <div class="menu-card menu-card-highlight" style="padding: 1.25rem; border-radius: 0.75rem;">
                    <div style="font-weight: 700; color: #0c1e35;">Senin</div>
                    <div style="font-size: 0.8rem; color: #7b8ea3; margin-bottom: 1rem;">Apr 7</div>
                    <p style="font-weight: 700; font-size: 0.95rem; color: #0c1e35; margin-bottom: 0.5rem;">Nasi Ayam Bakar + Sayur</p>
                    <a href="#" style="color: var(--primary); font-size: 0.85rem; text-decoration: none; font-weight: 700;">Ganti Menu</a>
                </div>
                <div class="menu-card menu-card-highlight" style="padding: 1.25rem; border-radius: 0.75rem;">
                    <div style="font-weight: 700; color: #0c1e35;">Selasa</div>
                    <div style="font-size: 0.8rem; color: #7b8ea3; margin-bottom: 1rem;">Apr 8</div>
                    <p style="font-weight: 700; font-size: 0.95rem; color: #0c1e35; margin-bottom: 0.5rem;">Nasi Ikan Goreng + Tempe</p>
                    <a href="#" style="color: var(--primary); font-size: 0.85rem; text-decoration: none; font-weight: 700;">Ganti Menu</a>
                </div>
                <div class="menu-card" style="border: 2px dashed #d1d5db; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #7b8ea3; min-height: 120px; font-weight: 600; border-radius: 0.75rem;">
                    <span style="font-size: 1.5rem;">+</span>
                    <span>Tambah Menu</span>
                </div>
            </div>
        </section>

        <section>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="font-weight: 700; color: #0c1e35; font-size: 1.15rem;">Koleksi Menu (Library)</h3>
                <button class="btn btn-primary" style="width: auto; padding: 0.6rem 1.25rem; font-size: 0.9rem;">+ Tambah Menu Baru</button>
            </div>
            
            <div class="grid">
                @php
                    $menus = [
                        ['name' => 'Nasi Ayam Bakar + Sayur', 'kcal' => 580, 'p' => 45, 'c' => 65, 'f' => 20],
                        ['name' => 'Nasi Ikan Goreng + Tempe', 'kcal' => 550, 'p' => 40, 'c' => 70, 'f' => 18],
                        ['name' => 'Nasi Telur Balado + Tahu', 'kcal' => 525, 'p' => 35, 'c' => 68, 'f' => 22],
                    ];
                @endphp
                @foreach($menus as $menu)
                <div class="card" style="margin-bottom: 0; padding: 1.25rem;">
                    <div style="font-weight: 700; color: #0c1e35; font-size: 1rem;">{{ $menu['name'] }}</div>
                    <div style="font-size: 0.8rem; color: #7b8ea3; margin-bottom: 1.25rem;">{{ $menu['kcal'] }} kkal</div>
                    
                    <div class="metric-grid">
                        <div class="metric" style="background: #eef2ff;">
                            <div class="metric-label" style="color: #4f46e5;">Protein</div>
                            <div class="metric-value" style="color: #4f46e5;">{{ $menu['p'] }}g</div>
                        </div>
                        <div class="metric" style="background: #fff7ed;">
                            <div class="metric-label" style="color: #ea580c;">Karbo</div>
                            <div class="metric-value" style="color: #ea580c;">{{ $menu['c'] }}g</div>
                        </div>
                        <div class="metric" style="background: #fefce8;">
                            <div class="metric-label" style="color: #ca8a04;">Lemak</div>
                            <div class="metric-value" style="color: #ca8a04;">{{ $menu['f'] }}g</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
    </main>
</div>
@endsection
