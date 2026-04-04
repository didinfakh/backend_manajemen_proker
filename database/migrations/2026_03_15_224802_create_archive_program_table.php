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
        Schema::create('archive_program', function (Blueprint $table) {
                        $table->bigIncrements('id_archive_program');
            $table->bigInteger('id_program');
            $table->string('archive_year', 10)->nullable();
            $table->text('summary')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->bigInteger('id_organization')->nullable();
            $table->foreign('id_program')->references('id_program')->on('program_kerja');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archive_program');
    }
};
