<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        $sekolahs = DB::table('sekolahs')->pluck('id')->toArray();
        if (empty($sekolahs)) {
            $sekolahs = [1];
        }
        
        $faker = \Faker\Factory::create('id_ID');
        $siswas = [];
        $alergies = [null, null, null, null, 'Kacang', 'Susu Sapi', 'Seafood', 'Telur'];
        $kelasOptions = ['1A', '1B', '2A', '2B', '3A', '3B', '4A', '4B', '5A', '5B', '6A', '6B'];

        for ($i = 0; $i < 60; $i++) {
            $siswas[] = [
                'nisn' => $faker->unique()->numerify('##########'),
                'nama_siswa' => $faker->name(),
                'kelas' => $faker->randomElement($kelasOptions),
                'alergi' => $faker->randomElement($alergies),
                'sekolah_id' => $faker->randomElement($sekolahs),
                'contact' => $faker->phoneNumber(),
                'status' => $faker->randomElement(['Active', 'Active', 'Active', 'Inactive']),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('siswas')->insert($siswas);
    }
}
