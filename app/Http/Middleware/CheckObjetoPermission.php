<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckObjetoPermission
{
    public function handle(Request $request, Closure $next, $accion = 'VER')
    {
        // 1) Usuario y rol actual
        $user = Auth::user();
        if (!$user) abort(401);
        // Ajusta si tu columna de rol es distinta:
        $rolId = $user->FK_COD_ROL ?? $user->cod_rol ?? null;
        if (!$rolId) abort(403, 'Rol no asignado');

        // 2) Resolver el Objeto por URL (prefijo más largo que haga match)
        $path = '/' . ltrim($request->path(), '/'); // ej: /seguridad/roles
        $objetos = DB::table('tbl_objeto')->select('COD_OBJETO','NOM_OBJETO','URL_OBJETO','ESTADO_OBJETO')->get();

        $match = null; $maxLen = -1;
        foreach ($objetos as $o) {
            $url = rtrim($o->URL_OBJETO ?? '', '/');      // ej: /seguridad/roles
            if ($url === '') continue;
            // coincidencia por prefijo (AdminLTE suele usar rutas base)
            if (strpos($path, $url) === 0 && strlen($url) > $maxLen) {
                $match = $o; $maxLen = strlen($url);
            }
        }

        // Si no hay objeto registrado para esta URL, deja pasar (no es parte del catálogo de seguridad)
        if (!$match || (int)$match->ESTADO_OBJETO === 0) {
            return $next($request);
        }

        // 3) Chequear permiso con la función SQL
        $accion = strtoupper($accion);
        $sql = "SELECT fn_tiene_permiso(?, ?, ?) AS ok";
        $res = DB::selectOne($sql, [$rolId, $match->NOM_OBJETO, $accion]);
        $ok = $res && (int)$res->ok === 1;

        if (!$ok) abort(403, 'No tienes permiso para acceder a esta pantalla.');
        return $next($request);
    }
}
