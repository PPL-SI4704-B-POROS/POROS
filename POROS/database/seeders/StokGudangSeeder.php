<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\StokGudang; // 🌟 Pastikan Import Model Ini

class StokGudangSeeder extends Seeder
{
    public function run(): void
    {
        $bahans = DB::table('bahan_bakus')->get();

        foreach ($bahans as $bahan) {

            $qtyMasukGudang = 50000; // 50 kg

            // 🛠️ UBAH JADI MODEL ELOQUENT BIAR ACCESSOR DAN LOGIC MODELNYA JALAN
            StokGudang::create([
                'bahan_baku_id' => $bahan->id,
                'supplier_id'   => $bahan->supplier_id,
                'quantity'      => $qtyMasukGudang,
                'satuan'        => $bahan->satuan,
            ]);

            DB::table('bahan_bakus')
                ->where('id', $bahan->id)
                ->decrement('stok', $qtyMasukGudang);
        }
    }
}