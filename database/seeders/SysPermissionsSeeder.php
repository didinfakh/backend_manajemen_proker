<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SysPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::table('sys_permissions')->truncate();

        $data = [
            [
                'id_sys_permission' => '1',
                'code' => 'index',
                'description' => null,
                'created_at' => null,
                'updated_at' => null,
                'id_organization' => null,
            ],
            [
                'id_sys_permission' => '2',
                'code' => 'add',
                'description' => null,
                'created_at' => null,
                'updated_at' => null,
                'id_organization' => null,
            ],
            [
                'id_sys_permission' => '3',
                'code' => 'edit',
                'description' => null,
                'created_at' => null,
                'updated_at' => null,
                'id_organization' => null,
            ],
            [
                'id_sys_permission' => '4',
                'code' => 'delete',
                'description' => null,
                'created_at' => null,
                'updated_at' => null,
                'id_organization' => null,
            ],
            [
                'id_sys_permission' => '6',
                'code' => 'create',
                'description' => 'Create Access',
                'created_at' => '2026-03-09 02:08:15',
                'updated_at' => '2026-03-09 02:08:15',
                'id_organization' => '1',
            ],
            [
                'id_sys_permission' => '7',
                'code' => 'update',
                'description' => 'Update Access',
                'created_at' => '2026-03-09 02:08:15',
                'updated_at' => '2026-03-09 02:08:15',
                'id_organization' => '1',
            ],
        ];

        foreach (array_chunk($data, 100) as $chunk) {
            DB::table('sys_permissions')->insert($chunk);
        }
    }
}
