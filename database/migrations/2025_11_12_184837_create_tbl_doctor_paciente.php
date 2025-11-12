<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tbl_doctor_paciente', function (Blueprint $table) {
            $table->bigIncrements('COD_DP');
            $table->unsignedBigInteger('FK_COD_DOCTOR');
            $table->unsignedBigInteger('FK_COD_PACIENTE');
            $table->dateTime('FEC_ASIGNACION')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->boolean('ACTIVO')->default(true);

            $table->unique(['FK_COD_DOCTOR','FK_COD_PACIENTE'], 'uq_doctor_paciente');
            $table->index('FK_COD_DOCTOR', 'idx_dp_doctor');
            $table->index('FK_COD_PACIENTE', 'idx_dp_paciente');

            $table->foreign('FK_COD_DOCTOR')->references('COD_PERSONA')->on('tbl_persona')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('FK_COD_PACIENTE')->references('COD_PERSONA')->on('tbl_persona')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('tbl_doctor_paciente');
    }
};
