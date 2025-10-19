<?php

namespace App\Http\Middleware;


use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class CheckObjetoPermission
{
    /**
     * Middleware que valida permiso de acceso a una pantalla/URL.
     * Param: $accion = VER | CREAR | EDITAR | ELIMINAR
     */
    public function handle(Request $request, Closure $next, $accion = 'VER')
    {
        try {
            // 0) Si no hay sesión, 401
            $user = Auth::user();
            if (!$user) {
                abort(401);
            }

            // 1) Resolver columna de rol tolerante a tu esquema
            //    (cubre FK_COD_ROL, cod_rol, ROL_ID, role_id, etc.)
            $rolId = data_get($user, 'FK_COD_ROL')
                ?? data_get($user, 'cod_rol')
                ?? data_get($user, 'ROL_ID')
                ?? data_get($user, 'role_id')
                ?? null;

            if ($rolId === null) {
                // Si por alguna razón el usuario no tiene rol asociado, bloquea.
                abort(403, 'Rol no asignado');
            }

            // 2) Super-admin bypass (ajusta si tu ADMIN no es id=1)
            if ((int)$rolId === 1) {
                return $next($request);
            }

            // 3) Buscar Objeto por prefijo de URL (match más largo)
            $path = '/' . ltrim($request->path(), '/'); // ej: /seguridad/permisos
            $objetos = DB::table('tbl_objeto')
                ->select('COD_OBJETO', 'NOM_OBJETO', 'URL_OBJETO', 'ESTADO_OBJETO')
                ->get();

            $match = null; $maxLen = -1;
            foreach ($objetos as $o) {
                $url = rtrim((string) $o->URL_OBJETO, '/');
                if ($url === '') continue;
                if (strpos($path, $url) === 0 && strlen($url) > $maxLen) {
                    $match = $o; $maxLen = strlen($url);
                }
            }

            // Si no hay objeto registrado para esta URL, o está inactivo -> deja pasar
            if (!$match || (int)$match->ESTADO_OBJETO === 0) {
                logger()->info('permiso-debug-allow', [
  'path'   => $request->path(),
  'rolId'  => $rolId,
  'obj'    => $match ? $match->NOM_OBJETO : null,
  'urlObj' => $match ? $match->URL_OBJETO : null,
  'accion' => $accion,
  'ok'     => isset($ok) ? $ok : null,
]);

                return $next($request);
            }

            // 4) Chequeo de permiso vía función SQL
            $accion = strtoupper($accion);
            // Nota: usamos collation general para evitar choques utf8mb4_unicode_ci vs general_ci
            $res = DB::selectOne(
                "SELECT fn_tiene_permiso(?, ?, ?) AS ok",
                [$rolId, $match->NOM_OBJETO, $accion]
            );

            $ok = $res && (int)$res->ok === 1;
           if (str_starts_with($path, '/seguridad') && (!$match || (int)$match->ESTADO_OBJETO === 0)) {
    logger()->info('permiso-debug-deny', [
        'path'   => $request->path(),
        'rolId'  => $rolId,
        'obj'    => $match ? $match->NOM_OBJETO : null,
        'urlObj' => $match ? $match->URL_OBJETO : null,
        'accion' => $accion,
        'ok'     => null,
        'reason' => 'no-objeto-o-inactivo',
    ]);
    abort(403, 'Objeto no registrado o inactivo');
}

            return $next($request);
        } catch (Throwable $e) {
            // Log y 500 controlado para no romper con pantallas blancas
            logger()->error('CheckObjetoPermission error', [
                'path' => $request->path(),
                'user_id' => optional(Auth::user())->id,
                'msg' => $e->getMessage(),
            ]);
            abort(500, 'Error de autorización');
        }
    }
}
