<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SekolahSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('sekolahs')->insert([
            [
                'nama_sekolah' => 'SDN POROS 01 Jakarta',
                'alamat' => 'Jl. Kebangsaan No. 45, Jakarta Pusat',
                'jumlah_siswa' => 320,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_sekolah' => 'SDN POROS 15 Bandung',
                'alamat' => 'Jl. Merdeka No. 12, Bandung',
                'jumlah_siswa' => 280,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
