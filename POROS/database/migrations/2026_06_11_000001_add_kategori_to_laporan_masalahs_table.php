<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan_masalahs', function (Blueprint $table) {
            $table->enum('kategori', [
                'Bug Aplikasi',
                'Bahan Baku',
                'Transportasi & Pengiriman',
                'Menu & Produksi',
                'Data Siswa',
                'Keuangan',
                'Lainnya',
            ])->default('Lainnya')->after('deskripsi');
        });
    }

    public function down(): void
    {
        Schema::table('laporan_masalahs', function (Blueprint $table) {
            $table->dropColumn('kategori');
        });
    }
};