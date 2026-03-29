<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobsSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::table('jobs')->truncate();

        $data = [
        ];

    }
}
