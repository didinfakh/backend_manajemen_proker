<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SysMenuSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::table('sys_menu')->truncate();

        $data = [
            [
                'id_sys_menu' => '2',
                'name' => 'master',
                'description' => 'menu master',
                'created_at' => null,
                'updated_at' => null,
                'id_organization' => '17',
                'url' => null,
                'visible' => 't',
                'id_menu_parent' => null,
                'menu_order' => null,
                'icon' => null,
            ],
            [
                'id_sys_menu' => '5',
                'name' => ' Menu',
                'description' => 'Manage system menus',
                'created_at' => '2026-03-28 13:30:48',
                'updated_at' => '2026-03-28 13:30:48',
                'id_organization' => '17',
                'url' => '/menu',
                'visible' => 't',
                'id_menu_parent' => '2',
                'menu_order' => '90',
                'icon' => 'fa fa-cogs',
            ],
            [
                'id_sys_menu' => '4',
                'name' => 'Setting Roleeeee',
                'description' => 'Manage application roles and permissions',
                'created_at' => '2026-03-09 02:08:15',
                'updated_at' => '2026-03-09 02:08:15',
                'id_organization' => '17',
                'url' => '/role',
                'visible' => 't',
                'id_menu_parent' => null,
                'menu_order' => '91',
                'icon' => '🔑',
            ],
        ];

        foreach (array_chunk($data, 100) as $chunk) {
            DB::table('sys_menu')->insert($chunk);
        }
    }
}
