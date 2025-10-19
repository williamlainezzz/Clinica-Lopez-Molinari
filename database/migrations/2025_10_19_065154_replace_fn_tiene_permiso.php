<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
DROP FUNCTION IF EXISTS fn_tiene_permiso;
DELIMITER $$
CREATE FUNCTION fn_tiene_permiso(p_cod_rol BIGINT, p_nom_objeto VARCHAR(150), p_accion VARCHAR(20))
RETURNS TINYINT(1)
DETERMINISTIC
BEGIN
    DECLARE v_res TINYINT(1) DEFAULT 0;

    /*
      Regla:
      - Busca el permiso por (rol, objeto por NOM_OBJETO).
      - Lee el flag correspondiente según p_accion: VER/CREAR/EDITAR/ELIMINAR.
      - Considera activo si ESTADO_PERMISO <> 0 (soporta numérico 1/0). 
        (Si después cambias a ENUM, puedes ajustar aquí fácilmente.)
    */
    SELECT
        CASE UPPER(p_accion)
            WHEN 'VER'       THEN IFNULL(tp.VER, 0)
            WHEN 'CREAR'     THEN IFNULL(tp.CREAR, 0)
            WHEN 'EDITAR'    THEN IFNULL(tp.EDITAR, 0)
            WHEN 'ELIMINAR'  THEN IFNULL(tp.ELIMINAR, 0)
            ELSE 0
        END
    INTO v_res
    FROM tbl_permiso tp
    INNER JOIN tbl_objeto tobj
        ON tobj.COD_OBJETO = tp.FK_COD_OBJETO
    WHERE tp.FK_COD_ROL = p_cod_rol
      AND tobj.NOM_OBJETO = p_nom_objeto
      AND IFNULL(tp.ESTADO_PERMISO,1) <> 0
    LIMIT 1;

    RETURN IFNULL(v_res, 0);
END$$
DELIMITER ;
SQL);
    }

    public function down(): void
    {
        // Si necesitas volver a la versión previa, puedes recrearla aquí.
        DB::unprepared('DROP FUNCTION IF EXISTS fn_tiene_permiso;');
    }
};
