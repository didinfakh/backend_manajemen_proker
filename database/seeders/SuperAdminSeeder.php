<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Add the Super Admin user
        $userId = DB::table('users')->insertGetId([
            'name' => 'Super Admin',
            'email' => 'superadmin@a',
            'password' => Hash::make('a'),
            'id_organization' => 17,
            'created_at' => now(),
            'updated_at' => now(),
        ], 'id_user');

        // Assign the user to the Super Admin group (id_sys_group = 2)
        DB::table('sys_user_groups')->insert([
            'id_user' => $userId,
            'id_sys_group' => 2,
            'id_organization' => 17,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
