<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
{
    if (app()->environment('production')) {
        return;
    }
    try {
        DB::unprepared(<<<'SQL'
DROP FUNCTION IF EXISTS fn_tiene_permiso;
DELIMITER $$
CREATE FUNCTION fn_tiene_permiso(
    p_cod_rol BIGINT,
    p_nom_objeto VARCHAR(150),
    p_accion VARCHAR(20)
)
RETURNS TINYINT(1)
DETERMINISTIC
BEGIN
    DECLARE v_res TINYINT(1) DEFAULT 0;
    SELECT
        CASE UPPER(p_accion)
            WHEN 'VER'      THEN IFNULL(tp.VER, 0)
            WHEN 'CREAR'    THEN IFNULL(tp.CREAR, 0)
            WHEN 'EDITAR'   THEN IFNULL(tp.EDITAR, 0)
            WHEN 'ELIMINAR' THEN IFNULL(tp.ELIMINAR, 0)
            ELSE 0
        END
    INTO v_res
    FROM tbl_permiso tp
    JOIN tbl_objeto o ON o.COD_OBJETO = tp.FK_COD_OBJETO
    WHERE tp.FK_COD_ROL = p_cod_rol
      AND o.NOM_OBJETO = (p_nom_objeto COLLATE utf8mb4_general_ci)
      AND IFNULL(tp.ESTADO_PERMISO,1) <> 0
    LIMIT 1;
    RETURN IFNULL(v_res, 0);
END $$
DELIMITER ;
SQL);
    } catch (\Throwable $e) {}
}
public function down(): void
{
    if (app()->environment('production')) {
        return;
    }
    try { DB::unprepared('DROP FUNCTION IF EXISTS fn_tiene_permiso;'); } catch (\Throwable $e) {}
}
};
