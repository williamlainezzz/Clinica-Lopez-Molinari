<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_usuario', function (Blueprint $table) {
            $table->id('COD_USUARIO');
            $table->string('USR_USUARIO', 50)->unique();
            $table->string('PWD_USUARIO');
            $table->unsignedBigInteger('FK_COD_PERSONA');
            $table->unsignedBigInteger('FK_COD_ROL');
            $table->tinyInteger('ESTADO_USUARIO')->default(1);
            $table->timestamp('FEC_ULTIMO_ACCESO')->nullable();

            $table->foreign('FK_COD_PERSONA')->references('COD_PERSONA')->on('tbl_persona')->cascadeOnDelete();
            $table->foreign('FK_COD_ROL')->references('COD_ROL')->on('tbl_rol');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_usuario');
    }
};
