@extends('layouts.app')

@section('title', 'Dashboard Overview')

@section('styles')
<style>
    .grid-dashboard { display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 2rem; }
    
    .status-badge { padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.75rem; font-weight: 700; }
    .status-delivered { background: #dcfce7; color: #15803d; }
    .status-transit { background: #dbeafe; color: #1d4ed8; }
    .status-preparing { background: #fef9c3; color: #a16207; }
    .status-failed { background: #fee2e2; color: #b91c1c; }

    .alert-box { border-radius: 12px; padding: 1rem; margin-bottom: 1rem; display: flex; gap: 1rem; align-items: flex-start; }
    .alert-red { background: #fff1f2; border: 1px solid #fecdd3; color: #991b1b; border-left: 4px solid #ef4444; }
    .alert-yellow { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; border-left: 4px solid #f59e0b; }
    .alert-blue { background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; border-left: 4px solid #3b82f6; }

    .table { width: 100%; border-collapse: collapse; }
    .table th { text-align: left; padding: 1rem; color: #6b7280; font-size: 0.75rem; text-transform: uppercase; border-bottom: 1px solid #f3f4f6; }
    .table td { padding: 1rem; font-size: 0.875rem; border-bottom: 1px solid #f3f4f6; }
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content')
<div class="dashboard-layout">
    @include('partials.sidebar')

    <main class="main-content">
        @include('partials.header')

        <div class="grid-dashboard">
            <div class="card">
                <h4 style="margin-bottom: 1.5rem; color: #0c1e35;">Student Growth Monitoring (Avg)</h4>
                <div style="height: 250px;">
                    <canvas id="growthLineChart"></canvas>
                </div>
            </div>
            <div class="card">
                <h4 style="margin-bottom: 1.5rem; color: #0c1e35;">Food Waste Trends (Last 7 Days)</h4>
                <div style="height: 250px;">
                    <canvas id="wasteBarChart"></canvas>
                </div>
            </div>
        </div>

        <div class="grid-dashboard">
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h4 style="color: #0c1e35;">Today's Deliveries</h4>
                    <a href="#" style="color: var(--primary); font-size: 0.8rem; font-weight: 700; text-decoration: none;">View All &rarr;</a>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>School</th>
                            <th>Items</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>SDN 01 Jakarta Pusat</td><td>Ayam 50kg, Beras 100kg</td><td>08:30</td><td><span class="status-badge status-delivered">Delivered</span></td></tr>
                        <tr><td>SDN 15 Bandung</td><td>Sayur 30kg, Telur 200pcs</td><td>09:15</td><td><span class="status-badge status-transit">In Transit</span></td></tr>
                        <tr><td>SDN 08 Surabaya</td><td>Ikan 40kg, Tahu 50pcs</td><td>10:00</td><td><span class="status-badge status-preparing">Preparing</span></td></tr>
                        <tr><td>SDN 22 Semarang</td><td>Daging 35kg, Mie 20kg</td><td>07:45</td><td><span class="status-badge status-delivered">Delivered</span></td></tr>
                        <tr><td>SDN 05 Yogyakarta</td><td>Buah 60kg, Susu 100L</td><td>08:00</td><td><span class="status-badge status-failed">Failed</span></td></tr>
                    </tbody>
                </table>
            </div>
            <div>
                <h4 style="margin-bottom: 1rem; color: #0c1e35;">Alerts & Warnings</h4>
                <div class="alert-box alert-red">
                    <div style="font-size: 1.5rem;">⚠️</div>
                    <div>
                        <div style="font-weight: 700;">Low Stock Alert</div>
                        <div style="font-size: 0.8rem;">Ayam kurang 40kg - perlu restock segera</div>
                    </div>
                </div>
                <div class="alert-box alert-yellow">
                    <div style="font-size: 1.5rem;">🕒</div>
                    <div>
                        <div style="font-weight: 700;">Pending Confirmation</div>
                        <div style="font-size: 0.8rem;">3 deliveries menunggu konfirmasi</div>
                    </div>
                </div>
                <div class="alert-box alert-blue">
                    <div style="font-size: 1.5rem;">🚚</div>
                    <div>
                        <div style="font-weight: 700;">Upcoming Deliveries</div>
                        <div style="font-size: 0.8rem;">15 deliveries scheduled untuk besok</div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    // 1. Waste Bar Chart
    const ctxBar = document.getElementById('wasteBarChart').getContext('2d');
    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: {!! json_encode($wasteTrends->pluck('date')->map(fn($d) => date('d M', strtotime($d)))) !!},
            datasets: [{
                label: 'Waste (kg)',
                data: {!! json_encode($wasteTrends->pluck('total_waste')) !!},
                backgroundColor: '#10b981',
                borderRadius: 8,
                barThickness: 30
            }]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false, 
            plugins: { legend: { display: false } },
            scales: { 
                y: { beginAtZero: true, grid: { color: '#f3f4f6' } },
                x: { grid: { display: false } }
            } 
        }
    });

    // 2. Growth Line Chart
    const ctxLine = document.getElementById('growthLineChart').getContext('2d');
    new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: {!! json_encode($growthData->pluck('date')->map(fn($d) => date('M Y', strtotime($d)))) !!},
            datasets: [
                {
                    label: 'Avg Weight (kg)',
                    data: {!! json_encode($growthData->pluck('avg_weight')) !!},
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Avg Height (cm)',
                    data: {!! json_encode($growthData->pluck('avg_height')) !!},
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            },
            scales: {
                y: { beginAtZero: false, grid: { color: '#f3f4f6' } }
            }
        }
    });
</script>
@endsection
