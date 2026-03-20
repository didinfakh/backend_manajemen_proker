<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SysMenu;
use App\Models\SysMenuPermission;

class SysMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Sesuaikan dengan organization default yang ada di DatabaseSeeder
        $organizationId = 17;

        // Data Master Menu
        $menus = [
            [
                'url' => '/menu',
                'name' => ' Menu',
                'description' => 'Manage system menus',
                'icon' => 'fa fa-cogs',
                'menu_order' => 90,
                'visible' => 1,
                'id_menu_parent' => 2,
            ],
            // [
            //     'url' => '/role',
            //     'name' => 'Setting Role',
            //     'description' => 'Manage application roles and permissions',
            //     'icon' => 'fa fa-key',
            //     'menu_order' => 91,
            //     'visible' => 1,
            // ]
        ];

        foreach ($menus as $m) {
            // Kita gunakan updateOrCreate agar tidak duplikat jika di-run berkali-kali
            $menu = SysMenu::updateOrCreate(
                [
                    'url' => $m['url'],
                    'id_organization' => $organizationId
                ],
                [
                    'name' => $m['name'],
                    'description' => $m['description'],
                    'icon' => $m['icon'],
                    'menu_order' => $m['menu_order'],
                    'visible' => $m['visible'],
                ]
            );

            // Otomatis assign permissions 1, 2, 3, 4
            $permissions = [1, 2, 3, 4];
            foreach ($permissions as $permissionId) {
                // Gunakan updateOrCreate atau firstOrCreate untuk mencegah duplikat
                SysMenuPermission::firstOrCreate([
                    'id_sys_menu' => $menu->getKey(),
                    'id_sys_permission' => $permissionId,
                    'id_organization' => $organizationId,
                ]);
            }
        }

        $this->command->info('Master Menu & Role Menu beserta permissions telah berhasil di-seed.');
    }
}
