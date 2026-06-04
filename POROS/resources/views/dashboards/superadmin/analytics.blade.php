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
    .scorecard.warning h3 { color: #f59e0b; }
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
                <div style="display: flex; flex-direction: column; gap: 0.75rem; flex: 1; justify-content: center;">
                    <div class="scorecard good" style="padding: 0.85rem;">
                        <h3 style="font-size: 1.6rem; margin-bottom: 0px;">{{ $giziNormal }}</h3>
                        <p style="font-size: 0.75rem; margin-bottom: 0px;">Status Gizi Normal</p>
                    </div>
                    <div class="scorecard warning" style="padding: 0.85rem;">
                        <h3 style="font-size: 1.6rem; margin-bottom: 0px;">{{ $giziKurang }}</h3>
                        <p style="font-size: 0.75rem; margin-bottom: 0px;">Status Gizi Kurang</p>
                    </div>
                    <div class="scorecard bad" style="padding: 0.85rem;">
                        <h3 style="font-size: 1.6rem; margin-bottom: 0px;">{{ $giziLebih }}</h3>
                        <p style="font-size: 0.75rem; margin-bottom: 0px;">Status Gizi Lebih / Obesitas</p>
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
                <div style="height: 300px; flex: 1; display: flex; align-items: center; justify-content: center; position: relative;">
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

    // 1. Chart Tren Biaya Bulanan (Gradients & Rp Formatting)
    const biayaBulananCtx = document.getElementById('biayaBulananChart').getContext('2d');
    const biayaBulananGrad = biayaBulananCtx.createLinearGradient(0, 0, 0, 300);
    biayaBulananGrad.addColorStop(0, 'rgba(59, 130, 246, 0.85)');
    biayaBulananGrad.addColorStop(1, 'rgba(59, 130, 246, 0.15)');

    const biayaBulananChart = new Chart(biayaBulananCtx, {
        type: 'bar',
        data: {
            labels: bulananLabels,
            datasets: [{
                label: 'Total Belanja (Rp)',
                data: bulananTotals,
                backgroundColor: biayaBulananGrad,
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1,
                borderRadius: 6,
                barThickness: 35,
                trendlineLinear: {
                    style: "rgba(239, 68, 68, 0.85)",
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
                    padding: 12,
                    backgroundColor: 'rgba(12, 30, 53, 0.95)',
                    titleFont: { size: 13, weight: 'bold' },
                    bodyFont: { size: 12 },
                    callbacks: {
                        label: function(context) {
                            return 'Total: Rp ' + context.parsed.y.toLocaleString('id-ID');
                        },
                        afterLabel: function() {
                            return 'Klik untuk detail bahan baku';
                        }
                    }
                }
            },
            scales: {
                y: { 
                    grid: { borderDash: [4, 4], drawBorder: false },
                    ticks: { 
                        callback: value => 'Rp ' + (value >= 1000000 ? (value / 1000000) + 'jt' : value.toLocaleString('id-ID'))
                    }
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

    // 2. Chart Distribusi Biaya per Bahan Baku (Gradients & Rp Formatting)
    const detailChartCtx = document.getElementById('biayaChart').getContext('2d');
    const detailBiayaGrad = detailChartCtx.createLinearGradient(0, 0, 0, 300);
    detailBiayaGrad.addColorStop(0, 'rgba(245, 158, 11, 0.85)');
    detailBiayaGrad.addColorStop(1, 'rgba(245, 158, 11, 0.15)');

    let biayaDetailChart = new Chart(detailChartCtx, {
        type: 'bar',
        data: {
            labels: biayaGlobalLabels,
            datasets: [{
                label: 'Total Belanja (Rp)',
                data: biayaGlobalData,
                backgroundColor: detailBiayaGrad,
                borderColor: 'rgba(245, 158, 11, 1)',
                borderWidth: 1,
                borderRadius: 6,
                barThickness: 35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { display: false },
                tooltip: {
                    padding: 12,
                    backgroundColor: 'rgba(12, 30, 53, 0.95)',
                    callbacks: {
                        label: function(context) {
                            return 'Belanja: Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                y: { 
                    grid: { borderDash: [4, 4], drawBorder: false },
                    ticks: { 
                        callback: value => 'Rp ' + (value >= 1000000 ? (value / 1000000) + 'jt' : value.toLocaleString('id-ID'))
                    }
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

    // Trend Gizi Chart (PBI-35) - Monthly aggregated area chart with beautiful tooltips
    const trendGiziCtx = document.getElementById('trendGiziChart').getContext('2d');
    const tbGrad = trendGiziCtx.createLinearGradient(0, 0, 0, 300);
    tbGrad.addColorStop(0, 'rgba(59, 130, 246, 0.25)');
    tbGrad.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

    const bbGrad = trendGiziCtx.createLinearGradient(0, 0, 0, 300);
    bbGrad.addColorStop(0, 'rgba(16, 185, 129, 0.25)');
    bbGrad.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

    new Chart(trendGiziCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($trendGizi->pluck('tanggal_ukur')->map(fn($date) => \Carbon\Carbon::parse($date)->format('M y'))) !!},
            datasets: [
                {
                    label: 'Rata-rata TB (cm)',
                    data: {!! json_encode($trendGizi->pluck('avg_tb')) !!},
                    borderColor: '#3b82f6',
                    backgroundColor: tbGrad,
                    borderWidth: 3,
                    pointBackgroundColor: '#3b82f6',
                    pointHoverRadius: 7,
                    tension: 0.35,
                    fill: true,
                    yAxisID: 'y1'
                },
                {
                    label: 'Rata-rata BB (kg)',
                    data: {!! json_encode($trendGizi->pluck('avg_bb')) !!},
                    borderColor: '#10b981',
                    backgroundColor: bbGrad,
                    borderWidth: 3,
                    pointBackgroundColor: '#10b981',
                    pointHoverRadius: 7,
                    tension: 0.35,
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
                legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 8 } },
                tooltip: {
                    padding: 12,
                    backgroundColor: 'rgba(12, 30, 53, 0.95)',
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                if (context.dataset.yAxisID === 'y1') {
                                    label += context.parsed.y.toFixed(1).replace('.', ',') + ' cm';
                                } else {
                                    label += context.parsed.y.toFixed(1).replace('.', ',') + ' kg';
                                }
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: { 
                    type: 'linear',
                    display: true,
                    position: 'left',
                    grid: { borderDash: [4, 4], drawBorder: false },
                    title: { display: true, text: 'Berat (kg)', font: { weight: 'bold' } }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: { drawOnChartArea: false },
                    title: { display: true, text: 'Tinggi (cm)', font: { weight: 'bold' } }
                },
                x: { grid: { display: false } }
            }
        }
    });

    // Custom plugin to draw text exactly at the center of the doughnut chart area
    const centerTextPlugin = {
        id: 'centerText',
        afterDraw: function(chart) {
            try {
                if (chart.config.type !== 'doughnut') return;
                if (!chart.chartArea) return;
                
                const ctx = chart.ctx;
                
                // Get center coordinates of the doughnut chart area (excluding legend space)
                const x = (chart.chartArea.left + chart.chartArea.right) / 2;
                const y = (chart.chartArea.top + chart.chartArea.bottom) / 2;

                // Calculate visible total dynamically
                const dataset = chart.data.datasets[0];
                if (!dataset || !dataset.data) return;
                
                let visibleTotal = 0;
                for (let i = 0; i < dataset.data.length; i++) {
                    let isVisible = true;
                    if (typeof chart.getDataVisibility === 'function') {
                        isVisible = chart.getDataVisibility(i);
                    }
                    if (isVisible) {
                        visibleTotal += parseFloat(dataset.data[i]) || 0;
                    }
                }

                ctx.save();
                
                // Draw Value (e.g. 331,8 kg)
                ctx.font = "bold 1.55rem 'Plus Jakarta Sans', sans-serif";
                ctx.fillStyle = "#0c1e35";
                ctx.textBaseline = "middle";
                ctx.textAlign = "center";
                const textValue = visibleTotal.toFixed(1).replace('.', ',') + " kg";
                ctx.fillText(textValue, x, y - 8);
                
                // Draw Label (TOTAL SISA)
                ctx.font = "bold 0.7rem 'Plus Jakarta Sans', sans-serif";
                ctx.fillStyle = "#6b7280";
                const textLabel = "TOTAL SISA";
                ctx.fillText(textLabel, x, y + 14);
                
                ctx.restore();
            } catch (err) {
                console.error("Error in centerTextPlugin:", err);
            }
        }
    };

    // Waste Chart (PBI-36) - Doughnut based on volume kg
    new Chart(document.getElementById('wasteChart'), {
        type: 'doughnut',
        plugins: [centerTextPlugin],
        data: {
            labels: {!! json_encode($wasteData->pluck('keterangan')) !!},
            datasets: [{
                data: {!! json_encode($wasteData->pluck('total_kg')) !!},
                backgroundColor: ['#ff6b00', '#ef4444', '#f59e0b', '#10b981', '#94a3b8'],
                hoverOffset: 15,
                borderWidth: 4,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '72%',
            plugins: {
                legend: { 
                    position: 'right', 
                    labels: { 
                        padding: 15, 
                        usePointStyle: true,
                        font: { size: 11, weight: '600' }
                    } 
                },
                tooltip: {
                    padding: 12,
                    backgroundColor: 'rgba(12, 30, 53, 0.95)',
                    callbacks: {
                        label: function(context) {
                            const value = context.parsed;
                            return ' ' + context.label + ': ' + Number(value).toFixed(1).replace('.', ',') + ' kg';
                        }
                    }
                }
            }
        }
    });

    // Top Menu Chart (PBI-36) - Volume-based bar chart with horizontal gradient
    const topMenuCtx = document.getElementById('topMenuChart').getContext('2d');
    const topMenuGrad = topMenuCtx.createLinearGradient(0, 0, 300, 0);
    topMenuGrad.addColorStop(0, 'rgba(239, 68, 68, 0.85)');
    topMenuGrad.addColorStop(1, 'rgba(239, 68, 68, 0.15)');

    new Chart(topMenuCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($topMenus->pluck('nama_menu')) !!},
            datasets: [{
                label: 'Total Sisa (kg)',
                data: {!! json_encode($topMenus->pluck('total_waste')) !!},
                backgroundColor: topMenuGrad,
                borderColor: 'rgba(239, 68, 68, 1)',
                borderWidth: 1,
                borderRadius: 4,
                barThickness: 20
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { display: false },
                tooltip: {
                    padding: 12,
                    backgroundColor: 'rgba(12, 30, 53, 0.95)',
                    callbacks: {
                        label: function(context) {
                            return ' Total Sisa: ' + Number(context.parsed.x).toFixed(1).replace('.', ',') + ' kg';
                        }
                    }
                }
            },
            scales: {
                x: { 
                    grid: { borderDash: [4, 4], drawBorder: false },
                    ticks: { precision: 1 },
                    title: { display: true, text: 'Berat Sisa (kg)', font: { weight: 'bold' } }
                },
                y: { grid: { display: false } }
            }
        }
    });
</script>
@endsection