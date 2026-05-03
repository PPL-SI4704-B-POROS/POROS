<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stok_gudang', function (Blueprint $table) {
            $table->foreignId('bahan_baku_id')
                ->nullable()
                ->constrained('bahan_bakus')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('stok_gudang', function (Blueprint $table) {
            $table->dropForeign(['bahan_baku_id']);
            $table->dropColumn('bahan_baku_id');
        });
    }
};