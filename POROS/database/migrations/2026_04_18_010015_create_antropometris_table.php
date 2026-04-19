<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel ini merekam data pertumbuhan fisik siswa (Tinggi Badan, Berat Badan).
     */
    public function up(): void
    {
        Schema::create('antropometris', function (Blueprint $table) {
            $table->id();
            $table->decimal('berat_badan', 5, 2);
            $table->decimal('tinggi_badan', 5, 2);
            $table->date('tanggal_ukur');
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('antropometris');
    }
};
