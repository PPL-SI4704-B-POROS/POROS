<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengumuman', function (Blueprint $table) {
            $table->string('gambar')->nullable()->after('isi');
            $table->enum('target_role', ['umum', 'sekolah', 'dapur'])->default('umum')->after('gambar');
        });
    }

    public function down(): void
    {
        Schema::table('pengumuman', function (Blueprint $table) {
            $table->dropColumn(['gambar', 'target_role']);
        });
    }
};