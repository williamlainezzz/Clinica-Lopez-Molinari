<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    // Implementaremos en el PASO 3:
    public function store(Request $request) { abort(501, 'Por implementar en Paso 3'); }
    public function download($id)          { abort(501, 'Por implementar en Paso 3'); }
}
