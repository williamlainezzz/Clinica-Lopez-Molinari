<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

        $labels = $this->sectionLabels();
        $labelSet = $labels[$rolSlug][$sectionKey] ?? $labels['admin'][$sectionKey];

        $doctorPanels      = $this->demoDoctors();
        $availablePatients = $this->availablePatients();
        $activeDoctor      = $doctorPanels[0];
        $patientRecord     = $this->patientRecord($activeDoctor);
        $timeline          = $this->patientTimeline();

        $calendarMatrix      = $this->calendarMatrix();
        $calendarEventBundle = $this->buildCalendarEvents($doctorPanels, $rolSlug, $activeDoctor, $patientRecord);
        $calendarEvents      = $calendarEventBundle['byDate'];
        $eventList           = $calendarEventBundle['list'];

        $stats     = $this->buildStats($rolSlug, $doctorPanels, $availablePatients, $patientRecord, $eventList);
        $shareLink = url('/registro/paciente?doctor=dr-lopez');
        $shareCode = 'DR-LOPEZ-2025';

        $view = $this->resolveView($rolSlug, $sectionKey);

        return view($view, [
            'pageTitle'        => $labelSet['pageTitle'],
            'heading'          => $labelSet['heading'],
            'intro'            => $labelSet['intro'],
            'routeName'        => $routeName,
            'sectionKey'       => $sectionKey,
            'rolSlug'          => $rolSlug,
            'doctorPanels'     => $doctorPanels,
            'availablePatients'=> $availablePatients,
            'activeDoctor'     => $activeDoctor,
            'patientRecord'    => $patientRecord,
            'timeline'         => $timeline,
            'calendarMatrix'   => $calendarMatrix,
            'calendarEvents'   => $calendarEvents,
            'eventList'        => $eventList,
            'stats'            => $stats,
            'shareLink'        => $shareLink,
            'shareCode'        => $shareCode,
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

    private function demoDoctors(): array
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

    private function availablePatients(): array
    {
        return [
            [
                'nombre'      => 'Luis Navas',
                'motivo'      => 'Primera valoración',
                'preferencia' => 'Mañana',
                'ultima'      => '2025-11-08',
            ],
            [
                'nombre'      => 'Gabriela Torres',
                'motivo'      => 'Dolor agudo',
                'preferencia' => 'Urgente',
                'ultima'      => '2025-11-11',
            ],
            [
                'nombre'      => 'Mario Perdomo',
                'motivo'      => 'Seguimiento ortodoncia',
                'preferencia' => 'Tarde',
                'ultima'      => '2025-11-07',
            ],
        ];
    }

    private function patientRecord(array $activeDoctor): array
    {
        $proxima = $activeDoctor['pacientes'][0];

        return [
            'profile' => [
                'codigo'       => $proxima['codigo'],
                'nombre'       => $proxima['nombre'],
                'doctor'       => $activeDoctor['nombre'],
                'especialidad' => $activeDoctor['especialidad'],
                'estado'       => 'Activo',
                'correo'       => 'ana.rivera@correo.test',
                'telefono'     => '+504 9999-8888',
                'proxima'      => [
                    'fecha'  => $proxima['fecha'],
                    'hora'   => $proxima['hora'],
                    'motivo' => $proxima['motivo'],
                    'estado' => $proxima['estado'],
                ],
            ],
            'historial' => $this->patientHistory(),
        ];
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
            $paciente = $patientRecord['profile']['nombre'];
            $events   = $events->where('paciente', $paciente);
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
                ['label' => 'Pacientes activos', 'value' => count($doctorPanels[0]['pacientes']), 'icon' => 'fas fa-user-friends', 'color' => 'primary'],
                ['label' => 'Pendientes por confirmar', 'value' => $pendientes, 'icon' => 'fas fa-hourglass-half', 'color' => 'warning'],
                ['label' => 'Citas confirmadas', 'value' => $confirmadas, 'icon' => 'fas fa-check-circle', 'color' => 'success'],
            ],
            'paciente' => [
                ['label' => 'Próxima cita', 'value' => $patientRecord['profile']['proxima']['fecha'] . ' · ' . $patientRecord['profile']['proxima']['hora'], 'icon' => 'fas fa-calendar-day', 'color' => 'info'],
                ['label' => 'Estado', 'value' => $patientRecord['profile']['proxima']['estado'], 'icon' => 'fas fa-heartbeat', 'color' => 'success'],
                ['label' => 'Historial total', 'value' => count($patientRecord['historial']), 'icon' => 'fas fa-history', 'color' => 'secondary'],
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
