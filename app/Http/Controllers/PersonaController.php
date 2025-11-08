<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PersonaController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $currentRoleName = optional($user?->rol)->NOM_ROL;
        $normalizedRole = $this->normalizeRole($currentRoleName ?? 'ADMIN');
        $roleKey = match ($normalizedRole) {
            'ADMIN' => 'admin',
            'DOCTOR' => 'doctor',
            'RECEPCIONISTA' => 'recepcionista',
            'PACIENTE' => 'paciente',
            default => 'admin',
        };

        $catalog = $this->catalogoPersonas();
        $sections = $this->seccionesPorRol($normalizedRole, $catalog, $user);

        if (empty($sections)) {
            $sections = [
                'general' => $this->section('general', [
                    'label'       => 'Personas',
                    'icon'        => 'fas fa-users',
                    'description' => 'No se encontraron registros asociados al rol actual.',
                    'columns'     => [
                        ['key' => 'detalle', 'label' => 'Detalle'],
                    ],
                    'rows'        => [
                        ['detalle' => 'No hay información disponible.'],
                    ],
                    'show_actions' => false,
                    'stat_variant' => 'secondary',
                ]),
            ];
        }

        $requestedSlug = $request->query('section');
        if (!$requestedSlug || !isset($sections[$requestedSlug])) {
            $requestedSlug = array_key_first($sections);
        }
        $activeSection = $sections[$requestedSlug];

        $tabs = collect($sections)->map(function ($section) {
            return [
                'slug'  => $section['slug'],
                'label' => $section['label'],
                'icon'  => $section['icon'],
                'count' => count($section['rows'] ?? []),
                'badge' => $section['badge'] ?? null,
            ];
        })->values()->all();

        $stats = collect($sections)->map(function ($section) {
            return [
                'slug'    => $section['slug'],
                'label'   => $section['label'],
                'value'   => count($section['rows'] ?? []),
                'variant' => $section['stat_variant'] ?? 'primary',
                'icon'    => $section['icon'],
            ];
        })->values()->all();

        $roleLabels = [
            'ADMIN'         => 'Administrador',
            'DOCTOR'        => 'Doctor',
            'RECEPCIONISTA' => 'Recepcionista',
            'PACIENTE'      => 'Paciente',
        ];

        $roleLabel = $roleLabels[$normalizedRole] ?? ($currentRoleName ?: 'Usuario');

        return view('personas.index', [
            'pageTitle'     => 'Personas · ' . $roleLabel,
            'heading'       => 'Personas ' . $roleLabel,
            'rol'           => $normalizedRole,
            'rolLabel'      => $roleLabel,
            'bannerPartial' => "personas.banners.banner-{$roleKey}",
            'sections'      => $sections,
            'activeSlug'    => $activeSection['slug'],
            'activeSection' => $activeSection,
            'tabs'          => $tabs,
            'stats'         => $stats,
        ]);
    }

    private function catalogoPersonas(): array
    {
        $correoSub = DB::table('tbl_correo')
            ->select('FK_COD_PERSONA', DB::raw('MIN(CORREO) as CORREO'))
            ->groupBy('FK_COD_PERSONA');

        $telefonoSub = DB::table('tbl_telefono')
            ->select('FK_COD_PERSONA', DB::raw('MIN(NUM_TELEFONO) as TELEFONO'))
            ->groupBy('FK_COD_PERSONA');

        $nombreCompleto = "CONCAT_WS(' ', p.PRIMER_NOMBRE, NULLIF(p.SEGUNDO_NOMBRE,''), p.PRIMER_APELLIDO, NULLIF(p.SEGUNDO_APELLIDO,''))";

        $personas = DB::table('tbl_usuario as u')
            ->join('tbl_persona as p', 'p.COD_PERSONA', '=', 'u.FK_COD_PERSONA')
            ->leftJoin('tbl_rol as r', 'r.COD_ROL', '=', 'u.FK_COD_ROL')
            ->leftJoinSub($correoSub, 'c', function ($join) {
                $join->on('c.FK_COD_PERSONA', '=', 'p.COD_PERSONA');
            })
            ->leftJoinSub($telefonoSub, 't', function ($join) {
                $join->on('t.FK_COD_PERSONA', '=', 'p.COD_PERSONA');
            })
            ->select([
                'u.COD_USUARIO   as usuario_id',
                'u.USR_USUARIO   as usuario',
                'u.ESTADO_USUARIO as estado_id',
                'p.COD_PERSONA   as persona_id',
                DB::raw("COALESCE(r.NOM_ROL, 'SIN ROL') as rol"),
                DB::raw("$nombreCompleto as nombre"),
                DB::raw("COALESCE(c.CORREO, '') as correo"),
                DB::raw("COALESCE(t.TELEFONO, '') as telefono"),
            ])
            ->orderBy('p.PRIMER_APELLIDO')
            ->orderBy('p.PRIMER_NOMBRE')
            ->get()
            ->map(fn($row) => $this->mapPersonaRow($row));

        $grouped = $personas->groupBy('rol_normalized');

        return [
            'ADMIN'         => $grouped->get('ADMIN', collect())->values()->all(),
            'DOCTOR'        => $grouped->get('DOCTOR', collect())->values()->all(),
            'RECEPCIONISTA' => $grouped->get('RECEPCIONISTA', collect())->values()->all(),
            'PACIENTE'      => $grouped->get('PACIENTE', collect())->values()->all(),
            'OTROS'         => $grouped->except(['ADMIN','DOCTOR','RECEPCIONISTA','PACIENTE'])->flatten(1)->values()->all(),
        ];
    }

    private function mapPersonaRow($row): array
    {
        $estadoVariant = match ((int) ($row->estado_id ?? -1)) {
            1       => ['label' => 'Activo', 'variant' => 'success'],
            0       => ['label' => 'Inactivo', 'variant' => 'secondary'],
            default => ['label' => 'Sin definir', 'variant' => 'dark'],
        };

        $normalizedRole = $this->normalizeRole($row->rol ?? '');

        return [
            'usuario_id'     => (int) $row->usuario_id,
            'persona_id'     => (int) $row->persona_id,
            'usuario'        => $row->usuario,
            'nombre'         => trim($row->nombre) !== '' ? $row->nombre : $row->usuario,
            'correo'         => $row->correo ?: '—',
            'telefono'       => $row->telefono ?: '—',
            'rol'            => $row->rol ?? 'Sin rol',
            'rol_normalized' => $normalizedRole,
            'estado'         => $estadoVariant,
        ];
    }

    private function normalizeRole(?string $role): string
    {
        $slug = Str::slug((string) $role);

        return match ($slug) {
            'admin', 'administrador', 'administradora' => 'ADMIN',
            'doctor', 'doctora'                         => 'DOCTOR',
            'recepcionista', 'recepcion'                => 'RECEPCIONISTA',
            'paciente', 'pacientes'                     => 'PACIENTE',
            default                                     => 'OTROS',
        };
    }

    private function seccionesPorRol(string $rol, array $catalog, $user): array
    {
        $sections = [];

        $footnotes = [
            'DOCTOR'        => $this->footnoteFor('doctores', count($catalog['DOCTOR'])),
            'PACIENTE'      => $this->footnoteFor('pacientes', count($catalog['PACIENTE'])),
            'RECEPCIONISTA' => $this->footnoteFor('recepcionistas', count($catalog['RECEPCIONISTA'])),
            'ADMIN'         => $this->footnoteFor('administradores', count($catalog['ADMIN'])),
        ];

        switch ($rol) {
            case 'ADMIN':
                $sections['doctores'] = $this->personSection(
                    'doctores',
                    'Doctores',
                    'fas fa-user-md',
                    $catalog['DOCTOR'],
                    'primary',
                    true,
                    [
                        'description'    => 'Profesionales activos en la clínica.',
                        'header_actions' => [
                            [
                                'label' => 'Gestionar usuarios',
                                'icon'  => 'fas fa-users-cog',
                                'class' => 'btn-primary',
                                'url'   => route('seguridad.usuarios.index'),
                            ],
                            [
                                'label' => 'Nuevo usuario',
                                'icon'  => 'fas fa-user-plus',
                                'class' => 'btn-outline-primary',
                                'url'   => route('seguridad.usuarios.create'),
                            ],
                        ],
                        'footnote' => $footnotes['DOCTOR'],
                    ]
                );

                $sections['pacientes'] = $this->personSection(
                    'pacientes',
                    'Pacientes',
                    'fas fa-user-injured',
                    $catalog['PACIENTE'],
                    'success',
                    true,
                    [
                        'description' => 'Pacientes registrados dentro del sistema.',
                        'footnote'    => $footnotes['PACIENTE'],
                    ]
                );

                $sections['recepcionistas'] = $this->personSection(
                    'recepcionistas',
                    'Recepcionistas',
                    'fas fa-concierge-bell',
                    $catalog['RECEPCIONISTA'],
                    'warning',
                    true,
                    [
                        'description' => 'Equipo de recepción y asistencia a pacientes.',
                        'footnote'    => $footnotes['RECEPCIONISTA'],
                    ]
                );

                $sections['administradores'] = $this->personSection(
                    'administradores',
                    'Administradores',
                    'fas fa-user-shield',
                    $catalog['ADMIN'],
                    'danger',
                    true,
                    [
                        'description' => 'Usuarios con acceso total al sistema.',
                        'footnote'    => $footnotes['ADMIN'],
                    ]
                );
                break;

            case 'RECEPCIONISTA':
                $sections['doctores'] = $this->personSection(
                    'doctores',
                    'Doctores disponibles',
                    'fas fa-user-md',
                    $catalog['DOCTOR'],
                    'primary',
                    false,
                    [
                        'description' => 'Directorio de doctores para coordinar citas.',
                        'footnote'    => $footnotes['DOCTOR'],
                    ]
                );

                $sections['pacientes'] = $this->personSection(
                    'pacientes',
                    'Pacientes',
                    'fas fa-user-injured',
                    $catalog['PACIENTE'],
                    'success',
                    true,
                    [
                        'description'    => 'Pacientes en seguimiento. Utiliza el módulo de agenda para agendar citas.',
                        'header_actions' => [
                            [
                                'label' => 'Ir a agenda',
                                'icon'  => 'fas fa-calendar-alt',
                                'class' => 'btn-success',
                                'url'   => route('agenda.calendario'),
                            ],
                        ],
                        'footnote'       => $footnotes['PACIENTE'],
                    ]
                );

                $sections['recepcion'] = $this->personSection(
                    'recepcion',
                    'Equipo de recepción',
                    'fas fa-headset',
                    $catalog['RECEPCIONISTA'],
                    'info',
                    false,
                    [
                        'description' => 'Compañeros de recepción registrados.',
                        'footnote'    => $footnotes['RECEPCIONISTA'],
                    ]
                );
                break;

            case 'DOCTOR':
                $currentPersona = $this->currentUserRow($user, $catalog);

                $sections['perfil'] = $this->section('perfil', [
                    'label'       => 'Mi perfil',
                    'icon'        => 'fas fa-id-card',
                    'description' => 'Resumen de tus datos registrados.',
                    'columns'     => [
                        ['key' => 'campo', 'label' => 'Campo'],
                        ['key' => 'valor', 'label' => 'Valor'],
                    ],
                    'rows'         => $this->profileRows($currentPersona),
                    'show_actions' => false,
                    'stat_variant' => 'info',
                ]);

                $sections['pacientes'] = $this->personSection(
                    'pacientes',
                    'Pacientes en seguimiento',
                    'fas fa-user-injured',
                    $this->pacientesRelacionados($user, $catalog['PACIENTE']),
                    'success',
                    false,
                    [
                        'description' => 'Pacientes disponibles desde el sistema. Ajusta este listado según las asignaciones reales.',
                        'footnote'    => $footnotes['PACIENTE'],
                    ]
                );

                $sections['recepcion'] = $this->personSection(
                    'recepcion',
                    'Contacto de recepción',
                    'fas fa-headset',
                    $catalog['RECEPCIONISTA'],
                    'warning',
                    false,
                    [
                        'description' => 'Equipo de recepción para coordinar cambios de cita.',
                        'footnote'    => $footnotes['RECEPCIONISTA'],
                    ]
                );
                break;

            case 'PACIENTE':
                $currentPersona = $this->currentUserRow($user, $catalog);

                $sections['perfil'] = $this->section('perfil', [
                    'label'       => 'Mi información',
                    'icon'        => 'fas fa-id-card',
                    'description' => 'Datos básicos asociados a tu usuario.',
                    'columns'     => [
                        ['key' => 'campo', 'label' => 'Campo'],
                        ['key' => 'valor', 'label' => 'Valor'],
                    ],
                    'rows'         => $this->profileRows($currentPersona),
                    'show_actions' => false,
                    'stat_variant' => 'primary',
                ]);

                $sections['doctores'] = $this->personSection(
                    'doctores',
                    'Equipo médico',
                    'fas fa-user-md',
                    $catalog['DOCTOR'],
                    'primary',
                    false,
                    [
                        'description' => 'Directorio de doctores disponibles en la clínica.',
                        'footnote'    => $footnotes['DOCTOR'],
                    ]
                );

                $sections['recepcion'] = $this->personSection(
                    'recepcion',
                    'Recepción',
                    'fas fa-concierge-bell',
                    $catalog['RECEPCIONISTA'],
                    'info',
                    false,
                    [
                        'description' => 'Canales de apoyo para reagendar tus citas.',
                        'footnote'    => $footnotes['RECEPCIONISTA'],
                    ]
                );
                break;

            default:
                $sections['personas'] = $this->personSection(
                    'personas',
                    'Personas registradas',
                    'fas fa-users',
                    array_merge(
                        $catalog['ADMIN'],
                        $catalog['DOCTOR'],
                        $catalog['RECEPCIONISTA'],
                        $catalog['PACIENTE'],
                        $catalog['OTROS']
                    ),
                    'primary',
                    false,
                    [
                        'description' => 'Listado general de personas.',
                    ]
                );
                break;
        }

        return $sections;
    }

    private function pacientesRelacionados($user, array $pacientes): array
    {
        if (!$user) {
            return [];
        }

        // Si no existe aún lógica de asignación, devolvemos los registros disponibles.
        return collect($pacientes)->values()->all();
    }

    private function profileRows(?array $persona): array
    {
        if (!$persona) {
            return [
                ['campo' => 'Estado', 'valor' => 'Usuario no encontrado en la tabla de usuarios.'],
            ];
        }

        return [
            ['campo' => 'Nombre completo', 'valor' => $persona['nombre']],
            ['campo' => 'Usuario',         'valor' => $persona['usuario']],
            ['campo' => 'Correo',          'valor' => $persona['correo']],
            ['campo' => 'Teléfono',        'valor' => $persona['telefono']],
            ['campo' => 'Estado',          'valor' => $persona['estado']['label'] ?? 'Activo'],
        ];
    }

    private function currentUserRow($user, array $catalog): ?array
    {
        if (!$user) {
            return null;
        }

        return collect($catalog)
            ->flatten(1)
            ->firstWhere('usuario_id', (int) $user->COD_USUARIO);
    }

    private function personSection(
        string $slug,
        string $label,
        string $icon,
        array $rows,
        string $variant,
        bool $allowActions,
        array $options = []
    ): array {
        $columns = $options['columns'] ?? [
            ['key' => 'nombre',   'label' => 'Nombre'],
            ['key' => 'usuario',  'label' => 'Usuario'],
            ['key' => 'correo',   'label' => 'Correo'],
            ['key' => 'telefono', 'label' => 'Teléfono'],
            ['key' => 'estado',   'label' => 'Estado', 'type' => 'badge'],
        ];

        $actions = $allowActions && !empty($rows)
            ? ($options['actions'] ?? [
                ['icon' => 'fas fa-eye', 'class' => 'info', 'label' => 'Ver detalle'],
            ])
            : [];

        return $this->section($slug, array_merge([
            'label'          => $label,
            'icon'           => $icon,
            'description'    => $options['description'] ?? null,
            'columns'        => $columns,
            'rows'           => $rows,
            'actions'        => $actions,
            'show_actions'   => !empty($actions),
            'header_actions' => $options['header_actions'] ?? [],
            'footnote'       => $options['footnote'] ?? null,
            'stat_variant'   => $variant,
        ], $options));
    }

    private function footnoteFor(string $rolLabel, int $count): string
    {
        return $count > 0
            ? ucfirst($rolLabel) . ' obtenidos directamente desde la base de datos.'
            : 'No se encontraron ' . $rolLabel . ' registrados.';
    }

    private function section(string $slug, array $data): array
    {
        return array_merge([
            'slug'           => $slug,
            'label'          => 'Sección',
            'icon'           => 'fas fa-circle',
            'description'    => null,
            'columns'        => [],
            'rows'           => [],
            'actions'        => [],
            'show_actions'   => false,
            'header_actions' => [],
            'footnote'       => null,
            'stat_variant'   => 'primary',
        ], $data);
    }
}
