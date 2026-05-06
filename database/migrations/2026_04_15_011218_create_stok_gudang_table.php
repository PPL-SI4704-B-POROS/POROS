<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_gudang', function (Blueprint $table) {
            $table->id();

            $table->string('nama_bahan'); // Nama bahan
            $table->decimal('jumlah_masuk', 10, 2); // Biar bisa 1.5 kg, gak cuma angka bulat
            $table->string('satuan'); // kg, liter, dll
            $table->date('tanggal_terima'); // Tanggal diterima
            $table->text('keterangan')->nullable(); // Lebih fleksibel dari string

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_gudang');
    }
};