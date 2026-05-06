<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel ini mencatat sisa makanan di piring siswa sebagai indikator keberterimaan menu.
     */
    public function up(): void
    {
        Schema::create('plate_wastes', function (Blueprint $table) {
            $table->id();
            $table->decimal('jumlah_waste', 10, 2);
            $table->date('tanggal');
            $table->string('keterangan')->nullable();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->onDelete('cascade');
            $table->foreignId('pengiriman_id')->nullable()->constrained('pengirimans')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plate_wastes');
    }
};
