<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AntropometriSeeder extends Seeder
{
    public function run(): void
    {
        $siswaIds = DB::table('siswas')->pluck('id');

        $data = [];
        foreach ($siswaIds as $index => $siswaId) {
            // Data 3 bulan terakhir untuk setiap siswa
            $baseWeight = rand(35, 50);
            $baseHeight = rand(140, 160);

            for ($i = 2; $i >= 0; $i--) {
                $bb = $baseWeight + ($index * 0.5) + (2 - $i) * 0.4;
                $tb = $baseHeight + ($index * 1.5) + (2 - $i) * 0.2;
                $imt = $bb / (($tb / 100) ** 2);

                $status_gizi = 'Normal';
                if ($imt < 18.5) {
                    $status_gizi = 'Kurus';
                } elseif ($imt >= 25 && $imt < 30) {
                    $status_gizi = 'Gemuk';
                } elseif ($imt >= 30) {
                    $status_gizi = 'Obesitas';
                }

                $data[] = [
                    'siswa_id' => $siswaId,
                    'berat_badan' => $bb,
                    'tinggi_badan' => $tb,
                    'imt' => round($imt, 2),
                    'status_gizi' => $status_gizi,
                    'tanggal_ukur' => Carbon::today()->subMonths($i)->format('Y-m-d'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('antropometris')->insert($data);
    }
}
