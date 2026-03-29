<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();

        $this->call([
            MasterGroupSeeder::class,
            MasterJabatanSeeder::class,
            CacheSeeder::class,
            CacheLocksSeeder::class,
            FailedJobsSeeder::class,
            JobBatchesSeeder::class,
            JobsSeeder::class,
            DanaPengeluaranSeeder::class,
            MasterOrganizationSeeder::class,
            MasterStatusTaskSeeder::class,
            PasswordResetTokensSeeder::class,
            ReportSeeder::class,
            ProgramSeeder::class,
            SessionsSeeder::class,
            SysApiSeeder::class,
            SysPermissionsSeeder::class,
            SysGroupsSeeder::class,
            SysMenuSeeder::class,
            UsersSeeder::class,
            SysGroupPermissionsSeeder::class,
            SysPermissionApiSeeder::class,
            SysUserGroupsSeeder::class,
            TaskAssignmentSeeder::class,
            ArchiveProgramSeeder::class,
            AuthUserSeeder::class,
            TaskSeeder::class,
            SysMenuPermissionsSeeder::class,
            SieSeeder::class,
            SieMemberSeeder::class,
            MenuAccessRoleSeeder::class,
        ]);

        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
    }
}
