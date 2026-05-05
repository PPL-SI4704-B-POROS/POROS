<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('katalog_pangans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_tkpi')->unique();
            $table->string('nama_pangan');
            $table->string('kategori')->nullable();
            $table->string('sumber')->nullable();
            $table->decimal('energi_per_100g', 8, 2)->default(0);
            $table->decimal('protein_per_100g', 8, 2)->default(0);
            $table->decimal('lemak_per_100g', 8, 2)->default(0);
            $table->decimal('karbohidrat_per_100g', 8, 2)->default(0);
            $table->decimal('serat_per_100g', 8, 2)->nullable();
            $table->decimal('kalsium_per_100g', 8, 2)->nullable();
            $table->decimal('besi_per_100g', 8, 2)->nullable();
            $table->decimal('bdd_persen', 5, 2)->default(100);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('bahan_bakus', function (Blueprint $table) {
            $table->dropColumn(['energi_per_100g', 'protein_per_100g', 'karbohidrat_per_100g', 'lemak_per_100g']);
            $table->foreignId('katalog_pangan_id')->nullable()->constrained('katalog_pangans')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('bahan_bakus', function (Blueprint $table) {
            $table->dropForeign(['katalog_pangan_id']);
            $table->dropColumn('katalog_pangan_id');
            $table->decimal('energi_per_100g', 8, 2)->default(0);
            $table->decimal('protein_per_100g', 8, 2)->default(0);
            $table->decimal('karbohidrat_per_100g', 8, 2)->default(0);
            $table->decimal('lemak_per_100g', 8, 2)->default(0);
        });

        Schema::dropIfExists('katalog_pangans');
    }
};
