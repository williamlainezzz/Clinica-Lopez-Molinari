<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BitacoraService
{
    /**
     * Registra un evento en la bitácora.
     *
     * @param  string      $objeto      (ej. 'ROLES', 'USUARIOS', 'BACKUPS', 'AUTH')
     * @param  string      $accion      (ej. 'CREAR','EDITAR','ELIMINAR','LOGIN','LOGOUT','VER')
     * @param  string|null $descripcion (opcional, máx 255)
     */
    public static function log(string $objeto, string $accion, ?string $descripcion = null): void
    {
        // Usuario autenticado (compatibilidad si el modelo usa id o COD_USUARIO)
        $user = Auth::user();
        $userId = $user->COD_USUARIO ?? $user->id ?? null;
        if (!$userId) return;

        DB::table('tbl_bitacora')->insert([
            'FK_COD_USUARIO' => $userId,
            'OBJETO'         => strtoupper(trim($objeto)),
            'ACCION'         => strtoupper(trim($accion)),
            'DESCRIPCION'    => $descripcion ? mb_substr($descripcion, 0, 255) : null,
            'IP'             => request()->ip(),
            'USER_AGENT'     => mb_substr((string) request()->userAgent(), 0, 255),
            // created_at tiene DEFAULT CURRENT_TIMESTAMP en la BD
        ]);
    }
}
