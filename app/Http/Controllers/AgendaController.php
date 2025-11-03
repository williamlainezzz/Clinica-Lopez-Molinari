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
// data_get evita acceder propiedades en null; default 'ADMIN' para invitado
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

        // Filtros desde la query
        $filters = [
            'desde'  => $request->query('desde'),
            'hasta'  => $request->query('hasta'),
            'estado' => $request->query('estado'),  // Confirmada | Pendiente | Cancelada | null
            'doctor' => $request->query('doctor'),  // id numérico de doctor
        ];

        // === Consulta REAL a BD (ajusta nombres de columnas/tablas si difieren) ===
        $q = DB::table('tbl_cita as c')
            ->leftJoin('tbl_paciente as pa', 'pa.COD_PACIENTE', '=', 'c.FK_COD_PACIENTE')
            ->leftJoin('tbl_persona as pper', 'pper.COD_PERSONA', '=', 'pa.FK_COD_PERSONA')
            ->leftJoin('tbl_doctor as d', 'd.COD_DOCTOR', '=', 'c.FK_COD_DOCTOR')
            ->leftJoin('tbl_persona as dper', 'dper.COD_PERSONA', '=', 'd.FK_COD_PERSONA')
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
                c.EST_CITA as estado,
                c.DES_MOTIVO as motivo
            ");

        if (!empty($filters['estado'])) {
            $q->where('c.EST_CITA', $filters['estado']);
        }
        if (!empty($filters['doctor'])) {
            $q->where('c.FK_COD_DOCTOR', $filters['doctor']); // doctor = id numérico
        }
        if (!empty($filters['desde'])) {
            $q->whereDate('c.FEC_CITA', '>=', $filters['desde']);
        }
        if (!empty($filters['hasta'])) {
            $q->whereDate('c.FEC_CITA', '<=', $filters['hasta']);
        }

        // Si quisieras filtrar por rol (ej. Doctor solo ve lo suyo), aquí:
        if ($rol === 'DOCTOR') {
            // $q->where('c.FK_COD_DOCTOR', $user->doctor->COD_DOCTOR ?? 0);
            // Ajusta si tu User tiene relación con Doctor
        }
        if ($rol === 'PACIENTE') {
            // $q->where('c.FK_COD_PACIENTE', $user->paciente->COD_PACIENTE ?? 0);
            // Ajusta si tu User tiene relación con Paciente
        }

        $rows = $q->orderBy('c.FEC_CITA')->orderBy('c.HOR_CITA')->get()
            ->map(function ($r) {
                return [
                    'fecha'    => $r->fecha,
                    'hora'     => $r->hora,
                    'paciente' => $r->paciente,
                    'doctor'   => $r->doctor,
                    'estado'   => $r->estado,
                    'motivo'   => $r->motivo,
                    // puedes pasar id si vas a usar acciones con ese ID real
                    'id'       => $r->id,
                ];
            })->toArray();

        // Catálogos para selects (el JS los sobreescribe con la API, pero dejamos fallback)
        $catalogoEstados  = ['Confirmada', 'Pendiente', 'Cancelada'];
        $catalogoDoctores = []; // dejamos vacío; el JS lo llena desde /api/agenda/doctores

        // Mostrar columnas/acciones según rol
        $showDoctorColumn = in_array($rol, ['ADMIN','RECEPCIONISTA','PACIENTE']); // el Doctor puede ocultar su propia columna si prefieres
        $showActions      = in_array($rol, ['ADMIN','RECEPCIONISTA','DOCTOR']);

        // Títulos
        $pageTitle = "{$section} · {$rolLabel}";
        $heading   = "{$section} {$rolLabel}";

        // Partials (si no existen, includeIf no truena)
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
