<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\DB;

class LogUserLogin
{
    public function handle(Login $event): void
    {
        $u = $event->user;
        // compatibilidad: tu modelo puede usar COD_USUARIO o id
        $uid = $u->COD_USUARIO ?? $u->id ?? null;
        if (!$uid) return;

        // --- Guardar sólo el código del rol en sesión ---
        $rolId = $u->FK_COD_ROL ?? null;
        if ($rolId) {
            session(['FK_COD_ROL' => (int) $rolId]);
        }

        DB::table('tbl_bitacora')->insert([
            'FK_COD_USUARIO' => $uid,
            'OBJETO'         => 'AUTH',
            'ACCION'         => 'LOGIN',
            'DESCRIPCION'    => 'Ingreso al sistema',
            'IP'             => request()->ip(),
            'USER_AGENT'     => mb_substr((string) request()->userAgent(), 0, 255),
            // created_at: default CURRENT_TIMESTAMP
        ]);
    }
}
