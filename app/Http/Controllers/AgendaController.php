<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgendaController extends Controller
{
    public function citas(Request $request)      { return $this->render('Citas', $request); }
    public function calendario(Request $request)  { return $this->render('Calendario', $request); }
    public function reportes(Request $request)    { return $this->render('Reportes', $request); }

    private function render(string $section, Request $request)
    {
        $user = auth()->user();
        // Rol seguro aunque no haya sesión
        $rol  = strtoupper((string) data_get($user, 'rol.NOM_ROL', 'ADMIN'));

        // Etiquetas para títulos
        $labels = [
            'ADMIN'         => 'Admin',
            'DOCTOR'        => 'Doctor',
            'RECEPCIONISTA' => 'Recepción',
            'PACIENTE'      => 'Paciente',
        ];
        $rolLabel = $labels[$rol] ?? 'Admin';

        // Nombre de ruta actual
        $routeName = match (strtolower($section)) {
            'citas'       => 'agenda.citas',
            'calendario'  => 'agenda.calendario',
            default       => 'agenda.reportes',
        };

        // Filtros desde la URL
        $filters = [
            'desde'  => $request->query('desde'),
            'hasta'  => $request->query('hasta'),
            'estado' => $request->query('estado'), // 'Confirmada'|'Pendiente'|'Cancelada'
            'doctor' => $request->query('doctor'), // id numérico de persona (doctor)
        ];

        // Mapeo numérico<->texto para ESTADO_CITA
        $estadoMap = ['Confirmada' => 1, 'Pendiente' => 2, 'Cancelada' => 3];

        // Consulta REAL: cita -> persona (paciente) / persona (doctor)
        $q = DB::table('tbl_cita as c')
            // paciente (FK_COD_PACIENTE -> tbl_persona)
            ->leftJoin('tbl_persona as pper', 'pper.COD_PERSONA', '=', 'c.FK_COD_PACIENTE')
            // doctor  (FK_COD_DOCTOR   -> tbl_persona)
            ->leftJoin('tbl_persona as dper', 'dper.COD_PERSONA', '=', 'c.FK_COD_DOCTOR')
            ->selectRaw("
                c.COD_CITA as id,
                c.FEC_CITA as fecha,
                c.HOR_CITA as hora,
                TRIM(CONCAT(
                    COALESCE(pper.PRIMER_NOMBRE,''),' ',
                    COALESCE(pper.SEGUNDO_NOMBRE,''),' ',
                    COALESCE(pper.PRIMER_APELLIDO,''),' ',
                    COALESCE(pper.SEGUNDO_APELLIDO,'')
                )) as paciente,
                TRIM(CONCAT(
                    COALESCE(dper.PRIMER_NOMBRE,''),' ',
                    COALESCE(dper.SEGUNDO_NOMBRE,''),' ',
                    COALESCE(dper.PRIMER_APELLIDO,''),' ',
                    COALESCE(dper.SEGUNDO_APELLIDO,'')
                )) as doctor,
                CASE c.ESTADO_CITA
                    WHEN 1 THEN 'Confirmada'
                    WHEN 2 THEN 'Pendiente'
                    WHEN 3 THEN 'Cancelada'
                    ELSE 'Desconocido'
                END as estado,
                c.MOT_CITA as motivo
            ");

        // Filtros
        if (!empty($filters['estado']) && isset($estadoMap[$filters['estado']])) {
            $q->where('c.ESTADO_CITA', $estadoMap[$filters['estado']]);
        }
        if (!empty($filters['doctor'])) {
            // OJO: en tu esquema doctor es COD_PERSONA
            $q->where('c.FK_COD_DOCTOR', $filters['doctor']);
        }
        if (!empty($filters['desde'])) {
            $q->whereDate('c.FEC_CITA', '>=', $filters['desde']);
        }
        if (!empty($filters['hasta'])) {
            $q->whereDate('c.FEC_CITA', '<=', $filters['hasta']);
        }

        // Reglas por rol (si más adelante conectas usuarios/doctores/pacientes)
        if ($rol === 'DOCTOR' && $user) {
            // $q->where('c.FK_COD_DOCTOR', data_get($user, 'persona.COD_PERSONA', 0));
        }
        if ($rol === 'PACIENTE' && $user) {
            // $q->where('c.FK_COD_PACIENTE', data_get($user, 'persona.COD_PERSONA', 0));
        }

        $rows = $q->orderBy('c.FEC_CITA')->orderBy('c.HOR_CITA')->get()->map(function ($r) {
            return [
                'id'       => $r->id,
                'fecha'    => $r->fecha,
                'hora'     => $r->hora,
                'paciente' => $r->paciente,
                'doctor'   => $r->doctor,
                'estado'   => $r->estado,
                'motivo'   => $r->motivo,
            ];
        })->toArray();

        // Catálogos fallback (la vista los sobreescribe con la API)
        $catalogoEstados  = ['Confirmada', 'Pendiente', 'Cancelada'];
        $catalogoDoctores = [];

        // Mostrar columnas/acciones según rol
        $showDoctorColumn = in_array($rol, ['ADMIN','RECEPCIONISTA','PACIENTE']);
        $showActions      = in_array($rol, ['ADMIN','RECEPCIONISTA','DOCTOR']);

        // Títulos y partials
        $pageTitle = "{$section} · {$rolLabel}";
        $heading   = "{$section} {$rolLabel}";
        $bannerPartial  = "modulo-citas.shared.partials.banner.{$rol}.{$section}";
        $toolbarPartial = "modulo-citas.shared.partials.toolbar.{$rol}.{$section}";

        return view('modulo-citas.shared.lista', compact(
            'pageTitle', 'heading', 'rows', 'filters', 'routeName',
            'catalogoEstados', 'catalogoDoctores',
            'showDoctorColumn', 'showActions',
            'bannerPartial', 'toolbarPartial',
            'rol'
        ));
    }
}
