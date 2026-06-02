<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            SekolahSeeder::class,
            SiswaSeeder::class,
            AntropometriSeeder::class,
            UserSeeder::class,
            SupplierSeeder::class,
            KatalogPanganSeeder::class,
            BahanBakuSeeder::class,
            MenuSeeder::class,
            WasteSeeder::class,
            AnalyticsRealDataSeeder::class,
        ]);
    }
}
