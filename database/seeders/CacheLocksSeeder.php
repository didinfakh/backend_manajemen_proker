<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CacheLocksSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::table('cache_locks')->truncate();

        $data = [
        ];

    }
}
