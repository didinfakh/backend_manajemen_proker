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
        Schema::create('sys_user_groups', function (Blueprint $table) {
                        $table->bigIncrements('id_sys_user_group');
            $table->bigInteger('id_user');
            $table->bigInteger('id_sys_group');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->bigInteger('id_organization')->nullable();
            $table->foreign('id_sys_group')->references('id_sys_group')->on('sys_groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sys_user_groups');
    }
};
