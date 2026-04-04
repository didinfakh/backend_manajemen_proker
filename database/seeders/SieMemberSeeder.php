<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SieMemberSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::table('program_sie_member')->truncate();

        $data = [
        ];

    }
}
