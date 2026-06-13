<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlateWaste;
use App\Models\BahanBaku;
use App\Models\Siswa;
use App\Models\Antropometri;
use App\Models\Pengiriman;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Shared dashboard for all roles.
     */
    public function index()
    {
        // 1. Total Students & Trend
        $totalStudents = Siswa::count();
        $studentsLastMonth = Siswa::where('created_at', '<', Carbon::now()->startOfMonth())->count();
        $studentTrend = $studentsLastMonth > 0 
            ? (($totalStudents - $studentsLastMonth) / $studentsLastMonth) * 100 
            : 0;

        // 2. Today's Deliveries
        $todayDeliveriesCount = Pengiriman::whereDate('created_at', Carbon::today())->count();
        $completedDeliveries = Pengiriman::whereDate('created_at', Carbon::today())->where('status_kirim', 'Sampai')->count();

        // 3. Stock Status
        $lowStockCount = BahanBaku::whereColumn('stok', '<', 'stok_minimal')->count();
        $stockStatus = $lowStockCount > 3 ? 'Warning' : 'Good';

        // 4. Food Waste (Proxy using feedback from Logistics)
        $totalDeliveries = Pengiriman::count();
        $deliveriesWithFeedback = Pengiriman::whereNotNull('keterangan')->where('keterangan', '<>', '')->count();
        $wastePercentage = $totalDeliveries > 0 ? round(($deliveriesWithFeedback / $totalDeliveries) * 100, 1) : 0;
        
        // Improvement calculation (placeholder for now, comparing to last month's ratio)
        $wasteImprovement = 3.8; 

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
        $waiting = Pengiriman::where('status_kirim', 'Menunggu')->count();
        $transit = Pengiriman::where('status_kirim', 'Jalan')->count();
        $delivered = Pengiriman::where('status_kirim', 'Sampai')->count();
        $totalP = $waiting + $transit + $delivered;
        
        $deliveryStats = [
            'labels' => ['Delivered', 'In Transit', 'Pending'],
            'data' => $totalP > 0 ? [
                round(($delivered / $totalP) * 100),
                round(($transit / $totalP) * 100),
                round(($waiting / $totalP) * 100)
            ] : [0, 0, 0],
            'colors' => ['#10b981', '#3b82f6', '#f59e0b']
        ];

        // 7. Waste Trends (Last 7 Days - using feedback as proxy)
        $wasteTrends = Pengiriman::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_waste')
            )
            ->whereNotNull('keterangan')
            ->where('keterangan', '<>', '')
            ->where('created_at', '>=', Carbon::today()->subDays(6))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        $lowStockItems = BahanBaku::with('supplier')
            ->whereColumn('stok', '<', 'stok_minimal')
            ->limit(5)
            ->get();

        return view('dashboards.index', compact(
            'totalStudents', 'studentTrend', 
            'todayDeliveriesCount', 'completedDeliveries',
            'stockStatus', 'lowStockCount',
            'wastePercentage', 'wasteImprovement',
            'nutritionTrends', 'deliveryStats',
            'wasteTrends', 'lowStockItems'
        ));
    }
}
