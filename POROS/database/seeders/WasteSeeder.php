<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WasteSeeder extends Seeder
{
    public function run(): void
    {
        $sekolahs = DB::table('sekolahs')->pluck('id');
        
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            foreach ($sekolahs as $sekolahId) {
                $data[] = [
                    'sekolah_id' => $sekolahId,
                    'jumlah_waste' => rand(500, 2500) / 100, // 5.00 - 25.00 kg
                    'tanggal' => Carbon::today()->subDays($i),
                    'keterangan' => 'Sisa makanan rutin',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        
        DB::table('plate_wastes')->insert($data);
    }
}
