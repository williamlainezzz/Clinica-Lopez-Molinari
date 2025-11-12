<?php

namespace App\Http\Controllers\Personas;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PersonaController extends Controller
{
    private const ROLE_MAP = [
        'doctores' => ['DOCTOR', 'DOCTORA', 'ODONTOLOGO', 'ODONTOLOGA', 'MEDICO', 'MEDICA'],
        'pacientes' => ['PACIENTE', 'PACIENTES'],
        'recepcionistas' => ['RECEPCIONISTA', 'RECEPCIONISTAS'],
        'administradores' => ['ADMIN', 'ADMINISTRADOR', 'ADMINISTRADORA'],
    ];

    public function doctores(Request $request)
    {
        return $this->renderListado($request, [
            'slug' => 'doctores',
            'titulo' => 'Doctores',
            'descripcion' => 'Listado general de doctores registrados en el sistema.',
            'view' => 'personas.index',
            'citas' => [
                'fk' => 'FK_COD_DOCTOR',
                'label' => 'Citas (próximas / totales)',
            ],
        ]);
    }

    public function pacientes(Request $request)
    {
        return $this->renderListado($request, [
            'slug' => 'pacientes',
            'titulo' => 'Pacientes',
            'descripcion' => 'Pacientes asociados a citas o con cuentas activas.',
            'view' => 'personas.index',
            'citas' => [
                'fk' => 'FK_COD_PACIENTE',
                'label' => 'Citas (próximas / totales)',
            ],
        ]);
    }

    public function recepcionistas(Request $request)
    {
        return $this->renderListado($request, [
            'slug' => 'recepcionistas',
            'titulo' => 'Recepcionistas',
            'descripcion' => 'Equipo de recepción y asistencia al paciente.',
            'view' => 'personas.index',
        ]);
    }

    public function administradores(Request $request)
    {
        return $this->renderListado($request, [
            'slug' => 'administradores',
            'titulo' => 'Administradores',
            'descripcion' => 'Usuarios con privilegios administrativos.',
            'view' => 'personas.index',
        ]);
    }

    private function renderListado(Request $request, array $config)
    {
        $slug = $config['slug'];
        $view = $config['view'];

        $query = $this->baseQuery();
        $this->applyScope($query, $slug);

        if (isset($config['citas'])) {
            $query->leftJoinSub($this->citasSubQuery($config['citas']['fk']), 'ct', function ($join) {
                $join->on('ct.persona_id', '=', 'p.COD_PERSONA');
            });
            $query->addSelect([
                DB::raw('COALESCE(ct.total_citas, 0) as total_citas'),
                DB::raw('COALESCE(ct.proximas_citas, 0) as proximas_citas'),
            ]);
        }

        $filtros = [
            'q' => trim((string) $request->input('q', '')),
            'estado' => $request->input('estado', 'todos'),
        ];

        if ($filtros['q'] !== '') {
            $this->applySearch($query, $filtros['q']);
        }

        $this->applyEstadoFilter($query, $filtros['estado']);

        $resumen = $this->resumen(clone $query);

        $personas = $query
            ->orderBy('p.PRIMER_APELLIDO')
            ->orderBy('p.PRIMER_NOMBRE')
            ->paginate(10)
            ->appends($request->query());

        $personas->setCollection($this->transformPersonas($personas, $config));

        return view($view, [
            'pageTitle' => $config['titulo'],
            'pageDescription' => $config['descripcion'],
            'personas' => $personas,
            'resumen' => $resumen,
            'filtros' => $filtros,
            'citasLabel' => $config['citas']['label'] ?? null,
        ]);
    }

    private function baseQuery()
    {
        $correoSub = DB::table('tbl_correo')
            ->select('FK_COD_PERSONA', DB::raw("GROUP_CONCAT(CORREO ORDER BY CORREO SEPARATOR '||') as correos"))
            ->groupBy('FK_COD_PERSONA');

        $telSub = DB::table('tbl_telefono')
            ->select('FK_COD_PERSONA', DB::raw("GROUP_CONCAT(NUM_TELEFONO ORDER BY NUM_TELEFONO SEPARATOR '||') as telefonos"))
            ->groupBy('FK_COD_PERSONA');

        $dirSub = DB::table('tbl_direccion')
            ->select(
                'FK_COD_PERSONA',
                DB::raw("GROUP_CONCAT(TRIM(CONCAT_WS(', ', NULLIF(COLONIA, ''), NULLIF(CIUDAD, ''), NULLIF(MUNICIPIO, ''), NULLIF(DEPARTAMENTO, ''), NULLIF(REFERENCIA, ''))) ORDER BY COD_DIRECCION SEPARATOR '||') as direcciones")
            )
            ->groupBy('FK_COD_PERSONA');

        $nombreCompleto = "CONCAT_WS(' ', p.PRIMER_NOMBRE, NULLIF(p.SEGUNDO_NOMBRE,''), p.PRIMER_APELLIDO, NULLIF(p.SEGUNDO_APELLIDO,''))";

        return DB::table('tbl_persona as p')
            ->leftJoin('tbl_usuario as u', 'u.FK_COD_PERSONA', '=', 'p.COD_PERSONA')
            ->leftJoin('tbl_rol as r', 'r.COD_ROL', '=', 'u.FK_COD_ROL')
            ->leftJoinSub($correoSub, 'c', function ($join) {
                $join->on('c.FK_COD_PERSONA', '=', 'p.COD_PERSONA');
            })
            ->leftJoinSub($telSub, 't', function ($join) {
                $join->on('t.FK_COD_PERSONA', '=', 'p.COD_PERSONA');
            })
            ->leftJoinSub($dirSub, 'd', function ($join) {
                $join->on('d.FK_COD_PERSONA', '=', 'p.COD_PERSONA');
            })
            ->select([
                'p.COD_PERSONA',
                'p.PRIMER_NOMBRE',
                'p.SEGUNDO_NOMBRE',
                'p.PRIMER_APELLIDO',
                'p.SEGUNDO_APELLIDO',
                'p.TIPO_GENERO',
                'u.COD_USUARIO',
                'u.USR_USUARIO',
                'u.ESTADO_USUARIO',
                'r.NOM_ROL as rol',
                DB::raw("$nombreCompleto as nombre"),
                DB::raw("COALESCE(c.correos, '') as correos"),
                DB::raw("COALESCE(t.telefonos, '') as telefonos"),
                DB::raw("COALESCE(d.direcciones, '') as direcciones"),
            ]);
    }

