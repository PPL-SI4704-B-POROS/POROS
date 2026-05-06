<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel ini mencatat target dan status produksi harian berdasarkan menu.
     */
    public function up(): void
    {
        Schema::create('produksi_harians', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_produksi')->index();
            $table->integer('total_target_porsi');
            $table->enum('status_produksi', ['Menunggu', 'Memasak', 'Siap Kirim']);
            $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produksi_harians');
    }
};
