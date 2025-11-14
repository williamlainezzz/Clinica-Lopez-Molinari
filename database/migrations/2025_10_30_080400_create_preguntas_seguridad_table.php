<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_pregunta_seguridad', function (Blueprint $table) {
            $table->id('COD_PREGUNTA');
            $table->string('PREGUNTA', 255);
            $table->timestamps();
        });

        Schema::create('tbl_usuario_pregunta', function (Blueprint $table) {
            $table->id('COD_USUARIO_PREGUNTA');
            $table->unsignedBigInteger('FK_COD_USUARIO');
            $table->unsignedBigInteger('FK_COD_PREGUNTA');
            $table->string('RESPUESTA_HASH');
            $table->timestamps();

            $table->foreign('FK_COD_USUARIO')->references('COD_USUARIO')->on('tbl_usuario')->cascadeOnDelete();
            $table->foreign('FK_COD_PREGUNTA')->references('COD_PREGUNTA')->on('tbl_pregunta_seguridad')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_usuario_pregunta');
        Schema::dropIfExists('tbl_pregunta_seguridad');
    }
};
