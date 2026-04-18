<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        $sekolahs = DB::table('sekolahs')->pluck('id');

        DB::table('siswas')->insert([
            [
                'nisn' => '0123456789',
                'nama_siswa' => 'Rizky Pratama',
                'kelas' => '6A',
                'sekolah_id' => $sekolahs[0] ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nisn' => '0123456790',
                'nama_siswa' => 'Anisa Rahma',
                'kelas' => '6B',
                'sekolah_id' => $sekolahs[0] ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nisn' => '0123456791',
                'nama_siswa' => 'Bintang Ramadhan',
                'kelas' => '5C',
                'sekolah_id' => $sekolahs[1] ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
