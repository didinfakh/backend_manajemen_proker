<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SieSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::table('program_sie')->truncate();

        $data = [
        ];

    }
}
