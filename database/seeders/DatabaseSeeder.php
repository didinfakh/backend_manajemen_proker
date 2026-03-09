<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Get the default organization (or fallback to 17)
        $organizationId = 17;

        // 1. Create permissions if they don't exist
        $permissions = [
            ['code' => 'index', 'description' => 'Index Access'],
            ['code' => 'create', 'description' => 'Create Access'],
            ['code' => 'update', 'description' => 'Update Access'],
            ['code' => 'delete', 'description' => 'Delete Access'],
        ];

        foreach ($permissions as $p) {
            $exists = DB::table('sys_permissions')
                ->where('code', $p['code'])
                ->first();

            if (!$exists) {
                DB::table('sys_permissions')->insert([
                    'code' => $p['code'],
                    'description' => $p['description'],
                    'id_organization' => $organizationId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Get the 'index' permission ID to attach to menus
        $readPermission = DB::table('sys_permissions')
            ->where('code', 'index')
            ->first();

        // 2. Default System Admin Group (Force ID 1)
        $idGroupAdmin = 1;
        $groupAdmin = DB::table('sys_groups')->where('id_sys_group', $idGroupAdmin)->first();
        
        if (!$groupAdmin) {
            DB::table('sys_groups')->insert([
                'id_sys_group' => $idGroupAdmin,
                'name' => 'Super Admin',
                'description' => 'Administrator group with full access',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 3. Menus to insert
        $menus = [
            [
                'name' => 'Master Menu',
                'description' => 'Manage system menus',
                'url' => '/master-menu',
                'icon' => '📂',
                'menu_order' => 90,
            ],
            [
                'name' => 'Setting Role',
                'description' => 'Manage application roles and permissions',
                'url' => '/role',
                'icon' => '🔑',
                'menu_order' => 91,
            ]
        ];

        foreach ($menus as $m) {
            $menuObj = DB::table('sys_menu')
                ->where('url', $m['url'])
                ->where('id_organization', $organizationId)
                ->first();

            $idMenu = null;
            if (!$menuObj) {
                $idMenu = DB::table('sys_menu')->insertGetId([
                    'name' => $m['name'],
                    'description' => $m['description'],
                    'url' => $m['url'],
                    'icon' => $m['icon'],
                    'menu_order' => $m['menu_order'],
                    'visible' => true,
                    'id_organization' => $organizationId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ], 'id_sys_menu');
            } else {
                $idMenu = $menuObj->id_sys_menu;
            }

            // Create Menu Permission Mapping
            if ($readPermission) {
                $menuPerm = DB::table('sys_menu_permissions')
                    ->where('id_sys_menu', $idMenu)
                    ->where('id_sys_permission', $readPermission->id_sys_permission)
                    ->first();

                $idMenuPerm = null;
                if (!$menuPerm) {
                    $idMenuPerm = DB::table('sys_menu_permissions')->insertGetId([
                        'id_sys_menu' => $idMenu,
                        'id_sys_permission' => $readPermission->id_sys_permission,
                        'id_organization' => $organizationId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ], 'id_sys_menu_permission');
                } else {
                    $idMenuPerm = $menuPerm->id_sys_menu_permission;
                }

                // Grant access to Super Admin
                if ($idMenuPerm && $idGroupAdmin) {
                    $hasAccess = DB::table('sys_group_permissions')
                        ->where('id_sys_group', $idGroupAdmin)
                        ->where('id_sys_menu_permission', $idMenuPerm)
                        ->first();

                    if (!$hasAccess) {
                        DB::table('sys_group_permissions')->insert([
                            'id_sys_group' => $idGroupAdmin,
                            'id_sys_menu_permission' => $idMenuPerm,
                            'id_organization' => $organizationId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
    }
}
