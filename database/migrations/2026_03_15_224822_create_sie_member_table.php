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
        Schema::create('sie_member', function (Blueprint $table) {
                        $table->bigIncrements('id_sie_member');
            $table->bigInteger('id_sie');
            $table->bigInteger('id_auth_user');
            $table->string('role', 100)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->bigInteger('id_organization')->nullable();
            $table->foreign('id_sie')->references('id_sie')->on('sie')->onDelete('cascade');
            $table->foreign('id_auth_user')->references('id_auth_user')->on('auth_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sie_member');
    }
};
