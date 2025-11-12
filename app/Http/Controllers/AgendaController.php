<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class AgendaController extends Controller
{
    public function citas(Request $request)
    {
        return $this->render('citas');
    }

    public function calendario(Request $request)
    {
        return $this->render('calendario');
    }

    public function reportes(Request $request)
    {
        return $this->render('reportes');
    }

    private function render(string $section)
    {
        $role = strtoupper(optional(auth()->user()?->rol)->NOM_ROL ?? 'ADMIN');

        [$view, $data] = match ($role) {
            'ADMIN'         => $this->forAdmin($section),
            'RECEPCIONISTA' => $this->forRecepcionista($section),
            'DOCTOR'        => $this->forDoctor($section),
            'PACIENTE'      => $this->forPaciente($section),
            default         => $this->forAdmin($section),
        };

        return view($view, array_merge($data, [
            'pageSection' => $section,
            'role'        => $role,
        ]));
    }

    private function forAdmin(string $section): array
    {
        $appointments = $this->sampleAppointments();

        return match ($section) {
            'citas' => [
                'modulo-citas.admin.ver-citas',
                [
                    'pageTitle'    => 'Citas · Administración',
                    'heading'      => 'Ver citas',
                    'subheading'   => 'Gestiona todas las citas programadas y pendientes de la clínica.',
                    'metrics'      => $this->summarizeAppointments($appointments),
                    'filters'      => $this->buildFilters($appointments),
                    'appointments' => $appointments,
                ],
            ],
            'calendario' => [
                'modulo-citas.admin.agenda',
                [
                    'pageTitle'  => 'Agenda general',
                    'heading'    => 'Agenda general',
                    'subheading' => 'Visualiza todas las citas en un solo calendario y gestiona cambios rápidos.',
                    'events'     => $this->mapEvents($appointments),
                    'legend'     => $this->legend(),
                ],
            ],
            'reportes' => [
                'modulo-citas.admin.reportes',
                [
                    'pageTitle'  => 'Reportes de citas',
                    'heading'    => 'Reportes y métricas',
                    'subheading' => 'Un vistazo rápido al desempeño de la agenda.',
                    'metrics'    => $this->summarizeAppointments($appointments),
                    'topDoctors' => $this->topDoctors($appointments),
                    'lastUpdates'=> $this->recentActivity(),
                ],
            ],
            default => $this->forAdmin('citas'),
        };
    }

    private function forRecepcionista(string $section): array
    {
        $appointments = $this->sampleAppointments();

        return match ($section) {
            'citas' => [
                'modulo-citas.recepcionista.ver-citas',
                [
                    'pageTitle'    => 'Citas · Recepción',
                    'heading'      => 'Ver citas',
                    'subheading'   => 'Confirma, reagenda o coordina citas para todos los doctores.',
                    'appointments' => $appointments,
                    'filters'      => $this->buildFilters($appointments),
                ],
            ],
            'calendario' => [
                'modulo-citas.recepcionista.agenda',
                [
                    'pageTitle'  => 'Agenda de recepción',
                    'heading'    => 'Agenda',
                    'subheading' => 'Consulta y actualiza la disponibilidad del equipo médico.',
                    'events'     => $this->mapEvents($appointments),
                    'legend'     => $this->legend(),
                ],
            ],
            'reportes' => $this->forRecepcionista('citas'),
            default    => $this->forRecepcionista('citas'),
        };
    }

    private function forDoctor(string $section): array
    {
        $appointments = $this->filterAppointmentsForRole($this->sampleAppointments(), 'DOCTOR');
        $patients     = $this->doctorPatients();

        return match ($section) {
            'citas' => [
                'modulo-citas.doctor.mis-pacientes',
                [
                    'pageTitle'        => 'Mis pacientes',
                    'heading'          => 'Mis pacientes',
                    'subheading'       => 'Administra a tus pacientes asignados y agrega nuevos desde enlaces compartidos.',
                    'assignedPatients' => $patients['assigned'],
                    'availablePatients'=> $patients['available'],
                    'invitations'      => $this->doctorInvitations(),
                ],
            ],
            'calendario' => [
                'modulo-citas.doctor.agenda',
                [
                    'pageTitle'  => 'Agenda del doctor',
                    'heading'    => 'Agenda',
                    'subheading' => 'Organiza tus citas confirmadas y pendientes.',
                    'events'     => $this->mapEvents($appointments),
                    'legend'     => $this->legend(),
                    'upcoming'   => $appointments,
                ],
            ],
            'reportes' => $this->forDoctor('citas'),
            default    => $this->forDoctor('citas'),
        };
    }

    private function forPaciente(string $section): array
    {
        $appointments = $this->filterAppointmentsForRole($this->sampleAppointments(), 'PACIENTE');

        return match ($section) {
            'citas' => [
                'modulo-citas.paciente.mis-citas',
                [
                    'pageTitle'      => 'Mis citas',
                    'heading'        => 'Mis citas',
                    'subheading'     => 'Consulta tu calendario y confirma los próximos controles.',
                    'events'         => $this->mapEvents($appointments),
                    'legend'         => $this->legend(),
                    'doctorProfile'  => $this->patientDoctorProfile(),
                    'nextAppointment'=> Arr::first($appointments),
                ],
            ],
            'calendario' => $this->forPaciente('citas'),
            'reportes' => [
                'modulo-citas.paciente.historial',
                [
                    'pageTitle'  => 'Historial de citas',
                    'heading'    => 'Historial de citas',
                    'subheading' => 'Revisa las citas atendidas, canceladas o reprogramadas.',
                    'history'    => $this->patientHistory($appointments),
                ],
            ],
            default => $this->forPaciente('citas'),
        };
    }

    private function sampleAppointments(): array
    {
        return [
            [
                'id'            => 401,
                'fecha'         => '2025-11-12',
                'hora'          => '08:30',
                'duracion_min'  => 45,
                'paciente'      => 'Ana Rivera',
                'paciente_id'   => 1001,
                'doctor'        => 'Dr. Gabriel López',
                'doctor_id'     => 201,
                'estado'        => 'Confirmada',
                'motivo'        => 'Profilaxis semestral',
                'ubicacion'     => 'Consultorio 2',
                'nota'          => 'Traer radiografía panorámica',
                'canal'         => 'Presencial',
            ],
            [
                'id'            => 402,
                'fecha'         => '2025-11-12',
                'hora'          => '09:30',
                'duracion_min'  => 30,
                'paciente'      => 'Carlos Pérez',
                'paciente_id'   => 1002,
                'doctor'        => 'Dra. Mariana Molina',
                'doctor_id'     => 202,
                'estado'        => 'Pendiente',
                'motivo'        => 'Evaluación ortodoncia',
                'ubicacion'     => 'Consultorio 5',
                'nota'          => 'Confirmar disponibilidad del laboratorio',
                'canal'         => 'Presencial',
            ],
            [
                'id'            => 403,
                'fecha'         => '2025-11-13',
                'hora'          => '11:00',
                'duracion_min'  => 45,
                'paciente'      => 'Lucía Fernández',
                'paciente_id'   => 1003,
                'doctor'        => 'Dr. Gabriel López',
                'doctor_id'     => 201,
                'estado'        => 'Confirmada',
                'motivo'        => 'Tratamiento de caries',
                'ubicacion'     => 'Consultorio 2',
                'nota'          => 'Revisión previa a la limpieza',
                'canal'         => 'Presencial',
            ],
            [
                'id'            => 404,
                'fecha'         => '2025-11-14',
                'hora'          => '15:00',
                'duracion_min'  => 30,
                'paciente'      => 'Ana Rivera',
                'paciente_id'   => 1001,
                'doctor'        => 'Dr. Gabriel López',
                'doctor_id'     => 201,
                'estado'        => 'Pendiente',
                'motivo'        => 'Control de ortodoncia',
                'ubicacion'     => 'Consultorio 4',
                'nota'          => 'Ajustar brackets superiores',
                'canal'         => 'Presencial',
            ],
            [
                'id'            => 405,
                'fecha'         => '2025-10-02',
                'hora'          => '10:00',
                'duracion_min'  => 30,
                'paciente'      => 'Ana Rivera',
                'paciente_id'   => 1001,
                'doctor'        => 'Dr. Gabriel López',
                'doctor_id'     => 201,
                'estado'        => 'Completada',
                'motivo'        => 'Limpieza profunda',
                'ubicacion'     => 'Consultorio 1',
                'nota'          => 'Sin incidencias',
                'canal'         => 'Presencial',
            ],
            [
                'id'            => 406,
                'fecha'         => '2025-09-18',
                'hora'          => '09:00',
                'duracion_min'  => 45,
                'paciente'      => 'Carlos Pérez',
                'paciente_id'   => 1002,
                'doctor'        => 'Dra. Mariana Molina',
                'doctor_id'     => 202,
                'estado'        => 'Cancelada',
                'motivo'        => 'Extracción cordal',
                'ubicacion'     => 'Consultorio 3',
                'nota'          => 'Paciente reagendó para diciembre',
                'canal'         => 'Presencial',
            ],
        ];
    }

    private function summarizeAppointments(array $appointments): array
    {
        $collection = collect($appointments);
        $today      = Carbon::today();

        return [
            'total'       => $collection->count(),
            'confirmadas' => $collection->where('estado', 'Confirmada')->count(),
            'pendientes'  => $collection->where('estado', 'Pendiente')->count(),
            'canceladas'  => $collection->where('estado', 'Cancelada')->count(),
            'completadas' => $collection->whereIn('estado', ['Completada', 'Realizada'])->count(),
            'proximas'    => $collection->filter(fn ($cita) => Carbon::parse($cita['fecha'])->greaterThanOrEqualTo($today))->count(),
        ];
    }

    private function buildFilters(array $appointments): array
    {
        $collection = collect($appointments);

        return [
            'estados'  => $collection->pluck('estado')->unique()->values()->all(),
            'doctores' => $collection->pluck('doctor')->unique()->values()->all(),
        ];
    }

    private function legend(): array
    {
        return [
            ['label' => 'Confirmada', 'color' => '#16a34a'],
            ['label' => 'Pendiente',  'color' => '#f97316'],
            ['label' => 'Completada', 'color' => '#2563eb'],
            ['label' => 'Cancelada',  'color' => '#dc2626'],
        ];
    }

    private function mapEvents(array $appointments): array
    {
        return collect($appointments)
            ->map(function ($cita) {
                $start = Carbon::parse($cita['fecha'] . ' ' . $cita['hora']);
                $end   = $start->copy()->addMinutes($cita['duracion_min']);

                return [
                    'id'             => $cita['id'],
                    'title'          => $cita['paciente'] . ' · ' . $cita['motivo'],
                    'start'          => $start->toIso8601String(),
                    'end'            => $end->toIso8601String(),
                    'backgroundColor'=> $this->statusColor($cita['estado']),
                    'borderColor'    => $this->statusColor($cita['estado']),
                    'extendedProps'  => [
                        'paciente'  => $cita['paciente'],
                        'doctor'    => $cita['doctor'],
                        'estado'    => $cita['estado'],
                        'motivo'    => $cita['motivo'],
                        'ubicacion' => $cita['ubicacion'],
                        'nota'      => $cita['nota'],
                        'hora'      => $cita['hora'],
                        'canal'     => $cita['canal'],
                    ],
                ];
            })
            ->values()
            ->all();
    }

    private function statusColor(string $estado): string
    {
        return match ($estado) {
            'Confirmada' => '#16a34a',
            'Pendiente'  => '#f97316',
            'Completada' => '#2563eb',
            'Realizada'  => '#2563eb',
            'Cancelada'  => '#dc2626',
            default      => '#6b7280',
        };
    }

    private function topDoctors(array $appointments): array
    {
        return collect($appointments)
            ->groupBy('doctor')
            ->map(function ($citas, $doctor) {
                return [
                    'doctor'      => $doctor,
                    'total'       => $citas->count(),
                    'confirmadas' => $citas->where('estado', 'Confirmada')->count(),
                    'pendientes'  => $citas->where('estado', 'Pendiente')->count(),
                    'completadas' => $citas->whereIn('estado', ['Completada', 'Realizada'])->count(),
                ];
            })
            ->sortByDesc('total')
            ->values()
            ->all();
    }

    private function recentActivity(): array
    {
        return [
            [
                'titulo' => 'Cita reprogramada',
                'detalle'=> 'Recepción movió la cita de Carlos Pérez al 15 de noviembre.',
                'fecha'  => '2025-11-10 16:30',
                'tipo'   => 'warning',
            ],
            [
                'titulo' => 'Nueva cita',
                'detalle'=> 'Ana Rivera solicitó control de ortodoncia para el 14 de noviembre.',
                'fecha'  => '2025-11-09 10:05',
                'tipo'   => 'success',
            ],
            [
                'titulo' => 'Cita cancelada',
                'detalle'=> 'Dra. Molina canceló extracción cordal por reagendamiento.',
                'fecha'  => '2025-11-07 08:40',
                'tipo'   => 'danger',
            ],
        ];
    }

    private function doctorPatients(): array
    {
        return [
            'assigned' => [
                [
                    'nombre'        => 'Ana Rivera',
                    'edad'          => 29,
                    'telefono'      => '+504 9988-1122',
                    'correo'        => 'ana.rivera@email.com',
                    'ultima_cita'   => '02/10/2025',
                    'proxima_cita'  => '12/11/2025',
                    'plan'          => 'Ortodoncia correctiva',
                    'estado'        => 'Activa',
                ],
                [
                    'nombre'        => 'Lucía Fernández',
                    'edad'          => 34,
                    'telefono'      => '+504 9977-1144',
                    'correo'        => 'lucia.fernandez@email.com',
                    'ultima_cita'   => '05/09/2025',
                    'proxima_cita'  => '13/11/2025',
                    'plan'          => 'Tratamiento de caries',
                    'estado'        => 'En tratamiento',
                ],
            ],
            'available' => [
                [
                    'nombre'  => 'Luis Torres',
                    'motivo'  => 'Valoración ortodoncia',
                    'desde'   => '09/11/2025',
                ],
                [
                    'nombre'  => 'Paola Medina',
                    'motivo'  => 'Dolor molar derecho',
                    'desde'   => '08/11/2025',
                ],
            ],
        ];
    }

    private function doctorInvitations(): array
    {
        return [
            'link'       => 'https://clinica.lopezmolinari.test/registro?doctor=DR-201',
            'codigo'     => 'CDLM-DR201',
            'descripcion'=> 'Comparte este enlace o código para que un paciente complete su registro y se asigne automáticamente a tu agenda.',
        ];
    }

    private function filterAppointmentsForRole(array $appointments, string $role): array
    {
        return match ($role) {
            'DOCTOR' => array_values(array_filter($appointments, fn ($cita) => $cita['doctor_id'] === 201)),
            'PACIENTE' => array_values(array_filter($appointments, fn ($cita) => $cita['paciente_id'] === 1001)),
            default => $appointments,
        };
    }

    private function patientDoctorProfile(): array
    {
        return [
            'nombre'       => 'Dr. Gabriel López',
            'especialidad' => 'Ortodoncia y estética dental',
            'correo'       => 'glopez@clinicadl.com',
            'telefono'     => '+504 2233-4455',
            'ubicacion'    => 'Consultorio 2 · Torre principal',
            'horario'      => 'Lunes a viernes · 8:00 a. m. – 4:00 p. m.',
        ];
    }

    private function patientHistory(array $appointments): array
    {
        $today = Carbon::today();

        return collect($appointments)
            ->filter(fn ($cita) => Carbon::parse($cita['fecha'])->lessThan($today) || in_array($cita['estado'], ['Completada', 'Cancelada']))
            ->map(function ($cita) {
                return [
                    'fecha'     => Carbon::parse($cita['fecha'])->format('d/m/Y'),
                    'hora'      => $cita['hora'],
                    'doctor'    => $cita['doctor'],
                    'motivo'    => $cita['motivo'],
                    'estado'    => $cita['estado'],
                    'resultado' => match ($cita['estado']) {
                        'Completada' => 'Atendida con éxito',
                        'Cancelada'  => 'Cancelada por el paciente',
                        default      => 'Pendiente de actualización',
                    },
                ];
            })
            ->values()
            ->all();
    }
}
