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
        Schema::table('users', function (Blueprint $table) {
            $table->string('no_telp')->nullable()->after('email');
            $table->string('lokasi')->nullable()->after('no_telp');
            $table->timestamp('last_login_at')->nullable()->after('lokasi');
            $table->string('status')->default('Active')->after('last_login_at'); // Active/Inactive
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['no_telp', 'lokasi', 'last_login_at', 'status']);
        });
    }
};
