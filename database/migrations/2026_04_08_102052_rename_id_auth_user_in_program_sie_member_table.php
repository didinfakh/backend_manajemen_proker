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
        Schema::table('program_sie_member', function (Blueprint $table) {
            // Drop old foreign key if it exists
            $table->dropForeign('fk_program_sie_member_auth_user');
            
            // Rename column
            $table->renameColumn('id_auth_user', 'id_user');
            
            // Add new foreign key referencing users table
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('program_sie_member', function (Blueprint $table) {
            $table->dropForeign(['id_user']);
            $table->renameColumn('id_user', 'id_auth_user');
            $table->foreign('id_auth_user', 'fk_program_sie_member_auth_user')->references('id_auth_user')->on('auth_user');
        });
    }
};
