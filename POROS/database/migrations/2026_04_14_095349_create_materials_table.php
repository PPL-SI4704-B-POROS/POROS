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
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')
                    ->constrained('suppliers')
                    ->onDelete('cascade'); 
            $table->string('nama_bahan', 150);
            $table->string('nama_satuan', 20);
            $table->double('jumlah_satuan')->default(0);            
            $table->unsignedBigInteger('harga')->default(0);            
            $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
