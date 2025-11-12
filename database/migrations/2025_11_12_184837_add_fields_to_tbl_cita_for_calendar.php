<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('tbl_cita', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_cita', 'HOR_FIN')) {
                $table->time('HOR_FIN')->nullable()->after('HOR_CITA');
            }
            if (!Schema::hasColumn('tbl_cita', 'OBSERVACIONES')) {
                $table->string('OBSERVACIONES', 255)->nullable()->after('MOT_CITA');
            }
            if (!Schema::hasColumn('tbl_cita', 'ORIGEN')) {
                $table->string('ORIGEN', 20)->default('RECEPCION')->after('OBSERVACIONES');
            }
            if (!Schema::hasColumn('tbl_cita', 'USUARIO_CREA')) {
                $table->bigInteger('USUARIO_CREA')->nullable()->after('ORIGEN');
            }
            if (!Schema::hasColumn('tbl_cita', 'USUARIO_MOD')) {
                $table->bigInteger('USUARIO_MOD')->nullable()->after('USUARIO_CREA');
            }

            $table->index(['FEC_CITA', 'FK_COD_DOCTOR'], 'idx_cita_fecha_doctor');
            $table->index(['ESTADO_CITA'], 'idx_cita_estado');
        });
    }

    public function down(): void {
        Schema::table('tbl_cita', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_cita', 'USUARIO_MOD')) $table->dropColumn('USUARIO_MOD');
            if (Schema::hasColumn('tbl_cita', 'USUARIO_CREA')) $table->dropColumn('USUARIO_CREA');
            if (Schema::hasColumn('tbl_cita', 'ORIGEN'))      $table->dropColumn('ORIGEN');
            if (Schema::hasColumn('tbl_cita', 'OBSERVACIONES')) $table->dropColumn('OBSERVACIONES');
            if (Schema::hasColumn('tbl_cita', 'HOR_FIN'))     $table->dropColumn('HOR_FIN');

            $table->dropIndex('idx_cita_fecha_doctor');
            $table->dropIndex('idx_cita_estado');
        });
    }
};
