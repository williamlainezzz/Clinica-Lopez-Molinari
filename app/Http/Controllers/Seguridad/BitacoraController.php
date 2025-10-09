<?php
// app/Http/Controllers/Seguridad/BitacoraController.php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BitacoraController extends Controller
{
    public function index(Request $request)
    {
        $bitacora = DB::table('tbl_bitacora as b')
            ->join('tbl_usuario as u', 'u.COD_USUARIO', '=', 'b.FK_COD_USUARIO')
            ->join('tbl_persona as p', 'p.COD_PERSONA', '=', 'u.FK_COD_PERSONA')
            ->select(
                'b.COD_BITACORA',
                'b.OBJETO',
                'b.ACCION',
                'b.DESCRIPCION',
                'b.IP',
                'b.USER_AGENT',
                'b.created_at',
                DB::raw("CONCAT(p.PRIMER_NOMBRE,' ',p.PRIMER_APELLIDO) as usuario")
            )
            ->orderBy('b.created_at', 'desc')
            ->paginate(15);

        return view('seguridad.bitacora.index', compact('bitacora'));
    }
}
