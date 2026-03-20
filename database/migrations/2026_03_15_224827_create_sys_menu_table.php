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
        Schema::create('sys_menu', function (Blueprint $table) {
                        $table->bigIncrements('id_sys_menu');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->bigInteger('id_organization')->nullable();
            $table->string('url', 255)->nullable();
            $table->boolean('visible')->nullable()->default(true);
            $table->bigInteger('id_menu_parent')->nullable();
            $table->integer('menu_order')->nullable();
            $table->string('icon', 20)->nullable();
            $table->foreign('id_menu_parent')->references('id_sys_menu')->on('sys_menu')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sys_menu');
    }
};
