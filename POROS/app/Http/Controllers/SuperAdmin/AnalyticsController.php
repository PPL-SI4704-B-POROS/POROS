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

        // PBI-34: hitung biaya belanja bahan baku
        $queryBiaya = BiayaBelanja::query()
            ->join('bahan_bakus', 'biaya_belanja.bahan_baku_id', '=', 'bahan_bakus.id');

        if ($selectedDapur !== 'all') {
            $queryBiaya->where('biaya_belanja.dapur_id', $selectedDapur);
        }

        if ($startDate && $endDate) {
            $queryBiaya->whereBetween('biaya_belanja.tanggal_belanja', [$startDate, $endDate]);
        }

        $biayaData = (clone $queryBiaya)->select('bahan_bakus.nama_bahan', DB::raw('SUM(total_harga) as total'))
            ->groupBy('bahan_bakus.nama_bahan')
            ->get();

        // ambil data mentah buat dikelompokkin bulanan di memori (biar database-agnostic)
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

        // nyari top 3 supplier dengan total belanjaan paling gede
        $querySupplier = BiayaBelanja::query()
            ->join('suppliers', 'biaya_belanja.supplier_id', '=', 'suppliers.id');

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

        // PBI-35: hitung tren berat badan & tinggi badan
        $queryAntropometri = Antropometri::query()
            ->join('siswas', 'antropometris.siswa_id', '=', 'siswas.id')
            ->whereNull('siswas.deleted_at');

        if ($selectedSekolah !== 'all') {
            $queryAntropometri->where('siswas.sekolah_id', $selectedSekolah);
        }
        if ($startDate && $endDate) {
            $queryAntropometri->whereBetween('antropometris.tanggal_ukur', [$startDate, $endDate]);
        }

        // ambil data mentah dulu terus dikelompokkin per bulan biar tren pertumbuhannya mulus
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

        // status gizi scorecard: dihitung dinamis dari data pengukuran terbaru per siswa
        $latestGiziQuery = (clone $queryAntropometri)->whereIn('antropometris.id', function ($query) {
            $query->select(DB::raw('MAX(id)'))
                ->from('antropometris')
                ->whereNull('deleted_at')
                ->groupBy('siswa_id');
        });

        $giziNormal = (clone $latestGiziQuery)->whereIn('antropometris.status_gizi', ['Normal', 'Baik'])->count();
        $giziKurang = (clone $latestGiziQuery)->whereIn('antropometris.status_gizi', ['Kurus', 'Kurang'])->count();
        $giziLebih = (clone $latestGiziQuery)->whereIn('antropometris.status_gizi', ['Gemuk', 'Obesitas'])->count();

        // PBI-36: hitung sisa makanan (food waste) yang udah sinkron sama bagian logistik
        $queryPengiriman = Pengiriman::query();
        if ($selectedSekolah !== 'all') {
            $queryPengiriman->where('sekolah_id', $selectedSekolah);
        }
        if ($startDate && $endDate) {
            $queryPengiriman->whereBetween('tanggal_sisa', [$startDate, $endDate]);
        }

        // query data sisa makanan per kategori dari tabel plate_wastes
        $queryPlateWaste = PlateWaste::query();
        if ($selectedSekolah !== 'all') {
            $queryPlateWaste->where('sekolah_id', $selectedSekolah);
        }
        if ($startDate && $endDate) {
            $queryPlateWaste->whereBetween('tanggal', [$startDate, $endDate]);
        }

        // hitung total sisa porsi dikelompokkin berdasarkan alasan sisanya
        $wasteData = $queryPlateWaste->select(
            'keterangan',
            DB::raw('SUM(jumlah_waste) as total_porsi')
        )
            ->whereNotNull('keterangan')
            ->where('keterangan', '<>', '')
            ->groupBy('keterangan')
            ->get()
            ->map(function ($item) {
                // map total_porsi ke total_kg biar grafik di frontend tetep kebaca dengan baik
                $item->total_kg = (float) ($item->total_porsi ?? 0);

                return $item;
            }) ?? collect();

        // total sampah makanan dikelompokkin pake basis total porsi
        $totalWasteKg = $wasteData->sum('total_kg') ?? 0;

        // cari top 3 menu makanan yang paling sering sisa berdasarkan input logistik
        $topMenus = (clone $queryPengiriman)
            ->select('menu_tersisa as nama_menu', DB::raw('SUM(jumlah_sisa_ompreng) as total_waste'))
            ->whereNotNull('menu_tersisa')
            ->where('menu_tersisa', '<>', '')
            ->groupBy('menu_tersisa')
            ->orderByDesc('total_waste')
            ->limit(3)
            ->get() ?? collect();

        // ambil opsi data buat ditaruh di dropdown filter
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
