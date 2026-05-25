<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Antropometri;
use App\Models\BiayaBelanja;
use App\Models\PlateWaste;
use App\Models\Sekolah;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $selectedDapur = $request->get('dapur', 'all');
        $selectedSekolah = $request->get('sekolah_id', 'all');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // --- PBI-34: Logic Biaya Belanja ---
        $queryBiaya = BiayaBelanja::query()
            ->join('bahan_bakus', 'biaya_belanja.bahan_baku_id', '=', 'bahan_bakus.id');

        // Unit Dapur filter is not applicable to biaya_belanja as there is no user_id column

        if ($startDate && $endDate) {
            $queryBiaya->whereBetween('biaya_belanja.tanggal_belanja', [$startDate, $endDate]);
        }

        $biayaData = (clone $queryBiaya)->select('bahan_bakus.nama_bahan', DB::raw('SUM(total_harga) as total'))
            ->groupBy('bahan_bakus.nama_bahan')
            ->get();

        // Ambil data raw untuk grouping bulanan di memory (database agnostic)
        $rawBiaya = (clone $queryBiaya)
            ->select('biaya_belanja.tanggal_belanja', 'bahan_bakus.nama_bahan', 'biaya_belanja.total_harga')
            ->get();
        $groupedRaw = $rawBiaya->groupBy(function ($item) {
            return Carbon::parse($item->tanggal_belanja)->format('Y-m');
        })->sortKeys();

        $biayaBulanan = $groupedRaw->mapWithKeys(function ($group, $key) {
            $monthName = Carbon::createFromFormat('Y-m', $key)->format('M Y');

            return [$monthName => $group->sum('total_harga')];
        });

        $biayaDetailBulanan = $groupedRaw->mapWithKeys(function ($group, $key) {
            $monthName = Carbon::createFromFormat('Y-m', $key)->format('M Y');
            $detail = $group->groupBy('nama_bahan')->map(function ($subGroup) {
                return $subGroup->sum('total_harga');
            });

            return [$monthName => $detail];
        });

        // Top 3 Supplier
        $querySupplier = BiayaBelanja::query()
            ->join('suppliers', 'biaya_belanja.supplier_id', '=', 'suppliers.id');

        // Unit Dapur filter is not applicable to biaya_belanja as there is no user_id column
        if ($startDate && $endDate) {
            $querySupplier->whereBetween('biaya_belanja.tanggal_belanja', [$startDate, $endDate]);
        }

        $topSuppliers = $querySupplier->select('suppliers.nama_supplier', DB::raw('SUM(biaya_belanja.total_harga) as total'))
            ->groupBy('suppliers.nama_supplier')
            ->orderByDesc('total')
            ->limit(3)
            ->get() ?? collect();

        // --- PBI-35: Logic Tren BB/TB ---
        $queryAntropometri = Antropometri::query()
            ->join('siswas', 'antropometris.siswa_id', '=', 'siswas.id');

        if ($selectedSekolah !== 'all') {
            $queryAntropometri->where('siswas.sekolah_id', $selectedSekolah);
        }
        if ($startDate && $endDate) {
            $queryAntropometri->whereBetween('antropometris.tanggal_ukur', [$startDate, $endDate]);
        }

        $trendGizi = $queryAntropometri->select(
            'antropometris.tanggal_ukur',
            DB::raw('AVG(antropometris.berat_badan) as avg_bb'),
            DB::raw('AVG(antropometris.tinggi_badan) as avg_tb')
        )
            ->groupBy('antropometris.tanggal_ukur')
            ->orderBy('antropometris.tanggal_ukur', 'asc')
            ->get() ?? collect();

        // Status Gizi Scorecard
        $giziBaik = (clone $queryAntropometri)->where('antropometris.berat_badan', '>=', 20)->count();
        $giziKurang = (clone $queryAntropometri)->where('antropometris.berat_badan', '<', 20)->count();

        // --- PBI-36: Logic Waste ---
        $queryWaste = PlateWaste::query();
        if ($selectedSekolah !== 'all') {
            $queryWaste->where('sekolah_id', $selectedSekolah);
        }
        if ($startDate && $endDate) {
            $queryWaste->whereBetween('tanggal', [$startDate, $endDate]);
        }

        $wasteData = (clone $queryWaste)->select('keterangan', DB::raw('COUNT(*) as total'))
            ->whereNotNull('keterangan')
            ->groupBy('keterangan')
            ->get() ?? collect();

        // Top 3 Waste Menu
        $topWasteMenus = PlateWaste::query()
            ->join('pengirimans', 'plate_wastes.pengiriman_id', '=', 'pengirimans.id')
            ->join('produksi_harians', 'pengirimans.produksi_id', '=', 'produksi_harians.id')
            ->join('menus', 'produksi_harians.menu_id', '=', 'menus.id');

        if ($selectedSekolah !== 'all') {
            $topWasteMenus->where('plate_wastes.sekolah_id', $selectedSekolah);
        }
        if ($startDate && $endDate) {
            $topWasteMenus->whereBetween('plate_wastes.tanggal', [$startDate, $endDate]);
        }

        $topMenus = $topWasteMenus->select('menus.nama_menu', DB::raw('COUNT(*) as frekuensi'))
            ->groupBy('menus.nama_menu')
            ->orderByDesc('frekuensi')
            ->limit(3)
            ->get() ?? collect();

        // --- DATA UNTUK FILTER ---
        $daftarDapur = User::whereHas('role', function ($q) {
            $q->where('nama_role', 'dapur');
        })->get() ?? collect();

        $daftarSekolah = Sekolah::all() ?? collect();

        return view('dashboards.superadmin.analytics', compact(
            'biayaData', 'biayaBulanan', 'biayaDetailBulanan', 'topSuppliers',
            'trendGizi', 'giziBaik', 'giziKurang',
            'wasteData', 'topMenus',
            'daftarDapur', 'daftarSekolah'
        ));
    }
}
