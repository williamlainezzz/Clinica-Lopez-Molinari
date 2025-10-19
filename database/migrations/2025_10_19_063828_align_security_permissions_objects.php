<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Agregar flags VER/CREAR/EDITAR/ELIMINAR (aditivo, sin romper lo anterior)
        Schema::table('tbl_permiso', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_permiso', 'VER')) {
                $table->tinyInteger('VER')->default(0)->after('ESTADO_PERMISO');
            }
            if (!Schema::hasColumn('tbl_permiso', 'CREAR')) {
                $table->tinyInteger('CREAR')->default(0)->after('VER');
            }
            if (!Schema::hasColumn('tbl_permiso', 'EDITAR')) {
                $table->tinyInteger('EDITAR')->default(0)->after('CREAR');
            }
            if (!Schema::hasColumn('tbl_permiso', 'ELIMINAR')) {
                $table->tinyInteger('ELIMINAR')->default(0)->after('EDITAR');
            }
        });

        // 2) Índice único en (FK_COD_ROL, FK_COD_OBJETO) para evitar duplicados
        try {
            DB::statement("ALTER TABLE `tbl_permiso` ADD UNIQUE INDEX `uq_permiso_rol_obj` (`FK_COD_ROL`, `FK_COD_OBJETO`)");
        } catch (\Throwable $e) {
            // si ya existe, ignorar
        }

        // 3) Índice único en NOM_OBJETO en tbl_objeto
        try {
            DB::statement("ALTER TABLE `tbl_objeto` ADD UNIQUE INDEX `uq_objeto_nom_objeto` (`NOM_OBJETO`)");
        } catch (\Throwable $e) {
            // si ya existe, ignorar
        }
    }

    public function down(): void
    {
        Schema::table('tbl_permiso', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_permiso', 'ELIMINAR')) $table->dropColumn('ELIMINAR');
            if (Schema::hasColumn('tbl_permiso', 'EDITAR'))   $table->dropColumn('EDITAR');
            if (Schema::hasColumn('tbl_permiso', 'CREAR'))    $table->dropColumn('CREAR');
            if (Schema::hasColumn('tbl_permiso', 'VER'))      $table->dropColumn('VER');
        });

        try { DB::statement("ALTER TABLE `tbl_permiso` DROP INDEX `uq_permiso_rol_obj`"); } catch (\Throwable $e) {}
        try { DB::statement("ALTER TABLE `tbl_objeto` DROP INDEX `uq_objeto_nom_objeto`"); } catch (\Throwable $e) {}
    }
};
