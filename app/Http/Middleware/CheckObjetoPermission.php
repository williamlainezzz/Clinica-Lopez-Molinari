<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class CheckObjetoPermission
{
    /**
     * Valida permiso de acceso a una pantalla/URL.
     * Param: $accion = VER | CREAR | EDITAR | ELIMINAR
     */
    public function handle(Request $request, Closure $next, $accion = 'VER')
    {
        try {
            // 0) Requiere sesión
            $user = Auth::user();
            if (!$user) {
                abort(401);
            }

            // ⚠️ Refrescar desde BD para reflejar cambios de rol al instante
            if (method_exists($user, 'refresh')) {
                $user->refresh();
            }

            // 1) Resolver rol
            $rolId = data_get($user, 'FK_COD_ROL')
                ?? data_get($user, 'cod_rol')
                ?? data_get($user, 'ROL_ID')
                ?? data_get($user, 'role_id');

            if ($rolId === null) {
                abort(403, 'Rol no asignado');
            }

            // 2) Super-admin bypass (ajusta si tu ADMIN no es 1)
            if ((int)$rolId === 1) {
                return $next($request);
            }

            // 3) Resolver objeto por prefijo de URL (match más largo, case-insensitive)
            $path = '/' . ltrim(Str::lower($request->path()), '/');
            $objetos = DB::table('tbl_objeto')
                ->select('COD_OBJETO', 'NOM_OBJETO', 'URL_OBJETO', 'ESTADO_OBJETO')
                ->get();

            $match = null;
            $maxLen = -1;
            foreach ($objetos as $o) {
                $url = Str::lower((string) $o->URL_OBJETO);
                $url = '/' . ltrim($url, '/');
                $url = rtrim($url, '/');
                if ($url === '') continue;

                if (str_starts_with($path, $url) && strlen($url) > $maxLen) {
                    $match = $o;
                    $maxLen = strlen($url);
                }
            }

            // 4) Si NO hay objeto o el objeto está INACTIVO -> dejar pasar
            if (!$match || (int)$match->ESTADO_OBJETO === 0) {
                return $next($request);
            }

            // 5) Chequeo de permiso vía función SQL
            $accion = strtoupper($accion);
            $res = DB::selectOne(
                "SELECT fn_tiene_permiso(?, ?, ?) AS ok",
                [$rolId, $match->NOM_OBJETO, $accion]
            );
            $ok = $res && (int)$res->ok === 1;

            if (!$ok) {
                abort(403, 'No tienes permiso para acceder a esta pantalla.');
            }

            return $next($request);

        } catch (Throwable $e) {
            logger()->error('CheckObjetoPermission error', [
                'path'    => $request->path(),
                'user_id' => optional(Auth::user())->{"COD_USUARIO"} ?? null,
                'msg'     => $e->getMessage(),
            ]);
            abort(500, 'Error de autorización');
        }
    }
}
