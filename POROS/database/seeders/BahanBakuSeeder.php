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
            // ═══ SAYURAN ═══
            ['nama_bahan'=>'Bayam Hijau','stok'=>5000,'stok_minimal'=>1250,'satuan'=>'gram','energi_per_100g'=>23.00,'protein_per_100g'=>2.90,'karbohidrat_per_100g'=>3.60,'lemak_per_100g'=>0.40,'supplier_id'=>$suppliers[0]??1,'created_at'=>now(),'updated_at'=>now()],
            ['nama_bahan'=>'Kangkung','stok'=>5000,'stok_minimal'=>1250,'satuan'=>'gram','energi_per_100g'=>29.00,'protein_per_100g'=>3.00,'karbohidrat_per_100g'=>5.40,'lemak_per_100g'=>0.30,'supplier_id'=>$suppliers[0]??1,'created_at'=>now(),'updated_at'=>now()],
            ['nama_bahan'=>'Wortel','stok'=>5000,'stok_minimal'=>1250,'satuan'=>'gram','energi_per_100g'=>41.00,'protein_per_100g'=>0.90,'karbohidrat_per_100g'=>9.60,'lemak_per_100g'=>0.20,'supplier_id'=>$suppliers[0]??1,'created_at'=>now(),'updated_at'=>now()],
            ['nama_bahan'=>'Kubis','stok'=>5000,'stok_minimal'=>1250,'satuan'=>'gram','energi_per_100g'=>25.00,'protein_per_100g'=>1.30,'karbohidrat_per_100g'=>5.80,'lemak_per_100g'=>0.10,'supplier_id'=>$suppliers[0]??1,'created_at'=>now(),'updated_at'=>now()],
            ['nama_bahan'=>'Buncis','stok'=>5000,'stok_minimal'=>1250,'satuan'=>'gram','energi_per_100g'=>31.00,'protein_per_100g'=>1.80,'karbohidrat_per_100g'=>7.00,'lemak_per_100g'=>0.20,'supplier_id'=>$suppliers[0]??1,'created_at'=>now(),'updated_at'=>now()],
            ['nama_bahan'=>'Tomat','stok'=>5000,'stok_minimal'=>1250,'satuan'=>'gram','energi_per_100g'=>18.00,'protein_per_100g'=>0.90,'karbohidrat_per_100g'=>3.90,'lemak_per_100g'=>0.20,'supplier_id'=>$suppliers[0]??1,'created_at'=>now(),'updated_at'=>now()],
            ['nama_bahan'=>'Terong','stok'=>5000,'stok_minimal'=>1250,'satuan'=>'gram','energi_per_100g'=>25.00,'protein_per_100g'=>1.00,'karbohidrat_per_100g'=>6.00,'lemak_per_100g'=>0.20,'supplier_id'=>$suppliers[0]??1,'created_at'=>now(),'updated_at'=>now()],

            // ═══ BUAH ═══
            ['nama_bahan'=>'Pisang','stok'=>6000,'stok_minimal'=>1500,'satuan'=>'gram','energi_per_100g'=>89.00,'protein_per_100g'=>1.10,'karbohidrat_per_100g'=>22.80,'lemak_per_100g'=>0.30,'supplier_id'=>$suppliers[1]??1,'created_at'=>now(),'updated_at'=>now()],
            ['nama_bahan'=>'Pepaya','stok'=>6000,'stok_minimal'=>1500,'satuan'=>'gram','energi_per_100g'=>43.00,'protein_per_100g'=>0.50,'karbohidrat_per_100g'=>11.00,'lemak_per_100g'=>0.30,'supplier_id'=>$suppliers[1]??1,'created_at'=>now(),'updated_at'=>now()],
            ['nama_bahan'=>'Jeruk','stok'=>6000,'stok_minimal'=>1500,'satuan'=>'gram','energi_per_100g'=>47.00,'protein_per_100g'=>0.90,'karbohidrat_per_100g'=>11.80,'lemak_per_100g'=>0.10,'supplier_id'=>$suppliers[1]??1,'created_at'=>now(),'updated_at'=>now()],
            ['nama_bahan'=>'Semangka','stok'=>6000,'stok_minimal'=>1500,'satuan'=>'gram','energi_per_100g'=>30.00,'protein_per_100g'=>0.60,'karbohidrat_per_100g'=>7.60,'lemak_per_100g'=>0.20,'supplier_id'=>$suppliers[1]??1,'created_at'=>now(),'updated_at'=>now()],
            ['nama_bahan'=>'Apel','stok'=>6000,'stok_minimal'=>1500,'satuan'=>'gram','energi_per_100g'=>52.00,'protein_per_100g'=>0.30,'karbohidrat_per_100g'=>14.00,'lemak_per_100g'=>0.20,'supplier_id'=>$suppliers[1]??1,'created_at'=>now(),'updated_at'=>now()],

            // ═══ KARBOHIDRAT ═══
            ['nama_bahan'=>'Beras Putih','stok'=>10000,'stok_minimal'=>2000,'satuan'=>'gram','energi_per_100g'=>360.00,'protein_per_100g'=>7.60,'karbohidrat_per_100g'=>78.90,'lemak_per_100g'=>0.60,'supplier_id'=>$suppliers[2]??1,'created_at'=>now(),'updated_at'=>now()],
            ['nama_bahan'=>'Beras Merah','stok'=>8000,'stok_minimal'=>2000,'satuan'=>'gram','energi_per_100g'=>350.00,'protein_per_100g'=>7.50,'karbohidrat_per_100g'=>77.00,'lemak_per_100g'=>2.70,'supplier_id'=>$suppliers[2]??1,'created_at'=>now(),'updated_at'=>now()],
            ['nama_bahan'=>'Jagung','stok'=>8000,'stok_minimal'=>2000,'satuan'=>'gram','energi_per_100g'=>96.00,'protein_per_100g'=>3.40,'karbohidrat_per_100g'=>21.00,'lemak_per_100g'=>1.50,'supplier_id'=>$suppliers[2]??1,'created_at'=>now(),'updated_at'=>now()],
            ['nama_bahan'=>'Kentang','stok'=>7000,'stok_minimal'=>1500,'satuan'=>'gram','energi_per_100g'=>77.00,'protein_per_100g'=>2.00,'karbohidrat_per_100g'=>17.00,'lemak_per_100g'=>0.10,'supplier_id'=>$suppliers[2]??1,'created_at'=>now(),'updated_at'=>now()],
            ['nama_bahan'=>'Ubi Jalar','stok'=>7000,'stok_minimal'=>1500,'satuan'=>'gram','energi_per_100g'=>86.00,'protein_per_100g'=>1.60,'karbohidrat_per_100g'=>20.10,'lemak_per_100g'=>0.10,'supplier_id'=>$suppliers[2]??1,'created_at'=>now(),'updated_at'=>now()],
            ['nama_bahan'=>'Singkong','stok'=>7000,'stok_minimal'=>1500,'satuan'=>'gram','energi_per_100g'=>160.00,'protein_per_100g'=>1.40,'karbohidrat_per_100g'=>38.00,'lemak_per_100g'=>0.30,'supplier_id'=>$suppliers[2]??1,'created_at'=>now(),'updated_at'=>now()],

            // ═══ PROTEIN NABATI ═══
            ['nama_bahan'=>'Tahu','stok'=>5000,'stok_minimal'=>1000,'satuan'=>'gram','energi_per_100g'=>76.00,'protein_per_100g'=>8.00,'karbohidrat_per_100g'=>1.90,'lemak_per_100g'=>4.80,'supplier_id'=>$suppliers[3]??1,'created_at'=>now(),'updated_at'=>now()],
            ['nama_bahan'=>'Tempe','stok'=>5000,'stok_minimal'=>1000,'satuan'=>'gram','energi_per_100g'=>192.00,'protein_per_100g'=>20.80,'karbohidrat_per_100g'=>13.50,'lemak_per_100g'=>10.80,'supplier_id'=>$suppliers[3]??1,'created_at'=>now(),'updated_at'=>now()],
            ['nama_bahan'=>'Kacang Tanah','stok'=>5000,'stok_minimal'=>1000,'satuan'=>'gram','energi_per_100g'=>567.00,'protein_per_100g'=>25.80,'karbohidrat_per_100g'=>16.10,'lemak_per_100g'=>49.20,'supplier_id'=>$suppliers[3]??1,'created_at'=>now(),'updated_at'=>now()],
            ['nama_bahan'=>'Kacang Hijau','stok'=>5000,'stok_minimal'=>1000,'satuan'=>'gram','energi_per_100g'=>347.00,'protein_per_100g'=>24.00,'karbohidrat_per_100g'=>63.00,'lemak_per_100g'=>1.20,'supplier_id'=>$suppliers[3]??1,'created_at'=>now(),'updated_at'=>now()],

            // ═══ PROTEIN HEWANI ═══
            ['nama_bahan'=>'Daging Sapi','stok'=>5000,'stok_minimal'=>1000,'satuan'=>'gram','energi_per_100g'=>250.00,'protein_per_100g'=>26.00,'karbohidrat_per_100g'=>0.00,'lemak_per_100g'=>15.00,'supplier_id'=>$suppliers[4]??1,'created_at'=>now(),'updated_at'=>now()],
            ['nama_bahan'=>'Daging Ayam','stok'=>5000,'stok_minimal'=>1000,'satuan'=>'gram','energi_per_100g'=>239.00,'protein_per_100g'=>27.00,'karbohidrat_per_100g'=>0.00,'lemak_per_100g'=>14.00,'supplier_id'=>$suppliers[4]??1,'created_at'=>now(),'updated_at'=>now()],
            ['nama_bahan'=>'Telur Ayam','stok'=>6000,'stok_minimal'=>1500,'satuan'=>'gram','energi_per_100g'=>155.00,'protein_per_100g'=>13.00,'karbohidrat_per_100g'=>1.10,'lemak_per_100g'=>11.00,'supplier_id'=>$suppliers[4]??1,'created_at'=>now(),'updated_at'=>now()],
            ['nama_bahan'=>'Telur Bebek','stok'=>6000,'stok_minimal'=>1500,'satuan'=>'gram','energi_per_100g'=>185.00,'protein_per_100g'=>13.30,'karbohidrat_per_100g'=>1.40,'lemak_per_100g'=>14.00,'supplier_id'=>$suppliers[4]??1,'created_at'=>now(),'updated_at'=>now()],

            // ═══ IKAN ═══
            ['nama_bahan'=>'Ikan Lele','stok'=>5000,'stok_minimal'=>1000,'satuan'=>'gram','energi_per_100g'=>105.00,'protein_per_100g'=>18.00,'karbohidrat_per_100g'=>0.00,'lemak_per_100g'=>2.90,'supplier_id'=>$suppliers[5]??1,'created_at'=>now(),'updated_at'=>now()],
            ['nama_bahan'=>'Ikan Kembung','stok'=>5000,'stok_minimal'=>1000,'satuan'=>'gram','energi_per_100g'=>167.00,'protein_per_100g'=>20.00,'karbohidrat_per_100g'=>0.00,'lemak_per_100g'=>9.40,'supplier_id'=>$suppliers[5]??1,'created_at'=>now(),'updated_at'=>now()],
            ['nama_bahan'=>'Ikan Tuna','stok'=>5000,'stok_minimal'=>1000,'satuan'=>'gram','energi_per_100g'=>144.00,'protein_per_100g'=>23.00,'karbohidrat_per_100g'=>0.00,'lemak_per_100g'=>4.90,'supplier_id'=>$suppliers[5]??1,'created_at'=>now(),'updated_at'=>now()],

            // ═══ SUSU & LAINNYA ═══
            ['nama_bahan'=>'Susu Sapi','stok'=>8000,'stok_minimal'=>2000,'satuan'=>'gram','energi_per_100g'=>61.00,'protein_per_100g'=>3.20,'karbohidrat_per_100g'=>4.80,'lemak_per_100g'=>3.30,'supplier_id'=>$suppliers[6]??1,'created_at'=>now(),'updated_at'=>now()],
            ['nama_bahan'=>'Minyak Goreng','stok'=>8000,'stok_minimal'=>2000,'satuan'=>'gram','energi_per_100g'=>884.00,'protein_per_100g'=>0.00,'karbohidrat_per_100g'=>0.00,'lemak_per_100g'=>100.00,'supplier_id'=>$suppliers[6]??1,'created_at'=>now(),'updated_at'=>now()],
            ['nama_bahan'=>'Gula Pasir','stok'=>8000,'stok_minimal'=>2000,'satuan'=>'gram','energi_per_100g'=>387.00,'protein_per_100g'=>0.00,'karbohidrat_per_100g'=>100.00,'lemak_per_100g'=>0.00,'supplier_id'=>$suppliers[6]??1,'created_at'=>now(),'updated_at'=>now()],
            ['nama_bahan'=>'Garam','stok'=>8000,'stok_minimal'=>2000,'satuan'=>'gram','energi_per_100g'=>0.00,'protein_per_100g'=>0.00,'karbohidrat_per_100g'=>0.00,'lemak_per_100g'=>0.00,'supplier_id'=>$suppliers[6]??1,'created_at'=>now(),'updated_at'=>now()],
        ]);
    }
}
