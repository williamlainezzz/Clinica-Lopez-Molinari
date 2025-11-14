<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_rol', function (Blueprint $table) {
            $table->id('COD_ROL');
            $table->string('NOM_ROL', 100)->unique();
            $table->string('DESC_ROL', 255)->nullable();
            $table->tinyInteger('ESTADO_ROL')->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_rol');
    }
};
