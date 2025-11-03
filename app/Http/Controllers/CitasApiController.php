<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CitasApiController extends Controller
{
    /** Catálogo local de estados (ajusta ids si difieren en tu BD) */
    private const ESTADOS = [
        1 => 'Confirmada',
        2 => 'Pendiente',
        3 => 'Cancelada',
    ];

    /** name/id → id (o null si no coincide) */
    private function estadoToId(?string $valor): ?int
    {
        if (!$valor) return null;
        $valor = trim($valor);
        // si vino “2”
        if (ctype_digit($valor)) return (int) $valor;
        // si vino “Pendiente”
        foreach (self::ESTADOS as $id => $nom) {
            if (strcasecmp($nom, $valor) === 0) return (int) $id;
        }
        return null;
    }

    /**
     * GET /api/agenda/citas
     * Filtros: estado (id o nombre), doctor (COD_PERSONA), desde (Y-m-d), hasta (Y-m-d)
     */
    public function index(Request $request)
    {
        $estado = $request->query('estado');     // 'Confirmada' | '2' | null
        $doctor = $request->query('doctor');     // COD_PERSONA del doctor
        $desde  = $request->query('desde');      // 'YYYY-MM-DD'
        $hasta  = $request->query('hasta');      // 'YYYY-MM-DD'

        $q = DB::table('tbl_cita as c')
            ->leftJoin('tbl_persona as pcte', 'pcte.COD_PERSONA', '=', 'c.FK_COD_PACIENTE')
            ->leftJoin('tbl_persona as pdoc', 'pdoc.COD_PERSONA', '=', 'c.FK_COD_DOCTOR')
            ->selectRaw("
                c.COD_CITA                                        as id,
                c.FEC_CITA                                        as fecha,
                c.HOR_CITA                                        as hora,
                TRIM(CONCAT(
                    COALESCE(pcte.PRIMER_NOMBRE,''),' ',
                    COALESCE(pcte.SEGUNDO_NOMBRE,''),' ',
                    COALESCE(pcte.PRIMER_APELLIDO,''),' ',
                    COALESCE(pcte.SEGUNDO_APELLIDO,'')
                ))                                                as paciente,
                TRIM(CONCAT(
                    COALESCE(pdoc.PRIMER_NOMBRE,''),' ',
                    COALESCE(pdoc.SEGUNDO_NOMBRE,''),' ',
                    COALESCE(pdoc.PRIMER_APELLIDO,''),' ',
                    COALESCE(pdoc.SEGUNDO_APELLIDO,'')
                ))                                                as doctor,
                c.ESTADO_CITA                                     as estado_id,
                c.MOT_CITA                                        as motivo
            ");

        if ($desde)  $q->whereDate('c.FEC_CITA', '>=', $desde);
        if ($hasta)  $q->whereDate('c.FEC_CITA', '<=', $hasta);
        if ($doctor) $q->where('c.FK_COD_DOCTOR', $doctor);

        if ($estado) {
            $estadoId = $this->estadoToId($estado);
            if ($estadoId !== null) {
                $q->where('c.ESTADO_CITA', $estadoId);
            }
        }

        $rows = $q->orderBy('c.FEC_CITA')->orderBy('c.HOR_CITA')->get()
            ->map(function ($r) {
                return [
                    'id'       => (int)$r->id,
                    'fecha'    => (string)$r->fecha,
                    'hora'     => (string)$r->hora,
                    'paciente' => $r->paciente,
                    'doctor'   => $r->doctor,
                    'estado'   => self::ESTADOS[(int)$r->estado_id] ?? '—',
                    'motivo'   => (string)$r->motivo,
                ];
            });

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
        $r = DB::table('tbl_cita as c')
            ->leftJoin('tbl_persona as pcte', 'pcte.COD_PERSONA', '=', 'c.FK_COD_PACIENTE')
            ->leftJoin('tbl_persona as pdoc', 'pdoc.COD_PERSONA', '=', 'c.FK_COD_DOCTOR')
            ->selectRaw("
                c.COD_CITA                                        as id,
                c.FEC_CITA                                        as fecha,
                c.HOR_CITA                                        as hora,
                TRIM(CONCAT(
                    COALESCE(pcte.PRIMER_NOMBRE,''),' ',
                    COALESCE(pcte.SEGUNDO_NOMBRE,''),' ',
                    COALESCE(pcte.PRIMER_APELLIDO,''),' ',
                    COALESCE(pcte.SEGUNDO_APELLIDO,'')
                ))                                                as paciente,
                TRIM(CONCAT(
                    COALESCE(pdoc.PRIMER_NOMBRE,''),' ',
                    COALESCE(pdoc.SEGUNDO_NOMBRE,''),' ',
                    COALESCE(pdoc.PRIMER_APELLIDO,''),' ',
                    COALESCE(pdoc.SEGUNDO_APELLIDO,'')
                ))                                                as doctor,
                c.ESTADO_CITA                                     as estado_id,
                c.MOT_CITA                                        as motivo
            ")
            ->where('c.COD_CITA', $id)
            ->first();

        if (!$r) {
            return response()->json(['ok' => false, 'message' => 'No encontrado'], 404);
        }

        return response()->json([
            'ok'   => true,
            'data' => [
                'id'            => (int)$r->id,
                'fecha'         => (string)$r->fecha,
                'hora'          => (string)$r->hora,
                'paciente'      => $r->paciente,
                'doctor'        => $r->doctor,
                'estado'        => self::ESTADOS[(int)$r->estado_id] ?? '—',
                'motivo'        => (string)$r->motivo,
                'observaciones' => null, // no existe columna en tu schema (devolver null)
            ],
        ]);
    }

    /**
     * GET /api/agenda/doctores
     * Lista personas con rol = 'DOCTOR'
     */
    public function doctores()
    {
        $rows = DB::table('tbl_usuario as u')
            ->join('tbl_rol as r', 'r.COD_ROL', '=', 'u.FK_COD_ROL')
            ->join('tbl_persona as p', 'p.COD_PERSONA', '=', 'u.FK_COD_PERSONA')
            ->whereRaw('UPPER(r.NOM_ROL) = ?', ['DOCTOR'])
            ->selectRaw("
                p.COD_PERSONA as id,
                TRIM(CONCAT(
                    COALESCE(p.PRIMER_NOMBRE,''),' ',
                    COALESCE(p.SEGUNDO_NOMBRE,''),' ',
                    COALESCE(p.PRIMER_APELLIDO,''),' ',
                    COALESCE(p.SEGUNDO_APELLIDO,'')
                )) as nombre
            ")
            ->orderBy('nombre')
            ->distinct()
            ->get();

        return response()->json(['ok' => true, 'data' => $rows]);
    }

    /**
     * GET /api/agenda/estados
     */
    public function estados()
    {
        $data = [];
        foreach (self::ESTADOS as $id => $nombre) {
            $data[] = ['id' => $id, 'nombre' => $nombre];
        }
        return response()->json(['ok' => true, 'data' => $data]);
    }
}
