<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Antropometri;
use App\Models\BiayaBelanja;
use App\Models\Pengiriman;
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
            ->join('bahan_bakus', 'biaya_belanja.bahan_baku_id', '=', 'bahan_bakus.id')
            ->whereNull('bahan_bakus.deleted_at');

        if ($selectedDapur !== 'all') {
            $queryBiaya->where('biaya_belanja.dapur_id', $selectedDapur);
        }

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
            ->join('suppliers', 'biaya_belanja.supplier_id', '=', 'suppliers.id')
            ->whereNull('suppliers.deleted_at');

        if ($selectedDapur !== 'all') {
            $querySupplier->where('biaya_belanja.dapur_id', $selectedDapur);
        }

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
            ->join('siswas', 'antropometris.siswa_id', '=', 'siswas.id')
            ->whereNull('siswas.deleted_at');

        if ($selectedSekolah !== 'all') {
            $queryAntropometri->where('siswas.sekolah_id', $selectedSekolah);
        }
        if ($startDate && $endDate) {
            $queryAntropometri->whereBetween('antropometris.tanggal_ukur', [$startDate, $endDate]);
        }

        // Fetch raw records to group by month in memory for a smooth monthly growth trend
        $rawAntropometri = (clone $queryAntropometri)
            ->select('antropometris.tanggal_ukur', 'antropometris.berat_badan', 'antropometris.tinggi_badan')
            ->get();

        $groupedAntropometri = $rawAntropometri->groupBy(function ($item) {
            return Carbon::parse($item->tanggal_ukur)->format('Y-m');
        })->sortKeys();

        $trendGizi = $groupedAntropometri->map(function ($group, $key) {
            return (object) [
                'tanggal_ukur' => Carbon::createFromFormat('Y-m', $key)->startOfMonth()->format('Y-m-d'),
                'avg_bb' => round($group->avg('berat_badan'), 2),
                'avg_tb' => round($group->avg('tinggi_badan'), 2),
            ];
        })->values();

        // Status Gizi Scorecard (Dinamis dari database berdasarkan IMT dan status gizi riil)
        $giziNormal = (clone $queryAntropometri)->whereIn('antropometris.status_gizi', ['Normal', 'Baik'])->count();
        $giziKurang = (clone $queryAntropometri)->whereIn('antropometris.status_gizi', ['Kurus', 'Kurang'])->count();
        $giziLebih = (clone $queryAntropometri)->whereIn('antropometris.status_gizi', ['Gemuk', 'Obesitas'])->count();

        // --- PBI-36: Logic Waste (Synchronized with Logistics) ---
        $queryPengiriman = Pengiriman::query();
        if ($selectedSekolah !== 'all') {
            $queryPengiriman->where('sekolah_id', $selectedSekolah);
        }
        if ($startDate && $endDate) {
            $queryPengiriman->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Query dari tabel plate_wastes untuk rincian per kategori
        $queryPlateWaste = PlateWaste::query();
        if ($selectedSekolah !== 'all') {
            $queryPlateWaste->where('sekolah_id', $selectedSekolah);
        }
        if ($startDate && $endDate) {
            $queryPlateWaste->whereBetween('tanggal', [$startDate, $endDate]);
        }

        // Hitung total sisa porsi per kategori alasan dari plate_wastes
        $wasteData = $queryPlateWaste->select(
            'keterangan',
            DB::raw('SUM(jumlah_waste) as total_porsi')
        )
            ->whereNotNull('keterangan')
            ->where('keterangan', '<>', '')
            ->groupBy('keterangan')
            ->get()
            ->map(function ($item) {
                // Map total_porsi to total_kg to keep frontend chart compatibility
                $item->total_kg = (float) ($item->total_porsi ?? 0);

                return $item;
            }) ?? collect();

        // Total akumulasi sampah makanan (menggunakan total porsi sebagai basis)
        $totalWasteKg = $wasteData->sum('total_kg') ?? 0;

        // Top 3 Waste Menu berdasarkan input menu tersisa di logistik
        $topMenus = (clone $queryPengiriman)
            ->select('menu_tersisa as nama_menu', DB::raw('SUM(jumlah_sisa_ompreng) as total_waste'))
            ->whereNotNull('menu_tersisa')
            ->where('menu_tersisa', '<>', '')
            ->groupBy('menu_tersisa')
            ->orderByDesc('total_waste')
            ->limit(3)
            ->get() ?? collect();

        // --- DATA UNTUK FILTER ---
        $daftarDapur = User::whereHas('role', function ($q) {
            $q->where('nama_role', 'dapur');
        })->get() ?? collect();

        $daftarSekolah = Sekolah::all() ?? collect();

        return view('dashboards.superadmin.analytics', compact(
            'biayaData', 'biayaBulanan', 'biayaDetailBulanan', 'topSuppliers',
            'trendGizi', 'giziNormal', 'giziKurang', 'giziLebih',
            'wasteData', 'totalWasteKg', 'topMenus',
            'daftarDapur', 'daftarSekolah'
        ));
    }
}
