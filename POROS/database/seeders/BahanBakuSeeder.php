<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BahanBakuSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = DB::table('suppliers')->pluck('id');

        Schema::disableForeignKeyConstraints();
        DB::table('bahan_bakus')->truncate();
        Schema::enableForeignKeyConstraints();

        DB::table('bahan_bakus')->insert([
            [
                'nama_bahan' => 'Beras Merah Organik',
                'stok' => 150500,        // 150.5 kg → gram
                'stok_minimal' => 50000, // 50 kg → gram
                'satuan' => 'gram',
                'energi_per_100g' => 350.00,
                'protein_per_100g' => 7.50,
                'karbohidrat_per_100g' => 76.00,
                'lemak_per_100g' => 2.50,
                'supplier_id' => $suppliers[2] ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_bahan' => 'Daging Ayam Broiler',
                'stok' => 45000,         // 45 kg → gram
                'stok_minimal' => 10000, // 10 kg → gram
                'satuan' => 'gram',
                'energi_per_100g' => 239.00,
                'protein_per_100g' => 27.00,
                'karbohidrat_per_100g' => 0.00,
                'lemak_per_100g' => 14.00,
                'supplier_id' => $suppliers[1] ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_bahan' => 'Bayam Hijau',
                'stok' => 5000,          // 20 ikat × 250g → gram
                'stok_minimal' => 1250,  // 5 ikat × 250g → gram
                'satuan' => 'gram',
                'energi_per_100g' => 23.00,
                'protein_per_100g' => 2.90,
                'karbohidrat_per_100g' => 3.60,
                'lemak_per_100g' => 0.40,
                'supplier_id' => $suppliers[0] ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_bahan' => 'Telur Ayam Kampung',
                'stok' => 30000,         // 500 butir × 60g → gram
                'stok_minimal' => 6000,  // 100 butir × 60g → gram
                'satuan' => 'gram',
                'energi_per_100g' => 155.00,
                'protein_per_100g' => 13.00,
                'karbohidrat_per_100g' => 1.10,
                'lemak_per_100g' => 11.00,
                'supplier_id' => $suppliers[1] ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_bahan' => 'Tempe Kedelai',
                'stok' => 30000, 'stok_minimal' => 8000, 'satuan' => 'gram',
                'energi_per_100g' => 201.00, 'protein_per_100g' => 20.80,
                'karbohidrat_per_100g' => 13.50, 'lemak_per_100g' => 8.80,
                'supplier_id' => $suppliers[0] ?? 1, 'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'nama_bahan' => 'Tahu Putih',
                'stok' => 25000, 'stok_minimal' => 5000, 'satuan' => 'gram',
                'energi_per_100g' => 68.00, 'protein_per_100g' => 7.80,
                'karbohidrat_per_100g' => 1.60, 'lemak_per_100g' => 4.60,
                'supplier_id' => $suppliers[0] ?? 1, 'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'nama_bahan' => 'Ikan Tongkol',
                'stok' => 35000, 'stok_minimal' => 10000, 'satuan' => 'gram',
                'energi_per_100g' => 117.00, 'protein_per_100g' => 25.00,
                'karbohidrat_per_100g' => 0.00, 'lemak_per_100g' => 1.50,
                'supplier_id' => $suppliers[1] ?? 1, 'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'nama_bahan' => 'Wortel',
                'stok' => 15000, 'stok_minimal' => 3000, 'satuan' => 'gram',
                'energi_per_100g' => 42.00, 'protein_per_100g' => 1.20,
                'karbohidrat_per_100g' => 9.30, 'lemak_per_100g' => 0.30,
                'supplier_id' => $suppliers[0] ?? 1, 'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'nama_bahan' => 'Kentang',
                'stok' => 40000, 'stok_minimal' => 10000, 'satuan' => 'gram',
                'energi_per_100g' => 62.00, 'protein_per_100g' => 2.10,
                'karbohidrat_per_100g' => 13.50, 'lemak_per_100g' => 0.20,
                'supplier_id' => $suppliers[0] ?? 1, 'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'nama_bahan' => 'Kangkung',
                'stok' => 2500, 'stok_minimal' => 750, 'satuan' => 'gram',
                'energi_per_100g' => 29.00, 'protein_per_100g' => 3.00,
                'karbohidrat_per_100g' => 5.40, 'lemak_per_100g' => 0.30,
                'supplier_id' => $suppliers[0] ?? 1, 'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'nama_bahan' => 'Ikan Lele',
                'stok' => 30000, 'stok_minimal' => 8000, 'satuan' => 'gram',
                'energi_per_100g' => 96.00, 'protein_per_100g' => 17.70,
                'karbohidrat_per_100g' => 0.00, 'lemak_per_100g' => 2.80,
                'supplier_id' => $suppliers[1] ?? 1, 'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'nama_bahan' => 'Daging Sapi',
                'stok' => 20000, 'stok_minimal' => 5000, 'satuan' => 'gram',
                'energi_per_100g' => 207.00, 'protein_per_100g' => 26.40,
                'karbohidrat_per_100g' => 0.00, 'lemak_per_100g' => 11.00,
                'supplier_id' => $suppliers[1] ?? 1, 'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'nama_bahan' => 'Minyak Goreng',
                'stok' => 45000, 'stok_minimal' => 9000, 'satuan' => 'gram',
                'energi_per_100g' => 884.00, 'protein_per_100g' => 0.00,
                'karbohidrat_per_100g' => 0.00, 'lemak_per_100g' => 100.00,
                'supplier_id' => $suppliers[2] ?? 1, 'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'nama_bahan' => 'Kacang Hijau',
                'stok' => 15000, 'stok_minimal' => 5000, 'satuan' => 'gram',
                'energi_per_100g' => 323.00, 'protein_per_100g' => 22.20,
                'karbohidrat_per_100g' => 56.80, 'lemak_per_100g' => 1.20,
                'supplier_id' => $suppliers[2] ?? 1, 'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'nama_bahan' => 'Susu UHT',
                'stok' => 206000, 'stok_minimal' => 51500, 'satuan' => 'gram',
                'energi_per_100g' => 61.00, 'protein_per_100g' => 3.20,
                'karbohidrat_per_100g' => 4.80, 'lemak_per_100g' => 3.50,
                'supplier_id' => $suppliers[2] ?? 1, 'created_at' => now(), 'updated_at' => now(),
            ],
        ]);
    }
}
