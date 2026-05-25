@extends('layouts.app')

@section('title', 'Advanced Analytics')

@section('styles')
<style>
    .analytics-card {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: var(--shadow);
        border: 1px solid var(--border-color);
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .filter-section {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow);
    }
    .chart-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1.5rem;
        align-items: end;
    }
    .analytics-row {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .analytics-row.reversed {
        grid-template-columns: 1fr 2fr;
    }
    @media (max-width: 992px) {
        .analytics-row, .analytics-row.reversed {
            grid-template-columns: 1fr;
        }
    }
    .scorecard {
        background: #f8fafc;
        border: 1px solid var(--border-color);
        border-radius: 0.75rem;
        padding: 1.25rem;
        text-align: center;
    }
    .scorecard h3 {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 0.25rem;
    }
    .scorecard.good h3 { color: #10b981; }
    .scorecard.bad h3 { color: #ef4444; }
    .scorecard p {
        font-size: 0.85rem;
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .supplier-list {
        list-style: none;
        padding: 0;
        margin: 0;
        flex: 1;
    }
    .supplier-item {
        display: flex;
        justify-content: space-between;
        padding: 0.85rem 0;
        border-bottom: 1px solid var(--border-color);
    }
    .supplier-item:last-child {
        border-bottom: none;
    }
    .supplier-name {
        font-weight: 600;
        color: var(--text-dark);
        font-size: 0.95rem;
    }
    .supplier-total {
        font-weight: 700;
        color: var(--primary);
        font-size: 0.95rem;
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
                <h1 style="font-size: 1.75rem; font-weight: 800; color: #0c1e35;">Advanced Analytics</h1>
                <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 0.25rem;">Monitoring biaya, gizi siswa, & efisiensi operasional Makan Bergizi Gratis.</p>
            </div>
        </div>

        <div class="filter-section">
            <form action="{{ route('superadmin.analytics') }}" method="GET" class="filter-grid">
                <div>
                    <label class="form-label">Mulai Tanggal</label>
                    <input type="date" name="start_date" class="form-input" value="{{ request('start_date') }}">
                </div>
                <div>
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-input" value="{{ request('end_date') }}">
                </div>
                <div>
                    <label class="form-label">Unit Dapur</label>
                    <select name="dapur" class="form-input">
                        <option value="all">Semua Unit Dapur</option>
                        @foreach($daftarDapur as $d)
                            <option value="{{ $d->id }}" {{ request('dapur') == $d->id ? 'selected' : '' }}>{{ $d->nama_lengkap ?? $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Sekolah Mitra</label>
                    <select name="sekolah_id" class="form-input">
                        <option value="all">Semua Sekolah</option>
                        @foreach($daftarSekolah as $s)
                            <option value="{{ $s->id }}" {{ request('sekolah_id') == $s->id ? 'selected' : '' }}>{{ $s->nama_sekolah }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary" style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; height: 48px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- PBI-34: Biaya Belanja -->
        <div class="analytics-row">
            <div class="analytics-card">
                <div class="chart-title">
                    <div style="width: 14px; height: 14px; background: var(--primary); border-radius: 4px;"></div>
                    Tren Biaya Bulanan
                </div>
                <div style="height: 300px; flex: 1;">
                    <canvas id="biayaBulananChart"></canvas>
                </div>
            </div>
            <div class="analytics-card">
                <div class="chart-title">
                    <div style="width: 14px; height: 14px; background: #8b5cf6; border-radius: 4px;"></div>
                    Top 3 Supplier
                </div>
                @if($topSuppliers->isEmpty())
                    <p style="color: var(--text-muted); font-size: 0.9rem; text-align: center; margin-top: 2rem; flex: 1;">Belum ada data supplier.</p>
                @else
                    <ul class="supplier-list">
                        @foreach($topSuppliers as $sup)
                            <li class="supplier-item">
                                <span class="supplier-name">{{ $sup->nama_supplier }}</span>
                                <span class="supplier-total">Rp {{ number_format($sup->total, 0, ',', '.') }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div class="analytics-row" style="grid-template-columns: 1fr;">
            <div class="analytics-card">
                <div class="chart-title" style="justify-content: space-between; display: flex; width: 100%;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 14px; height: 14px; background: #f59e0b; border-radius: 4px;"></div>
                        <span id="detailBiayaTitle">Distribusi Biaya Bahan Baku (Semua Bulan)</span>
                    </div>
                    <button type="button" class="btn btn-primary" id="resetDetailBtn" style="display: none; padding: 0.25rem 0.75rem; font-size: 0.85rem; height: auto;">
                        Tampilkan Semua
                    </button>
                </div>
                <div style="height: 300px; flex: 1;">
                    <canvas id="biayaChart"></canvas>
                </div>
            </div>
        </div>

        <!-- PBI-35: Tren Gizi -->
        <div class="analytics-row reversed">
            <div class="analytics-card">
                <div class="chart-title">
                    <div style="width: 14px; height: 14px; background: #10b981; border-radius: 4px;"></div>
                    Status Gizi Scorecard
                </div>
                <div style="display: flex; flex-direction: column; gap: 1rem; flex: 1; justify-content: center;">
                    <div class="scorecard good">
                        <h3>{{ $giziBaik }}</h3>
                        <p>Gizi Baik (≥ 20kg)</p>
                    </div>
                    <div class="scorecard bad">
                        <h3>{{ $giziKurang }}</h3>
                        <p>Gizi Kurang (< 20kg)</p>
                    </div>
                </div>
            </div>
            <div class="analytics-card">
                <div class="chart-title">
                    <div style="width: 14px; height: 14px; background: #3b82f6; border-radius: 4px;"></div>
                    Tren Rata-Rata BB/TB
                </div>
                <div style="height: 300px; flex: 1;">
                    <canvas id="trendGiziChart"></canvas>
                </div>
            </div>
        </div>

        <!-- PBI-36: Plate Waste -->
        <div class="analytics-row">
            <div class="analytics-card">
                <div class="chart-title">
                    <div style="width: 14px; height: 14px; background: #ef4444; border-radius: 50%;"></div>
                    Evaluasi Sisa Makanan
                </div>
                <div style="height: 300px; flex: 1;">
                    <canvas id="wasteChart"></canvas>
                </div>
            </div>
            <div class="analytics-card">
                <div class="chart-title">
                    <div style="width: 14px; height: 14px; background: #f59e0b; border-radius: 4px;"></div>
                    Top 3 Waste Menu
                </div>
                <div style="height: 300px; flex: 1;">
                    <canvas id="topMenuChart"></canvas>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-trendline"></script>
<script>
    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
    Chart.defaults.color = '#6b7280';

    // Biaya Chart (PBI-34) - Data Global
    const biayaGlobalLabels = {!! json_encode($biayaData->pluck('nama_bahan')) !!};
    const biayaGlobalData = {!! json_encode($biayaData->pluck('total')) !!};
    
    // Data Bulanan & Detail
    const biayaBulananData = {!! json_encode($biayaBulanan) !!};
    const biayaDetailBulananData = {!! json_encode($biayaDetailBulanan) !!};

    const bulananLabels = Object.keys(biayaBulananData);
    const bulananTotals = Object.values(biayaBulananData);

    // 1. Chart Tren Biaya Bulanan
    const biayaBulananChart = new Chart(document.getElementById('biayaBulananChart'), {
        type: 'bar',
        data: {
            labels: bulananLabels,
            datasets: [{
                label: 'Total Belanja (Rp)',
                data: bulananTotals,
                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                borderRadius: 6,
                barThickness: 35,
                trendlineLinear: {
                    style: "rgba(239, 68, 68, 0.8)",
                    lineStyle: "dotted",
                    width: 2
                }
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                        },
                        afterLabel: function() {
                            return 'Klik untuk melihat detail bahan baku';
                        }
                    }
                }
            },
            scales: {
                y: { 
                    grid: { borderDash: [4, 4], drawBorder: false },
                    ticks: { callback: value => 'Rp ' + value.toLocaleString('id-ID') }
                },
                x: { grid: { display: false } }
            },
            onClick: (e, elements) => {
                if (elements.length > 0) {
                    const index = elements[0].index;
                    const selectedMonth = bulananLabels[index];
                    updateDetailChart(selectedMonth);
                }
            }
        }
    });

    // 2. Chart Distribusi Biaya per Bahan Baku
    const detailChartCtx = document.getElementById('biayaChart');
    let biayaDetailChart = new Chart(detailChartCtx, {
        type: 'bar',
        data: {
            labels: biayaGlobalLabels,
            datasets: [{
                label: 'Total Belanja (Rp)',
                data: biayaGlobalData,
                backgroundColor: '#f59e0b',
                borderRadius: 6,
                barThickness: 35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { 
                    grid: { borderDash: [4, 4], drawBorder: false },
                    ticks: { callback: value => 'Rp ' + value.toLocaleString('id-ID') }
                },
                x: { grid: { display: false } }
            }
        }
    });

    // Fungsi update chart saat bulan di-klik
    function updateDetailChart(month) {
        const detailData = biayaDetailBulananData[month] || {};
        const labels = Object.keys(detailData);
        const data = Object.values(detailData);

        biayaDetailChart.data.labels = labels;
        biayaDetailChart.data.datasets[0].data = data;
        biayaDetailChart.update();

        document.getElementById('detailBiayaTitle').innerText = 'Distribusi Biaya Bahan Baku (' + month + ')';
        document.getElementById('resetDetailBtn').style.display = 'block';
    }

    // Fungsi reset chart ke semua bulan
    document.getElementById('resetDetailBtn').addEventListener('click', function() {
        biayaDetailChart.data.labels = biayaGlobalLabels;
        biayaDetailChart.data.datasets[0].data = biayaGlobalData;
        biayaDetailChart.update();

        document.getElementById('detailBiayaTitle').innerText = 'Distribusi Biaya Bahan Baku (Semua Bulan)';
        this.style.display = 'none';
    });

    // Trend Gizi Chart (PBI-35)
    new Chart(document.getElementById('trendGiziChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($trendGizi->pluck('tanggal_ukur')->map(fn($date) => \Carbon\Carbon::parse($date)->format('d M'))) !!},
            datasets: [
                {
                    label: 'Rata-rata TB (cm)',
                    data: {!! json_encode($trendGizi->pluck('avg_tb')) !!},
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true,
                    yAxisID: 'y1'
                },
                {
                    label: 'Rata-rata BB (kg)',
                    data: {!! json_encode($trendGizi->pluck('avg_bb')) !!},
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true,
                    yAxisID: 'y'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 8 } }
            },
            scales: {
                y: { 
                    type: 'linear',
                    display: true,
                    position: 'left',
                    grid: { borderDash: [4, 4], drawBorder: false },
                    title: { display: true, text: 'Berat (kg)' }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: { drawOnChartArea: false },
                    title: { display: true, text: 'Tinggi (cm)' }
                },
                x: { grid: { display: false } }
            }
        }
    });

    // Waste Chart (PBI-36)
    new Chart(document.getElementById('wasteChart'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($wasteData->pluck('keterangan')) !!},
            datasets: [{
                data: {!! json_encode($wasteData->pluck('total')) !!},
                backgroundColor: ['#ff6b00', '#ef4444', '#f59e0b', '#10b981', '#94a3b8'],
                hoverOffset: 20,
                borderWidth: 4,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: { position: 'right', labels: { padding: 20, usePointStyle: true } }
            }
        }
    });

    // Top Menu Chart (PBI-36)
    new Chart(document.getElementById('topMenuChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($topMenus->pluck('nama_menu')) !!},
            datasets: [{
                label: 'Frekuensi Sisa',
                data: {!! json_encode($topMenus->pluck('frekuensi')) !!},
                backgroundColor: '#f59e0b',
                borderRadius: 4,
                barThickness: 20
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { 
                    grid: { borderDash: [4, 4], drawBorder: false },
                    ticks: { precision: 0 }
                },
                y: { grid: { display: false } }
            }
        }
    });
</script>
@endsection