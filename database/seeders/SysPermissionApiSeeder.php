<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SysPermissionApiSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::table('sys_permission_api')->truncate();

        $data = [
        ];

    }
}
