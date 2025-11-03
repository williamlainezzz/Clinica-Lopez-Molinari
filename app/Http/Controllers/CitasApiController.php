<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CitasApiController extends Controller
{
    /**
     * GET /api/agenda/citas
     * Filtros: estado, doctor (id), desde (Y-m-d), hasta (Y-m-d)
     */
    public function index(Request $request)
    {
        // Mapear estados "humanos" a lo que guarda la BD si difiere
        // Si en tu BD EST_CITA ya guarda textos ('Confirmada', etc.), dejamos directo.
        $estado = $request->query('estado');    // 'Confirmada'|'Pendiente'|'Cancelada'|null
        $doctor = $request->query('doctor');    // id numérico del doctor
        $desde  = $request->query('desde');     // 'YYYY-MM-DD'
        $hasta  = $request->query('hasta');     // 'YYYY-MM-DD'

        // Armamos SELECT con JOINs a paciente->persona y doctor->persona.
        // OJO: Si tu tabla usa otros nombres de columna, cambia SOLO estos alias:
        $q = DB::table('tbl_cita as c')
            ->leftJoin('tbl_paciente as pa', 'pa.COD_PACIENTE', '=', 'c.FK_COD_PACIENTE')
            ->leftJoin('tbl_persona as pper', 'pper.COD_PERSONA', '=', 'pa.FK_COD_PERSONA')
            ->leftJoin('tbl_doctor as d', 'd.COD_DOCTOR', '=', 'c.FK_COD_DOCTOR')
            ->leftJoin('tbl_persona as dper', 'dper.COD_PERSONA', '=', 'd.FK_COD_PERSONA')
            ->select([
                'c.COD_CITA as id',
                'c.FEC_CITA as fecha',
                'c.HOR_CITA as hora',
                DB::raw("TRIM(CONCAT(COALESCE(pper.PRIMER_NOMBRE,''),' ',COALESCE(pper.SEGUNDO_NOMBRE,''),' ',COALESCE(pper.PRIMER_APELLIDO,''),' ',COALESCE(pper.SEGUNDO_APELLIDO,''))) as paciente"),
                DB::raw("TRIM(CONCAT(COALESCE(dper.PRIMER_NOMBRE,''),' ',COALESCE(dper.SEGUNDO_NOMBRE,''),' ',COALESCE(dper.PRIMER_APELLIDO,''),' ',COALESCE(dper.SEGUNDO_APELLIDO,''))) as doctor"),
                'c.EST_CITA as estado',
                'c.DES_MOTIVO as motivo',
            ]);

        if (!empty($estado)) {
            $q->where('c.EST_CITA', $estado);
        }
        if (!empty($doctor)) {
            $q->where('c.FK_COD_DOCTOR', $doctor);
        }
        if (!empty($desde)) {
            $q->whereDate('c.FEC_CITA', '>=', $desde);
        }
        if (!empty($hasta)) {
            $q->whereDate('c.FEC_CITA', '<=', $hasta);
        }

        $rows = $q->orderBy('c.FEC_CITA')->orderBy('c.HOR_CITA')->get();

        return response()->json([
            'ok'    => true,
            'total' => $rows->count(),
            'data'  => $rows,
        ]);
    }

    /**
     * GET /api/agenda/citas/{id}
     */
    public function show($id)
    {
        $row = DB::table('tbl_cita as c')
            ->leftJoin('tbl_paciente as pa', 'pa.COD_PACIENTE', '=', 'c.FK_COD_PACIENTE')
            ->leftJoin('tbl_persona as pper', 'pper.COD_PERSONA', '=', 'pa.FK_COD_PERSONA')
            ->leftJoin('tbl_doctor as d', 'd.COD_DOCTOR', '=', 'c.FK_COD_DOCTOR')
            ->leftJoin('tbl_persona as dper', 'dper.COD_PERSONA', '=', 'd.FK_COD_PERSONA')
            ->select([
                'c.COD_CITA as id',
                'c.FEC_CITA as fecha',
                'c.HOR_CITA as hora',
                DB::raw("TRIM(CONCAT(COALESCE(pper.PRIMER_NOMBRE,''),' ',COALESCE(pper.SEGUNDO_NOMBRE,''),' ',COALESCE(pper.PRIMER_APELLIDO,''),' ',COALESCE(pper.SEGUNDO_APELLIDO,''))) as paciente"),
                DB::raw("TRIM(CONCAT(COALESCE(dper.PRIMER_NOMBRE,''),' ',COALESCE(dper.SEGUNDO_NOMBRE,''),' ',COALESCE(dper.PRIMER_APELLIDO,''),' ',COALESCE(dper.SEGUNDO_APELLIDO,''))) as doctor"),
                'c.EST_CITA as estado',
                'c.DES_MOTIVO as motivo',
                'c.DES_OBSERVACIONES as observaciones',
            ])
            ->where('c.COD_CITA', $id)
            ->first();

        if (!$row) {
            return response()->json(['ok' => false, 'message' => 'No encontrado'], 404);
        }

        return response()->json(['ok' => true, 'data' => $row]);
    }

    /**
     * GET /api/agenda/doctores
     * Devuelve catálogo (id, nombre) leyendo doctor->persona
     */
    public function doctores()
    {
        $doctores = DB::table('tbl_doctor as d')
            ->leftJoin('tbl_persona as p', 'p.COD_PERSONA', '=', 'd.FK_COD_PERSONA')
            ->select([
                'd.COD_DOCTOR as id',
                DB::raw("TRIM(CONCAT(COALESCE(p.PRIMER_NOMBRE,''),' ',COALESCE(p.SEGUNDO_NOMBRE,''),' ',COALESCE(p.PRIMER_APELLIDO,''),' ',COALESCE(p.SEGUNDO_APELLIDO,''))) as nombre"),
            ])
            ->orderBy('nombre')
            ->get();

        return response()->json(['ok' => true, 'data' => $doctores]);
    }

    /**
     * GET /api/agenda/estados
     * Si en tu BD tienes otra tabla para estados, reemplaza por SELECT a esa tabla.
     */
    public function estados()
    {
        // Si manejas catálogo real, cambia este array por SELECT a tu tabla de estados
        $data = [
            ['id' => 'Confirmada', 'nombre' => 'Confirmada'],
            ['id' => 'Pendiente',  'nombre' => 'Pendiente'],
            ['id' => 'Cancelada',  'nombre' => 'Cancelada'],
        ];

        return response()->json(['ok' => true, 'data' => $data]);
    }
}
