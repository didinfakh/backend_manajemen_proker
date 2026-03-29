<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SysGroupPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::table('sys_group_permissions')->truncate();

        $data = [
            [
                'id_sys_group_permission' => '1',
                'id_sys_group' => '1',
                'id_sys_menu_permission' => '1',
                'created_at' => null,
                'updated_at' => null,
                'id_organization' => '17',
            ],
            [
                'id_sys_group_permission' => '2',
                'id_sys_group' => '1',
                'id_sys_menu_permission' => '2',
                'created_at' => null,
                'updated_at' => null,
                'id_organization' => '17',
            ],
            [
                'id_sys_group_permission' => '3',
                'id_sys_group' => '1',
                'id_sys_menu_permission' => '3',
                'created_at' => null,
                'updated_at' => null,
                'id_organization' => null,
            ],
            [
                'id_sys_group_permission' => '4',
                'id_sys_group' => '1',
                'id_sys_menu_permission' => '4',
                'created_at' => null,
                'updated_at' => null,
                'id_organization' => null,
            ],
            [
                'id_sys_group_permission' => '6',
                'id_sys_group' => '1',
                'id_sys_menu_permission' => '5',
                'created_at' => null,
                'updated_at' => null,
                'id_organization' => '17',
            ],
            [
                'id_sys_group_permission' => '7',
                'id_sys_group' => '2',
                'id_sys_menu_permission' => '9',
                'created_at' => '2026-03-09 02:08:15',
                'updated_at' => '2026-03-09 02:08:15',
                'id_organization' => '1',
            ],
            [
                'id_sys_group_permission' => '8',
                'id_sys_group' => '2',
                'id_sys_menu_permission' => '10',
                'created_at' => '2026-03-09 02:08:15',
                'updated_at' => '2026-03-09 02:08:15',
                'id_organization' => '1',
            ],
            [
                'id_sys_group_permission' => '9',
                'id_sys_group' => '1',
                'id_sys_menu_permission' => '9',
                'created_at' => '2026-03-09 02:14:27',
                'updated_at' => '2026-03-09 02:14:27',
                'id_organization' => '17',
            ],
            [
                'id_sys_group_permission' => '10',
                'id_sys_group' => '1',
                'id_sys_menu_permission' => '10',
                'created_at' => '2026-03-09 02:14:27',
                'updated_at' => '2026-03-09 02:14:27',
                'id_organization' => '17',
            ],
            [
                'id_sys_group_permission' => '11',
                'id_sys_group' => '1',
                'id_sys_menu_permission' => '11',
                'created_at' => null,
                'updated_at' => null,
                'id_organization' => null,
            ],
            [
                'id_sys_group_permission' => '12',
                'id_sys_group' => '1',
                'id_sys_menu_permission' => '12',
                'created_at' => null,
                'updated_at' => null,
                'id_organization' => null,
            ],
            [
                'id_sys_group_permission' => '13',
                'id_sys_group' => '1',
                'id_sys_menu_permission' => '13',
                'created_at' => null,
                'updated_at' => null,
                'id_organization' => null,
            ],
            [
                'id_sys_group_permission' => '14',
                'id_sys_group' => '1',
                'id_sys_menu_permission' => '14',
                'created_at' => null,
                'updated_at' => null,
                'id_organization' => null,
            ],
        ];

        foreach (array_chunk($data, 100) as $chunk) {
            DB::table('sys_group_permissions')->insert($chunk);
        }
    }
}
