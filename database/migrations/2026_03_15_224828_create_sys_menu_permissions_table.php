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
        Schema::create('sys_menu_permissions', function (Blueprint $table) {
                        $table->bigIncrements('id_sys_menu_permission');
            $table->bigInteger('id_sys_menu')->nullable();
            $table->bigInteger('id_sys_permission')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->bigInteger('id_organization')->nullable();
            $table->foreign('id_sys_menu')->references('id_sys_menu')->on('sys_menu')->onDelete('set null');
            $table->foreign('id_sys_permission')->references('id_sys_permission')->on('sys_permissions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sys_menu_permissions');
    }
};
