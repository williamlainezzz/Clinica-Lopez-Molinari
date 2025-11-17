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

    // sección actual (citas / calendario / reportes)
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

    // -----------------------------
    // Valores por defecto
    // -----------------------------
    $doctorPanels      = $this->demoDoctors();           // demo doctores (fallback)
    $availablePatients = $this->availablePatientsFromDb(); // pacientes sin doctor reales
    $activeDoctor      = $doctorPanels[0] ?? null;
    $patientRecord     = $activeDoctor ? $this->patientRecord($activeDoctor) : null;
    $timeline          = $this->patientTimeline();
    $calendarMatrix    = $this->calendarMatrix();
    $calendarEvents    = [];
    $eventList         = collect();
    $stats             = [];

    // ==========================================================
    // DOCTOR: usar datos REALES de BD para todas las secciones
    // ==========================================================
    if ($rolSlug === 'doctor') {
        // Limpiamos los datos de ejemplo; si algo falla, la vista mostrará "sin citas"
        $doctorPanels      = [];
        $availablePatients = [];
        $activeDoctor      = null;
        $patientRecord     = null;

        $personaId = (int) ($user->FK_COD_PERSONA ?? optional($user->persona)->COD_PERSONA ?? 0);

        if ($personaId > 0) {
            // Citas reales SOLO de este doctor
            $citas = $this->fetchCitasFromDatabase($rolSlug, $personaId);

            if ($citas->isNotEmpty()) {
                // Paneles (pacientes + agenda) construidos desde la BD
                $doctorPanels = $this->buildDoctorsFromCitas($citas);
                $activeDoctor = $doctorPanels[0] ?? null;

                // Paciente destacado = el de la cita más cercana
                $primerCita        = $citas->sortBy(['FEC_CITA', 'HOR_CITA'])->first();
                $pacientePersonaId = (int) ($primerCita->paciente_persona_id ?? 0);

                if ($pacientePersonaId > 0) {
                    $patientRecord = $this->buildPatientRecordFromDb($pacientePersonaId, $citas);
                } elseif ($activeDoctor) {
                    // Fallback: ficha simple basada en el panel
                    $patientRecord = $this->patientRecordFromPanels($activeDoctor);
                }

                // Pacientes sin doctor (widget lateral)
                $availablePatients = $this->availablePatientsFromDb();
            } else {
                // Sin citas aún: ficha vacía para no romper la vista
                $patientRecord = [
                    'profile' => [
                        'codigo'       => null,
                        'nombre'       => 'Sin pacientes',
                        'doctor'       => $user->name ?? 'Doctor',
                        'especialidad' => 'Odontología',
                        'estado'       => 'Sin citas',
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
        }
    }

    // ==========================================================
    // ADMIN en sección "citas": datos reales de BD
    // ==========================================================
    if ($sectionKey === 'citas' && $rolSlug === 'admin') {
        $personaId = (int) ($user->FK_COD_PERSONA ?? 0);

        // Traemos todas las citas de la BD
        $citas = $this->fetchCitasFromDatabase($rolSlug, $personaId);

        // Convertimos esa colección en el formato que espera la vista
        $doctorPanels      = $this->buildDoctorPanelsFromCitas($citas);
        $availablePatients = $this->availablePatientsFromDb();

        // Lista plana solo para las tarjetas de estadísticas
        $eventList = $citas->map(function ($c) {
            $estado = strtoupper(trim($c->estado_nombre ?? ''));
            return [
                'estado' => match ($estado) {
                    'CONFIRMADA' => 'Confirmada',
                    'CANCELADA'  => 'Cancelada',
                    default      => 'Pendiente',
                },
            ];
        });

        // Reusamos buildStats
        $stats = $this->buildStats(
            $rolSlug,
            $doctorPanels,
            $availablePatients,
            [],                    // patientRecord no se usa en rama admin
            $eventList->all()
        );
    } else {
        // Resto de roles / secciones: usamos los paneles (demo o reales) para calendario y estadísticas
        $calendarEventBundle = $this->buildCalendarEvents(
            $doctorPanels,
            $rolSlug,
            $activeDoctor ?? [],
            $patientRecord ?? []
        );

        $calendarEvents = $calendarEventBundle['byDate'];
        $eventList      = collect($calendarEventBundle['list']);

        $stats = $this->buildStats(
            $rolSlug,
            $doctorPanels,
            $availablePatients,
            $patientRecord ?? [],
            $eventList->all()
        );
    }

    // ===========================================
    // Enlace / código para registro de pacientes
    // ===========================================
    $shareLink = null;
    $shareCode = null;

    if ($rolSlug === 'doctor') {
        // Intentamos primero con el usuario (login)
        $username  = $user->USR_USUARIO ?? null;
        $personaId = (int) ($user->FK_COD_PERSONA ?? optional($user->persona)->COD_PERSONA ?? 0);

        if ($username) {
            // Ej: /registro/paciente?doctor=olagos
            $shareLink = url('/registro/paciente?doctor=' . urlencode($username));
            $shareCode = 'DOC-' . strtoupper($username);
        } elseif ($personaId > 0) {
            // Fallback por ID de persona
            // Ej: /registro/paciente?doctor_id=20
            $shareLink = url('/registro/paciente?doctor_id=' . $personaId);
            $shareCode = 'DOC-' . str_pad($personaId, 4, '0', STR_PAD_LEFT);
        }
    } elseif (in_array($rolSlug, ['admin', 'recepcionista'])) {
        // Link genérico de registro cuando es admin/recepción
        $shareLink = url('/registro/paciente');
        $shareCode = 'REGISTRO-GENERAL';
    }

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
        // Ejemplo: modulo-citas.admin.citas.index
        $view = "modulo-citas.{$rolSlug}.{$sectionKey}.index";

        return view()->exists($view)
            ? $view
            : 'modulo-citas.shared.lista';
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

    /* =========================================================
     *  LABELS POR ROL / SECCIÓN
     * =======================================================*/
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

    /* =========================================================
     *  WRAPPERS PARA DEMO + BD (CORRECCIÓN DEL ERROR)
     * =======================================================*/

    /**
     * Obtiene los paneles de doctores que usa el módulo.
     * Intenta usar datos reales de BD y, si no hay, cae a los datos demo estáticos.
     */
    private function demoDoctors(): array
    {
        $user    = auth()->user();
        $rolName = strtoupper(optional($user->rol)->NOM_ROL ?? 'ADMIN');
        $rolSlug = $this->mapRol($rolName);

        // Intentamos armar los paneles con datos reales
        $panels = $this->loadDoctorPanelsFromDb($rolSlug, $user);

        if (!empty($panels)) {
            return $panels;
        }

        // Si la BD no tiene citas todavía, devolvemos los datos de ejemplo
        return $this->demoDoctorsStatic();
    }

    /**
     * Wrapper para el widget de "Pacientes sin doctor asignado".
     * Intenta usar la BD y, si no hay resultados, devuelve algunos pacientes demo.
     */
    private function availablePatients(): array
    {
        $fromDb = $this->availablePatientsFromDb();

        if (!empty($fromDb)) {
            return $fromDb;
        }

        // Fallback estático sencillo
        return [
            [
                'codigo'      => 'PAC-0001',
                'nombre'      => 'Paciente demo 1',
                'motivo'      => 'Pendiente de asignar doctor',
                'estado'      => 'Pendiente',
                'preferencia' => null,
                'ultima'      => null,
            ],
            [
                'codigo'      => 'PAC-0002',
                'nombre'      => 'Paciente demo 2',
                'motivo'      => 'Pendiente de asignar doctor',
                'estado'      => 'Pendiente',
                'preferencia' => null,
                'ultima'      => null,
            ],
            [
                'codigo'      => 'PAC-0003',
                'nombre'      => 'Paciente demo 3',
                'motivo'      => 'Pendiente de asignar doctor',
                'estado'      => 'Pendiente',
                'preferencia' => null,
                'ultima'      => null,
            ],
        ];
    }

    /**
     * Wrapper para construir la ficha del paciente según el doctor activo.
     * De momento usa la lógica basada en los paneles (patientRecordFromPanels).
     */
    private function patientRecord(array $activeDoctor): array
    {
        return $this->patientRecordFromPanels($activeDoctor);
    }

    /* =========================================================
     *  CARGA DESDE BD: DOCTORES + PACIENTES + AGENDA
     * =======================================================*/
    private function loadDoctorPanelsFromDb(string $rolSlug, $user): array
    {
        try {
            // Si no existe tbl_cita, devolvemos demo
            if (!DB::getSchemaBuilder()->hasTable('tbl_cita')) {
                return [];
            }

            $q = DB::table('tbl_cita as c')
                ->join('tbl_persona as d', 'c.FK_COD_DOCTOR', '=', 'd.COD_PERSONA')
                ->join('tbl_persona as p', 'c.FK_COD_PACIENTE', '=', 'p.COD_PERSONA')
                ->leftJoin('tbl_estado_cita as e', 'c.ESTADO_CITA', '=', 'e.COD_ESTADO')
                ->select([
                    'c.COD_CITA',
                    'c.FK_COD_DOCTOR',
                    'c.FK_COD_PACIENTE',
                    'c.FEC_CITA',
                    'c.HOR_CITA',
                    'c.HOR_FIN',
                    'c.MOT_CITA',
                    'c.OBSERVACIONES',
                    'c.ESTADO_CITA',
                    'd.COD_PERSONA as doctor_persona_id',
                    DB::raw("CONCAT(d.PRIMER_NOMBRE,' ',d.PRIMER_APELLIDO) as doctor_nombre"),
                    'p.COD_PERSONA as paciente_persona_id',
                    DB::raw("CONCAT(p.PRIMER_NOMBRE,' ',p.PRIMER_APELLIDO) as paciente_nombre"),
                    'e.NOM_ESTADO as estado_nombre',
                ])
                ->orderBy('c.FEC_CITA')
                ->orderBy('c.HOR_CITA');

            // Filtro por rol
            if ($rolSlug === 'doctor' && $user?->FK_COD_PERSONA) {
                $q->where('c.FK_COD_DOCTOR', $user->FK_COD_PERSONA);
            } elseif ($rolSlug === 'paciente' && $user?->FK_COD_PERSONA) {
                $q->where('c.FK_COD_PACIENTE', $user->FK_COD_PERSONA);
            }

            $citas = collect($q->get());

            if ($citas->isEmpty()) {
                return [];
            }

            return $this->buildDoctorsFromCitas($citas);
        } catch (\Throwable $e) {
            // Si algo truena (producción, error de conexión, etc.) devolvemos vacío
            return [];
        }
    }

    private function buildDoctorsFromCitas($citas): array
    {
        // Agrupamos todas las citas por doctor
        $byDoctor   = $citas->groupBy('doctor_persona_id');
        $doctorIds  = $byDoctor->keys()->all();

        // Buscamos usuarios (login) de esos doctores
        $doctorUsers = DB::table('tbl_usuario')
            ->whereIn('FK_COD_PERSONA', $doctorIds)
            ->pluck('USR_USUARIO', 'FK_COD_PERSONA');

        $colors       = ['#0d6efd', '#20c997', '#6610f2', '#fd7e14'];
        $doctorPanels = [];

        foreach ($byDoctor as $doctorPersonaId => $rows) {
            $rows  = $rows->sortBy(['FEC_CITA', 'HOR_CITA']);
            $first = $rows->first();

            $color    = $colors[count($doctorPanels) % count($colors)];
            $codigo   = 'DOC-' . str_pad($doctorPersonaId, 4, '0', STR_PAD_LEFT);
            $usuario  = $doctorUsers[$doctorPersonaId] ?? 'N/D';
            $contacto = "Usuario: {$usuario}";

            // -------- helper para normalizar el estado --------
            $normalizeEstado = function ($nombreEstado = null) {
                if (!$nombreEstado) {
                    return 'Pendiente';
                }
                $nombreEstado = ucfirst(strtolower(trim($nombreEstado)));
                // Por si viniera "En_curso" o "NO_SHOW" en el futuro
                $nombreEstado = str_replace('_', ' ', $nombreEstado);
                return $nombreEstado;
            };

            // Resumen por paciente (para la tabla principal)
            $pacientes = $rows
                ->groupBy('paciente_persona_id')
                ->map(function ($pRows) use ($normalizeEstado) {
                    $pRows = $pRows->sortBy(['FEC_CITA', 'HOR_CITA']);
                    $first = $pRows->first();

                    return [
                        'id'      => $first->COD_CITA,  // <-- ID REAL DE LA CITA
                        'codigo'  => 'PAC-' . str_pad($first->paciente_persona_id, 4, '0', STR_PAD_LEFT),
                        'nombre'  => $first->paciente_nombre,
                        'motivo'  => $first->MOT_CITA,
                        'estado'  => $normalizeEstado($first->estado_nombre),
                        'fecha'   => $first->FEC_CITA,
                        'hora'    => $first->HOR_CITA,
                        'nota'    => $first->OBSERVACIONES,
                    ];
                })
                ->values()
                ->all();

            // Agenda completa del doctor (para el calendario)
            $agenda = $rows->map(function ($row) use ($normalizeEstado) {
                return [
                    'id'        => $row->COD_CITA,  // <-- ID REAL DE LA CITA
                    'fecha'     => $row->FEC_CITA,
                    'hora'      => $row->HOR_CITA,
                    'paciente'  => $row->paciente_nombre,
                    'estado'    => $normalizeEstado($row->estado_nombre),
                    'motivo'    => $row->MOT_CITA,
                    'duracion'  => null, // luego podemos calcular con HOR_FIN si quieres
                    'ubicacion' => 'Consultorio',
                ];
            })->values()->all();

            $doctorPanels[] = [
                'codigo'       => $codigo,
                'nombre'       => $first->doctor_nombre,
                'especialidad' => 'Odontología',
                'color'        => $color,
                'contacto'     => $contacto,
                'pacientes'    => $pacientes,
                'agenda'       => $agenda,
            ];
        }

        return $doctorPanels;
    }

    /* =========================================================
     *  PACIENTES SIN DOCTOR (widget)
     * =======================================================*/
    private function availablePatientsFromDb(): array
{
    try {
        if (
            !DB::getSchemaBuilder()->hasTable('tbl_usuario') ||
            !DB::getSchemaBuilder()->hasTable('tbl_rol') ||
            !DB::getSchemaBuilder()->hasTable('tbl_doctor_paciente')
        ) {
            return [];
        }

        $rows = DB::table('tbl_usuario as u')
            ->join('tbl_rol as r', 'u.FK_COD_ROL', '=', 'r.COD_ROL')
            ->join('tbl_persona as p', 'u.FK_COD_PERSONA', '=', 'p.COD_PERSONA')
            ->leftJoin('tbl_doctor_paciente as dp', function ($join) {
                $join->on('dp.FK_COD_PACIENTE', '=', 'p.COD_PERSONA')
                     ->where('dp.ACTIVO', '=', 1);
            })
            ->where('r.NOM_ROL', 'PACIENTE')          // solo usuarios con rol PACIENTE
            ->whereNull('dp.COD_DP')                  // que no estén asignados a ningún doctor
            ->orderBy('p.PRIMER_NOMBRE')
            ->orderBy('p.PRIMER_APELLIDO')
            ->limit(20)
            ->select([
                'p.COD_PERSONA',
                'p.PRIMER_NOMBRE',
                'p.PRIMER_APELLIDO',
            ])
            ->get();

        if ($rows->isEmpty()) {
            return [];
        }

        return $rows->map(function ($row) {
            return [
                'persona_id' => (int) $row->COD_PERSONA,                               // << IMPORTANTE
                'nombre'     => $row->PRIMER_NOMBRE . ' ' . $row->PRIMER_APELLIDO,
                'motivo'     => 'Pendiente de asignar doctor',
                'preferencia'=> null,
                'ultima'     => null,
            ];
        })->all();
    } catch (\Throwable $e) {
        return [];
    }
}


    /* =========================================================
     *  DATOS DEMO (por si BD no está lista)
     * =======================================================*/

    // Versión original "fake" de doctores y agenda
    private function demoDoctorsStatic(): array
    {
        return [
            [
                'codigo'       => 'DOC-102',
                'nombre'       => 'Dr. Juan López',
                'especialidad' => 'Odontología Restaurativa',
                'color'        => '#0d6efd',
                'contacto'     => 'Ext. 203 · juan.lopez@clinica.test',
                'pacientes'    => [
                    [
                        'codigo'  => 'PAC-2045',
                        'nombre'  => 'Ana Rivera',
                        'motivo'  => 'Control semestral',
                        'estado'  => 'Confirmada',
                        'fecha'   => '2025-11-12',
                        'hora'    => '08:30',
                        'nota'    => 'Traer radiografías previas.',
                    ],
                    [
                        'codigo'  => 'PAC-2051',
                        'nombre'  => 'Carlos Pérez',
                        'motivo'  => 'Rehabilitación molar',
                        'estado'  => 'Pendiente',
                        'fecha'   => '2025-11-12',
                        'hora'    => '10:15',
                        'nota'    => 'Confirmar disponibilidad de laboratorio.',
                    ],
                    [
                        'codigo'  => 'PAC-2060',
                        'nombre'  => 'María Gómez',
                        'motivo'  => 'Blanqueamiento',
                        'estado'  => 'Cancelada',
                        'fecha'   => '2025-11-13',
                        'hora'    => '09:45',
                        'nota'    => 'Reagendar por viaje.',
                    ],
                ],
                'agenda' => [
                    [
                        'fecha'     => '2025-11-12',
                        'hora'      => '08:30',
                        'paciente'  => 'Ana Rivera',
                        'estado'    => 'Confirmada',
                        'motivo'    => 'Control semestral',
                        'duracion'  => '45 min',
                        'ubicacion' => 'Consultorio 2',
                    ],
                    [
                        'fecha'     => '2025-11-12',
                        'hora'      => '10:15',
                        'paciente'  => 'Carlos Pérez',
                        'estado'    => 'Pendiente',
                        'motivo'    => 'Rehabilitación molar',
                        'duracion'  => '60 min',
                        'ubicacion' => 'Consultorio 1',
                    ],
                    [
                        'fecha'     => '2025-11-13',
                        'hora'      => '09:45',
                        'paciente'  => 'María Gómez',
                        'estado'    => 'Cancelada',
                        'motivo'    => 'Blanqueamiento',
                        'duracion'  => '40 min',
                        'ubicacion' => 'Consultorio 3',
                    ],
                    [
                        'fecha'     => '2025-11-14',
                        'hora'      => '11:30',
                        'paciente'  => 'Pedro Izaguirre',
                        'estado'    => 'Confirmada',
                        'motivo'    => 'Implante',
                        'duracion'  => '90 min',
                        'ubicacion' => 'Quirófano 1',
                    ],
                ],
            ],
            [
                'codigo'       => 'DOC-118',
                'nombre'       => 'Dra. Laura Molina',
                'especialidad' => 'Ortodoncia y Estética',
                'color'        => '#20c997',
                'contacto'     => 'Ext. 214 · laura.molina@clinica.test',
                'pacientes'    => [
                    [
                        'codigo' => 'PAC-2070',
                        'nombre' => 'Sofía Aguilar',
                        'motivo' => 'Ajuste de brackets',
                        'estado' => 'Confirmada',
                        'fecha'  => '2025-11-12',
                        'hora'   => '11:15',
                        'nota'   => 'Revisar elasticos transparentes.',
                    ],
                    [
                        'codigo' => 'PAC-2091',
                        'nombre' => 'Diego López',
                        'motivo' => 'Valoración estética',
                        'estado' => 'Pendiente',
                        'fecha'  => '2025-11-13',
                        'hora'   => '13:00',
                        'nota'   => 'Enviar presupuesto digital.',
                    ],
                ],
                'agenda' => [
                    [
                        'fecha'     => '2025-11-12',
                        'hora'      => '11:15',
                        'paciente'  => 'Sofía Aguilar',
                        'estado'    => 'Confirmada',
                        'motivo'    => 'Ajuste de brackets',
                        'duracion'  => '30 min',
                        'ubicacion' => 'Consultorio 4',
                    ],
                    [
                        'fecha'     => '2025-11-13',
                        'hora'      => '13:00',
                        'paciente'  => 'Diego López',
                        'estado'    => 'Pendiente',
                        'motivo'    => 'Valoración estética',
                        'duracion'  => '60 min',
                        'ubicacion' => 'Consultorio 4',
                    ],
                    [
                        'fecha'     => '2025-11-15',
                        'hora'      => '09:30',
                        'paciente'  => 'Claudia Soto',
                        'estado'    => 'Confirmada',
                        'motivo'    => 'Colocación de retenedores',
                        'duracion'  => '50 min',
                        'ubicacion' => 'Consultorio 5',
                    ],
                ],
            ],
        ];
    }

    /**
     * Versión simplificada: toma el primer paciente del doctor activo
     * (para ADMIN / RECEPCIÓN / DOCTOR cuando no estamos viendo un paciente específico).
     */
    private function patientRecordFromPanels(array $activeDoctor): array
    {
        $pacientes = $activeDoctor['pacientes'] ?? [];

        if (empty($pacientes)) {
            return [
                'profile' => [
                    'codigo'       => null,
                    'nombre'       => 'Sin pacientes',
                    'doctor'       => $activeDoctor['nombre'] ?? null,
                    'especialidad' => $activeDoctor['especialidad'] ?? 'Odontología',
                    'estado'       => 'Sin citas',
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

        $proxima = $pacientes[0];

        return [
            'profile' => [
                'codigo'       => $proxima['codigo'] ?? null,
                'nombre'       => $proxima['nombre'] ?? null,
                'doctor'       => $activeDoctor['nombre'] ?? null,
                'especialidad' => $activeDoctor['especialidad'] ?? 'Odontología',
                'estado'       => 'Activo',
                'correo'       => 'ana.rivera@correo.test', // luego lo sacamos de BD si quieres
                'telefono'     => '+504 9999-8888',
                'proxima'      => [
                    'fecha'  => $proxima['fecha']  ?? null,
                    'hora'   => $proxima['hora']   ?? null,
                    'motivo' => $proxima['motivo'] ?? null,
                    'estado' => $proxima['estado'] ?? null,
                ],
            ],
            'historial' => $this->patientHistory(),
        ];
    }

    /**
 * Asigna un paciente (persona_id) al doctor logueado en tbl_doctor_paciente.
 */
public function asignarPaciente(Request $request, int $pacientePersonaId)
{
    $user = auth()->user();

    if (!$user || !$user->FK_COD_PERSONA) {
        return back()->with('error', 'No se pudo identificar al doctor actual.');
    }

    $doctorPersonaId = (int) $user->FK_COD_PERSONA;

    try {
        // Gracias al UNIQUE (FK_COD_DOCTOR, FK_COD_PACIENTE) esto evita duplicados
        DB::table('tbl_doctor_paciente')->updateOrInsert(
            [
                'FK_COD_DOCTOR'   => $doctorPersonaId,
                'FK_COD_PACIENTE' => $pacientePersonaId,
            ],
            [
                'ACTIVO'         => 1,
                'FEC_ASIGNACION' => now(),   // usa la hora del server
            ]
        );

        return back()->with('success', 'Paciente asignado correctamente a tu panel.');
    } catch (\Throwable $e) {
        return back()->with('error', 'No se pudo asignar el paciente.');
    }
}



    private function patientHistory(): array
    {
        return [
            [
                'fecha'   => '2025-09-20',
                'estado'  => 'Completada',
                'motivo'  => 'Limpieza',
                'doctor'  => 'Dr. Juan López',
                'detalle' => 'Sin hallazgos relevantes.',
            ],
            [
                'fecha'   => '2025-07-10',
                'estado'  => 'Completada',
                'motivo'  => 'Control de caries',
                'doctor'  => 'Dr. Juan López',
                'detalle' => 'Aplicación de barniz.',
            ],
            [
                'fecha'   => '2025-05-03',
                'estado'  => 'Cancelada',
                'motivo'  => 'Ausencia del paciente',
                'doctor'  => 'Dr. Juan López',
                'detalle' => 'Se reprogramó para junio.',
            ],
        ];
    }

    private function patientTimeline(): array
    {
        return [
            ['fecha' => '2025-11-10', 'descripcion' => 'Se envió recordatorio vía correo.', 'estado' => 'Notificado'],
            ['fecha' => '2025-11-08', 'descripcion' => 'Paciente confirmó asistencia.', 'estado' => 'Confirmado'],
            ['fecha' => '2025-11-05', 'descripcion' => 'Recepción cargó nueva radiografía.', 'estado' => 'Documentado'],
        ];
    }

    private function calendarMatrix(): array
    {
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
        // Partimos de las agendas de cada doctor
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

        // Filtros por rol
        if ($rolSlug === 'doctor') {
            $events = $events->where('doctor', $activeDoctor['nombre'] ?? null);
        }

        if ($rolSlug === 'paciente') {
            $paciente = $patientRecord['profile']['nombre'] ?? null;
            $events   = $events->where('paciente', $paciente);
        }

        $events = $events->values();

        return [
            'list'  => $events->all(),
            'byDate'=> $events->groupBy('fecha')->map(fn ($g) => $g->values()->all())->all(),
        ];
    }

    private function buildStats(
        string $rolSlug,
        array $doctorPanels,
        array $availablePatients,
        array $patientRecord,
        array $eventList
    ): array {
        $eventsCollection = collect($eventList);
        $totalCitas       = $eventsCollection->count();
        $pendientes       = $eventsCollection->where('estado', 'Pendiente')->count();
        $confirmadas      = $eventsCollection->where('estado', 'Confirmada')->count();
        $canceladas       = $eventsCollection->where('estado', 'Cancelada')->count();
        $pacientesActivos = collect($doctorPanels)->sum(fn ($doc) => count($doc['pacientes'] ?? []));

        return match ($rolSlug) {
            'doctor' => [
                ['label' => 'Pacientes activos', 'value' => $pacientesActivos, 'icon' => 'fas fa-user-friends', 'color' => 'primary'],
                ['label' => 'Pendientes por confirmar', 'value' => $pendientes, 'icon' => 'fas fa-hourglass-half', 'color' => 'warning'],
                ['label' => 'Citas confirmadas', 'value' => $confirmadas, 'icon' => 'fas fa-check-circle', 'color' => 'success'],
            ],
            'paciente' => [
                [
                    'label' => 'Próxima cita',
                    'value' => ($patientRecord['profile']['proxima']['fecha'] ?? '') . ' · ' .
                               ($patientRecord['profile']['proxima']['hora'] ?? ''),
                    'icon'  => 'fas fa-calendar-day',
                    'color' => 'info',
                ],
                [
                    'label' => 'Estado',
                    'value' => $patientRecord['profile']['proxima']['estado'] ?? 'Pendiente',
                    'icon'  => 'fas fa-heartbeat',
                    'color' => 'success',
                ],
                [
                    'label' => 'Historial total',
                    'value' => count($patientRecord['historial'] ?? []),
                    'icon'  => 'fas fa-history',
                    'color' => 'secondary',
                ],
            ],
            default => [
                ['label' => 'Citas programadas',   'value' => $totalCitas,                     'icon' => 'fas fa-calendar-check',     'color' => 'primary'],
                ['label' => 'Pendientes',          'value' => $pendientes,                     'icon' => 'fas fa-exclamation-circle', 'color' => 'warning'],
                ['label' => 'Canceladas',          'value' => $canceladas,                     'icon' => 'fas fa-times-circle',       'color' => 'danger'],
                ['label' => 'Pacientes sin doctor','value' => count($availablePatients ?? []), 'icon' => 'fas fa-user-clock',         'color' => 'info'],
            ],
        };
    }

    /**
     * Trae las citas reales desde la BD, filtrando según el rol.
     */
    private function fetchCitasFromDatabase(string $rolSlug, int $personaId)
    {
        $query = DB::table('tbl_cita as c')
            ->join('tbl_persona as d', 'd.COD_PERSONA', '=', 'c.FK_COD_DOCTOR')
            ->join('tbl_persona as p', 'p.COD_PERSONA', '=', 'c.FK_COD_PACIENTE')
            ->leftJoin('tbl_estado_cita as e', 'e.COD_ESTADO', '=', 'c.ESTADO_CITA')
            ->select(
                'c.COD_CITA',
                'c.FK_COD_DOCTOR   as doctor_persona_id',
                'c.FK_COD_PACIENTE as paciente_persona_id',
                'c.FEC_CITA',
                'c.HOR_CITA',
                'c.HOR_FIN',
                'c.MOT_CITA',
                'c.OBSERVACIONES',
                'c.ESTADO_CITA',
                'e.NOM_ESTADO      as estado_nombre',
                DB::raw("CONCAT(d.PRIMER_NOMBRE, ' ', d.PRIMER_APELLIDO) as doctor_nombre"),
                DB::raw("CONCAT(p.PRIMER_NOMBRE, ' ', p.PRIMER_APELLIDO) as paciente_nombre")
            );

        // Filtros por rol
        if ($rolSlug === 'doctor' && $personaId > 0) {
            $query->where('c.FK_COD_DOCTOR', $personaId);
        } elseif ($rolSlug === 'paciente' && $personaId > 0) {
            $query->where('c.FK_COD_PACIENTE', $personaId);
        }
        // ADMIN y RECEPCIONISTA ven todas las citas

        return $query
            ->orderBy('c.FEC_CITA')
            ->orderBy('c.HOR_CITA')
            ->get();
    }

    /**
     * Convierte la colección de citas de BD en el formato que usa la vista:
     *  - doctorPanels[ ] con:
     *      - nombre del doctor
     *      - lista de pacientes/citas
     */
    private function buildDoctorPanelsFromCitas($citas): array
    {
        if ($citas->isEmpty()) {
            return [];
        }

        return $citas
            ->groupBy('doctor_persona_id')
            ->map(function ($rows) {
                $first = $rows->first();

                return [
                    'doctor_persona_id' => $first->doctor_persona_id,
                    'nombre'            => $first->doctor_nombre ?? 'Doctor sin nombre',
                    // De momento usamos texto genérico; luego podemos sacarlo de otra tabla
                    'especialidad'      => 'Odontología',
                    'contacto'          => '',

                    'pacientes' => $rows->map(function ($row) {
                        $estadoNombre = strtoupper($row->estado_nombre ?? '');

                        return [
                            'cita_id' => $row->COD_CITA,
                            'nombre'  => $row->paciente_nombre,
                            'motivo'  => $row->MOT_CITA,
                            'fecha'   => $row->FEC_CITA,
                            'hora'    => substr($row->HOR_CITA, 0, 5),
                            'estado'  => $estadoNombre,          // PENDIENTE / CONFIRMADA / CANCELADA ...
                            'nota'    => $row->OBSERVACIONES,
                        ];
                    })->all(),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Construye la ficha + historial del paciente basado en sus citas reales.
     */
    private function buildPatientRecordFromDb(int $pacientePersonaId, $citas): array
    {
        // Nos quedamos solo con las citas de ese paciente (por si la colección viene global)
        $misCitas = collect($citas)->where('paciente_persona_id', $pacientePersonaId);

        if ($misCitas->isEmpty()) {
            // Si no tiene citas aún, devolvemos algo básico
            return [
                'profile' => [
                    'codigo'       => 'PAC-' . str_pad($pacientePersonaId, 4, '0', STR_PAD_LEFT),
                    'nombre'       => 'Paciente',
                    'doctor'       => 'Sin asignar',
                    'especialidad' => 'Odontología',
                    'estado'       => 'Sin citas',
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

        // Próxima cita = la más cercana en fecha/hora
        $proxima = $misCitas
            ->sortBy(['FEC_CITA', 'HOR_CITA'])
            ->first();

        $normalizeEstado = function ($nombreEstado = null) {
            if (!$nombreEstado) {
                return 'Pendiente';
            }
            $nombreEstado = ucfirst(strtolower(trim($nombreEstado)));
            return str_replace('_', ' ', $nombreEstado);
        };

        $profile = [
            'codigo'       => 'PAC-' . str_pad($pacientePersonaId, 4, '0', STR_PAD_LEFT),
            'nombre'       => $proxima->paciente_nombre,
            'doctor'       => $proxima->doctor_nombre,
            'especialidad' => 'Odontología',
            'estado'       => 'Activo',
            'correo'       => null,
            'telefono'     => null,
            'proxima'      => [
                'fecha'  => $proxima->FEC_CITA,
                'hora'   => $proxima->HOR_CITA,
                'motivo' => $proxima->MOT_CITA,
                'estado' => $normalizeEstado($proxima->estado_nombre),
            ],
        ];

        // Historial = todas las citas del paciente
        $historial = $misCitas
            ->sortByDesc('FEC_CITA')
            ->map(function ($cita) use ($normalizeEstado) {
                return [
                    'fecha'   => $cita->FEC_CITA,
                    'estado'  => $normalizeEstado($cita->estado_nombre),
                    'motivo'  => $cita->MOT_CITA,
                    'doctor'  => $cita->doctor_nombre,
                    'detalle' => $cita->OBSERVACIONES,
                ];
            })
            ->values()
            ->all();

        return [
            'profile'   => $profile,
            'historial' => $historial,
        ];
    }

    /**
     * Marca una cita como CONFIRMADA.
     */
    public function confirmar(Request $request, int $id)
    {
        $ok = $this->updateEstadoCita($id, 'CONFIRMADA');

        if ($request->expectsJson()) {
            return response()->json(['ok' => $ok]);
        }

        return back()->with(
            $ok ? 'success' : 'error',
            $ok ? 'La cita se confirmó correctamente.' : 'No se pudo confirmar la cita.'
        );
    }

    /**
     * Marca una cita como CANCELADA.
     */
    public function cancelar(Request $request, int $id)
    {
        $ok = $this->updateEstadoCita($id, 'CANCELADA');

        if ($request->expectsJson()) {
            return response()->json(['ok' => $ok]);
        }

        return back()->with(
            $ok ? 'success' : 'error',
            $ok ? 'La cita se canceló correctamente.' : 'No se pudo cancelar la cita.'
        );
    }

    /**
     * Reprograma una cita (cambia fecha/hora y la deja como PENDIENTE).
     * Campos esperados:
     *  - fecha       (YYYY-MM-DD)
     *  - hora_inicio (HH:MM)
     *  - hora_fin    (HH:MM) [opcional]
     */
    public function reprogramar(Request $request, int $id)
    {
        $request->validate([
            'fecha'       => ['required', 'date'],
            'hora_inicio' => ['required'],
            'hora_fin'    => ['nullable'],
        ]);

        $ok = $this->updateEstadoCita(
            $id,
            'PENDIENTE',
            $request->input('fecha'),
            $request->input('hora_inicio'),
            $request->input('hora_fin')
        );

        if ($request->expectsJson()) {
            return response()->json(['ok' => $ok]);
        }

        return back()->with(
            $ok ? 'success' : 'error',
            $ok ? 'La cita se reprogramó correctamente.' : 'No se pudo reprogramar la cita.'
        );
    }

    /**
     * Devuelve el COD_ESTADO dado el nombre (PENDIENTE, CONFIRMADA, etc.)
     */
    private function findEstadoId(string $nombre): ?int
    {
        $row = DB::table('tbl_estado_cita')
            ->whereRaw('UPPER(TRIM(NOM_ESTADO)) = ?', [strtoupper(trim($nombre))])
            ->first();

        return $row ? (int)$row->COD_ESTADO : null;
    }

    /**
     * Aplica cambio de estado (y opcionalmente fecha/hora) a una cita.
     */
    private function updateEstadoCita(
        int $codCita,
        string $estadoNombre,
        ?string $nuevaFecha = null,
        ?string $nuevaHoraInicio = null,
        ?string $nuevaHoraFin = null
    ): bool {
        $estadoId = $this->findEstadoId($estadoNombre);

        if (!$estadoId) {
            return false;
        }

        $data = [
            'ESTADO_CITA' => $estadoId,
            'USUARIO_MOD' => auth()->id(),
        ];

        if ($nuevaFecha !== null) {
            $data['FEC_CITA'] = $nuevaFecha;
        }

        if ($nuevaHoraInicio !== null) {
            $data['HOR_CITA'] = $nuevaHoraInicio;
        }

        if ($nuevaHoraFin !== null) {
            $data['HOR_FIN'] = $nuevaHoraFin;
        }

        return DB::table('tbl_cita')
            ->where('COD_CITA', $codCita)
            ->update($data) > 0;
    }
}
