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

        // Helper function to find a katalog pangan
        $like = DB::getDriverName() === 'pgsql' ? 'ilike' : 'like';
        $findKatalogId = function($keyword) use ($like) {
            if (!$keyword) return null;
            $kp = DB::table('katalog_pangans')->where('nama_pangan', $like, "%{$keyword}%")->first();
            return $kp ? $kp->id : null;
        };

        // Extracting all unique ingredients from the provided menus
        $ingredients = [
            // Sayuran & Buah-buahan
            ['Bayam Hijau', 'Bayam, segar'],
            ['Kangkung', 'Kangkung, segar'],
            ['Wortel', 'Wortel, segar'],
            ['Kubis', 'Daun kubis, segar'],
            ['Buncis', 'Buncis, segar'],
            ['Tomat', 'Tomat merah, segar'],
            ['Terong', 'Terong, segar'],
            ['Kacang Panjang', 'Kacang panjang, segar'],
            ['Labu Siam', 'Labu siam, segar'],
            ['Jagung Manis', 'Jagung kuning muda'],
            ['Melinjo', 'Melinjo, segar'],
            ['Kembang Kol', 'Kool kembang'],
            ['Sawi Putih', 'Sawi putih/ pecai, segar'],
            ['Brokoli', 'Brokoli'],
            ['Daun Bawang', 'Daun bawang merah, segar'],
            ['Seledri', 'Seledri, segar'],
            ['Pisang', 'Pisang ambon, segar'],
            ['Pepaya', 'Pepaya, segar'],
            ['Jeruk', 'Jeruk manis, segar'],

            // Karbohidrat & Serealia
            ['Beras Putih', 'Beras giling, mentah'],
            ['Beras Merah', 'Beras tumbuk merah, mentah'],
            ['Jagung', 'Jagung kuning, tepung'],
            ['Kentang', 'Kentang, segar'],
            ['Ubi Jalar', 'Ubi jalar putih, segar'],
            ['Singkong', 'Ketela pohon/ singkong, segar'],
            ['Makaroni', 'Makaroni, mentah'],
            ['Tepung Tapioka', 'Tepung singkong/ tapioka'],
            ['Tepung Terigu', 'Tepung terigu'],

            // Protein Nabati
            ['Tahu', 'Tahu, mentah'],
            ['Tempe', 'Tempe kedelai murni, mentah'],
            ['Kacang Tanah', 'Kacang tanah, kering'],
            ['Kacang Hijau', 'Kacang hijau, kering'],

            // Protein Hewani
            ['Daging Sapi', 'Sapi, daging, sedang, segar'],
            ['Daging Ayam', 'Ayam, daging, segar'],
            ['Daging Sapi Giling', 'Sapi, daging, segar'],
            ['Telur Ayam', 'Ayam, telur, segar'],
            ['Telur Bebek', 'Bebek, telur, segar'],
            ['Telur Puyuh', 'puyuh'],
            ['Ikan Lele', 'Lele, segar'],
            ['Ikan Kembung', 'Kembung, segar'],
            ['Ikan Tuna', 'Tuna, segar'],
            ['Ikan Bandeng', 'Bandeng, segar'],
            ['Fillet Ikan Kakap/Dori', 'Kakap, segar'],
            ['Bakso Ikan', 'Bakso'],

            // Bumbu, Rempah & Bahan Lainnya
            ['Susu Sapi', 'Sapi, susu, segar'],
            ['Minyak Goreng', 'Kelapa sawit, minyak'],
            ['Gula Pasir', 'Gula pasir'],
            ['Garam', 'Garam'],
            ['Bawang Putih', 'Bawang putih'],
            ['Bawang Merah', 'Bawang merah'],
            ['Bawang Bombay', 'Bawang bombay'],
            ['Bawang Goreng', null], // usually processed
            ['Kemiri', 'Kemiri'],
            ['Kunyit', 'Kunyit'],
            ['Lengkuas', 'Lengkuas'],
            ['Jahe', 'Jahe'],
            ['Pala', 'Pala'],
            ['Kayu Manis', 'Kayu manis'],
            ['Wijen', 'Wijen, kering'],
            ['Serai', 'Serai'],
            ['Daun Salam', 'Daun salam'],
            ['Daun Kemangi', 'Kemangi'],
            ['Cabai Merah', 'Cabai merah, segar'],
            ['Ketumbar', 'Ketumbar'],
            ['Lada', 'Lada'],
            ['Kecap Manis', 'Kecap'],
            ['Saus Teriyaki', null],
            ['Saus Tiram', null],
            ['Tauco', 'Tauco'],
            ['Santan', 'Santan'],
            ['Air Kelapa', 'Kelapa, air, segar'],
            ['Asam Jawa', 'Asam'],
            ['Gula Jawa/Aren', 'Gula kelapa'],
            ['Kaldu Tulang/Sapi', 'Kaldu'],
            ['Ragi', 'Ragi'],
            ['Daun Pisang', null],
        ];

        $bahanBakus = [];
        foreach ($ingredients as $index => $item) {
            // Assign roughly to suppliers by category
            // 1: Buah/Sayur, 2: Beras/Tepung, 3: Nabati/Bumbu, 4: Daging/Telur, 5: Ikan, 6: Minyak/Susu
            $supp_id = $suppliers[0] ?? 1;
            if ($index > 18) $supp_id = $suppliers[2] ?? 1; // Serealia
            if ($index > 27) $supp_id = $suppliers[3] ?? 1; // Nabati
            if ($index > 31) $supp_id = $suppliers[4] ?? 1; // Hewani
            if ($index > 40) $supp_id = $suppliers[5] ?? 1; // Ikan
            if ($index > 43) $supp_id = $suppliers[6] ?? 1; // Bumbu dll

            $bahanBakus[] = [
                'nama_bahan' => $item[0],
                'stok' => 150000, // 150 kg in grams
                'stok_minimal' => 150000, // 150 kg in grams
                'satuan' => 'gram',
                'katalog_pangan_id' => $findKatalogId($item[1]),
                'supplier_id' => $supp_id,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        DB::table('bahan_bakus')->insert($bahanBakus);

        // Tambahkan semua Katalog Pangan sisanya ke dalam Bahan Baku
        $usedKatalogIds = array_filter(array_column($bahanBakus, 'katalog_pangan_id'));

        $remainingKatalogs = DB::table('katalog_pangans')
            ->whereNotIn('id', $usedKatalogIds)
            ->get();

        $additionalBahanBakus = [];
        $chunkSize = 500;
        
        foreach ($remainingKatalogs as $kp) {
            $additionalBahanBakus[] = [
                'nama_bahan' => $kp->nama_pangan,
                'stok' => 150000, // 150 kg in grams
                'stok_minimal' => 150000, // 150 kg in grams
                'satuan' => 'gram',
                'katalog_pangan_id' => $kp->id,
                'supplier_id' => $suppliers[0] ?? 1,
                'created_at' => now(),
                'updated_at' => now()
            ];

            if (count($additionalBahanBakus) >= $chunkSize) {
                DB::table('bahan_bakus')->insert($additionalBahanBakus);
                $additionalBahanBakus = [];
            }
        }

        if (count($additionalBahanBakus) > 0) {
            DB::table('bahan_bakus')->insert($additionalBahanBakus);
        }
    }
}
