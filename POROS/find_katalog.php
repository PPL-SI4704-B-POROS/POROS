<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$items = ["Brokoli", "Ayam", "Bebek", "Lele", "Kembung", "Tuna", "Bandeng", "Kakap", "Bakso", "Sapi", "Minyak", "Gula", "Garam", "Kemiri", "Lengkuas", "Jahe", "Kayu manis", "Serai", "Kemangi", "Lada", "Kelapa", "Kaldu", "Puyuh", "Teri", "Daging"];
foreach($items as $i) {
    echo "--- $i ---\n";
    $results = \App\Models\KatalogPangan::where('nama_pangan', 'ilike', '%' . $i . '%')->limit(5)->pluck('nama_pangan')->toArray();
    print_r($results);
}
