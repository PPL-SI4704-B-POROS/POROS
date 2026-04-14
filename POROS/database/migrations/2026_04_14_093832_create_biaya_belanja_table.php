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
        Schema::create('biaya_belanja', function (Blueprint $table) {
            $table->id();
            $table->string('item_bahan'); 
            $table->string('kategori')->nullable(); 
            $table->integer('jumlah_beli'); 
            $table->string('satuan')->default('kg'); 
            $table->decimal('harga_satuan', 12, 2); 
            $table->decimal('total_harga', 12, 2); 
            $table->date('tanggal_belanja'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biaya_belanja');
    }
};
