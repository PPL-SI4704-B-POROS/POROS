<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop dependents dulu sebelum drop parent
        Schema::dropIfExists('stock_histories');
        Schema::dropIfExists('stok_gudang');

        Schema::create('stok_gudang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bahan_baku_id')
                  ->constrained('bahan_bakus')
                  ->onDelete('cascade');
            $table->foreignId('supplier_id')
                  ->constrained('suppliers')
                  ->onDelete('cascade');
            $table->decimal('quantity', 10, 2)->default(0);
            $table->string('satuan'); // kg, pcs, liter
            $table->timestamps();
        });

        Schema::create('stock_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stok_gudang_id')
                  ->constrained('stok_gudang')
                  ->onDelete('cascade');
            $table->string('status')->default('incoming');
            $table->decimal('quantity', 10, 2);
            $table->date('incoming_date');
            $table->string('batch_id')->nullable();
            $table->date('expired_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_histories');
        Schema::dropIfExists('stok_gudang');
    }
};