    private function citasSubQuery(string $foreignKey)
    {
        return DB::table('tbl_cita')
            ->select([
                DB::raw("{$foreignKey} as persona_id"),
                DB::raw('COUNT(*) as total_citas'),
                DB::raw("SUM(CASE WHEN FEC_CITA >= CURRENT_DATE THEN 1 ELSE 0 END) as proximas_citas"),
            ])
            ->groupBy('persona_id');
    }

    private function applyScope($query, string $slug): void
    {
        $roles = Arr::get(self::ROLE_MAP, $slug, []);
        $roles = array_map(fn ($v) => strtoupper(trim($v)), $roles);

        $query->where(function ($scope) use ($roles, $slug) {
            if (!empty($roles)) {
                $scope->whereIn(DB::raw("UPPER(TRIM(r.NOM_ROL))"), $roles);
            }

            if (in_array($slug, ['doctores', 'pacientes'], true)) {
                $scope->orWhereExists(function ($sub) use ($slug) {
                    $column = $slug === 'doctores' ? 'FK_COD_DOCTOR' : 'FK_COD_PACIENTE';
                    $sub->select(DB::raw('1'))
                        ->from('tbl_cita as c')
                        ->whereColumn("c.{$column}", 'p.COD_PERSONA');
                });
            }
        });
    }

    private function applySearch($query, string $term): void
    {
        $term = "%{$term}%";
        $query->where(function ($search) use ($term) {
            $search->where('p.PRIMER_NOMBRE', 'like', $term)
                ->orWhere('p.SEGUNDO_NOMBRE', 'like', $term)
                ->orWhere('p.PRIMER_APELLIDO', 'like', $term)
                ->orWhere('p.SEGUNDO_APELLIDO', 'like', $term)
                ->orWhere('u.USR_USUARIO', 'like', $term)
                ->orWhere('c.correos', 'like', $term)
                ->orWhere('t.telefonos', 'like', $term);
        });
    }

    private function applyEstadoFilter($query, string $estado): void
    {
        switch ($estado) {
            case 'activos':
                $query->where('u.ESTADO_USUARIO', 1);
                break;
            case 'inactivos':
                $query->where('u.ESTADO_USUARIO', 0);
                break;
            case 'sin_usuario':
                $query->whereNull('u.COD_USUARIO');
                break;
        }
    }

    private function resumen($query): array
    {
        $datos = $query->select(['p.COD_PERSONA', 'u.ESTADO_USUARIO'])->get()
            ->unique('COD_PERSONA');

        $total = $datos->count();
        $activos = $datos->where('ESTADO_USUARIO', 1)->count();
        $inactivos = $datos->where('ESTADO_USUARIO', 0)->count();
        $sinCuenta = $datos->whereNull('ESTADO_USUARIO')->count();

        return [
            'total' => $total,
            'activos' => $activos,
            'inactivos' => $inactivos,
            'sinCuenta' => $sinCuenta,
        ];
    }

    private function transformPersonas(LengthAwarePaginator $personas, array $config): Collection
    {
        $items = $personas->getCollection();

        return $items->map(function ($row) use ($config) {
            $estado = $this->estadoInfo($row->ESTADO_USUARIO);
            $genero = $this->generoLabel($row->TIPO_GENERO);

            $contactos = [
                'correos' => $this->splitList($row->correos),
                'telefonos' => $this->splitList($row->telefonos),
                'direcciones' => $this->splitList($row->direcciones),
            ];

            $citas = null;
            if (isset($config['citas'])) {
                $total = (int) ($row->total_citas ?? 0);
                $proximas = (int) ($row->proximas_citas ?? 0);
                $citas = [
                    'total' => $total,
                    'proximas' => $proximas,
                ];
            }

            return (object) [
                'id' => $row->COD_PERSONA,
                'nombre' => $row->nombre,
                'genero' => $genero,
                'usuario' => $row->USR_USUARIO,
                'rol' => $row->rol,
                'estado' => $estado,
                'contactos' => $contactos,
                'citas' => $citas,
            ];
        });
    }

    private function splitList(?string $value): array
    {
        $value = trim((string) $value);

        if ($value === '') {
            return [];
        }

        return array_values(array_filter(array_map(fn ($v) => trim($v), explode('||', $value))));
    }

    private function estadoInfo($estado): array
    {
        return match ($estado) {
            1 => ['label' => 'Activo', 'class' => 'success'],
            0 => ['label' => 'Inactivo', 'class' => 'danger'],
            default => ['label' => 'Sin usuario', 'class' => 'secondary'],
        };
    }

    private function generoLabel($genero): string
    {
        return match ((int) $genero) {
            1 => 'Masculino',
            2 => 'Femenino',
            3 => 'No binario',
            default => 'Sin especificar',
        };
    }
}
