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
        Schema::create('auth_user', function (Blueprint $table) {
                        $table->bigIncrements('id_auth_user');
            $table->bigInteger('id_master_jabatan')->nullable();
            $table->bigInteger('id_master_group')->nullable();
            $table->string('name', 255);
            $table->string('email', 255);
            $table->string('password', 255);
            $table->string('role_global', 100)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->bigInteger('id_organization')->nullable();
            $table->foreign('id_master_group')->references('id_master_group')->on('master_group');
            $table->foreign('id_master_jabatan')->references('id_master_jabatan')->on('master_jabatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auth_user');
    }
};
