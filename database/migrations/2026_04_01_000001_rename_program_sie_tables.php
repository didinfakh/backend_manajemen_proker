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
        // 1. Drop semua FK yang merujuk ke tabel lama (Gunakan nama constraint yang ada di DB)
        Schema::table('archive_program', function (Blueprint $table) {
            $table->dropForeign('fk_archive_program_program');
        });
        Schema::table('task', function (Blueprint $table) {
            $table->dropForeign('fk_task_program');
            $table->dropForeign('fk_task_sie');
        });
        Schema::table('sie_member', function (Blueprint $table) {
            $table->dropForeign('fk_sie_member_sie');
            $table->dropForeign('fk_sie_member_user');
        });
        Schema::table('sie', function (Blueprint $table) {
            $table->dropForeign('fk_sie_program');
        });

        // 2. Rename tabel
        Schema::rename('sie_member', 'program_sie_member');
        Schema::rename('sie', 'program_sie');
        Schema::rename('program', 'program_kerja');

        // 3. Buat ulang FK dengan nama tabel baru
        Schema::table('program_sie', function (Blueprint $table) {
            $table->foreign('id_program', 'fk_program_sie_program_kerja')->references('id_program')->on('program_kerja');
        });
        Schema::table('program_sie_member', function (Blueprint $table) {
            $table->foreign('id_sie', 'fk_program_sie_member_program_sie')->references('id_sie')->on('program_sie')->onDelete('cascade');
            $table->foreign('id_auth_user', 'fk_program_sie_member_auth_user')->references('id_auth_user')->on('auth_user');
        });
        Schema::table('archive_program', function (Blueprint $table) {
            $table->foreign('id_program', 'fk_archive_program_program_kerja')->references('id_program')->on('program_kerja');
        });
        Schema::table('task', function (Blueprint $table) {
            $table->foreign('id_program', 'fk_task_program_kerja')->references('id_program')->on('program_kerja');
            $table->foreign('id_sie', 'fk_task_program_sie')->references('id_sie')->on('program_sie');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop FK baru
        Schema::table('task', function (Blueprint $table) {
            $table->dropForeign('fk_task_program_kerja');
            $table->dropForeign('fk_task_program_sie');
        });
        Schema::table('archive_program', function (Blueprint $table) {
            $table->dropForeign('fk_archive_program_program_kerja');
        });
        Schema::table('program_sie_member', function (Blueprint $table) {
            $table->dropForeign('fk_program_sie_member_program_sie');
            $table->dropForeign('fk_program_sie_member_auth_user');
        });
        Schema::table('program_sie', function (Blueprint $table) {
            $table->dropForeign('fk_program_sie_program_kerja');
        });

        // Rename balik
        Schema::rename('program_kerja', 'program');
        Schema::rename('program_sie', 'sie');
        Schema::rename('program_sie_member', 'sie_member');

        // Buat ulang FK lama dengan nama asli
        Schema::table('sie', function (Blueprint $table) {
            $table->foreign('id_program', 'fk_sie_program')->references('id_program')->on('program');
        });
        Schema::table('sie_member', function (Blueprint $table) {
            $table->foreign('id_sie', 'fk_sie_member_sie')->references('id_sie')->on('sie')->onDelete('cascade');
            $table->foreign('id_auth_user', 'fk_sie_member_user')->references('id_auth_user')->on('auth_user');
        });
        Schema::table('archive_program', function (Blueprint $table) {
            $table->foreign('id_program', 'fk_archive_program_program')->references('id_program')->on('program');
        });
        Schema::table('task', function (Blueprint $table) {
            $table->foreign('id_program', 'fk_task_program')->references('id_program')->on('program');
            $table->foreign('id_sie', 'fk_task_sie')->references('id_sie')->on('sie');
        });
    }
};
