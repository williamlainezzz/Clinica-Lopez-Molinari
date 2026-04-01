<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('tbl_notificacion_usuario')) {
            return;
        }

        Schema::create('tbl_notificacion_usuario', function (Blueprint $table) {
            $table->bigIncrements('COD_NU');
            $table->unsignedBigInteger('FK_COD_NOTIFICACION');
            $table->unsignedBigInteger('FK_COD_USUARIO');
            $table->boolean('LEIDA')->default(false);
            $table->timestamp('FEC_LEIDA')->nullable();

            $table->unique(['FK_COD_NOTIFICACION', 'FK_COD_USUARIO'], 'uq_notif_usuario');
            $table->index(['FK_COD_USUARIO', 'LEIDA'], 'idx_notif_usuario_leida');
            $table->index('FK_COD_NOTIFICACION', 'idx_notif_usuario_notif');
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('tbl_notificacion_usuario')) {
            Schema::drop('tbl_notificacion_usuario');
        }
    }
};

