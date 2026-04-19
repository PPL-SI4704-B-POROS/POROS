<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel ini menyimpan komposisi bahan baku (gramasi) untuk setiap porsi menu.
     */
    public function up(): void
    {
        Schema::create('reseps', function (Blueprint $table) {
            $table->id();
            $table->decimal('gramasi_per_porsi', 10, 2);
            $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
            $table->foreignId('bahan_id')->constrained('bahan_bakus')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reseps');
    }
};
