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
        Schema::table('program_sie', function (Blueprint $table) {
            $table->unsignedBigInteger('id_koordinator')->nullable();
            $table->foreign('id_koordinator', 'fk_program_sie_koordinator')->references('id_user')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('program_sie', function (Blueprint $table) {
            $table->dropForeign('fk_program_sie_koordinator');
            $table->dropColumn('id_koordinator');
        });
    }
};
