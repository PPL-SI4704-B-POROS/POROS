<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('materials')->insert([
            [
                'supplier_id' => 1,
                'nama_bahan' => 'Ayam',
                'nama_satuan' => 'Ekor',
                'jumlah_satuan' => 1,
                'harga' => 35000,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'supplier_id' => 1,
                'nama_bahan' => 'Nasi',
                'nama_satuan' => 'Kilogram',
                'jumlah_satuan' => 1,
                'harga' => 70000,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'supplier_id' => 2,
                'nama_bahan' => 'Selada',
                'nama_satuan' => 'Kilogram',
                'jumlah_satuan' => 1,
                'harga' => 20000,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}