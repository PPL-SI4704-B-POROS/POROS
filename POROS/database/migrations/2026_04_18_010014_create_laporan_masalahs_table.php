<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel ini mencatat kendala atau laporan masalah dari pengguna sistem.
     */
    public function up(): void
    {
        Schema::create('laporan_masalahs', function (Blueprint $table) {
            $table->id();
            $table->string('judul_masalah');
            $table->text('deskripsi');
            $table->string('foto_bukti')->nullable();
            $table->enum('status', ['Open', 'In Progress', 'Resolved']);
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_masalahs');
    }
};
