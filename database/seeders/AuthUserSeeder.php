<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AuthUserSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::table('auth_user')->truncate();

        $data = [
        ];

    }
}
