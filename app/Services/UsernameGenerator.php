<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UsernameGenerator
{
    /**
     * Genera un nombre de usuario único siguiendo la convención de registro:
     * primera letra del nombre + apellido (sin espacios/acentos) en minúsculas.
     * Si existe, agrega sufijo incremental respetando la longitud máxima.
     */
    public static function generate(string $nombre, string $apellido, int $maxLen = 50): string
    {
        $base = Str::ascii(Str::lower(
            substr(trim($nombre), 0, 1) . preg_replace('/\s+/', '', trim($apellido))
        ));

        $base = preg_replace('/[^a-z0-9]/', '', $base) ?: 'user';
        $base = substr($base, 0, $maxLen);

        $user = $base;
        $i    = 1;

        while (DB::table('tbl_usuario')->where('USR_USUARIO', $user)->exists()) {
            $suffix = (string) $i++;
            $slice  = max(1, $maxLen - strlen($suffix));
            $user   = substr($base, 0, $slice) . $suffix;
        }

        return $user;
    }
}
