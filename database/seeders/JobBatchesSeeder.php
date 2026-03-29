<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobBatchesSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::table('job_batches')->truncate();

        $data = [
        ];

    }
}
