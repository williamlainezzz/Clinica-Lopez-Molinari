<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function store(Request $request): RedirectResponse
    {
        $result = $this->generateBackupForUser(auth()->user());

        return back()->with(
            $result['ok'] ? 'success' : 'error',
            $result['message']
        );
    }

    public function download($id)
    {
        $row = DB::table('tbl_backup')->where('COD_BACKUP', $id)->first();
        if (!$row) {
            abort(404);
        }

        $path = storage_path('app/' . $row->RUTA_STORAGE);
        if (!is_file($path)) {
            abort(404, 'Archivo no encontrado');
        }

        return response()->download($path, $row->NOMBRE_ARCHIVO);
    }

    public function restore(Request $request, $id): RedirectResponse
    {
        $backup = DB::table('tbl_backup')->where('COD_BACKUP', $id)->first();
        if (!$backup) {
            abort(404);
        }

        $path = storage_path('app/' . $backup->RUTA_STORAGE);
        if (!is_file($path)) {
            return back()->with('error', 'El archivo de respaldo no existe en el servidor.');
        }

        if (strtoupper((string) $backup->ESTADO) !== 'OK') {
            return back()->with('error', 'Solo se pueden restaurar respaldos con estado OK.');
        }

        $confirmedName = trim((string) $request->input('confirm_backup'));
        if ($confirmedName !== $backup->NOMBRE_ARCHIVO) {
            return back()->with('error', 'Debes escribir exactamente el nombre del archivo para confirmar la restauracion.');
        }

        $clientPath = $this->resolveMysqlClientPath();
        if (!$clientPath) {
            return back()->with('error', 'No se encontro el cliente mysql para restaurar el respaldo.');
        }

        $connection = config('database.connections.mysql');
        $command = [
            $clientPath,
            '--host=' . $connection['host'],
            '--port=' . $connection['port'],
            '--user=' . $connection['username'],
        ];

        if (($connection['password'] ?? '') !== '') {
            $command[] = '--password=' . $connection['password'];
        }

        $command[] = $connection['database'];

        $process = new Process($command);
        $handle = fopen($path, 'r');

        if ($handle === false) {
            return back()->with('error', 'No se pudo abrir el archivo de respaldo para restaurar.');
        }

        try {
            $process->setTimeout(300);
            $process->setInput($handle);
            $process->mustRun();

            BitacoraService::log(
                'BACKUPS',
                'RESTAURAR',
                'Se restauro el respaldo ' . $backup->NOMBRE_ARCHIVO
            );

            return back()->with('success', 'Respaldo restaurado correctamente.');
        } catch (\Throwable $e) {
            $detail = trim($process->getErrorOutput() ?: $process->getOutput() ?: $e->getMessage());

            BitacoraService::log(
                'BACKUPS',
                'RESTAURAR',
                'Fallo al restaurar el respaldo ' . $backup->NOMBRE_ARCHIVO
            );

            return back()->with('error', 'No se pudo restaurar el respaldo. ' . $detail);
        } finally {
            if (is_resource($handle)) {
                fclose($handle);
            }
        }
    }

    private function generateBackupForUser($user): array
    {
        $db = config('database.connections.mysql.database');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');
        $userDb = config('database.connections.mysql.username');
        $passDb = config('database.connections.mysql.password');

        $stamp = now()->format('Y-m-d_H-i-s');
        $file = "backup_{$db}_{$stamp}.sql";
        $dir = env('BACKUP_DIR', 'backups');
        $pathAbs = storage_path("app/{$dir}");

        if (!is_dir($pathAbs)) {
            @mkdir($pathAbs, 0775, true);
        }

        $fullAbs = "{$pathAbs}/{$file}";
        $ok = false;
        $msg = null;

        try {
            $dumpPath = env('DB_DUMP_PATH');

            if (!$dumpPath) {
                $which = @trim(shell_exec('where mysqldump 2>NUL') ?: shell_exec('which mysqldump 2>/dev/null') ?: '');
                if ($which) {
                    $dumpPath = strtok($which, PHP_EOL);
                }
            }

            if ($dumpPath && is_file($dumpPath)) {
                $command = [
                    $dumpPath,
                    '--host=' . $host,
                    '--port=' . (string) $port,
                    '--user=' . $userDb,
                ];

                if ($passDb !== '') {
                    $command[] = '--password=' . $passDb;
                }

                $command = array_merge($command, [
                    '--routines',
                    '--events',
                    '--triggers',
                    '--single-transaction',
                    '--quick',
                    '--add-drop-table',
                    $db,
                ]);

                $process = new Process($command);
                $process->setTimeout(300);
                $process->run();

                if ($process->isSuccessful()) {
                    file_put_contents($fullAbs, $process->getOutput());
                } else {
                    $msg = trim($process->getErrorOutput()) ?: 'mysqldump no disponible o fallo.';
                }

                if (is_file($fullAbs) && filesize($fullAbs) > 0) {
                    $ok = true;
                }
            } else {
                $msg = 'mysqldump no encontrado.';
            }
        } catch (\Throwable $e) {
            $msg = 'Error ejecutando mysqldump: ' . $e->getMessage();
        }

        if (!$ok) {
            try {
                $fh = fopen($fullAbs, 'w');
                if (!$fh) {
                    throw new \RuntimeException('No se pudo abrir el archivo de salida.');
                }

                fwrite($fh, "-- Backup portable generado " . now()->toDateTimeString() . PHP_EOL);
                fwrite($fh, "SET FOREIGN_KEY_CHECKS=0;\n\n");

                $tables = DB::select('SHOW TABLES');
                $key = "Tables_in_{$db}";

                foreach ($tables as $t) {
                    $table = $t->$key;

                    $create = DB::select("SHOW CREATE TABLE `{$table}`");
                    $ddl = $create[0]->{'Create Table'} ?? null;
                    if ($ddl) {
                        fwrite($fh, "DROP TABLE IF EXISTS `{$table}`;\n");
                        fwrite($fh, $ddl . ";\n\n");
                    }

                    DB::table($table)->orderBy(DB::raw('1'))->chunk(500, function ($rows) use ($fh, $table) {
                        foreach ($rows as $row) {
                            $arr = (array) $row;
                            $cols = array_map(fn ($c) => "`{$c}`", array_keys($arr));
                            $vals = array_map(function ($v) {
                                if (is_null($v)) {
                                    return 'NULL';
                                }

                                return "'" . str_replace("'", "\\'", (string) $v) . "'";
                            }, array_values($arr));

                            fwrite(
                                $fh,
                                "INSERT INTO `{$table}` (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ");\n"
                            );
                        }

                        fwrite($fh, "\n");
                    });
                }

                fwrite($fh, "SET FOREIGN_KEY_CHECKS=1;\n");
                fclose($fh);

                if (is_file($fullAbs) && filesize($fullAbs) > 0) {
                    $ok = true;
                } else {
                    $msg = 'Archivo vacio en exportacion PHP.';
                    @unlink($fullAbs);
                }
            } catch (\Throwable $e) {
                $msg = 'Error en exportacion PHP: ' . $e->getMessage();
                @is_file($fullAbs) && @unlink($fullAbs);
            }
        }

        $bytes = $ok && is_file($fullAbs) ? filesize($fullAbs) : 0;
        DB::table('tbl_backup')->insert([
            'NOMBRE_ARCHIVO' => $file,
            'RUTA_STORAGE' => "{$dir}/{$file}",
            'TAMANIO_BYTES' => $bytes,
            'ESTADO' => $ok ? 'OK' : 'FALLIDO',
            'MENSAJE' => $ok ? 'Respaldo generado' : ($msg ?: 'Fallo de respaldo'),
            'FK_COD_USUARIO' => $user?->COD_USUARIO,
            'created_at' => now(),
        ]);

        if ($ok) {
            BitacoraService::log('BACKUPS', 'CREAR', 'Se genero el respaldo ' . $file);
        }

        return [
            'ok' => $ok,
            'message' => $ok ? 'Respaldo generado correctamente.' : ($msg ?: 'No se pudo generar el respaldo.'),
        ];
    }

    private function resolveMysqlClientPath(): ?string
    {
        $restorePath = env('DB_RESTORE_PATH');
        if ($restorePath && is_file($restorePath)) {
            return $restorePath;
        }

        $dumpPath = env('DB_DUMP_PATH');
        if ($dumpPath) {
            $dir = dirname($dumpPath);
            foreach (['mysql.exe', 'mysql', 'mariadb.exe', 'mariadb'] as $candidate) {
                $full = $dir . DIRECTORY_SEPARATOR . $candidate;
                if (is_file($full)) {
                    return $full;
                }
            }
        }

        $which = @trim(shell_exec('where mysql 2>NUL') ?: shell_exec('which mysql 2>/dev/null') ?: '');
        if ($which) {
            return strtok($which, PHP_EOL);
        }

        return null;
    }
}
