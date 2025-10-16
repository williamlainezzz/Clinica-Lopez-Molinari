<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\DB;

class LogUserLogout
{
    public function handle(Logout $event): void
    {
        $u = $event->user;
        $uid = $u->COD_USUARIO ?? $u->id ?? null;
        if (!$uid) return;

        DB::table('tbl_bitacora')->insert([
            'FK_COD_USUARIO' => $uid,
            'OBJETO'         => 'AUTH',
            'ACCION'         => 'LOGOUT',
            'DESCRIPCION'    => 'Salida del sistema',
            'IP'             => request()->ip(),
            'USER_AGENT'     => mb_substr((string) request()->userAgent(), 0, 255),
        ]);
    }
}
