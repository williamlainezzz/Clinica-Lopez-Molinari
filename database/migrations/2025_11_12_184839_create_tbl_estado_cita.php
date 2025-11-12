<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tbl_estado_cita', function (Blueprint $table) {
            $table->bigIncrements('COD_ESTADO');
            $table->string('NOM_ESTADO', 30)->unique();
        });
    }
    public function down(): void {
        Schema::dropIfExists('tbl_estado_cita');
    }
};

