<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('tbl_invitacion_paciente')) {
            Schema::create('tbl_invitacion_paciente', function (Blueprint $table) {
                $table->bigIncrements('COD_INVITACION');
                $table->unsignedBigInteger('FK_COD_DOCTOR');
                $table->char('TOKEN', 64)->unique();
                $table->integer('USOS_MAX')->default(1);
                $table->integer('USOS_ACTUALES')->default(0);
                $table->dateTime('EXPIRA_EN');
                $table->boolean('ACTIVA')->default(true);
                $table->dateTime('CREATED_AT')->default(DB::raw('CURRENT_TIMESTAMP'));

                $table->index('FK_COD_DOCTOR', 'idx_inv_doctor');
                $table->foreign('FK_COD_DOCTOR')->references('COD_PERSONA')->on('tbl_persona')->onDelete('cascade')->onUpdate('cascade');
            });
        }
    }
    public function down(): void {
        Schema::dropIfExists('tbl_invitacion_paciente');
    }
};