<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('biaya_belanja', function (Blueprint $table) {
            $table->foreignId('dapur_id')
                ->nullable()
                ->after('supplier_id')
                ->constrained('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_belanja', function (Blueprint $table) {
            $table->dropForeign(['dapur_id']);
            $table->dropColumn('dapur_id');
        });
    }
};
