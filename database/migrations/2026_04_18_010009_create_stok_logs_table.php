<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel ini mencatatkan riwayat masuk dan keluarnya stok bahan baku.
     */
    public function up(): void
    {
        Schema::create('stok_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis_transaksi', ['masuk', 'keluar']);
            $table->decimal('jumlah', 10, 2);
            $table->decimal('harga_beli', 12, 2)->nullable();
            $table->text('keterangan')->nullable();
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
        Schema::dropIfExists('stok_logs');
    }
};
