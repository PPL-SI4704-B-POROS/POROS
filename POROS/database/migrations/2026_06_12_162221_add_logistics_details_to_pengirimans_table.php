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
            $table->integer('ompreng_kembali')->nullable()->after('keterangan');
            $table->string('waste_menu_1')->nullable()->after('ompreng_kembali');
            $table->string('waste_menu_2')->nullable()->after('waste_menu_1');
            $table->string('waste_menu_3')->nullable()->after('waste_menu_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengirimans', function (Blueprint $table) {
            $table->dropColumn(['ompreng_kembali', 'waste_menu_1', 'waste_menu_2', 'waste_menu_3']);
        });
    }
};
