<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\BitacoraService;
use Illuminate\Support\Str; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class BackupController extends Controller
{
    public function index(Request $request)
    {
        $backups = DB::table('tbl_backup as b')
            ->join('tbl_usuario as u', 'u.COD_USUARIO', '=', 'b.FK_COD_USUARIO')
            ->join('tbl_persona as p', 'p.COD_PERSONA', '=', 'u.FK_COD_PERSONA')
            ->select(
                'b.COD_BACKUP',
                'b.NOMBRE_ARCHIVO',
                'b.RUTA_STORAGE',
                'b.TAMANIO_BYTES',
                'b.ESTADO',
                'b.MENSAJE',
                'b.created_at',
                DB::raw("CONCAT(p.PRIMER_NOMBRE,' ',p.PRIMER_APELLIDO) as usuario")
            )
            ->orderBy('b.created_at', 'desc')
            ->paginate(10);

        return view('seguridad.backups.index', compact('backups'));
    }

    public function store(Request $request)
{
    $user   = auth()->user();
    $db     = config('database.connections.mysql.database');
    $host   = config('database.connections.mysql.host');
    $port   = config('database.connections.mysql.port');
    $userDb = config('database.connections.mysql.username');
    $passDb = config('database.connections.mysql.password');

    $stamp  = now()->format('Y-m-d_H-i-s');
    $file   = "backup_{$db}_{$stamp}.sql";

    // Carpeta destino: storage/app/{BACKUP_DIR}
    $dir     = env('BACKUP_DIR', 'backups');
    $pathAbs = storage_path("app/{$dir}");
    if (!is_dir($pathAbs)) @mkdir($pathAbs, 0775, true);
    $fullAbs = "{$pathAbs}/{$file}";

    $ok = false; 
    $msg = null;

    // 1) Intento con mysqldump (Windows/XAMPP o Linux)
    try {
        $dumpPath = env('DB_DUMP_PATH'); // Ej: C:\xampp\mysql\bin\mysqldump.exe  |  /usr/bin/mysqldump
        if (!$dumpPath) {
            // Detección simple en Linux
            $which = @trim(shell_exec('which mysqldump') ?? '');
            if ($which) $dumpPath = $which;
        }

        if ($dumpPath && is_file($dumpPath)) {
            // Flags para incluir procedimientos/rutinas/eventos/trigger
            $flags = '--routines --events --triggers --single-transaction --quick --add-drop-table';

            // Comando (comillas dobles para evitar problemas en Windows CMD)
            $cmd = sprintf(
                '"%s" -h"%s" -P"%s" -u"%s" %s "%s" %s > "%s"',
                $dumpPath,
                $host,
                (string) $port,
                $userDb,
                $passDb !== '' ? ('-p"' . $passDb . '"') : '',
                $db,
                $flags,
                $fullAbs
            );

            $ret = 0;
            @exec($cmd, $out, $ret);

            if ($ret === 0 && is_file($fullAbs) && filesize($fullAbs) > 0) {
                $ok = true;
            } else {
                $msg = 'mysqldump no disponible o falló.';
            }
        } else {
            $msg = 'mysqldump no encontrado.';
        }
    } catch (\Throwable $e) {
        $msg = 'Error ejecutando mysqldump: ' . $e->getMessage();
    }

    // 2) Fallback en PHP (estructura + datos) si falló mysqldump
    if (!$ok) {
        try {
            $fh = fopen($fullAbs, 'w');
            if (!$fh) throw new \RuntimeException('No se pudo abrir el archivo de salida.');

            fwrite($fh, "-- Backup portable generado " . now()->toDateTimeString() . PHP_EOL);
            fwrite($fh, "SET FOREIGN_KEY_CHECKS=0;\n\n");

            // Listado de tablas
            $tables = DB::select('SHOW TABLES');
            $key = "Tables_in_{$db}";

            foreach ($tables as $t) {
                $table = $t->$key;

                // DDL
                $create = DB::select("SHOW CREATE TABLE `{$table}`");
                $ddl = $create[0]->{'Create Table'} ?? null;
                if ($ddl) {
                    fwrite($fh, "DROP TABLE IF EXISTS `{$table}`;\n");
                    fwrite($fh, $ddl . ";\n\n");
                }

                // Datos por lotes
                $chunk = 500;
                DB::table($table)->orderBy(DB::raw('1'))->chunk($chunk, function($rows) use ($fh, $table) {
                    foreach ($rows as $row) {
                        $arr  = (array) $row;
                        $cols = array_map(fn($c) => "`{$c}`", array_keys($arr));
                        $vals = array_map(function($v){
                            if (is_null($v)) return 'NULL';
                            return "'" . str_replace("'", "\\'", (string) $v) . "'";
                        }, array_values($arr));
                        fwrite($fh, "INSERT INTO `{$table}` (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ");\n");
                    }
                    fwrite($fh, "\n");
                });
            }

            fwrite($fh, "SET FOREIGN_KEY_CHECKS=1;\n");
            fclose($fh);

            if (is_file($fullAbs) && filesize($fullAbs) > 0) {
                $ok = true;
            } else {
                $msg = 'Archivo vacío en exportación PHP.';
                @unlink($fullAbs);
            }
        } catch (\Throwable $e) {
            $msg = 'Error en exportación PHP: ' . $e->getMessage();
            @is_file($fullAbs) && @unlink($fullAbs);
        }
    }

    // 3) Registrar en BD y responder a la UI
    $bytes = $ok ? filesize($fullAbs) : 0;
    DB::table('tbl_backup')->insert([
        'NOMBRE_ARCHIVO' => $file,
        'RUTA_STORAGE'   => "{$dir}/{$file}",
        'TAMANIO_BYTES'  => $bytes,
        'ESTADO'         => $ok ? 'OK' : 'FALLIDO',
        'MENSAJE'        => $ok ? 'Respaldo generado' : ($msg ?: 'Fallo de respaldo'),
        'FK_COD_USUARIO' => $user?->COD_USUARIO,
        'created_at'     => now(),
    ]);

    return back()->with($ok ? 'ok' : 'error', $ok ? 'Respaldo generado correctamente.' : 'No se pudo generar el respaldo.');
}


public function download($id)
{
    $row = DB::table('tbl_backup')->where('COD_BACKUP', $id)->first();
    if (!$row) abort(404);

    $path = storage_path('app/' . $row->RUTA_STORAGE);
    if (!is_file($path)) abort(404, 'Archivo no encontrado');

    return response()->download($path, $row->NOMBRE_ARCHIVO);
}


}
