<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramKerjaMenuSeeder extends Seeder
{
    public function run(): void
    {
        // Cek apakah menu "Program Kerja" sudah ada
        $exists = DB::table('sys_menu')->where('name', 'Program Kerja')->first();

        if (!$exists) {
            DB::table('sys_menu')->insert([
                'name'           => 'Program Kerja',
                'description'    => 'Manajemen Program Kerja',
                'url'            => '/program-kerja',
                'visible'        => true,
                'id_menu_parent' => 2,
                'menu_order'     => 100,
                'icon'           => 'fa fa-clipboard-list',
                'id_organization'=> 17,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }
    }
}
