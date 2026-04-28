<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('suppliers')->insert([
            [
                'nama_supplier' => 'Kebun Tani Organik Jaya',
                'alamat' => 'Kabupaten Bogor, Jawa Barat',
                'kontak' => '0812-9988-7766',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_supplier' => 'Sumber Protein Sejahtera',
                'alamat' => 'Cianjur, Jawa Barat',
                'kontak' => '0813-1122-3344',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_supplier' => 'Beras Organik Mulyo',
                'alamat' => 'Klaten, Jawa Tengah',
                'kontak' => '0857-4455-6677',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
