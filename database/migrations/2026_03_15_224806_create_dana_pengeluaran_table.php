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
        Schema::create('dana_pengeluaran', function (Blueprint $table) {
                        $table->bigIncrements('id_dana_pengeluaran');
            $table->bigInteger('id_task');
            $table->string('nama_barang', 255)->nullable();
            $table->integer('jumlah')->nullable();
            $table->text('bukti_foto')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->bigInteger('id_organization')->nullable();
            $table->foreign('id_task')->references('id_task')->on('task')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dana_pengeluaran');
    }
};
