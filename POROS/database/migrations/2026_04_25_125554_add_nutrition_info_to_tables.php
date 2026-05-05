<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bahan_bakus', function (Blueprint $table) {
            $table->decimal('energi_per_100g', 8, 2)->default(0)->after('satuan');
            $table->decimal('protein_per_100g', 8, 2)->default(0)->after('energi_per_100g');
            $table->decimal('karbohidrat_per_100g', 8, 2)->default(0)->after('protein_per_100g');
            $table->decimal('lemak_per_100g', 8, 2)->default(0)->after('karbohidrat_per_100g');
        });

        Schema::table('menus', function (Blueprint $table) {
            $table->decimal('total_kalori', 10, 2)->default(0)->after('nama_menu');
            $table->decimal('total_protein', 10, 2)->default(0)->after('total_kalori');
            $table->decimal('total_karbohidrat', 10, 2)->default(0)->after('total_protein');
            $table->decimal('total_lemak', 10, 2)->default(0)->after('total_karbohidrat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bahan_bakus', function (Blueprint $table) {
            $table->dropColumn(['energi_per_100g', 'protein_per_100g', 'karbohidrat_per_100g', 'lemak_per_100g']);
        });

        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn(['total_kalori', 'total_protein', 'total_karbohidrat', 'total_lemak']);
        });
    }
};
