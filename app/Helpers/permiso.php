<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

if (! function_exists('puede')) {
    /**
     * Retorna true si el rol del usuario autenticado tiene la ACCIÓN sobre el OBJETO.
     * Ej: puede('SEGURIDAD_BACKUPS', 'VER')
     */
    function puede(string $objeto, string $accion = 'VER'): bool
    {
        $u = Auth::user();
        if (!$u) return false;

        $rolId = (int)($u->FK_COD_ROL ?? 0);

        $res = DB::selectOne(
            "SELECT fn_tiene_permiso(?, ?, ?) AS ok",
            [$rolId, $objeto, strtoupper($accion)]
        );

        return $res && (int)$res->ok === 1;
    }
}
