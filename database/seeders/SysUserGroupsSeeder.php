<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SysUserGroupsSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::table('sys_user_groups')->truncate();

        $data = [
            [
                'id_sys_user_group' => '1',
                'id_user' => '1',
                'id_sys_group' => '1',
                'created_at' => null,
                'updated_at' => null,
                'id_organization' => '17',
            ],
        ];

        foreach (array_chunk($data, 100) as $chunk) {
            DB::table('sys_user_groups')->insert($chunk);
        }
    }
}
