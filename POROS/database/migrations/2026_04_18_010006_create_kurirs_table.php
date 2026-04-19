<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel ini menyimpan data kurir pengantar hasil produksi.
     */
    public function up(): void
    {
        Schema::create('kurirs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kurir');
            $table->string('no_plat');
            $table->string('kontak');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kurirs');
    }
};
