<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::table('users')->truncate();

        $data = [
            [
                'id_user' => '1',
                'name' => 'a',
                'email' => 'a@a',
                'email_verified_at' => null,
                'password' => '$2y$12$bHB4wIHmhCBHpS.tAyMtz.e7SI501QFF.odw3fglOaol/I9xLki.C',
                'remember_token' => null,
                'created_at' => '2026-01-10 08:42:29',
                'updated_at' => '2026-01-10 08:42:29',
                'id_organization' => '17',
            ],
        ];

        foreach (array_chunk($data, 100) as $chunk) {
            DB::table('users')->insert($chunk);
        }
    }
}
