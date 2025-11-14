<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AgendaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

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

        $citas = $this->citasForRol($rolSlug, $user);
        $doctorPanels = $this->buildDoctorPanels($citas);
        $activeDoctor = $this->resolveActiveDoctor($rolSlug, $doctorPanels, $user);
        $availablePatients = $this->availablePatients();
        $patientRecord = $this->buildPatientRecord($rolSlug, $citas, $activeDoctor, $user);
        $timeline = $this->buildTimeline($patientRecord);

        $calendarMatrix = $this->calendarMatrix();
        $calendarEventBundle = $this->buildCalendarEvents($citas);
        $calendarEvents = $calendarEventBundle['byDate'];
        $eventList = $calendarEventBundle['list'];

        $stats = $this->buildStats($rolSlug, $citas, count($availablePatients));
        $shareSlug = $activeDoctor['slug'] ?? 'agenda';
        $shareLink = url('/registro/paciente?doctor=' . $shareSlug);
        $shareCode = strtoupper(Str::slug($shareSlug)) . '-' . now()->format('ymd');

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

    private function citasForRol(string $rolSlug, $user): Collection
    {
        $personaId = $user->FK_COD_PERSONA ?? null;

        $query = Cita::query()
            ->with([
                'doctor.telefonos',
                'doctor.correos',
                'paciente.telefonos',
                'paciente.correos',
            ])
            ->orderBy('FEC_CITA');

        if ($horaColumn = $this->resolveHoraColumn()) {
            $query->orderBy($horaColumn);
        }

        switch ($rolSlug) {
            case 'doctor':
                if ($personaId) {
                    $query->where('FK_COD_DOCTOR', $personaId);
                }
                break;
            case 'paciente':
                if ($personaId) {
                    $query->where('FK_COD_PACIENTE', $personaId);
                }
                break;
            default:
                $query->whereDate('FEC_CITA', '>=', now()->subWeeks(2)->toDateString());
                break;
        }

        return $query->get();
    }

    private function resolveHoraColumn(): ?string
    {
        static $cachedColumn = null;

        if ($cachedColumn === false) {
            return null;
        }

        if (is_string($cachedColumn)) {
            return $cachedColumn;
        }

        $candidates = [
            'HORA_CITA',
            'hora_cita',
            'HORA',
            'hora',
            'HOR_CITA',
            'hor_cita',
        ];

        foreach ($candidates as $column) {
            if (Schema::hasColumn('tbl_cita', $column)) {
                return $cachedColumn = $column;
            }
        }

        $cachedColumn = false;

        return null;
    }

    private function buildDoctorPanels(Collection $citas): array
    {
        $grouped = $citas->filter(fn ($cita) => $cita->FK_COD_DOCTOR)->groupBy('FK_COD_DOCTOR');

        return $grouped->map(function (Collection $doctorCitas) {
            $doctor = $doctorCitas->first()->doctor;
            $contacto = optional($doctor?->telefonos->first())->NUM_TELEFONO
                ?? optional($doctor?->correos->first())->CORREO
                ?? 'Sin contacto';

            return [
                'id' => $doctor?->COD_PERSONA,
                'slug' => Str::slug($doctor?->nombre_completo ?? 'doctor'),
                'nombre' => $doctor?->nombre_completo ?? 'Sin doctor asignado',
                'especialidad' => $doctor?->ESPECIALIDAD ?? 'General',
                'contacto' => $contacto,
                'pacientes' => $doctorCitas->map(function (Cita $cita) {
                    $estado = ucfirst(strtolower($cita->ESTADO_CITA));
                    return [
                        'codigo' => $cita->COD_CITA,
                        'nombre' => optional($cita->paciente)->nombre_completo ?? 'Paciente',
                        'motivo' => $cita->MOTIVO_CITA,
                        'fecha'  => $cita->FEC_CITA?->format('Y-m-d') ?? $cita->FEC_CITA,
                        'hora'   => $cita->hora_label,
                        'estado' => $estado,
                        'nota'   => $cita->NOTAS_CITA ?? '',
                    ];
                })->values()->all(),
            ];
        })->values()->all();
    }

    private function resolveActiveDoctor(string $rolSlug, array $doctorPanels, $user): array
    {
        if ($rolSlug === 'doctor' && $user?->FK_COD_PERSONA) {
            foreach ($doctorPanels as $panel) {
                if ($panel['id'] === $user->FK_COD_PERSONA) {
                    return $panel;
                }
            }
        }

        return $doctorPanels[0] ?? [
            'id' => null,
            'slug' => 'doctor',
            'nombre' => 'Sin doctor',
            'especialidad' => 'General',
            'contacto' => 'N/D',
            'pacientes' => [],
        ];
    }

    private function buildPatientRecord(string $rolSlug, Collection $citas, array $activeDoctor, $user): array
    {
        $target = match ($rolSlug) {
            'paciente' => $citas->sortBy('FEC_CITA')->first(),
            'doctor'   => $citas->sortBy('FEC_CITA')->first(),
            default    => $citas->sortBy('FEC_CITA')->first(),
        };

        if (!$target && !empty($activeDoctor['pacientes'])) {
            $firstPaciente = $activeDoctor['pacientes'][0] ?? null;
            if ($firstPaciente) {
                $target = $citas->firstWhere('COD_CITA', $firstPaciente['codigo']);
            }
        }

        if (!$target) {
            return [
                'profile' => [
                    'codigo' => null,
                    'nombre' => 'Sin paciente',
                    'doctor' => $activeDoctor['nombre'] ?? 'Sin doctor',
                    'especialidad' => $activeDoctor['especialidad'] ?? 'General',
                    'estado' => 'Sin citas',
                    'correo' => 'No registrado',
                    'telefono' => 'No registrado',
                    'proxima' => [
                        'fecha' => now()->toDateString(),
                        'hora' => '00:00',
                        'motivo' => 'Sin citas registradas',
                        'estado' => 'Pendiente',
                    ],
                ],
                'historial' => [],
            ];
        }

        $paciente = $target->paciente;
        $doctor = $target->doctor;

        $historial = Cita::query()
            ->where('FK_COD_PACIENTE', $paciente?->COD_PERSONA)
            ->orderByDesc('FEC_CITA')
            ->take(5)
            ->get()
            ->map(function (Cita $cita) {
                return [
                    'fecha' => $cita->FEC_CITA?->format('Y-m-d') ?? $cita->FEC_CITA,
                    'estado' => ucfirst(strtolower($cita->ESTADO_CITA)),
                    'motivo' => $cita->MOTIVO_CITA,
                    'detalle' => $cita->NOTAS_CITA ?? 'Sin notas registradas.',
                ];
            })->toArray();

        return [
            'profile' => [
                'codigo' => $paciente?->COD_PERSONA,
                'nombre' => $paciente?->nombre_completo ?? 'Paciente',
                'doctor' => $doctor?->nombre_completo ?? ($activeDoctor['nombre'] ?? 'Sin doctor'),
                'especialidad' => $doctor?->ESPECIALIDAD ?? ($activeDoctor['especialidad'] ?? 'General'),
                'estado' => ucfirst(strtolower($target->ESTADO_CITA)),
                'correo' => optional($paciente?->correos->first())->CORREO ?? 'No registrado',
                'telefono' => optional($paciente?->telefonos->first())->NUM_TELEFONO ?? 'No registrado',
                'proxima' => [
                    'fecha' => $target->FEC_CITA?->format('Y-m-d') ?? $target->FEC_CITA,
                    'hora' => $target->hora_label,
                    'motivo' => $target->MOTIVO_CITA,
                    'estado' => ucfirst(strtolower($target->ESTADO_CITA)),
                ],
            ],
            'historial' => $historial,
        ];
    }

    private function buildTimeline(array $patientRecord): array
    {
        return collect($patientRecord['historial'] ?? [])->take(3)->map(function ($item) {
            return [
                'fecha' => $item['fecha'],
                'descripcion' => $item['motivo'],
                'estado' => $item['estado'],
            ];
        })->values()->all();
    }

    private function availablePatients(): array
    {
        $rows = DB::table('tbl_cita as c')
            ->join('tbl_persona as p', 'p.COD_PERSONA', '=', 'c.FK_COD_PACIENTE')
            ->where(function ($query) {
                $query->whereNull('c.FK_COD_DOCTOR')
                    ->orWhereRaw('UPPER(c.ESTADO_CITA) = ?', ['SOLICITADA']);
            })
            ->orderByDesc('c.created_at')
            ->select([
                'p.PRIMER_NOMBRE',
                'p.PRIMER_APELLIDO',
                'c.MOTIVO_CITA',
                'c.CANAL',
                'c.created_at',
            ])
            ->limit(5)
            ->get();

        return $rows->map(function ($row) {
            $fecha = $row->created_at ? Carbon::parse($row->created_at) : null;
            return [
                'nombre' => trim($row->PRIMER_NOMBRE . ' ' . $row->PRIMER_APELLIDO),
                'motivo' => $row->MOTIVO_CITA,
                'preferencia' => $row->CANAL ?? 'Sin preferencia',
                'ultima' => $fecha ? $fecha->diffForHumans() : 'Reciente',
            ];
        })->toArray();
    }

    private function calendarMatrix(): array
    {
        $today = Carbon::today();
        $start = $today->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY);
        $end = $today->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $matrix = [];
        $cursor = $start->copy();

        while ($cursor <= $end) {
            $week = [];
            for ($i = 0; $i < 7; $i++) {
                $week[] = [
                    'label' => $cursor->format('j'),
                    'date' => $cursor->toDateString(),
                    'isMuted' => !$cursor->isSameMonth($today),
                    'isToday' => $cursor->isSameDay($today),
                ];
                $cursor->addDay();
            }
            $matrix[] = $week;
        }

        return $matrix;
    }

    private function buildCalendarEvents(Collection $citas): array
    {
        $byDate = [];
        $list = [];

        foreach ($citas as $cita) {
            $date = $cita->FEC_CITA?->toDateString() ?? $cita->FEC_CITA;
            $event = [
                'id' => $cita->COD_CITA,
                'fecha' => $date,
                'hora' => $cita->hora_label,
                'doctor' => optional($cita->doctor)->nombre_completo,
                'paciente' => optional($cita->paciente)->nombre_completo,
                'estado' => ucfirst(strtolower($cita->ESTADO_CITA)),
                'motivo' => $cita->MOTIVO_CITA,
            ];

            $byDate[$date][] = $event;
            $list[] = $event;
        }

        usort($list, function ($a, $b) {
            return [$a['fecha'], $a['hora']] <=> [$b['fecha'], $b['hora']];
        });

        return [
            'byDate' => $byDate,
            'list' => array_values($list),
        ];
    }

    private function buildStats(string $rolSlug, Collection $citas, int $pendingAssignments): array
    {
        $confirmadas = $this->countByEstado($citas, 'CONFIRMADA');
        $pendientes = $this->countByEstado($citas, 'PENDIENTE');
        $canceladas = $this->countByEstado($citas, 'CANCELADA');
        $total = $citas->count();

        if ($rolSlug === 'paciente') {
            return [
                ['label' => 'Citas programadas', 'value' => $total],
                ['label' => 'Confirmadas', 'value' => $confirmadas],
                ['label' => 'Pendientes', 'value' => $pendientes],
            ];
        }

        $stats = [
            ['label' => 'Citas programadas', 'value' => $total, 'icon' => 'fas fa-calendar-check', 'color' => 'primary'],
            ['label' => 'Confirmadas', 'value' => $confirmadas, 'icon' => 'fas fa-check-circle', 'color' => 'success'],
            ['label' => 'Pendientes', 'value' => $pendientes, 'icon' => 'fas fa-clock', 'color' => 'warning'],
            ['label' => 'Canceladas', 'value' => $canceladas, 'icon' => 'fas fa-exclamation-triangle', 'color' => 'danger'],
        ];

        if ($pendingAssignments > 0) {
            $stats[] = ['label' => 'Solicitudes sin doctor', 'value' => $pendingAssignments, 'icon' => 'fas fa-user-clock', 'color' => 'info'];
        }

        return $stats;
    }

    private function countByEstado(Collection $citas, string $estado): int
    {
        $target = strtoupper($estado);
        return $citas->filter(function (Cita $cita) use ($target) {
            return strtoupper($cita->ESTADO_CITA ?? '') === $target;
        })->count();
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
                    'intro'     => 'Coordina doctores, salas y recordatorios para los pacientes.',
                ],
                'calendario' => [
                    'pageTitle' => 'Agenda · Recepción',
                    'heading'   => 'Agenda diaria',
                    'intro'     => 'Controla las citas de hoy por doctor y consultorio.',
                ],
                'reportes' => [
                    'pageTitle' => 'Reportes · Recepción',
                    'heading'   => 'Bitácora de citas',
                    'intro'     => 'Exporta citas confirmadas, pendientes y canceladas.',
                ],
            ],
            'doctor' => [
                'citas' => [
                    'pageTitle' => 'Mis pacientes',
                    'heading'   => 'Pacientes asignados',
                    'intro'     => 'Gestiona tus próximas consultas y notas clínicas.',
                ],
                'calendario' => [
                    'pageTitle' => 'Agenda del doctor',
                    'heading'   => 'Mi calendario',
                    'intro'     => 'Visualiza tus citas confirmadas dentro del calendario.',
                ],
                'reportes' => [
                    'pageTitle' => 'Reportes del doctor',
                    'heading'   => 'Historial de citas',
                    'intro'     => 'Consulta indicadores rápidos de productividad clínica.',
                ],
            ],
            'paciente' => [
                'citas' => [
                    'pageTitle' => 'Mis citas',
                    'heading'   => 'Mis citas',
                    'intro'     => 'Consulta tu doctor asignado y la próxima cita.',
                ],
                'calendario' => [
                    'pageTitle' => 'Mi agenda',
                    'heading'   => 'Calendario personal',
                    'intro'     => 'Visualiza tus citas confirmadas dentro del calendario.',
                ],
                'reportes' => [
                    'pageTitle' => 'Historial de citas',
                    'heading'   => 'Historial de citas',
                    'intro'     => 'Bitácora básica de tus atenciones en la clínica.',
                ],
            ],
        ];
    }
}
