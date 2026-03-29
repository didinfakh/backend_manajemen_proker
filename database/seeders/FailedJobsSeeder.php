<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FailedJobsSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::table('failed_jobs')->truncate();

        $data = [
        ];

    }
}
