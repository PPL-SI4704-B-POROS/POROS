<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['nama_role' => 'super admin', 'created_at' => now(), 'updated_at' => now()],
            ['nama_role' => 'dapur', 'created_at' => now(), 'updated_at' => now()],
            ['nama_role' => 'sekolah', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('roles')->insert($roles);
    }
}
