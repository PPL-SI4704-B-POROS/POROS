<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel ini memantau perubahan harga satuan bahan baku dari setiap supplier (Daily Price Tracking).
     */
    public function up(): void
    {
        Schema::create('form_hargas', function (Blueprint $table) {
            $table->id();
            $table->decimal('harga_satuan', 12, 2);
            $table->date('tanggal_update');
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->foreignId('bahan_id')->constrained('bahan_bakus')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_hargas');
    }
};
