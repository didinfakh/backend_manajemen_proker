<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CacheSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::table('cache')->truncate();

        $data = [
            [
                'key' => 'perm:user:1',
                'value' => 'a:2:{i:0;a:6:{s:3:"url";s:5:"/role";s:7:"id_menu";i:4;s:5:"label";s:16:"Setting Roleeeee";s:4:"icon";s:4:"🔑";s:7:"actions";a:1:{i:0;s:5:"index";}s:8:"children";a:0:{}}i:1;a:6:{s:3:"url";N;s:7:"id_menu";i:2;s:5:"label";s:6:"master";s:4:"icon";N;s:7:"actions";a:1:{i:0;s:5:"index";}s:8:"children";a:1:{i:0;a:6:{s:3:"url";s:5:"/menu";s:7:"id_menu";i:5;s:5:"label";s:5:" Menu";s:4:"icon";s:10:"fa fa-cogs";s:7:"actions";a:4:{i:0;s:3:"add";i:1;s:6:"delete";i:2;s:4:"edit";i:3;s:5:"index";}s:8:"children";a:0:{}}}}}',
                'expiration' => '1774728339',
            ],
        ];

        foreach (array_chunk($data, 100) as $chunk) {
            DB::table('cache')->insert($chunk);
        }
    }
}
