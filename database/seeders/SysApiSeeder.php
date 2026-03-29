<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SysApiSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::table('sys_api')->truncate();

        $data = [
        ];

    }
}
