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
        $tables = [
            'users',
            'sys_permissions',
            'sys_menu',
            'sys_groups',
            'sys_group_permissions',
            'sys_menu_permissions',
            'sys_user_groups',
            'master_group',
            'master_jabatan',
            'master_organization',
            'sys_permission_api',
            'auth_user',
        ];

        foreach ($tables as $table) {
            try {
                if (!Schema::hasColumn($table, 'deleted_at')) {
                    Schema::table($table, function (Blueprint $table) {
                        $table->softDeletes();
                    });
                }
            } catch (\Exception $e) {
                // Skip if error occurs (e.g. table not found or column already exists)
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'users',
            'sys_permissions',
            'sys_menu',
            'sys_groups',
            'sys_group_permissions',
            'sys_menu_permissions',
            'sys_user_groups',
            'master_group',
            'master_jabatan',
            'master_organization',
            'sys_permission_api',
            'auth_user',
        ];

        foreach ($tables as $table) {
            try {
                if (Schema::hasColumn($table, 'deleted_at')) {
                    Schema::table($table, function (Blueprint $table) {
                        $table->dropSoftDeletes();
                    });
                }
            } catch (\Exception $e) {
                // Skip
            }
        }
    }
};
