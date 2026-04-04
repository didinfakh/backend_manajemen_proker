<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('program_kerja', function (Blueprint $table) {
            // 1. Drop existing FK constraint
            // The name discovered earlier was 'fk_program_leader'
            $table->dropForeign('fk_program_leader');

            // 2. Rename the column
            $table->renameColumn('id_auth_user_leader', 'id_user_leader');
        });

        Schema::table('program_kerja', function (Blueprint $table) {
            // 3. Add new FK constraint to users table
            $table->foreign('id_user_leader', 'fk_program_kerja_user_leader')
                  ->references('id_user')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('program_kerja', function (Blueprint $table) {
            $table->dropForeign('fk_program_kerja_user_leader');
            $table->renameColumn('id_user_leader', 'id_auth_user_leader');
        });

        Schema::table('program_kerja', function (Blueprint $table) {
            $table->foreign('id_auth_user_leader', 'fk_program_leader')
                  ->references('id_auth_user')
                  ->on('auth_user')
                  ->onDelete('set null');
        });
    }
};
