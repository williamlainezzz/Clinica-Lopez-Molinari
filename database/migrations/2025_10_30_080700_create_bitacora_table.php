<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_bitacora', function (Blueprint $table) {
            $table->id('COD_BITACORA');
            $table->unsignedBigInteger('FK_COD_USUARIO')->nullable();
            $table->string('OBJETO', 100);
            $table->string('ACCION', 50);
            $table->string('DESCRIPCION', 255)->nullable();
            $table->string('IP', 45)->nullable();
            $table->string('USER_AGENT', 255)->nullable();
            $table->timestamps();

            $table->foreign('FK_COD_USUARIO')->references('COD_USUARIO')->on('tbl_usuario')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_bitacora');
    }
};
