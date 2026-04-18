<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BahanBakuSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = DB::table('suppliers')->pluck('id');

        DB::table('bahan_bakus')->insert([
            [
                'nama_bahan' => 'Beras Merah Organik',
                'stok' => 150.5,
                'stok_minimal' => 50.0,
                'satuan' => 'kg',
                'supplier_id' => $suppliers[2] ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_bahan' => 'Daging Ayam Broiler',
                'stok' => 45.0,
                'stok_minimal' => 10.0,
                'satuan' => 'kg',
                'supplier_id' => $suppliers[1] ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_bahan' => 'Bayam Hijau',
                'stok' => 20.0,
                'stok_minimal' => 5.0,
                'satuan' => 'ikat',
                'supplier_id' => $suppliers[0] ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_bahan' => 'Telur Ayam Kampung',
                'stok' => 500,
                'stok_minimal' => 100,
                'satuan' => 'butir',
                'supplier_id' => $suppliers[1] ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
