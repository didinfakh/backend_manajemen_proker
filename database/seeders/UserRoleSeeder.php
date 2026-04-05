<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orgId = 17; // Based on other seeders
        $now = Carbon::now();

        // 1. Ensure Groups Exist
        $groups = [
            ['id_sys_group' => 3, 'name' => 'BPH', 'description' => 'Badan Pengurus Harian', 'id_organization' => $orgId],
            ['id_sys_group' => 4, 'name' => 'KOORDINATOR', 'description' => 'Koordinator Divisi', 'id_organization' => $orgId],
            ['id_sys_group' => 5, 'name' => 'STAFF', 'description' => 'Staff Divisi', 'id_organization' => $orgId],
        ];

        foreach ($groups as $group) {
            DB::table('sys_groups')->updateOrInsert(
                ['id_sys_group' => $group['id_sys_group']],
                $group
            );
        }

        // 2. Define users to create
        $users = [
            ['name' => 'BPH 1', 'email' => 'bph1@a', 'group_id' => 3],
            ['name' => 'BPH 2', 'email' => 'bph2@a', 'group_id' => 3],
            ['name' => 'Koordinator Perkab', 'email' => 'perkab@a', 'group_id' => 4],
            ['name' => 'Koordinator Acara', 'email' => 'acara@a', 'group_id' => 4],
            ['name' => 'Staff Perkab 2', 'email' => 'perkab2@a', 'group_id' => 5],
            ['name' => 'Staff Perkab 3', 'email' => 'perkab3@a', 'group_id' => 5],
            ['name' => 'Staff Acara', 'email' => 'staf_acara@a', 'group_id' => 5],
            ['name' => 'Staff Acara 2', 'email' => 'staf_acara2@a', 'group_id' => 5],
            ['name' => 'Koordinator Kesehatan', 'email' => 'kesehatan@a', 'group_id' => 4],
            ['name' => 'Staff Kesehatan', 'email' => 'kesehatan_staff@a', 'group_id' => 5],
        ];

        foreach ($users as $userData) {
            // Create user
            $userId = DB::table('users')->updateOrInsert(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('a'),
                    'id_organization' => $orgId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            // Get the id_user (since updateOrInsert doesn't return ID directly for existing records)
            $user = DB::table('users')->where('email', $userData['email'])->first();

            // Assign Group
            DB::table('sys_user_groups')->updateOrInsert(
                ['id_user' => $user->id_user, 'id_sys_group' => $userData['group_id']],
                [
                    'id_organization' => $orgId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
