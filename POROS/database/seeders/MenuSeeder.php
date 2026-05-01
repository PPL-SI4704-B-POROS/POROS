<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Menu;
use App\Models\Resep;
use App\Models\BahanBaku;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        DB::table('reseps')->truncate();
        DB::table('menus')->truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        // Helper to find BahanBaku id by name
        $findBahanId = function($keyword) {
            $bahan = BahanBaku::where('nama_bahan', 'like', "%{$keyword}%")->first();
            return $bahan ? $bahan->id : null;
        };

        // Menus array: name => [ ingredients => gramasi ]
        $menuData = [
            // Menu Berbasis Daging Sapi & Ayam
            'Ayam Goreng Lengkuas' => [
                'Daging Ayam' => 100,
                'Lengkuas' => 10,
                'Kunyit' => 5,
                'Bawang Putih' => 5,
                'Kemiri' => 5,
                'Minyak Goreng' => 15,
            ],
            'Semur Daging Sapi' => [
                'Daging Sapi' => 100,
                'Kecap Manis' => 15,
                'Kentang' => 50,
                'Pala' => 2,
                'Kayu Manis' => 2,
            ],
            'Ayam Teriyaki' => [
                'Daging Ayam' => 100,
                'Saus Teriyaki' => 20,
                'Bawang Bombay' => 15,
                'Wijen' => 3,
                'Minyak Goreng' => 10,
            ],
            'Opor Ayam Kuning' => [
                'Daging Ayam' => 100,
                'Santan' => 50,
                'Kunyit' => 5,
                'Serai' => 5,
                'Daun Salam' => 2,
            ],
            'Bakso Kuah' => [
                'Daging Sapi Giling' => 80,
                'Tepung Tapioka' => 20,
                'Kaldu Tulang/Sapi' => 200,
                'Seledri' => 5,
                'Bawang Putih' => 5,
            ],
            'Ayam Kecap' => [
                'Daging Ayam' => 100,
                'Kecap Manis' => 20,
                'Jahe' => 5,
                'Bawang Putih' => 5,
                'Bawang Bombay' => 10,
            ],
            'Gulai Ayam' => [
                'Daging Ayam' => 100,
                'Santan' => 50,
                'Cabai Merah' => 10,
                'Lengkuas' => 5,
                'Kunyit' => 5,
            ],

            // Menu Berbasis Ikan & Telur
            'Ikan Bandeng Presto' => [
                'Ikan Bandeng' => 100,
                'Ragi' => 2,
                'Kunyit' => 5,
                'Bawang Putih' => 5,
                'Minyak Goreng' => 15,
            ],
            'Telur Dadar Sayur' => [
                'Telur Ayam' => 60,
                'Wortel' => 20,
                'Daun Bawang' => 10,
                'Tepung Terigu' => 5,
                'Minyak Goreng' => 10,
            ],
            'Ikan Goreng Tepung' => [
                'Fillet Ikan Kakap/Dori' => 100,
                'Tepung Terigu' => 20,
                'Telur Ayam' => 15,
                'Lada' => 2,
                'Minyak Goreng' => 20,
            ],
            'Telur Puyuh Semur' => [
                'Telur Puyuh' => 60,
                'Tahu' => 40,
                'Kecap Manis' => 15,
                'Bawang Merah' => 5,
                'Bawang Putih' => 5,
            ],
            'Pepes Ikan' => [
                'Ikan Kembung' => 100,
                'Daun Kemangi' => 10,
                'Tomat' => 20,
                'Kunyit' => 5,
                'Daun Pisang' => 10,
            ],
            'Telur Balado (Tidak Pedas)' => [
                'Telur Ayam' => 60,
                'Tomat' => 30,
                'Cabai Merah' => 10,
                'Gula Jawa/Aren' => 5,
                'Minyak Goreng' => 10,
            ],

            // Menu Sayuran & Lauk Nabati
            'Sayur Asem' => [
                'Kacang Panjang' => 20,
                'Labu Siam' => 30,
                'Jagung Manis' => 30,
                'Melinjo' => 10,
                'Asam Jawa' => 5,
            ],
            'Sop Ayam Makaroni' => [
                'Daging Ayam' => 30,
                'Wortel' => 20,
                'Kentang' => 30,
                'Makaroni' => 20,
                'Kembang Kol' => 20,
                'Kaldu Tulang/Sapi' => 150,
            ],
            'Tumis Buncis Wortel' => [
                'Buncis' => 50,
                'Wortel' => 50,
                'Bawang Putih' => 5,
                'Saus Tiram' => 10,
                'Minyak Goreng' => 5,
            ],
            'Capcay Kuah' => [
                'Bakso Ikan' => 30,
                'Wortel' => 30,
                'Sawi Putih' => 40,
                'Brokoli' => 30,
                'Bawang Putih' => 5,
            ],
            'Sayur Lodeh' => [
                'Labu Siam' => 30,
                'Kacang Panjang' => 30,
                'Terong' => 30,
                'Santan' => 40,
            ],
            'Tumis Kangkung' => [
                'Kangkung' => 100,
                'Bawang Putih' => 5,
                'Tauco' => 10,
                'Tomat' => 20,
                'Minyak Goreng' => 5,
            ],
            'Tahu Tempe Bacem' => [
                'Tahu' => 50,
                'Tempe' => 50,
                'Air Kelapa' => 50,
                'Gula Jawa/Aren' => 15,
                'Ketumbar' => 3,
                'Bawang Putih' => 5,
            ],
            'Perkedel Kentang' => [
                'Kentang' => 80,
                'Telur Ayam' => 15,
                'Seledri' => 5,
                'Bawang Goreng' => 5,
                'Minyak Goreng' => 10,
            ],

            // Menu Pelengkap & Buah
            'Nasi Putih' => [
                'Beras Putih' => 100,
            ],
            'Nasi Merah' => [
                'Beras Merah' => 100,
            ],
            'Bubur Kacang Hijau' => [
                'Kacang Hijau' => 50,
                'Santan' => 30,
                'Gula Jawa/Aren' => 20,
                'Jahe' => 5,
            ],
            'Buah Potong Campur' => [
                'Pisang' => 50,
                'Pepaya' => 50,
                'Jeruk' => 50,
            ],
            'Susu Sapi Segar' => [
                'Susu Sapi' => 200,
            ],
        ];

        foreach ($menuData as $menuName => $ingredients) {
            $totalKalori = 0;
            $totalProtein = 0;
            $totalKarbohidrat = 0;
            $totalLemak = 0;

            $resepsToInsert = [];

            foreach ($ingredients as $bahanName => $gramasi) {
                $bahan = BahanBaku::where('nama_bahan', 'like', "%{$bahanName}%")->first();
                if ($bahan) {
                    $resepsToInsert[] = [
                        'bahan_id' => $bahan->id,
                        'gramasi_per_porsi' => $gramasi,
                    ];

                    // Calculate nutrition
                    $totalKalori += $bahan->energi_per_gram * $gramasi;
                    $totalProtein += $bahan->protein_per_gram * $gramasi;
                    $totalKarbohidrat += $bahan->karbohidrat_per_gram * $gramasi;
                    $totalLemak += $bahan->lemak_per_gram * $gramasi;
                }
            }

            // Create Menu
            $menu = Menu::create([
                'nama_menu' => $menuName,
                'total_kalori' => $totalKalori,
                'total_protein' => $totalProtein,
                'total_karbohidrat' => $totalKarbohidrat,
                'total_lemak' => $totalLemak,
                'deskripsi_gizi' => "Kalkulasi otomatis dari resep",
            ]);

            // Create Resep
            foreach ($resepsToInsert as $rt) {
                Resep::create([
                    'menu_id' => $menu->id,
                    'bahan_id' => $rt['bahan_id'],
                    'gramasi_per_porsi' => $rt['gramasi_per_porsi'],
                ]);
            }
        }
    }
}
