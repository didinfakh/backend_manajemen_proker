<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::table('task_assignment')->truncate();

        $data = [
        ];

    }
}
