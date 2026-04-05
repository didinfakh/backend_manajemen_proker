<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('task_assignment', function (Blueprint $table) {
            // Drop old foreign key if it exists
            // We use a raw query or try-catch because the name might vary, 
            // but the user's error explicitly named 'fk_task_assignment_user'
            try {
                $table->dropForeign('fk_task_assignment_user');
            } catch (\Exception $e) {
                // If it fails, maybe it uses Laravel's default naming or doesn't exist
                try {
                    $table->dropForeign(['id_user']);
                } catch (\Exception $e2) {
                    // Ignore if not found
                }
            }

            // Ensure id_user is the correct type and points to users table
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_assignment', function (Blueprint $table) {
            $table->dropForeign(['id_user']);
            // We don't necessarily want to restore the old 'broken' constraint here
            // but we could if needed. For now, just dropping the new one is enough for rollback.
        });
    }
};
