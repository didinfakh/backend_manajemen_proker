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
        // Create the dynamic task_status table
        Schema::create('task_status', function (Blueprint $table) {
            $table->bigIncrements('id_task_status');
            $table->unsignedBigInteger('id_program');
            $table->unsignedBigInteger('id_sie')->nullable();
            $table->unsignedBigInteger('id_organization')->nullable();
            $table->string('kode', 50)->nullable();
            $table->string('nama', 100);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_program')->references('id_program')->on('program_kerja')->onDelete('cascade');
            $table->foreign('id_sie')->references('id_sie')->on('program_sie')->onDelete('cascade');
        });

        // Update task table to reference the new task_status table
        Schema::table('task', function (Blueprint $table) {
            $table->renameColumn('id_master_status_task', 'id_task_status');
        });

        Schema::table('task', function (Blueprint $table) {
            $table->unsignedBigInteger('id_task_status')->change();
            $table->foreign('id_task_status')->references('id_task_status')->on('task_status')->onDelete('set null');
        });

        // Drop the legacy master_status_task table
        Schema::dropIfExists('master_status_task');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task', function (Blueprint $table) {
            $table->dropForeign(['id_task_status']);
            $table->dropColumn('id_task_status');
        });

        Schema::dropIfExists('task_status');

        // Recreate legacy master_status_task table if needed for rollback
        Schema::create('master_status_task', function (Blueprint $table) {
            $table->increments('id_master_status_task');
            $table->string('nama_status', 50);
            $table->string('kode_status', 20);
            $table->integer('urutan');
            $table->boolean('is_done')->nullable()->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
