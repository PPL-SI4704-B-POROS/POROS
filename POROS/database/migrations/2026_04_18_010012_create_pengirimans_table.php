<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel ini mengatur proses logistik pengiriman dari produksi ke sekolah tujuan.
     */
    public function up(): void
    {
        Schema::create('pengirimans', function (Blueprint $table) {
            $table->id();
            $table->timestamp('waktu_berangkat');
            $table->timestamp('waktu_sampai')->nullable();
            $table->string('nama_penerima')->nullable();
            $table->enum('status_kirim', ['Menunggu', 'Perjalanan', 'Sampai'])->index();
            $table->foreignId('produksi_id')->constrained('produksi_harians')->onDelete('cascade');
            $table->foreignId('sekolah_id')->constrained('sekolahs')->onDelete('cascade');
            $table->foreignId('kurir_id')->constrained('kurirs')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengirimans');
    }
};
