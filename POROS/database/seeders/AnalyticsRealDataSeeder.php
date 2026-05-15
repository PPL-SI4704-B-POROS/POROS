<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsRealDataSeeder extends Seeder
{
    public function run(): void
    {
        // ---------------------------------------------------------
        // 1. BIAYA BELANJA (PBI-34)
        // ---------------------------------------------------------
        
        $supplierIds = DB::table('suppliers')->pluck('id')->toArray();
        if (empty($supplierIds)) {
            $suppliers = [
                ['nama_supplier' => 'CV Makmur Pangan', 'alamat' => 'Jakarta', 'kontak' => '081'],
                ['nama_supplier' => 'PT Agro Nusantara', 'alamat' => 'Bogor', 'kontak' => '082'],
                ['nama_supplier' => 'Toko Bintang', 'alamat' => 'Depok', 'kontak' => '083'],
            ];
            foreach ($suppliers as $sup) {
                $supplierIds[] = DB::table('suppliers')->insertGetId(array_merge($sup, ['created_at' => now(), 'updated_at' => now()]));
            }
        }

        $bahanIds = DB::table('bahan_bakus')->pluck('id')->toArray();
        if (empty($bahanIds)) {
            $bahanIds[] = DB::table('bahan_bakus')->insertGetId(['nama_bahan' => 'Beras', 'stok' => 10, 'satuan' => 'kg', 'created_at' => now(), 'updated_at' => now()]);
        }

        // Limit bahan to a few so the chart looks good (not 1000 items)
        $selectedBahanIds = array_slice((array) $bahanIds, 0, 8);

        $biayaData = [];
        for ($i = 0; $i < 30; $i++) {
            $date = Carbon::now()->subDays(30 - $i)->format('Y-m-d');
            for ($j = 0; $j < rand(3, 6); $j++) {
                $bahan_id = $selectedBahanIds[array_rand($selectedBahanIds)];
                $biayaData[] = [
                    'bahan_baku_id' => $bahan_id,
                    'supplier_id' => $supplierIds[array_rand($supplierIds)],
                    'jumlah_beli' => rand(10, 50),
                    'total_harga' => rand(100000, 1000000),
                    'tanggal_belanja' => $date,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }
        DB::table('biaya_belanja')->insert($biayaData);

        // ---------------------------------------------------------
        // 2. TREN GIZI BB/TB (PBI-35)
        // ---------------------------------------------------------
        $siswaIds = DB::table('siswas')->pluck('id')->toArray();
        
        if (empty($siswaIds)) {
            $sekolahId = DB::table('sekolahs')->first()->id ?? DB::table('sekolahs')->insertGetId(['nama_sekolah' => 'SDN 01', 'created_at' => now(), 'updated_at' => now()]);
            for ($i=0; $i<15; $i++) {
                $siswaIds[] = DB::table('siswas')->insertGetId([
                    'nama_siswa' => 'Siswa ' . $i,
                    'nisn' => '1000' . $i,
                    'sekolah_id' => $sekolahId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        $antropometriData = [];
        // Just take 15 siswas to generate trend
        $selectedSiswas = array_slice($siswaIds, 0, 15);
        foreach ($selectedSiswas as $idx => $siswaId) {
            $isKurangGizi = $idx < 2; 
            $startBB = $isKurangGizi ? rand(160, 190) / 10 : rand(220, 350) / 10;
            $startTB = $isKurangGizi ? rand(110, 120) : rand(125, 140);

            for ($m = 0; $m <= 6; $m++) { // 6 bulan terakhir
                $date = Carbon::now()->subMonths(6 - $m)->startOfMonth()->format('Y-m-d');
                $antropometriData[] = [
                    'siswa_id' => $siswaId,
                    'berat_badan' => $startBB + ($m * 0.2), // Naik BB tiap bulan
                    'tinggi_badan' => $startTB + ($m * 0.5), // Naik TB tiap bulan
                    'imt' => 0,
                    'status_gizi' => $isKurangGizi ? 'Kurang' : 'Baik',
                    'tanggal_ukur' => $date,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }
        DB::table('antropometris')->insert($antropometriData);

        // ---------------------------------------------------------
        // 3. PLATE WASTE (PBI-36)
        // ---------------------------------------------------------
        $alasanWaste = [
            'Rasa tidak enak' => 35,
            'Porsi kebanyakan' => 30,
            'Menu ga menarik' => 20,
            'Siswa sedang sakit' => 10,
            'Kurang matang' => 5
        ];

        $menuIds = DB::table('menus')->pluck('id')->toArray();
        if (empty($menuIds)) {
            $menus = ['Nasi Ayam', 'Sayur Sop', 'Ikan Balado'];
            foreach ($menus as $menu) {
                $menuIds[] = DB::table('menus')->insertGetId([
                    'nama_menu' => $menu, 'total_kalori' => 500, 'total_protein' => 20, 
                    'total_karbohidrat' => 60, 'total_lemak' => 15, 'created_at' => now(), 'updated_at' => now()
                ]);
            }
        }

        $sekolahId = DB::table('sekolahs')->first()->id ?? DB::table('sekolahs')->insertGetId(['nama_sekolah' => 'SDN 1', 'created_at' => now(), 'updated_at' => now()]);
        $kurirId = DB::table('kurirs')->first()->id ?? DB::table('kurirs')->insertGetId(['nama_kurir' => 'Kurir Cepat', 'no_plat' => 'B 1234 CD', 'kontak' => '081234', 'created_at' => now(), 'updated_at' => now()]);

        $plateWasteData = [];
        for ($i = 0; $i < 40; $i++) {
            $date = Carbon::now()->subDays(rand(1, 30))->format('Y-m-d');
            $menuId = $menuIds[array_rand($menuIds)];
            
            $produksiId = DB::table('produksi_harians')->insertGetId([
                'tanggal_produksi' => $date,
                'total_target_porsi' => 100,
                'status_produksi' => 'Siap Kirim',
                'menu_id' => $menuId,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $pengirimanId = DB::table('pengirimans')->insertGetId([
                'waktu_berangkat' => Carbon::parse($date)->setHour(8),
                'waktu_sampai' => Carbon::parse($date)->setHour(9),
                'nama_penerima' => 'Bapak Budi',
                'status_kirim' => 'Sampai',
                'produksi_id' => $produksiId,
                'sekolah_id' => $sekolahId,
                'kurir_id' => $kurirId,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $rand = rand(1, 100);
            $cumulative = 0;
            $selectedReason = 'Lainnya';
            foreach ($alasanWaste as $reason => $chance) {
                $cumulative += $chance;
                if ($rand <= $cumulative) {
                    $selectedReason = $reason;
                    break;
                }
            }

            $plateWasteData[] = [
                'jumlah_waste' => rand(10, 50) / 10,
                'tanggal' => $date,
                'keterangan' => $selectedReason,
                'sekolah_id' => $sekolahId,
                'pengiriman_id' => $pengirimanId,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        DB::table('plate_wastes')->insert($plateWasteData);
    }
}
