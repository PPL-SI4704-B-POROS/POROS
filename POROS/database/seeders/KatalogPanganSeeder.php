<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KatalogPangan;

class KatalogPanganSeeder extends Seeder
{
    public function run(): void
    {
        $csvFile = database_path('data/tkpi_2020.csv');
        
        if (!file_exists($csvFile)) {
            $this->command->error("CSV file not found at: {$csvFile}");
            return;
        }

        $file = fopen($csvFile, 'r');
        $header = fgetcsv($file); // Skip header

        // ['code', 'name', 'source', 'category', 'section', 'page_pdf', 'air_g', 'energy_kcal', 'protein_g', 'fat_g', 'carb_g', 'fiber_g', 'ash_g', 'calcium_mg', 'phosphorus_mg', 'iron_mg', 'sodium_mg', 'potassium_mg', 'copper_mg', 'zinc_mg', 'retinol_mcg', 'beta_carotene_mcg', 'carotene_total_mcg', 'thiamin_mg', 'riboflavin_mg', 'niacin_mg', 'vitamin_c_mg', 'bdd_percent']
        
        $count = 0;
        while (($row = fgetcsv($file)) !== false) {
            // Check if row matches header length to avoid empty lines or malformed rows
            if (count($row) < 28) continue;

            KatalogPangan::updateOrCreate(
                ['kode_tkpi' => $row[0]],
                [
                    'nama_pangan' => $row[1],
                    'sumber' => $row[2],
                    'kategori' => $row[3],
                    'energi_per_100g' => (float)($row[7] ?: 0),
                    'protein_per_100g' => (float)($row[8] ?: 0),
                    'lemak_per_100g' => (float)($row[9] ?: 0),
                    'karbohidrat_per_100g' => (float)($row[10] ?: 0),
                    'serat_per_100g' => (float)($row[11] ?: 0),
                    'kalsium_per_100g' => (float)($row[13] ?: 0),
                    'besi_per_100g' => (float)($row[15] ?: 0),
                    'bdd_persen' => (float)($row[27] ?: 100),
                ]
            );
            $count++;
        }

        fclose($file);
        $this->command->info("Successfully imported {$count} TKPI items!");
    }
}
