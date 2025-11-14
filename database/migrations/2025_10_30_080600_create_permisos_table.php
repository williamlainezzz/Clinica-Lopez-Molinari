<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_permiso', function (Blueprint $table) {
            $table->id('COD_PERMISO');
            $table->unsignedBigInteger('FK_COD_ROL');
            $table->unsignedBigInteger('FK_COD_OBJETO');
            $table->tinyInteger('ESTADO_PERMISO')->default(1);
            $table->tinyInteger('PER_SELECT')->default(0);
            $table->tinyInteger('PER_INSERTAR')->default(0);
            $table->tinyInteger('PER_UPDATE')->default(0);
            $table->tinyInteger('PER_DELETE')->default(0);
            $table->tinyInteger('VER')->default(0);
            $table->tinyInteger('CREAR')->default(0);
            $table->tinyInteger('EDITAR')->default(0);
            $table->tinyInteger('ELIMINAR')->default(0);
            $table->timestamps();

            $table->foreign('FK_COD_ROL')->references('COD_ROL')->on('tbl_rol')->cascadeOnDelete();
            $table->foreign('FK_COD_OBJETO')->references('COD_OBJETO')->on('tbl_objeto')->cascadeOnDelete();
            $table->unique(['FK_COD_ROL', 'FK_COD_OBJETO'], 'uq_permiso_rol_obj');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_permiso');
    }
};
