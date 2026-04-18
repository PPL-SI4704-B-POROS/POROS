<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlateWaste;
use App\Models\BahanBaku;
use App\Models\Siswa;
use App\Models\Antropometri;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function superadmin()
    {
        // 1. Total Students & Trend
        $totalStudents = Siswa::count();
        $studentsLastMonth = Siswa::where('created_at', '<', Carbon::now()->startOfMonth())->count();
        $studentTrend = $studentsLastMonth > 0 
            ? (($totalStudents - $studentsLastMonth) / $studentsLastMonth) * 100 
            : 0;

        // 2. Today's Deliveries (Placeholder until Pengiriman seeded)
        $todayDeliveriesCount = 87; // Mock for visual
        $completedDeliveries = 65; // Mock for visual

        // 3. Stock Status
        $lowStockCount = BahanBaku::whereColumn('stok', '<', 'stok_minimal')->count();
        $stockStatus = $lowStockCount > 3 ? 'Warning' : 'Good';

        // 4. Food Waste % & Improvement
        $avgWasteThisWeek = PlateWaste::where('tanggal', '>=', Carbon::now()->startOfWeek())->avg('jumlah_waste') ?? 0;
        $avgWasteLastWeek = PlateWaste::whereBetween('tanggal', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()])->avg('jumlah_waste') ?? 8;
        $wastePercentage = 6.2; // Mock for visual
        $wasteImprovement = 3.8; // Mock for visual

        // 5. Nutrition Trends (Mock multi-series for visual)
        $nutritionTrends = [
            'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            'datasets' => [
                ['label' => 'Protein', 'data' => [65, 68, 70, 72, 75, 78, 82], 'color' => '#10b981'],
                ['label' => 'Carbs', 'data' => [45, 48, 52, 55, 58, 60, 62], 'color' => '#3b82f6'],
                ['label' => 'Fat', 'data' => [25, 28, 30, 32, 35, 38, 40], 'color' => '#f59e0b'],
            ]
        ];

        // 6. Delivery Status (Pie Chart)
        $deliveryStats = [
            'labels' => ['Delivered', 'In Transit', 'Pending', 'Failed'],
            'data' => [45, 28, 15, 12],
            'colors' => ['#10b981', '#3b82f6', '#f59e0b', '#ef4444']
        ];

        // 7. Waste Trends (Last 7 Days)
        $wasteTrends = PlateWaste::select(
                DB::raw('DATE(tanggal) as date'),
                DB::raw('SUM(jumlah_waste) as total_waste')
            )
            ->where('tanggal', '>=', Carbon::today()->subDays(6))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        $lowStockItems = BahanBaku::with('supplier')
            ->whereColumn('stok', '<', 'stok_minimal')
            ->limit(5)
            ->get();

        return view('dashboards.superadmin', compact(
            'totalStudents', 'studentTrend', 
            'todayDeliveriesCount', 'completedDeliveries',
            'stockStatus', 'lowStockCount',
            'wastePercentage', 'wasteImprovement',
            'nutritionTrends', 'deliveryStats',
            'wasteTrends', 'lowStockItems'
        ));
    }

    public function dapur() { return $this->superadmin(); } // Simplifying for now
    public function sekolah() { return $this->superadmin(); } // Simplifying for now
}
