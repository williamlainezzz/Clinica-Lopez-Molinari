<?php

namespace App\Http\Controllers\Citas;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CalendarioController extends Controller
{
    /** =========================
     *  VISTA
     *  ========================= */
    public function view(Request $r)
    {
        $estados = DB::table('tbl_estado_cita')
            ->pluck('NOM_ESTADO', 'COD_ESTADO')
            ->map(fn ($v) => strtoupper($v))
            ->toArray();

        return view('citas.calendario', compact('estados'));
    }

    /** =========================
     *  EVENTOS (FullCalendar)
     *  GET /citas/calendario/events?start=YYYY-MM-DD&end=YYYY-MM-DD
     *  ========================= */
    public function events(Request $r)
    {
        $user = $r->user();

        $q = Cita::query()->with(['paciente', 'doctor']);

        // Rango sugerido por FullCalendar (opcional)
        // Añadimos tolerancia de ±1 día para cortes de zona horaria
        if ($r->filled('start')) {
            $start = Carbon::parse($r->start)->subDay()->toDateString();
            $q->where('FEC_CITA', '>=', $start);
        }
        if ($r->filled('end')) {
            $end = Carbon::parse($r->end)->addDay()->toDateString();
            $q->where('FEC_CITA', '<=', $end);
        }

        // Alcance por rol
        if (method_exists($user, 'esRol')) {
            if ($user->esRol('DOCTOR') && method_exists($user, 'doctorId')) {
                $q->where('FK_COD_DOCTOR', $user->doctorId());
            } elseif ($user->esRol('PACIENTE') && method_exists($user, 'pacienteId')) {
                $q->where('FK_COD_PACIENTE', $user->pacienteId());
            }
            // ADMIN/RECEPCIONISTA ven todo
        }

        $rows = $q->orderBy('FEC_CITA')->orderBy('HOR_CITA')->get();

        // Catálogo para mostrar nombre de estado y colorear
        $mapEstados = DB::table('tbl_estado_cita')
            ->pluck('NOM_ESTADO', 'COD_ESTADO')
            ->map(fn ($v) => strtoupper($v))
            ->toArray();

        $events = $rows->map(function ($c) use ($mapEstados) {
            $start = Carbon::parse("{$c->FEC_CITA} {$c->HOR_CITA}");
            $end   = (clone $start)->addHour();

            $pacNom = trim(($c->paciente->PRIMER_NOMBRE ?? '') . ' ' . ($c->paciente->PRIMER_APELLIDO ?? ''));
            $docNom = trim(($c->doctor->PRIMER_NOMBRE ?? '')   . ' ' . ($c->doctor->PRIMER_APELLIDO ?? ''));
            $estadoNom = $mapEstados[$c->ESTADO_CITA] ?? '';
            $color = $this->colorPorEstado($estadoNom);

            return [
                'id'    => (string) $c->COD_CITA,
                'title' => $pacNom !== '' ? $pacNom : ($c->MOT_CITA ?: 'Cita'),
                'start' => $start->toIso8601String(),
                'end'   => $end->toIso8601String(),
                'backgroundColor' => $color,
                'borderColor'     => $color,
                'extendedProps'   => [
                    'estado'   => $estadoNom,
                    'motivo'   => $c->MOT_CITA,
                    'paciente' => $pacNom,
                    'doctor'   => $docNom,
                ],
            ];
        });

        return response()->json($events);
    }

    /** =========================
     *  CREAR DESDE CALENDARIO (click/drag)
     *  POST /citas/calendario/event
     *  ========================= */
    public function createFromCalendar(Request $r)
    {
        $this->authorize('create', Cita::class);

        // Soporta startISO o start
        $iso = $r->input('startISO', $r->input('start'));
        if (!$iso) {
            return response('start requerido', 422);
        }

        try {
            $dt = Carbon::parse($iso);
        } catch (\Throwable $e) {
            return response('start inválido', 422);
        }

        $c = Cita::create([
            'FK_COD_PACIENTE' => $r->integer('FK_COD_PACIENTE') ?: null,
            'FK_COD_DOCTOR'   => $r->integer('FK_COD_DOCTOR')   ?: null,
            'FEC_CITA'        => $dt->toDateString(),
            'HOR_CITA'        => $dt->format('H:i:s'),
            'MOT_CITA'        => $r->input('MOT_CITA', 'Cita'),
            'ESTADO_CITA'     => $r->integer('ESTADO_CITA') ?: 1, // 1 = PENDIENTE
        ]);

        return response()->json(['ok' => true, 'id' => $c->COD_CITA], 201);
    }

    /** =========================
     *  REPROGRAMAR / EDITAR
     *  PATCH /citas/calendario/event/{cita}
     *  ========================= */
    public function updateFromCalendar(Request $r, Cita $cita)
    {
        $this->authorize('update', $cita);

        if ($r->filled('start')) {
            try {
                $dt = Carbon::parse($r->start);
                $cita->FEC_CITA = $dt->toDateString();
                $cita->HOR_CITA = $dt->format('H:i:s');
            } catch (\Throwable $e) {
                return response('start inválido', 422);
            }
        }

        if ($r->filled('MOT_CITA'))    $cita->MOT_CITA     = $r->MOT_CITA;
        if ($r->filled('ESTADO_CITA')) $cita->ESTADO_CITA  = (int) $r->ESTADO_CITA;

        $cita->save();

        return response()->json(['ok' => true]);
    }

    /** Color por estado (ADMINLTE palette) */
    private function colorPorEstado(string $estadoUpper): string
    {
        return match (strtoupper($estadoUpper)) {
            'CONFIRMADA' => '#17a2b8', // info
            'PENDIENTE'  => '#ffc107', // warning
            'CANCELADA'  => '#dc3545', // danger
            'EN_CURSO'   => '#007bff', // primary
            'COMPLETADA' => '#28a745', // success
            'NO_SHOW'    => '#6c757d', // secondary
            default      => '#3788d8', // default FC
        };
    }
}
