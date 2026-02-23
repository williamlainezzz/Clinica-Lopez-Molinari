<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tbl_usuario')) {
            return;
        }

        Schema::table('tbl_usuario', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_usuario', 'PWD_ACTUALIZADA_EN')) {
                $table->timestamp('PWD_ACTUALIZADA_EN')->nullable()->after('PWD_USUARIO');
            }

            if (!Schema::hasColumn('tbl_usuario', 'PWD_RECORDATORIO_ENVIADO_EN')) {
                $table->timestamp('PWD_RECORDATORIO_ENVIADO_EN')->nullable()->after('PWD_ACTUALIZADA_EN');
            }

            if (!Schema::hasColumn('tbl_usuario', 'FORZAR_CAMBIO_PWD')) {
                $table->boolean('FORZAR_CAMBIO_PWD')->default(false)->after('PWD_RECORDATORIO_ENVIADO_EN');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('tbl_usuario')) {
            return;
        }

        Schema::table('tbl_usuario', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_usuario', 'FORZAR_CAMBIO_PWD')) {
                $table->dropColumn('FORZAR_CAMBIO_PWD');
            }

            if (Schema::hasColumn('tbl_usuario', 'PWD_RECORDATORIO_ENVIADO_EN')) {
                $table->dropColumn('PWD_RECORDATORIO_ENVIADO_EN');
            }

            if (Schema::hasColumn('tbl_usuario', 'PWD_ACTUALIZADA_EN')) {
                $table->dropColumn('PWD_ACTUALIZADA_EN');
            }
        });
    }
};
