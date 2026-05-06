<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
                $data[] = [
                    'siswa_id' => $siswaId,
                    'berat_badan' => $baseWeight + ($index * 0.5) + (2 - $i) * 0.4, // Naik sedikit tiap bulan
                    'tinggi_badan' => $baseHeight + ($index * 1.5) + (2 - $i) * 0.2, // Naik sedikit tiap bulan
                    'tanggal_ukur' => Carbon::today()->subMonths($i)->format('Y-m-d'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('antropometris')->insert($data);
    }
}
