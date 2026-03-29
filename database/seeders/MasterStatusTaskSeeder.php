<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterStatusTaskSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::table('master_status_task')->truncate();

        $data = [
        ];

    }
}
