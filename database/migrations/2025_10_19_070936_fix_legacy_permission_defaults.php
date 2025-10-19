<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Backfill por si hubiera NULLs
        DB::statement("UPDATE `tbl_permiso` SET `PER_SELECT`=0 WHERE `PER_SELECT` IS NULL");
        DB::statement("UPDATE `tbl_permiso` SET `PER_INSERTAR`=0 WHERE `PER_INSERTAR` IS NULL");
        DB::statement("UPDATE `tbl_permiso` SET `PER_UPDATE`=0 WHERE `PER_UPDATE` IS NULL");

        // Colocar DEFAULT 0 y NOT NULL en las legadas
        DB::statement("ALTER TABLE `tbl_permiso` MODIFY `PER_SELECT`   TINYINT(1) NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE `tbl_permiso` MODIFY `PER_INSERTAR` TINYINT(1) NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE `tbl_permiso` MODIFY `PER_UPDATE`   TINYINT(1) NOT NULL DEFAULT 0");
    }

    public function down(): void
    {
        // Si necesitas revertir, quita el DEFAULT (no es necesario en nuestro caso)
        DB::statement("ALTER TABLE `tbl_permiso` MODIFY `PER_SELECT`   TINYINT(1) NOT NULL");
        DB::statement("ALTER TABLE `tbl_permiso` MODIFY `PER_INSERTAR` TINYINT(1) NOT NULL");
        DB::statement("ALTER TABLE `tbl_permiso` MODIFY `PER_UPDATE`   TINYINT(1) NOT NULL");
    }
};
