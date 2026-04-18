@extends('layouts.app')

@section('title', 'Dashboard Overview')

@section('styles')
<style>
    .dashboard-header { margin-bottom: 2rem; }
    .dashboard-header h1 { font-size: 2rem; font-weight: 800; color: #0f172a; margin-bottom: 0.5rem; }
    .dashboard-header p { color: #64748b; font-size: 1rem; }

    /* Stats Grid */
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2rem; }
    .stat-card { background: white; padding: 1.5rem; border-radius: 20px; border: 1px solid #f1f5f9; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
    .stat-label { color: #64748b; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.75rem; }
    .stat-value { font-size: 2rem; font-weight: 800; color: #0f172a; margin-bottom: 0.5rem; }
    
    .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-left: auto; }
    .icon-blue { background: #eff6ff; color: #3b82f6; }
    .icon-green { background: #f0fdf4; color: #22c55e; }
    .icon-orange { background: #fff7ed; color: #f59e0b; }
    .icon-red { background: #fef2f2; color: #ef4444; }

    .trend-indicator { display: flex; align-items: center; gap: 0.5rem; font-size: 0.8rem; font-weight: 600; }
    .trend-up { color: #22c55e; }
    .trend-down { color: #ef4444; }
    .trend-sub { color: #64748b; font-weight: 400; }

    /* Main Chart Grid */
    .dashboard-main-grid { display: grid; grid-template-columns: 1.8fr 1fr; gap: 1.5rem; margin-bottom: 2rem; }
    .chart-card { background: white; padding: 1.5rem; border-radius: 20px; border: 1px solid #f1f5f9; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
    .chart-title { font-size: 1.1rem; font-weight: 700; color: #0f172a; margin-bottom: 2rem; }

    .delivery-stat-list { margin-top: 1.5rem; }
    .delivery-stat-item { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; font-size: 0.85rem; }
    .bullet { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 0.5rem; }
</style>
@endsection

@section('content')
<div class="dashboard-layout">
    @include('partials.sidebar')

    <main class="main-content">
        @include('partials.header')

        <div class="dashboard-header">
            <h1>Dashboard</h1>
            <p>Welcome back! Here's what's happening today.</p>
        </div>

        <!-- 4 Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div style="display: flex; justify-content: space-between;">
                    <div>
                        <p class="stat-label">Total Students</p>
                        <h2 class="stat-value">{{ number_format($totalStudents) }}</h2>
                        <div class="trend-indicator trend-up">
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 15l7-7 7 7"/></svg>
                            <span>{{ number_format($studentTrend, 1) }}% <span class="trend-sub">from last month</span></span>
                        </div>
                    </div>
                    <div class="stat-icon icon-blue">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87m-4-12a4 4 0 010 7.75"/></svg>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div style="display: flex; justify-content: space-between;">
                    <div>
                        <p class="stat-label">Today's Deliveries</p>
                        <h2 class="stat-value">{{ $todayDeliveriesCount }}</h2>
                        <div class="trend-indicator" style="color: #3b82f6;">
                            <span>{{ $completedDeliveries }} completed</span>
                        </div>
                    </div>
                    <div class="stat-icon icon-green">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 011 1v2.5a.5.5 0 01-.5.5H13m4 0h1.5a.5.5 0 00.5-.5V14c0-.35-.07-.71-.22-1.05l-1.12-2.45A1 1 0 0015.75 10H13"/></svg>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div style="display: flex; justify-content: space-between;">
                    <div>
                        <p class="stat-label">Stock Status</p>
                        <h2 class="stat-value" style="color: {{ $stockStatus == 'Warning' ? '#ef4444' : '#0f172a' }}">{{ $stockStatus }}</h2>
                        <div class="trend-indicator trend-down" style="color: #f59e0b;">
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0zM12 9v4m0 4h.01"/></svg>
                            <span>{{ $lowStockCount }} items low</span>
                        </div>
                    </div>
                    <div class="stat-icon icon-orange">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div style="display: flex; justify-content: space-between;">
                    <div>
                        <p class="stat-label">Food Waste</p>
                        <h2 class="stat-value">{{ $wastePercentage }}%</h2>
                        <div class="trend-indicator trend-up" style="color: #22c55e;">
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                            <span>{{ $wasteImprovement }}% <span class="trend-sub">improvement</span></span>
                        </div>
                    </div>
                    <div class="stat-icon icon-red">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="dashboard-main-grid">
            <div class="chart-card">
                <h4 class="chart-title">Nutrition Distribution Trends</h4>
                <div style="height: 350px;">
                    <canvas id="nutritionLineChart"></canvas>
                </div>
            </div>
            <div class="chart-card">
                <h4 class="chart-title">Delivery Status</h4>
                <div style="height: 250px; position: relative; margin-bottom: 2rem;">
                    <canvas id="deliveryPieChart"></canvas>
                </div>
                <div class="delivery-stat-list">
                    @foreach($deliveryStats['labels'] as $index => $label)
                        <div class="delivery-stat-item">
                            <span style="color: #64748b;">
                                <span class="bullet" style="background: {{ $deliveryStats['colors'][$index] }}"></span>
                                {{ $label }}
                            </span>
                            <span style="font-weight: 700; color: #0f172a;">{{ $deliveryStats['data'][$index] }}%</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // 1. Nutrition Distribution Line Chart
    const ctxLine = document.getElementById('nutritionLineChart').getContext('2d');
    new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: {!! json_encode($nutritionTrends['labels']) !!},
            datasets: {!! json_encode(array_map(function($ds) {
                return [
                    'label' => $ds['label'],
                    'data' => $ds['data'],
                    'borderColor' => $ds['color'],
                    'backgroundColor' => $ds['color'] . '1a',
                    'fill' => true,
                    'tension' => 0.4,
                    'pointRadius' => 4,
                    'pointBackgroundColor' => '#fff',
                    'pointBorderWidth' => 2
                ];
            }, $nutritionTrends['datasets'])) !!}
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
            },
            scales: {
                y: { 
                    beginAtZero: true, 
                    grid: { color: '#f1f5f9' },
                    border: { display: false }
                },
                x: { 
                    grid: { display: false },
                    border: { display: false }
                }
            }
        }
    });

    // 2. Delivery Status Pie Chart
    const ctxPie = document.getElementById('deliveryPieChart').getContext('2d');
    new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($deliveryStats['labels']) !!},
            datasets: [{
                data: {!! json_encode($deliveryStats['data']) !!},
                backgroundColor: {!! json_encode($deliveryStats['colors']) !!},
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>
@endsection
