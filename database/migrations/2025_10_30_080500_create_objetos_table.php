<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_objeto', function (Blueprint $table) {
            $table->id('COD_OBJETO');
            $table->string('NOM_OBJETO', 150);
            $table->string('DESC_OBJETO', 255)->nullable();
            $table->string('TIPO_OBJETO', 50)->default('MENU');
            $table->string('URL_OBJETO', 150)->nullable();
            $table->tinyInteger('ESTADO_OBJETO')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_objeto');
    }
};
