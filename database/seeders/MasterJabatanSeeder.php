<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterJabatanSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::table('master_jabatan')->truncate();

        $data = [
        ];

    }
}
