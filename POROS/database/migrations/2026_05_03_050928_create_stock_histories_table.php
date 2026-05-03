<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_histories', function (Blueprint $table) {

            $table->id();

            $table->foreignId('stok_gudang_id')
                ->constrained('stok_gudang')
                ->onDelete('cascade');

            $table->string('status');

            $table->integer('quantity');

            $table->date('incoming_date');

            $table->string('batch_id');

            $table->date('expired_date')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_histories');
    }
};