<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_persona', function (Blueprint $table) {
            $table->id('COD_PERSONA');
            $table->string('PRIMER_NOMBRE', 100);
            $table->string('SEGUNDO_NOMBRE', 100)->nullable();
            $table->string('PRIMER_APELLIDO', 100);
            $table->string('SEGUNDO_APELLIDO', 100)->nullable();
            $table->tinyInteger('TIPO_GENERO')->default(0);
            $table->date('FEC_NACIMIENTO')->nullable();
            $table->string('NUM_IDENTIFICACION', 50)->nullable();
            $table->string('ESPECIALIDAD', 150)->nullable();
            $table->string('OCUPACION', 150)->nullable();
            $table->tinyInteger('ESTADO_PERSONA')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_persona');
    }
};
