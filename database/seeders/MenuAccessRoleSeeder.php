<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuAccessRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organizationId = 17;
        $groupId = 1;

        // 1. Insert Menu 'Akses Role'
        $menuObj = DB::table('sys_menu')
            ->where('url', '/sys-group-permissions')
            ->where('id_organization', $organizationId)
            ->first();

        $idMenu = null;
        if (!$menuObj) {
            $idMenu = DB::table('sys_menu')->insertGetId([
                'name' => 'Akses Role',
                'description' => 'Manajemen Hak Akses Role Grup',
                'url' => '/sys-group-permissions',
                'icon' => '🛡️',
                'menu_order' => 95,
                'visible' => true,
                'id_menu_parent' => null,
                'id_organization' => $organizationId,
                'created_at' => now(),
                'updated_at' => now(),
            ], 'id_sys_menu');
        } else {
            $idMenu = $menuObj->id_sys_menu;
        }

        // 2. Map Permissions (index, create, update, delete)
        $permissions = DB::table('sys_permissions')
            ->whereIn('code', ['index', 'create', 'update', 'delete'])
            ->get();

        foreach ($permissions as $perm) {
            // Check if menu permission mapping exists
            $menuPerm = DB::table('sys_menu_permissions')
                ->where('id_sys_menu', $idMenu)
                ->where('id_sys_permission', $perm->id_sys_permission)
                ->first();

            $idMenuPerm = null;
            if (!$menuPerm) {
                $idMenuPerm = DB::table('sys_menu_permissions')->insertGetId([
                    'id_sys_menu' => $idMenu,
                    'id_sys_permission' => $perm->id_sys_permission,
                    'id_organization' => $organizationId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ], 'id_sys_menu_permission');
            } else {
                $idMenuPerm = $menuPerm->id_sys_menu_permission;
            }

            // 3. Grant access to group 1
            if ($idMenuPerm) {
                $hasAccess = DB::table('sys_group_permissions')
                    ->where('id_sys_group', $groupId)
                    ->where('id_sys_menu_permission', $idMenuPerm)
                    ->first();

                if (!$hasAccess) {
                    DB::table('sys_group_permissions')->insert([
                        'id_sys_group' => $groupId,
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
