<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil ID roles
        $superAdminRole = DB::table('roles')->where('nama_role', 'super admin')->first()->id;
        $dapurRole = DB::table('roles')->where('nama_role', 'dapur')->first()->id;
        $sekolahRole = DB::table('roles')->where('nama_role', 'sekolah')->first()->id;

        // 2. Ambil ID sekolah
        $sekolahIds = DB::table('sekolahs')->pluck('id');

        $users = [
            // 1 Super Admin
            [
                'nama_lengkap' => 'Super Admin POROS',
                'email' => 'admin@poros.com',
                'password' => Hash::make('password123'),
                'role_id' => $superAdminRole,
                'no_telp' => '0812-3456-7890',
                'lokasi' => 'Kantor Pusat Jakarta',
                'last_login_at' => Carbon::now()->subMinutes(5),
                'status' => 'Active',
                'sekolah_id' => null,
            ],
            // 1 Petugas Dapur
            [
                'nama_lengkap' => 'Kepala Dapur POROS',
                'email' => 'dapur@poros.com',
                'password' => Hash::make('password123'),
                'role_id' => $dapurRole,
                'no_telp' => '0813-4567-8901',
                'lokasi' => 'Dapur Utama Cibubur',
                'last_login_at' => Carbon::now()->subHours(1),
                'status' => 'Active',
                'sekolah_id' => null,
            ],
            // 1 Petugas Sekolah
            [
                'nama_lengkap' => 'Petugas SDN POROS 01',
                'email' => 'sekolah@poros.com',
                'password' => Hash::make('password123'),
                'role_id' => $sekolahRole,
                'no_telp' => '0814-5678-9012',
                'lokasi' => 'SDN POROS 01 Jakarta',
                'last_login_at' => Carbon::now()->subHours(3),
                'status' => 'Active',
                'sekolah_id' => $sekolahIds[0] ?? 1,
            ],
        ];

        foreach ($users as $u) {
            $u['created_at'] = now();
            $u['updated_at'] = now();
            DB::table('users')->insert($u);
        }
    }
}
