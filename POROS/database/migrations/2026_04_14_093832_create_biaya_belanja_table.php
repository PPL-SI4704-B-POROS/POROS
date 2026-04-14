<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('biaya_belanja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')
                    ->constrained('materials')
                    ->onDelete('cascade'); 
            
            $table->foreignId('supplier_id')
                    ->constrained('suppliers')
                    ->onDelete('cascade');

            $table->double('jumlah_beli'); 
            $table->unsignedBigInteger('total_harga'); 
            $table->date('tanggal_belanja');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('biaya_belanja');
    }
};