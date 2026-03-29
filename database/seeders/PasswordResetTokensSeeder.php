<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PasswordResetTokensSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::table('password_reset_tokens')->truncate();

        $data = [
        ];

    }
}
