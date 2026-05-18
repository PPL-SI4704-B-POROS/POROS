<?php

namespace Database\Seeders;

use App\Models\BahanBaku;
use App\Models\FormHarga;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FormHargaSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('form_hargas')->truncate();
        Schema::enableForeignKeyConstraints();

        $bahanBakus = BahanBaku::all();
        $prices = [];

        foreach ($bahanBakus as $bahan) {
            $nama = strtolower($bahan->nama_bahan);
            
            // Default: Harga grosir per kg
            $hargaBase = 15000.0; 
            $satuanHarga = 'kg';

            if (str_contains($nama, 'sapi')) {
                $hargaBase = 120000.0; // Rp 120.000 per kg
                $satuanHarga = 'kg';
            } elseif (str_contains($nama, 'ayam')) {
                $hargaBase = 45000.0; // Rp 45.000 per kg
                $satuanHarga = 'kg';
            } elseif (str_contains($nama, 'ikan') || str_contains($nama, 'lele') || str_contains($nama, 'bandeng') || str_contains($nama, 'kakap') || str_contains($nama, 'tuna')) {
                $hargaBase = 35000.0; // Rp 35.000 per kg
                $satuanHarga = 'kg';
            } elseif (str_contains($nama, 'beras')) {
                $hargaBase = 16000.0; // Rp 16.000 per kg
                $satuanHarga = 'kg';
            } elseif (str_contains($nama, 'tahu') || str_contains($nama, 'tempe')) {
                $hargaBase = 10000.0; // Rp 10.000 per kg
                $satuanHarga = 'kg';
            } elseif (str_contains($nama, 'telur')) {
                $hargaBase = 2000.0; // Rp 2.000 per butir
                $satuanHarga = 'butir';
            } elseif (str_contains($nama, 'bayam') || str_contains($nama, 'kangkung') || str_contains($nama, 'kubis') || str_contains($nama, 'sawi')) {
                $hargaBase = 8000.0; // Rp 8.000 per kg
                $satuanHarga = 'kg';
            } elseif (str_contains($nama, 'wortel') || str_contains($nama, 'kentang') || str_contains($nama, 'jagung') || str_contains($nama, 'tomat')) {
                $hargaBase = 15000.0; // Rp 15.000 per kg
                $satuanHarga = 'kg';
            } elseif (str_contains($nama, 'brokoli') || str_contains($nama, 'kembang kol')) {
                $hargaBase = 25000.0; // Rp 25.000 per kg
                $satuanHarga = 'kg';
            } elseif (str_contains($nama, 'bawang') || str_contains($nama, 'cabai') || str_contains($nama, 'kemiri') || str_contains($nama, 'jahe')) {
                $hargaBase = 40000.0; // Rp 40.000 per kg
                $satuanHarga = 'kg';
            } elseif (str_contains($nama, 'minyak') || str_contains($nama, 'susu') || str_contains($nama, 'mentega')) {
                $hargaBase = 25000.0; // Rp 25.000 per liter
                $satuanHarga = 'liter';
            } else {
                // Bahan lain random antara Rp 8.000 s/d Rp 30.000 per kg
                $hargaBase = (float) (rand(8, 30) * 1000);
                $satuanHarga = 'kg';
            }

            $supplierId = $bahan->supplier_id ?? 1;

            // Seed 3 hari historis
            // Hari ini
            $prices[] = [
                'harga_satuan' => round($hargaBase, 2),
                'satuan_harga' => $satuanHarga,
                'tanggal_update' => Carbon::now()->toDateString(),
                'supplier_id' => $supplierId,
                'bahan_id' => $bahan->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // 1 hari lalu
            $prices[] = [
                'harga_satuan' => round($hargaBase * (1 + (rand(-5, 5) / 100)), 2),
                'satuan_harga' => $satuanHarga,
                'tanggal_update' => Carbon::now()->subDay()->toDateString(),
                'supplier_id' => $supplierId,
                'bahan_id' => $bahan->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // 2 hari lalu
            $prices[] = [
                'harga_satuan' => round($hargaBase * (1 + (rand(-5, 5) / 100)), 2),
                'satuan_harga' => $satuanHarga,
                'tanggal_update' => Carbon::now()->subDays(2)->toDateString(),
                'supplier_id' => $supplierId,
                'bahan_id' => $bahan->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $chunks = array_chunk($prices, 500);
        foreach ($chunks as $chunk) {
            DB::table('form_hargas')->insert($chunk);
        }
    }
}
