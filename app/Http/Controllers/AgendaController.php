<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgendaController extends Controller
{
    public function citas(Request $request)
    {
        return $this->render('Citas', $request);
    }

    public function calendario(Request $request)
    {
        return $this->render('Calendario', $request);
    }

    public function reportes(Request $request)
    {
        return $this->render('Reportes', $request);
    }

    private function render(string $section, Request $request)
    {
        $user    = auth()->user();
        $rolName = strtoupper(optional($user->rol)->NOM_ROL ?? 'ADMIN');
        $rolSlug = $this->mapRol($rolName);

        $sectionKey = match (strtoupper($section)) {
            'CALENDARIO' => 'calendario',
            'REPORTES'   => 'reportes',
            default      => 'citas',
        };

        $routeName = match ($sectionKey) {
            'calendario' => 'agenda.calendario',
            'reportes'   => 'agenda.reportes',
            default      => 'agenda.citas',
        };

        $labels   = $this->sectionLabels();
        $labelSet = $labels[$rolSlug][$sectionKey] ?? $labels['admin'][$sectionKey];

        // *** AQUÍ EMPIEZA LA PARTE "REAL" ***
        // Construimos paneles de doctores, pacientes disponibles y resumen
        $doctorPanels      = $this->doctorPanelsFromDatabase();
        $availablePatients = $this->availablePatientsFromDatabase();

        // Si no hay doctores, evitamos errores dejando estructuras vacías
        $activeDoctor  = $doctorPanels[0] ?? [
            'codigo'       => null,
            'nombre'       => null,
            'especialidad' => null,
            'color'        => '#0d6efd',
            'contacto'     => null,
            'pacientes'    => [],
            'agenda'       => [],
        ];

        $patientRecord = $this->patientRecord($activeDoctor);
        $timeline      = $this->patientTimeline();

        $calendarMatrix      = $this->calendarMatrix();
        $calendarEventBundle = $this->buildCalendarEvents($doctorPanels, $rolSlug, $activeDoctor, $patientRecord);
        $calendarEvents      = $calendarEventBundle['byDate'];
        $eventList           = $calendarEventBundle['list'];

        $stats     = $this->buildStats($rolSlug, $doctorPanels, $availablePatients, $patientRecord, $eventList);
        $shareLink = url('/registro/paciente?doctor=dr-lopez'); // luego lo haremos dinámico
        $shareCode = 'DR-LOPEZ-2025';

        $view = $this->resolveView($rolSlug, $sectionKey);

        return view($view, [
            'pageTitle'         => $labelSet['pageTitle'],
            'heading'           => $labelSet['heading'],
            'intro'             => $labelSet['intro'],
            'routeName'         => $routeName,
            'sectionKey'        => $sectionKey,
            'rolSlug'           => $rolSlug,
            'doctorPanels'      => $doctorPanels,
            'availablePatients' => $availablePatients,
            'activeDoctor'      => $activeDoctor,
            'patientRecord'     => $patientRecord,
            'timeline'          => $timeline,
            'calendarMatrix'    => $calendarMatrix,
            'calendarEvents'    => $calendarEvents,
            'eventList'         => $eventList,
            'stats'             => $stats,
            'shareLink'         => $shareLink,
            'shareCode'         => $shareCode,
        ]);
    }

    private function resolveView(string $rolSlug, string $sectionKey): string
    {
        $view = "modulo-citas.{$rolSlug}.{$sectionKey}.index";

        return view()->exists($view) ? $view : 'modulo-citas.shared.lista';
    }

    private function mapRol(string $rolName): string
    {
        return match (true) {
            str_contains($rolName, 'DOCTOR')        => 'doctor',
            str_contains($rolName, 'RECEPCION')     => 'recepcionista',
            str_contains($rolName, 'PACIENT')       => 'paciente',
            default                                 => 'admin',
        };
    }

    private function sectionLabels(): array
    {
        return [
            'admin' => [
                'citas' => [
                    'pageTitle' => 'Citas · Administración',
                    'heading'   => 'Ver citas',
                    'intro'     => 'Supervisa doctores y pacientes asignados en tiempo real.',
                ],
                'calendario' => [
                    'pageTitle' => 'Agenda · Administración',
                    'heading'   => 'Agenda global',
                    'intro'     => 'Visualiza todas las citas sobre un calendario estilo AdminLTE.',
                ],
                'reportes' => [
                    'pageTitle' => 'Reportes · Administración',
                    'heading'   => 'Reporte operativo',
                    'intro'     => 'Historial de productividad y estados de cada cita.',
                ],
            ],
            'recepcionista' => [
                'citas' => [
                    'pageTitle' => 'Citas · Recepción',
                    'heading'   => 'Ver citas',
                    'intro'     => 'Confirma, reagenda o cancela según la disponibilidad de doctores.',
                ],
                'calendario' => [
                    'pageTitle' => 'Agenda · Recepción',
                    'heading'   => 'Agenda diaria',
                    'intro'     => 'Calendario consolidado para coordinar salas y recursos.',
                ],
                'reportes' => [
                    'pageTitle' => 'Historial · Recepción',
                    'heading'   => 'Bitácora de citas',
                    'intro'     => 'Seguimiento de confirmaciones y cancelaciones.',
                ],
            ],
            'doctor' => [
                'citas' => [
                    'pageTitle' => 'Mis pacientes',
                    'heading'   => 'Mis pacientes',
                    'intro'     => 'Asigna pacientes libres, crea enlaces y controla tu panel clínico.',
                ],
                'calendario' => [
                    'pageTitle' => 'Agenda del doctor',
                    'heading'   => 'Mi agenda',
                    'intro'     => 'Gestiona tus horarios con el calendario estilo AdminLTE.',
                ],
                'reportes' => [
                    'pageTitle' => 'Seguimiento clínico',
                    'heading'   => 'Seguimiento clínico',
                    'intro'     => 'Historial resumido de evolución por paciente.',
                ],
            ],
            'paciente' => [
                'citas' => [
                    'pageTitle' => 'Mis citas',
                    'heading'   => 'Mis citas',
                    'intro'     => 'Consulta tu doctor asignado y la próxima cita.',
                ],
                'calendario' => [
                    'pageTitle' => 'Agenda del paciente',
                    'heading'   => 'Agenda',
                    'intro'     => 'Visualiza tus citas confirmadas dentro del calendario.',
                ],
                'reportes' => [
                    'pageTitle' => 'Historial de citas',
                    'heading'   => 'Historial',
                    'intro'     => 'Todas tus visitas previas con detalles y estado.',
                ],
            ],
        ];
    }

    /**
     * Construye los "paneles de doctores" pero ahora desde la BD real.
     * Mantiene la misma estructura que usaban las vistas de demo.
     */
    private function doctorPanelsFromDatabase(): array
    {
        // 1) Doctores (usuarios cuyo rol es DOCTOR)
        $doctors = DB::table('tbl_usuario as u')
            ->join('tbl_rol as r', 'u.FK_COD_ROL', '=', 'r.COD_ROL')
            ->join('tbl_persona as p', 'u.FK_COD_PERSONA', '=', 'p.COD_PERSONA')
            ->where('r.NOM_ROL', 'DOCTOR')
            ->select(
                'u.COD_USUARIO',
                'u.USR_USUARIO',
                'p.COD_PERSONA as PERSONA_ID',
                'p.PRIMER_NOMBRE',
                'p.PRIMER_APELLIDO'
            )
            ->get();

        if ($doctors->isEmpty()) {
            // Si no hay doctores aún, devolvemos arreglo vacío.
            return [];
        }

        $colors = ['#0d6efd', '#20c997', '#6f42c1', '#fd7e14'];
        $panels = [];

        foreach ($doctors as $index => $doc) {
            // 2) Citas asociadas a este doctor
            $citas = DB::table('tbl_cita as c')
                ->join('tbl_persona as pac', 'c.FK_COD_PACIENTE', '=', 'pac.COD_PERSONA')
                ->join('tbl_estado_cita as e', 'c.ESTADO_CITA', '=', 'e.COD_ESTADO')
                ->where('c.FK_COD_DOCTOR', $doc->PERSONA_ID)
                ->orderBy('c.FEC_CITA')
                ->orderBy('c.HOR_CITA')
                ->select(
                    'c.COD_CITA',
                    'c.FEC_CITA',
                    'c.HOR_CITA',
                    'c.HOR_FIN',
                    'c.MOT_CITA',
                    'c.OBSERVACIONES',
                    'pac.COD_PERSONA as PACIENTE_ID',
                    DB::raw("CONCAT(pac.PRIMER_NOMBRE,' ',pac.PRIMER_APELLIDO) as PACIENTE_NOMBRE"),
                    'e.NOM_ESTADO'
                )
                ->get();

            // Lista única de pacientes del doctor (para la tabla de "Pacientes asignados")
            $pacientes = [];
            // Agenda diaria (para el calendario / próximos eventos)
            $agenda = [];

            foreach ($citas as $cita) {
                $pacId = $cita->PACIENTE_ID;

                if (!isset($pacientes[$pacId])) {
                    $pacientes[$pacId] = [
                        'persona_id' => $pacId,
                        'codigo'     => 'PAC-' . $pacId,
                        'nombre'     => $cita->PACIENTE_NOMBRE,
                        'motivo'     => $cita->MOT_CITA,
                        'estado'     => $cita->NOM_ESTADO,
                        'fecha'      => $cita->FEC_CITA,
                        'hora'       => substr($cita->HOR_CITA, 0, 5),
                        'nota'       => $cita->OBSERVACIONES,
                    ];
                }

                $agenda[] = [
                    'fecha'     => $cita->FEC_CITA,
                    'hora'      => substr($cita->HOR_CITA, 0, 5),
                    'paciente'  => $cita->PACIENTE_NOMBRE,
                    'estado'    => $cita->NOM_ESTADO,
                    'motivo'    => $cita->MOT_CITA,
                    'duracion'  => '30 min',      // campo de demo, por ahora fijo
                    'ubicacion' => 'Consultorio', // puedes refinarlo luego
                ];
            }

            $panels[] = [
                'codigo'       => 'DOC-' . $doc->PERSONA_ID,
                'nombre'       => trim($doc->PRIMER_NOMBRE . ' ' . $doc->PRIMER_APELLIDO),
                'especialidad' => 'Odontología', // más adelante la sacamos de otra tabla si quieres
                'color'        => $colors[$index % count($colors)],
                'contacto'     => 'Usuario: ' . $doc->USR_USUARIO,
                'pacientes'    => array_values($pacientes),
                'agenda'       => $agenda,
            ];
        }

        return $panels;
    }

    /**
     * Pacientes SIN doctor asignado:
     *  - Rol PACIENTE
     *  - Que no aparezcan en tbl_doctor_paciente.
     */
    private function availablePatientsFromDatabase(): array
    {
        $rows = DB::table('tbl_usuario as u')
            ->join('tbl_rol as r', 'u.FK_COD_ROL', '=', 'r.COD_ROL')
            ->join('tbl_persona as p', 'u.FK_COD_PERSONA', '=', 'p.COD_PERSONA')
            ->leftJoin('tbl_doctor_paciente as dp', 'p.COD_PERSONA', '=', 'dp.FK_COD_PACIENTE')
            ->where('r.NOM_ROL', 'PACIENTE')
            ->whereNull('dp.FK_COD_PACIENTE')
            ->select(
                'p.COD_PERSONA',
                'p.PRIMER_NOMBRE',
                'p.PRIMER_APELLIDO'
            )
            ->limit(20)
            ->get();

        return $rows->map(function ($row) {
            return [
                'nombre'      => trim($row->PRIMER_NOMBRE . ' ' . $row->PRIMER_APELLIDO),
                'motivo'      => 'Sin doctor asignado',
                'preferencia' => '',
                'ultima'      => null,
            ];
        })->toArray();
    }

    /**
     * Perfil + historial de un paciente destacado.
     * - Si el usuario logueado es PACIENTE: se usa él mismo.
     * - Si no: se usa el primer paciente del doctor activo.
     */
    private function patientRecord(array $activeDoctor): array
    {
        $user    = auth()->user();
        $rolName = strtoupper(optional($user->rol)->NOM_ROL ?? 'ADMIN');
        $rolSlug = $this->mapRol($rolName);

        $pacienteId   = null;
        $doctorNombre = $activeDoctor['nombre'] ?? null;
        $especialidad = $activeDoctor['especialidad'] ?? 'Odontología';

        if ($rolSlug === 'paciente') {
            $pacienteId = (int) ($user->FK_COD_PERSONA ?? 0);
        } else {
            // Para admin / recepcionista / doctor:
            $first = $activeDoctor['pacientes'][0] ?? null;
            if ($first && isset($first['persona_id'])) {
                $pacienteId = (int) $first['persona_id'];
            }
        }

        if (!$pacienteId) {
            return [
                'profile' => [
                    'codigo'       => null,
                    'nombre'       => null,
                    'doctor'       => $doctorNombre,
                    'especialidad' => $especialidad,
                    'estado'       => 'Sin datos',
                    'correo'       => null,
                    'telefono'     => null,
                    'proxima'      => [
                        'fecha'  => null,
                        'hora'   => null,
                        'motivo' => null,
                        'estado' => null,
                    ],
                ],
                'historial' => [],
            ];
        }

        $persona = DB::table('tbl_persona')
            ->where('COD_PERSONA', $pacienteId)
            ->first();

        $citas = DB::table('tbl_cita as c')
            ->join('tbl_persona as d', 'c.FK_COD_DOCTOR', '=', 'd.COD_PERSONA')
            ->join('tbl_estado_cita as e', 'c.ESTADO_CITA', '=', 'e.COD_ESTADO')
            ->where('c.FK_COD_PACIENTE', $pacienteId)
            ->orderBy('c.FEC_CITA')
            ->orderBy('c.HOR_CITA')
            ->select(
                'c.FEC_CITA',
                'c.HOR_CITA',
                'c.HOR_FIN',
                'c.MOT_CITA',
                'c.OBSERVACIONES',
                'e.NOM_ESTADO',
                DB::raw("CONCAT(d.PRIMER_NOMBRE,' ',d.PRIMER_APELLIDO) as DOCTOR_NOMBRE")
            )
            ->get();

        $now = now();

        $proxima = $citas->first(function ($c) use ($now) {
            $dt = $c->FEC_CITA . ' ' . $c->HOR_CITA;
            return $dt >= $now->toDateTimeString();
        }) ?? $citas->last();

        $historial = $citas
            ->sortByDesc('FEC_CITA')
            ->sortByDesc('HOR_CITA')
            ->map(function ($c) {
                return [
                    'fecha'   => $c->FEC_CITA,
                    'estado'  => $c->NOM_ESTADO,
                    'motivo'  => $c->MOT_CITA,
                    'doctor'  => $c->DOCTOR_NOMBRE,
                    'detalle' => $c->OBSERVACIONES,
                ];
            })
            ->take(10)
            ->values()
            ->all();

        $nombrePaciente = $persona
            ? trim(($persona->PRIMER_NOMBRE ?? '') . ' ' . ($persona->PRIMER_APELLIDO ?? ''))
            : 'Paciente #' . $pacienteId;

        return [
            'profile' => [
                'codigo'       => 'PAC-' . $pacienteId,
                'nombre'       => $nombrePaciente,
                'doctor'       => $proxima->DOCTOR_NOMBRE ?? $doctorNombre,
                'especialidad' => $especialidad,
                'estado'       => 'Activo',
                'correo'       => null,
                'telefono'     => null,
                'proxima'      => [
                    'fecha'  => $proxima->FEC_CITA ?? null,
                    'hora'   => isset($proxima->HOR_CITA) ? substr($proxima->HOR_CITA, 0, 5) : null,
                    'motivo' => $proxima->MOT_CITA ?? null,
                    'estado' => $proxima->NOM_ESTADO ?? null,
                ],
            ],
            'historial' => $historial,
        ];
    }

    private function patientTimeline(): array
    {
        // Por ahora sigue siendo de demo.
        // Más adelante lo podemos enlazar con tbl_bitacora o similares.
        return [
            ['fecha' => '2025-11-10', 'descripcion' => 'Se envió recordatorio vía correo.', 'estado' => 'Notificado'],
            ['fecha' => '2025-11-08', 'descripcion' => 'Paciente confirmó asistencia.', 'estado' => 'Confirmado'],
            ['fecha' => '2025-11-05', 'descripcion' => 'Recepción cargó nueva radiografía.', 'estado' => 'Documentado'],
        ];
    }

    private function calendarMatrix(): array
    {
        // Sigue siendo una grilla fija de demo (no afecta a la lógica de citas reales)
        return [
            [
                ['label' => '27', 'date' => '2025-10-27', 'isMuted' => true],
                ['label' => '28', 'date' => '2025-10-28', 'isMuted' => true],
                ['label' => '29', 'date' => '2025-10-29', 'isMuted' => true],
                ['label' => '30', 'date' => '2025-10-30', 'isMuted' => true],
                ['label' => '31', 'date' => '2025-10-31', 'isMuted' => true],
                ['label' => '1',  'date' => '2025-11-01'],
                ['label' => '2',  'date' => '2025-11-02'],
            ],
            [
                ['label' => '3',  'date' => '2025-11-03'],
                ['label' => '4',  'date' => '2025-11-04'],
                ['label' => '5',  'date' => '2025-11-05'],
                ['label' => '6',  'date' => '2025-11-06'],
                ['label' => '7',  'date' => '2025-11-07'],
                ['label' => '8',  'date' => '2025-11-08'],
                ['label' => '9',  'date' => '2025-11-09'],
            ],
            [
                ['label' => '10', 'date' => '2025-11-10'],
                ['label' => '11', 'date' => '2025-11-11'],
                ['label' => '12', 'date' => '2025-11-12', 'isToday' => true],
                ['label' => '13', 'date' => '2025-11-13'],
                ['label' => '14', 'date' => '2025-11-14'],
                ['label' => '15', 'date' => '2025-11-15'],
                ['label' => '16', 'date' => '2025-11-16'],
            ],
            [
                ['label' => '17', 'date' => '2025-11-17'],
                ['label' => '18', 'date' => '2025-11-18'],
                ['label' => '19', 'date' => '2025-11-19'],
                ['label' => '20', 'date' => '2025-11-20'],
                ['label' => '21', 'date' => '2025-11-21'],
                ['label' => '22', 'date' => '2025-11-22'],
                ['label' => '23', 'date' => '2025-11-23'],
            ],
            [
                ['label' => '24', 'date' => '2025-11-24'],
                ['label' => '25', 'date' => '2025-11-25'],
                ['label' => '26', 'date' => '2025-11-26'],
                ['label' => '27', 'date' => '2025-11-27'],
                ['label' => '28', 'date' => '2025-11-28'],
                ['label' => '29', 'date' => '2025-11-29'],
                ['label' => '30', 'date' => '2025-11-30'],
            ],
            [
                ['label' => '1', 'date' => '2025-12-01', 'isMuted' => true],
                ['label' => '2', 'date' => '2025-12-02', 'isMuted' => true],
                ['label' => '3', 'date' => '2025-12-03', 'isMuted' => true],
                ['label' => '4', 'date' => '2025-12-04', 'isMuted' => true],
                ['label' => '5', 'date' => '2025-12-05', 'isMuted' => true],
                ['label' => '6', 'date' => '2025-12-06', 'isMuted' => true],
                ['label' => '7', 'date' => '2025-12-07', 'isMuted' => true],
            ],
        ];
    }

    private function buildCalendarEvents(array $doctorPanels, string $rolSlug, array $activeDoctor, array $patientRecord): array
    {
        $events = collect($doctorPanels)
            ->flatMap(function ($doctor) {
                return collect($doctor['agenda'])->map(function ($event) use ($doctor) {
                    return array_merge($event, [
                        'doctor'       => $doctor['nombre'],
                        'especialidad' => $doctor['especialidad'],
                        'color'        => $doctor['color'],
                    ]);
                });
            });

        if ($rolSlug === 'doctor') {
            $events = $events->where('doctor', $activeDoctor['nombre']);
        }

        if ($rolSlug === 'paciente') {
            $paciente = $patientRecord['profile']['nombre'] ?? null;
            if ($paciente) {
                $events = $events->where('paciente', $paciente);
            }
        }

        $events = $events->values();

        return [
            'list'  => $events->all(),
            'byDate'=> $events->groupBy('fecha')->map(fn($group) => $group->values()->all())->all(),
        ];
    }

    private function buildStats(string $rolSlug, array $doctorPanels, array $availablePatients, array $patientRecord, array $eventList): array
    {
        $eventsCollection = collect($eventList);
        $totalCitas       = $eventsCollection->count();
        $pendientes       = $eventsCollection->where('estado', 'Pendiente')->count();
        $confirmadas      = $eventsCollection->where('estado', 'Confirmada')->count();
        $canceladas       = $eventsCollection->where('estado', 'Cancelada')->count();
        $pacientesActivos = collect($doctorPanels)->sum(fn($doc) => count($doc['pacientes']));

        return match ($rolSlug) {
            'doctor' => [
                ['label' => 'Pacientes activos', 'value' => $pacientesActivos, 'icon' => 'fas fa-user-friends', 'color' => 'primary'],
                ['label' => 'Pendientes por confirmar', 'value' => $pendientes, 'icon' => 'fas fa-hourglass-half', 'color' => 'warning'],
                ['label' => 'Citas confirmadas', 'value' => $confirmadas, 'icon' => 'fas fa-check-circle', 'color' => 'success'],
            ],
            'paciente' => [
                ['label' => 'Próxima cita', 'value' => ($patientRecord['profile']['proxima']['fecha'] ?? '') . ' · ' . ($patientRecord['profile']['proxima']['hora'] ?? ''), 'icon' => 'fas fa-calendar-day', 'color' => 'info'],
                ['label' => 'Estado', 'value' => $patientRecord['profile']['proxima']['estado'] ?? 'Sin datos', 'icon' => 'fas fa-heartbeat', 'color' => 'success'],
                ['label' => 'Historial total', 'value' => count($patientRecord['historial'] ?? []), 'icon' => 'fas fa-history', 'color' => 'secondary'],
            ],
            default => [
                ['label' => 'Citas programadas', 'value' => $totalCitas, 'icon' => 'fas fa-calendar-check', 'color' => 'primary'],
                ['label' => 'Pendientes', 'value' => $pendientes, 'icon' => 'fas fa-exclamation-circle', 'color' => 'warning'],
                ['label' => 'Canceladas', 'value' => $canceladas, 'icon' => 'fas fa-times-circle', 'color' => 'danger'],
                ['label' => 'Pacientes sin doctor', 'value' => count($availablePatients), 'icon' => 'fas fa-user-clock', 'color' => 'info'],
            ],
        };
    }
}
