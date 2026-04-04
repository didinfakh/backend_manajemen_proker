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
        Schema::create('task', function (Blueprint $table) {
                        $table->bigIncrements('id_task');
            $table->bigInteger('id_program');
            $table->bigInteger('id_sie')->nullable();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('status', 50)->nullable();
            $table->date('due_date')->nullable();
            $table->boolean('has_expense')->nullable()->default(false);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->bigInteger('id_organization')->nullable();
            $table->integer('id_master_status_task')->nullable();
            $table->foreign('id_program')->references('id_program')->on('program_kerja');
            $table->foreign('id_sie')->references('id_sie')->on('program_sie');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task');
    }
};
