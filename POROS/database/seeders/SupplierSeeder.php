<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('suppliers')->insert([
            [
                'nama_supplier' => 'Toko Makmur Jaya',
                'no_hp' => '081234567890',
                'gmail' => 'tokomakmurjaya@contoh.com',
                'alamat' => 'Perumahan ABC No. 123',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama_supplier' => 'Toko Sayur Berkah',
                'no_hp' => '085798765432',
                'gmail' => 'tokosayurberkah@contoh.com',
                'alamat' => 'Perumahan ABC No. 456',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}