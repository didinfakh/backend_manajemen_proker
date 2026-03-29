<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DanaPengeluaranSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::table('dana_pengeluaran')->truncate();

        $data = [
        ];

    }
}
