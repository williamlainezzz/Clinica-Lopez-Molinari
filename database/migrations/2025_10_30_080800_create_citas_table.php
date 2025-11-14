<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_cita', function (Blueprint $table) {
            $table->id('COD_CITA');
            $table->unsignedBigInteger('FK_COD_DOCTOR')->nullable();
            $table->unsignedBigInteger('FK_COD_PACIENTE');
            $table->date('FEC_CITA');
            $table->time('HORA_CITA');
            $table->string('ESTADO_CITA', 30)->default('PENDIENTE');
            $table->string('MOTIVO_CITA', 255);
            $table->string('UBICACION', 120)->nullable();
            $table->unsignedSmallInteger('DURACION_MINUTOS')->default(30);
            $table->string('CANAL', 50)->nullable();
            $table->text('NOTAS_CITA')->nullable();
            $table->timestamps();

            $table->foreign('FK_COD_DOCTOR')->references('COD_PERSONA')->on('tbl_persona')->nullOnDelete();
            $table->foreign('FK_COD_PACIENTE')->references('COD_PERSONA')->on('tbl_persona')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_cita');
    }
};
