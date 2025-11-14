<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_correo', function (Blueprint $table) {
            $table->id('COD_CORREO');
            $table->unsignedBigInteger('FK_COD_PERSONA');
            $table->string('CORREO', 150);
            $table->string('TIPO_CORREO', 30)->default('PERSONAL');
            $table->tinyInteger('ES_PRINCIPAL')->default(0);
            $table->timestamps();

            $table->foreign('FK_COD_PERSONA')->references('COD_PERSONA')->on('tbl_persona')->cascadeOnDelete();
        });

        Schema::create('tbl_telefono', function (Blueprint $table) {
            $table->id('COD_TELEFONO');
            $table->unsignedBigInteger('FK_COD_PERSONA');
            $table->string('NUM_TELEFONO', 25);
            $table->string('TIPO_TELEFONO', 30)->default('MOVIL');
            $table->tinyInteger('ES_PRINCIPAL')->default(0);
            $table->timestamps();

            $table->foreign('FK_COD_PERSONA')->references('COD_PERSONA')->on('tbl_persona')->cascadeOnDelete();
        });

        Schema::create('tbl_direccion', function (Blueprint $table) {
            $table->id('COD_DIRECCION');
            $table->unsignedBigInteger('FK_COD_PERSONA');
            $table->string('DEPARTAMENTO', 60)->nullable();
            $table->string('MUNICIPIO', 60)->nullable();
            $table->string('CIUDAD', 60)->nullable();
            $table->string('COLONIA', 120)->nullable();
            $table->string('REFERENCIA', 255)->nullable();
            $table->timestamps();

            $table->foreign('FK_COD_PERSONA')->references('COD_PERSONA')->on('tbl_persona')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_direccion');
        Schema::dropIfExists('tbl_telefono');
        Schema::dropIfExists('tbl_correo');
    }
};
