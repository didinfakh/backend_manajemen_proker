<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SessionsSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::table('sessions')->truncate();

        $data = [
            [
                'id' => '4grpHXG9FUgxKKb76SnKbmVswFwXycVmcmdijPh7',
                'user_id' => null,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',
                'payload' => 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOXJrZkdidGRaTDRCYldKaGJ1Ymh0clRDUDY0MDdKcUlFQUhWbVkxNSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMS9zYW5jdHVtL2NzcmYtY29va2llIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',
                'last_activity' => '1769757742',
            ],
        ];

        foreach (array_chunk($data, 100) as $chunk) {
            DB::table('sessions')->insert($chunk);
        }
    }
}
