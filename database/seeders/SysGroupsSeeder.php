<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SysGroupsSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::table('sys_groups')->truncate();

        $data = [
            [
                'id_sys_group' => '1',
                'name' => 'Administrator',
                'description' => 'ini deskripsi',
                'created_at' => null,
                'updated_at' => null,
                'id_organization' => '17',
            ],
            [
                'id_sys_group' => '2',
                'name' => 'Super Admin',
                'description' => 'Administrator group with full access',
                'created_at' => '2026-03-09 02:08:15',
                'updated_at' => '2026-03-09 02:08:15',
                'id_organization' => null,
            ],
        ];

        foreach (array_chunk($data, 100) as $chunk) {
            DB::table('sys_groups')->insert($chunk);
        }
    }
}
