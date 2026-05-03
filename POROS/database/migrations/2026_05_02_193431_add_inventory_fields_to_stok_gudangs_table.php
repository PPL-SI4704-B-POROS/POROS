<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stok_gudang', function (Blueprint $table) {

            $table->foreignId('supplier_id')->nullable();

            $table->string('batch_id')->nullable();

            $table->date('expired_date')->nullable();

        });
    }

    public function down(): void
    {
        Schema::table('stok_gudangs', function (Blueprint $table) {

            $table->dropColumn([
                'supplier_id',
                'batch_id',
                'expired_date'
            ]);

        });
    }
};