<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportesController extends Controller
{
    public function index()
    {
        $resumen = [
            'citas'     => $this->countTable('tbl_cita'),
            'pacientes' => $this->countPacientes(),
            'usuarios'  => $this->countTable('tbl_usuario'),
        ];

        return view('reportes.index', [
            'resumen' => $resumen,
        ]);
    }

    public function citasRango(Request $request)
    {
        $filters = $request->validate([
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin'    => ['nullable', 'date'],
            'doctor'       => ['nullable', 'integer'],
        ]);

        $doctores = $this->doctores();
        $citas    = collect();

        if ($this->tableExists('tbl_cita')) {
            $query = $this->citasQuery();

            if (!empty($filters['fecha_inicio'])) {
                $query->whereDate('c.FEC_CITA', '>=', $filters['fecha_inicio']);
            }

            if (!empty($filters['fecha_fin'])) {
                $query->whereDate('c.FEC_CITA', '<=', $filters['fecha_fin']);
            }

            if (!empty($filters['doctor'])) {
                $query->where('c.FK_COD_DOCTOR', $filters['doctor']);
            }

            $citas = $query->orderBy('c.FEC_CITA')->orderBy('c.HOR_CITA')->get();
        }

        return view('reportes.citas-rango', [
            'citas'   => $citas,
            'doctores'=> $doctores,
            'filters' => $filters,
        ]);
    }

    public function citasEstado(Request $request)
    {
        $filters = $request->validate([
            'estado'       => ['nullable', 'integer'],
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin'    => ['nullable', 'date'],
        ]);

        $estados = $this->estadosCita();
        $citas   = collect();

        if ($this->tableExists('tbl_cita')) {
            $query = $this->citasQuery();

            if (!empty($filters['estado'])) {
                $query->where('c.ESTADO_CITA', $filters['estado']);
            }

            if (!empty($filters['fecha_inicio'])) {
                $query->whereDate('c.FEC_CITA', '>=', $filters['fecha_inicio']);
            }

            if (!empty($filters['fecha_fin'])) {
                $query->whereDate('c.FEC_CITA', '<=', $filters['fecha_fin']);
            }

            $citas = $query->orderBy('c.FEC_CITA')->orderBy('c.HOR_CITA')->get();
        }

        return view('reportes.citas-estado', [
            'citas'   => $citas,
            'estados' => $estados,
            'filters' => $filters,
        ]);
    }

    public function agendaDoctor(Request $request)
    {
        $filters = $request->validate([
            'doctor'       => ['nullable', 'integer'],
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin'    => ['nullable', 'date'],
        ]);

        $doctores = $this->doctores();
        if (empty($filters['doctor']) && $doctores->isNotEmpty()) {
            $filters['doctor'] = (int) $doctores->first()->COD_PERSONA;
        }

        $citas = collect();

        if ($this->tableExists('tbl_cita') && !empty($filters['doctor'])) {
            $query = $this->citasQuery()
                ->where('c.FK_COD_DOCTOR', $filters['doctor']);

            if (!empty($filters['fecha_inicio'])) {
                $query->whereDate('c.FEC_CITA', '>=', $filters['fecha_inicio']);
            }

            if (!empty($filters['fecha_fin'])) {
                $query->whereDate('c.FEC_CITA', '<=', $filters['fecha_fin']);
            }

            $citas = $query->orderBy('c.FEC_CITA')->orderBy('c.HOR_CITA')->get();
        }

        return view('reportes.agenda-doctor', [
            'citas'   => $citas,
            'doctores'=> $doctores,
            'filters' => $filters,
        ]);
    }

    public function pacientesEstado(Request $request)
    {
        $filters = $request->validate([
            'estado' => ['nullable', 'string', 'in:activos,inactivos,todos'],
        ]);

        $estadoSeleccionado = $filters['estado'] ?? 'activos';
        $pacientes          = collect();

        if ($this->tableExists('tbl_persona')) {
            $pacientes = DB::table('tbl_persona as p')
                ->leftJoin('tbl_usuario as u', 'u.FK_COD_PERSONA', '=', 'p.COD_PERSONA')
                ->leftJoin('tbl_rol as r', 'r.COD_ROL', '=', 'u.FK_COD_ROL')
                ->leftJoin('tbl_telefono as t', 't.FK_COD_PERSONA', '=', 'p.COD_PERSONA')
                ->leftJoin(DB::raw('(
                    SELECT FK_COD_PACIENTE, MAX(FEC_CITA) AS ultima_cita
                    FROM tbl_cita
                    GROUP BY FK_COD_PACIENTE
                ) as c'), 'c.FK_COD_PACIENTE', '=', 'p.COD_PERSONA')
                ->whereRaw('UPPER(TRIM(r.NOM_ROL)) = ?', ['PACIENTE'])
                ->groupBy(
                    'p.COD_PERSONA',
                    'p.PRIMER_NOMBRE',
                    'p.SEGUNDO_NOMBRE',
                    'p.PRIMER_APELLIDO',
                    'p.SEGUNDO_APELLIDO',
                    'u.ESTADO_USUARIO'
                )
                ->select([
                    'p.COD_PERSONA',
                    'p.PRIMER_NOMBRE',
                    'p.SEGUNDO_NOMBRE',
                    'p.PRIMER_APELLIDO',
                    'p.SEGUNDO_APELLIDO',
                    'u.ESTADO_USUARIO',
                    DB::raw('COALESCE(c.ultima_cita, NULL) as ultima_cita'),
                    DB::raw("GROUP_CONCAT(DISTINCT t.NUM_TELEFONO ORDER BY t.NUM_TELEFONO SEPARATOR ', ') as telefonos"),
                ]);

            if ($estadoSeleccionado === 'activos') {
                $pacientes->where('u.ESTADO_USUARIO', 1);
            } elseif ($estadoSeleccionado === 'inactivos') {
                $pacientes->where('u.ESTADO_USUARIO', 0);
            }

            $pacientes = $pacientes
                ->orderBy('p.PRIMER_NOMBRE')
                ->orderBy('p.PRIMER_APELLIDO')
                ->get();
        }

        return view('reportes.pacientes-estado', [
            'pacientes' => $pacientes,
            'filters'   => ['estado' => $estadoSeleccionado],
        ]);
    }

    public function usuariosRol(Request $request)
    {
        $filters = $request->validate([
            'rol' => ['nullable', 'integer'],
        ]);

        $roles    = $this->roles();
        $usuarios = collect();

        if ($this->tableExists('tbl_usuario')) {
            $query = DB::table('tbl_usuario as u')
                ->leftJoin('tbl_persona as p', 'p.COD_PERSONA', '=', 'u.FK_COD_PERSONA')
                ->leftJoin('tbl_rol as r', 'r.COD_ROL', '=', 'u.FK_COD_ROL')
                ->select([
                    'u.COD_USUARIO',
                    'u.USR_USUARIO',
                    'u.ESTADO_USUARIO',
                    'r.NOM_ROL',
                    DB::raw("CONCAT(p.PRIMER_NOMBRE,' ',p.PRIMER_APELLIDO) as nombre"),
                ])
                ->orderBy('r.NOM_ROL')
                ->orderBy('p.PRIMER_NOMBRE')
                ->orderBy('p.PRIMER_APELLIDO');

            if (!empty($filters['rol'])) {
                $query->where('u.FK_COD_ROL', $filters['rol']);
            }

            $usuarios = $query->get();
        }

        return view('reportes.usuarios-rol', [
            'usuarios' => $usuarios,
            'roles'    => $roles,
            'filters'  => $filters,
        ]);
    }

    public function citasNoAtendidas(Request $request)
    {
        $filters = $request->validate([
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin'    => ['nullable', 'date'],
        ]);

        $estadosAusencia = $this->estadosAusencia();
        $citas           = collect();

        if ($this->tableExists('tbl_cita') && !empty($estadosAusencia)) {
            $query = $this->citasQuery()
                ->whereIn('c.ESTADO_CITA', $estadosAusencia);

            if (!empty($filters['fecha_inicio'])) {
                $query->whereDate('c.FEC_CITA', '>=', $filters['fecha_inicio']);
            }

            if (!empty($filters['fecha_fin'])) {
                $query->whereDate('c.FEC_CITA', '<=', $filters['fecha_fin']);
            }

            $citas = $query->orderBy('c.FEC_CITA')->orderBy('c.HOR_CITA')->get();
        }

        return view('reportes.citas-no-atendidas', [
            'citas'   => $citas,
            'filters' => $filters,
        ]);
    }

    public function procesos(Request $request)
    {
        $kpis        = collect();
        $movimientos = collect();

        if ($this->tableExists('tbl_cita')) {
            $kpis = DB::table('tbl_cita as c')
                ->leftJoin('tbl_estado_cita as e', 'e.COD_ESTADO', '=', 'c.ESTADO_CITA')
                ->select([
                    DB::raw('COALESCE(e.NOM_ESTADO, "Sin estado") as estado'),
                    DB::raw('COUNT(*) as total'),
                ])
                ->groupBy('estado')
                ->orderBy('estado')
                ->get()
                ->map(function ($row) {
                    return [
                        'Estado' => $row->estado,
                        'Cant'   => $row->total,
                        'class'  => $this->estadoColor($row->estado),
                    ];
                });
        }

        if ($this->tableExists('tbl_bitacora')) {
            $movimientos = DB::table('tbl_bitacora as b')
                ->leftJoin('tbl_usuario as u', 'u.COD_USUARIO', '=', 'b.FK_COD_USUARIO')
                ->leftJoin('tbl_persona as p', 'p.COD_PERSONA', '=', 'u.FK_COD_PERSONA')
                ->select([
                    'b.created_at',
                    'b.OBJETO',
                    'b.ACCION',
                    'b.DESCRIPCION',
                    DB::raw("CONCAT(p.PRIMER_NOMBRE,' ',p.PRIMER_APELLIDO) as usuario"),
                ])
                ->orderByDesc('b.created_at')
                ->limit(20)
                ->get();
        }

        return view('reportes.procesos', [
            'kpis'        => $kpis,
            'movimientos' => $movimientos,
        ]);
    }

    public function seguridadPermisos(Request $request)
    {
        $roles     = $this->roles();
        $permisos  = collect();

        if ($this->tableExists('tbl_permiso')) {
            $permisos = DB::table('tbl_permiso as p')
                ->join('tbl_rol as r', 'r.COD_ROL', '=', 'p.FK_COD_ROL')
                ->join('tbl_objeto as o', 'o.COD_OBJETO', '=', 'p.FK_COD_OBJETO')
                ->select([
                    'r.COD_ROL',
                    'r.NOM_ROL',
                    'o.NOM_OBJETO',
                    'p.VER',
                    'p.CREAR',
                    'p.EDITAR',
                    'p.ELIMINAR',
                ])
                ->orderBy('r.NOM_ROL')
                ->orderBy('o.NOM_OBJETO')
                ->get()
                ->groupBy('NOM_ROL');
        }

        return view('reportes.seguridad-permisos', [
            'roles'    => $roles,
            'permisos' => $permisos,
        ]);
    }

    private function citasQuery()
    {
        return DB::table('tbl_cita as c')
            ->join('tbl_persona as d', 'd.COD_PERSONA', '=', 'c.FK_COD_DOCTOR')
            ->join('tbl_persona as p', 'p.COD_PERSONA', '=', 'c.FK_COD_PACIENTE')
            ->leftJoin('tbl_estado_cita as e', 'e.COD_ESTADO', '=', 'c.ESTADO_CITA')
            ->select([
                'c.COD_CITA',
                'c.FK_COD_DOCTOR as doctor_persona_id',
                'c.FK_COD_PACIENTE as paciente_persona_id',
                'c.FEC_CITA',
                'c.HOR_CITA',
                'c.HOR_FIN',
                'c.MOT_CITA',
                'c.OBSERVACIONES',
                'c.ESTADO_CITA',
                DB::raw("COALESCE(e.NOM_ESTADO, 'Pendiente') as estado_nombre"),
                DB::raw("CONCAT(p.PRIMER_NOMBRE,' ',p.PRIMER_APELLIDO) as paciente_nombre"),
                DB::raw("CONCAT(d.PRIMER_NOMBRE,' ',d.PRIMER_APELLIDO) as doctor_nombre"),
            ]);
    }

    private function doctores()
    {
        if (
            !$this->tableExists('tbl_usuario') ||
            !$this->tableExists('tbl_rol') ||
            !$this->tableExists('tbl_persona')
        ) {
            return collect();
        }

        return DB::table('tbl_usuario as u')
            ->join('tbl_rol as r', 'r.COD_ROL', '=', 'u.FK_COD_ROL')
            ->join('tbl_persona as p', 'p.COD_PERSONA', '=', 'u.FK_COD_PERSONA')
            ->whereRaw('UPPER(TRIM(r.NOM_ROL)) = ?', ['DOCTOR'])
            ->orderBy('p.PRIMER_NOMBRE')
            ->orderBy('p.PRIMER_APELLIDO')
            ->select([
                'p.COD_PERSONA',
                'p.PRIMER_NOMBRE',
                'p.PRIMER_APELLIDO',
            ])
            ->get();
    }

    private function estadosCita()
    {
        if (!$this->tableExists('tbl_estado_cita')) {
            return collect();
        }

        return DB::table('tbl_estado_cita')
            ->select(['COD_ESTADO', 'NOM_ESTADO'])
            ->orderBy('NOM_ESTADO')
            ->get();
    }

    private function estadosAusencia(): array
    {
        return $this->estadosCita()
            ->filter(function ($row) {
                $nombre = strtoupper($row->NOM_ESTADO ?? '');
                return str_contains($nombre, 'AUSEN') || str_contains($nombre, 'NO AT') || str_contains($nombre, 'NO ASIST');
            })
            ->pluck('COD_ESTADO')
            ->map(fn($id) => (int) $id)
            ->all();
    }

    private function roles()
    {
        if (!$this->tableExists('tbl_rol')) {
            return collect();
        }

        return DB::table('tbl_rol')->select(['COD_ROL', 'NOM_ROL'])->orderBy('NOM_ROL')->get();
    }

    private function tableExists(string $table): bool
    {
        return DB::getSchemaBuilder()->hasTable($table);
    }

    private function countTable(string $table): int
    {
        return $this->tableExists($table) ? (int) DB::table($table)->count() : 0;
    }

    private function countPacientes(): int
    {
        if (!$this->tableExists('tbl_usuario') || !$this->tableExists('tbl_rol')) {
            return 0;
        }

        return (int) DB::table('tbl_usuario as u')
            ->join('tbl_rol as r', 'r.COD_ROL', '=', 'u.FK_COD_ROL')
            ->whereRaw('UPPER(TRIM(r.NOM_ROL)) = ?', ['PACIENTE'])
            ->count();
    }

    private function estadoColor(string $estado): string
    {
        $estado = strtoupper($estado);
        return match (true) {
            str_contains($estado, 'PEND')      => 'bg-warning',
            str_contains($estado, 'CONFIRM')   => 'bg-info',
            str_contains($estado, 'CANCEL')    => 'bg-danger',
            str_contains($estado, 'REPROG')    => 'bg-secondary',
            default                            => 'bg-primary',
        };
    }
}
