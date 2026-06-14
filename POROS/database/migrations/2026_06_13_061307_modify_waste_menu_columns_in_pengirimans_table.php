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
        Schema::table('pengirimans', function (Blueprint $table) {
            $table->dropColumn(['waste_menu_1', 'waste_menu_2', 'waste_menu_3']);
            $table->string('menu_tersisa')->nullable()->after('ompreng_kembali');
            $table->integer('jumlah_sisa_ompreng')->nullable()->after('menu_tersisa');
            $table->date('tanggal_sisa')->nullable()->after('jumlah_sisa_ompreng');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengirimans', function (Blueprint $table) {
            $table->dropColumn(['menu_tersisa', 'jumlah_sisa_ompreng', 'tanggal_sisa']);
            $table->string('waste_menu_1')->nullable()->after('ompreng_kembali');
            $table->string('waste_menu_2')->nullable()->after('waste_menu_1');
            $table->string('waste_menu_3')->nullable()->after('waste_menu_2');
        });
    }
};
