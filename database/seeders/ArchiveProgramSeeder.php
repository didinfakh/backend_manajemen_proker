<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArchiveProgramSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::table('archive_program')->truncate();

        $data = [
        ];

    }
}
