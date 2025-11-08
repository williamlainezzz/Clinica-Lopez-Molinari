<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    public function citas(Request $request)      { return $this->render('Citas', $request); }
    public function calendario(Request $request)  { return $this->render('Calendario', $request); }
    public function reportes(Request $request)    { return $this->render('Reportes', $request); }

    private function render(string $section, Request $request)
    {
        $user = auth()->user();
        Carbon::setLocale('es');
        $rol  = strtoupper(optional($user?->rol)->NOM_ROL ?? '');   // ADMIN | DOCTOR | RECEPCIONISTA | PACIENTE

        $labels = [
            'ADMIN'         => 'Admin',
            'DOCTOR'        => 'Doctor',
            'RECEPCIONISTA' => 'Recepción',
            'PACIENTE'      => 'Paciente',
        ];
        $rolLabel = $labels[$rol] ?? 'Admin';

        $routeName = match (strtoupper($section)) {
            'CITAS'       => 'agenda.citas',
            'CALENDARIO'  => 'agenda.calendario',
            default       => 'agenda.reportes',
        };

        $sectionKey = match (strtoupper($section)) {
            'CITAS'       => 'citas',
            'CALENDARIO'  => 'calendario',
            default       => 'reportes',
        };
        $rolKey = strtolower($rol ?: 'admin');

        $bannerPartial  = "modulo-citas.{$sectionKey}.banner-{$rolKey}";
        $toolbarPartial = "modulo-citas.{$sectionKey}.toolbar-{$rolKey}";

        $filters = [
            'desde'  => $request->query('desde'),
            'hasta'  => $request->query('hasta'),
            'estado' => $request->query('estado'),
            'doctor' => $request->query('doctor'),
        ];

        $catalogoEstados = ['Confirmada','Pendiente','Cancelada'];
        $catalogoDoctores = ['Dr. Marcos López', 'Dra. Lilian Molina'];

        $events = collect([
            [
                'id'       => 1,
                'title'    => 'Limpieza dental · Ana Rivera',
                'start'    => '2025-11-12T08:30:00',
                'end'      => '2025-11-12T09:15:00',
                'doctor'   => 'Dr. Marcos López',
                'paciente' => 'Ana Rivera',
                'estado'   => 'Confirmada',
                'motivo'   => 'Limpieza semestral',
                'color'    => '#28a745',
            ],
            [
                'id'       => 2,
                'title'    => 'Control ortodoncia · Carlos Pérez',
                'start'    => '2025-11-12T09:30:00',
                'end'      => '2025-11-12T10:10:00',
                'doctor'   => 'Dra. Lilian Molina',
                'paciente' => 'Carlos Pérez',
                'estado'   => 'Pendiente',
                'motivo'   => 'Ajuste de brackets',
                'color'    => '#ffc107',
            ],
            [
                'id'       => 3,
                'title'    => 'Evaluación endodoncia · María Gómez',
                'start'    => '2025-11-12T10:30:00',
                'end'      => '2025-11-12T11:15:00',
                'doctor'   => 'Dr. Marcos López',
                'paciente' => 'María Gómez',
                'estado'   => 'Cancelada',
                'motivo'   => 'Paciente reagendó',
                'color'    => '#dc3545',
            ],
            [
                'id'       => 4,
                'title'    => 'Emergencia · José Alvarado',
                'start'    => '2025-11-13T08:00:00',
                'end'      => '2025-11-13T09:00:00',
                'doctor'   => 'Dr. Gustavo Paredes',
                'paciente' => 'José Alvarado',
                'estado'   => 'Confirmada',
                'motivo'   => 'Dolor intenso',
                'color'    => '#28a745',
            ],
            [
                'id'       => 5,
                'title'    => 'Seguimiento · Ana Rivera',
                'start'    => '2025-11-15T15:00:00',
                'end'      => '2025-11-15T15:45:00',
                'doctor'   => 'Dr. Marcos López',
                'paciente' => 'Ana Rivera',
                'estado'   => 'Pendiente',
                'motivo'   => 'Revisión general',
                'color'    => '#ffc107',
            ],
        ]);

        $isAdmin  = ($rol === 'ADMIN');
        $isDoc    = ($rol === 'DOCTOR');
        $isRecep  = ($rol === 'RECEPCIONISTA');
        $isPac    = ($rol === 'PACIENTE');

        $doctorName = $isDoc ? (optional($user?->persona)->nombre_completo ?: 'Dr. Marcos López') : null;
        if ($isDoc && !$events->contains(fn($event) => $event['doctor'] === $doctorName)) {
            $doctorName = 'Dr. Marcos López';
        }

        $patientName = $isPac ? (optional($user?->persona)->nombre_completo ?: 'Ana Rivera') : null;
        if ($isPac && !$events->contains(fn($event) => $event['paciente'] === $patientName)) {
            $patientName = 'Ana Rivera';
        }

        $filteredEvents = $events
            ->when($isDoc, fn($collection) => $collection->where('doctor', $doctorName))
            ->when($isPac, fn($collection) => $collection->where('paciente', $patientName))
            ->values();

        $rows = $filteredEvents->map(function ($event) {
            $start = Carbon::parse($event['start']);
            return [
                'fecha'    => $start->format('Y-m-d'),
                'hora'     => $start->format('H:i'),
                'paciente' => $event['paciente'],
                'doctor'   => $event['doctor'],
                'estado'   => $event['estado'],
                'motivo'   => $event['motivo'],
            ];
        });

        $rows = $rows->filter(function ($row) use ($filters) {
            if ($filters['estado'] && strcasecmp($row['estado'], $filters['estado']) !== 0) {
                return false;
            }
            if ($filters['doctor'] && strcasecmp($row['doctor'], $filters['doctor']) !== 0) {
                return false;
            }
            if ($filters['desde'] && $row['fecha'] < $filters['desde']) {
                return false;
            }
            if ($filters['hasta'] && $row['fecha'] > $filters['hasta']) {
                return false;
            }
            return true;
        })->values()->all();

        $showDoctorColumn = $isAdmin || $isRecep;
        $showActions      = $isRecep || $isDoc;
        $readOnly         = $isPac || $isAdmin;

        $pageTitle = "{$section} · {$rolLabel}";
        $heading   = "{$section} {$rolLabel}";

        $view = $sectionKey === 'calendario'
            ? 'modulo-citas.calendario.index'
            : 'modulo-citas.shared.lista';

        $viewData = [
            'pageTitle'        => $pageTitle,
            'heading'          => $heading,
            'routeName'        => $routeName,
            'filters'          => $filters,
            'bannerPartial'    => $bannerPartial,
            'toolbarPartial'   => $toolbarPartial,
            'rol'              => $rol,
            'showDoctorColumn' => $showDoctorColumn,
            'showActions'      => $showActions,
            'readOnly'         => $readOnly,
            'sectionKey'       => $sectionKey,
        ];

        if ($sectionKey === 'calendario') {
            $legend = [
                ['label' => 'Confirmada', 'variant' => 'success', 'color' => '#28a745'],
                ['label' => 'Pendiente',  'variant' => 'warning', 'color' => '#ffc107'],
                ['label' => 'Cancelada',  'variant' => 'danger',  'color' => '#dc3545'],
            ];

            $quickActions = [
                [
                    'label'       => 'Nueva cita',
                    'icon'        => 'fas fa-plus-circle',
                    'class'       => 'btn btn-primary btn-block',
                    'description' => 'Agendar una cita en el espacio seleccionado.',
                    'disabled'    => !($isRecep || $isDoc),
                ],
                [
                    'label'       => 'Reprogramar',
                    'icon'        => 'fas fa-sync',
                    'class'       => 'btn btn-warning btn-block',
                    'description' => 'Mover una cita a otro horario disponible.',
                    'disabled'    => !($isRecep || $isDoc),
                ],
                [
                    'label'       => 'Cancelar cita',
                    'icon'        => 'fas fa-times-circle',
                    'class'       => 'btn btn-outline-danger btn-block',
                    'description' => 'Registrar una cancelación con motivo.',
                    'disabled'    => !($isRecep || $isDoc),
                ],
            ];

            $capabilities = match ($rol) {
                'ADMIN'         => ['Monitorea la agenda completa en tiempo real.', 'Sin acciones de edición para preservar la trazabilidad.'],
                'RECEPCIONISTA' => ['Crea y modifica citas directamente en el calendario.', 'Confirma o reprograma citas según disponibilidad del doctor.'],
                'DOCTOR'        => ['Reprograma y confirma citas propias.', 'Agrega notas médicas para compartir con recepción.'],
                'PACIENTE'      => ['Consulta tus citas programadas y su estado.', 'Solicita cambios a través de recepción.'],
                default         => ['Vista informativa del calendario.'],
            };

            $upcomingEvents = $filteredEvents
                ->sortBy('start')
                ->map(function ($event) {
                    $start = Carbon::parse($event['start']);
                    return [
                        'fecha'    => $start->translatedFormat('d M Y'),
                        'hora'     => $start->format('H:i'),
                        'doctor'   => $event['doctor'],
                        'paciente' => $event['paciente'],
                        'estado'   => $event['estado'],
                        'motivo'   => $event['motivo'],
                    ];
                })
                ->take(6)
                ->values()
                ->all();

            $viewData = array_merge($viewData, [
                'calendarEvents' => $filteredEvents->map(function ($event) {
                    return [
                        'id'              => $event['id'],
                        'title'           => $event['title'],
                        'start'           => $event['start'],
                        'end'             => $event['end'],
                        'backgroundColor' => $event['color'],
                        'borderColor'     => $event['color'],
                        'extendedProps'   => [
                            'doctor'   => $event['doctor'],
                            'paciente' => $event['paciente'],
                            'estado'   => $event['estado'],
                            'motivo'   => $event['motivo'],
                        ],
                    ];
                })->values()->all(),
                'legend'         => $legend,
                'quickActions'   => $quickActions,
                'upcomingEvents' => $upcomingEvents,
                'capabilities'   => $capabilities,
                'isReadOnly'     => $readOnly,
            ]);
        } else {
            $viewData = array_merge($viewData, [
                'rows'             => $rows,
                'catalogoEstados'  => $catalogoEstados,
                'catalogoDoctores' => $catalogoDoctores,
            ]);
        }

        return view($view, $viewData);
    }
}
