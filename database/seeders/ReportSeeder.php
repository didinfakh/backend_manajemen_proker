<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::table('report')->truncate();

        $data = [
        ];

    }
}
