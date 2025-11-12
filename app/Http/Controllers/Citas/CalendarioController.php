<?php

namespace App\Http\Controllers\Citas;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class CalendarioController extends Controller
{
    /** Vista de calendario (por rol) */
    public function view(Request $r)
    {
        $this->authorize('viewAny', Cita::class);

        // Catálogo de estados para selects del modal
        $estados = DB::table('tbl_estado_cita')
            ->orderBy('COD_ESTADO')
            ->pluck('NOM_ESTADO', 'COD_ESTADO')
            ->map(fn ($v) => strtoupper($v))
            ->toArray();

        return view('citas.calendario', compact('estados'));
    }

    /** Eventos para FullCalendar (JSON) */
    public function events(Request $r)
    {
        $this->authorize('viewAny', Cita::class);

        $q = Cita::query()->with(['paciente', 'doctor']);

        // Alcance por rol
        $user = $r->user();
        if ($user->esRol('DOCTOR'))   $q->where('FK_COD_DOCTOR', $user->doctorId());
        if ($user->esRol('PACIENTE')) $q->where('FK_COD_PACIENTE', $user->pacienteId());

        // Rango que manda FullCalendar (opcional)
        if ($r->filled('start')) $q->where('FEC_CITA', '>=', substr($r->start, 0, 10));
        if ($r->filled('end'))   $q->where('FEC_CITA', '<=', substr($r->end, 0, 10));

        // Colores por estado
        $palette = [
            'PENDIENTE'  => '#f59e0b', // amber
            'CONFIRMADA' => '#0ea5e9', // sky
            'EN_CURSO'   => '#3b82f6', // blue
            'COMPLETADA' => '#10b981', // emerald
            'CANCELADA'  => '#ef4444', // red
            'NO_SHOW'    => '#111827', // gray-900
        ];

        $estadoNom = DB::table('tbl_estado_cita')
            ->pluck('NOM_ESTADO', 'COD_ESTADO')
            ->map(fn($v)=>strtoupper($v))
            ->toArray();

        $events = $q->orderBy('FEC_CITA')->orderBy('HOR_CITA')->get()->map(function ($c) use ($estadoNom, $palette, $user) {
            $nomEstado = $estadoNom[$c->ESTADO_CITA] ?? 'PENDIENTE';
            $color = $palette[$nomEstado] ?? '#6b7280';

            // Título: depende del rol
            if ($user->esRol('DOCTOR')) {
                $title = trim(($c->paciente->PRIMER_NOMBRE ?? '').' '.($c->paciente->PRIMER_APELLIDO ?? ''));
            } elseif ($user->esRol('PACIENTE')) {
                $title = trim(($c->doctor->PRIMER_NOMBRE ?? '').' '.($c->doctor->PRIMER_APELLIDO ?? ''));
            } else {
                // ADMIN / RECEPCIONISTA
                $p = trim(($c->paciente->PRIMER_NOMBRE ?? '').' '.($c->paciente->PRIMER_APELLIDO ?? ''));
                $d = trim(($c->doctor->PRIMER_NOMBRE ?? '').' '.($c->doctor->PRIMER_APELLIDO ?? ''));
                $title = "$p ⟷ $d";
            }

            return [
                'id'            => (string) $c->COD_CITA,
                'title'         => $title.' • '.$nomEstado,
                'start'         => "{$c->FEC_CITA}T{$c->HOR_CITA}",
                'allDay'        => false,
                'backgroundColor'=> $color,
                'borderColor'   => $color,
                'extendedProps' => [
                    'estado'  => $nomEstado,
                    'motivo'  => $c->MOT_CITA,
                    'paciente'=> $c->paciente?->PRIMER_NOMBRE.' '.$c->paciente?->PRIMER_APELLIDO,
                    'doctor'  => $c->doctor?->PRIMER_NOMBRE.' '.$c->doctor?->PRIMER_APELLIDO,
                ],
            ];
        });

        return response()->json($events);
    }

    /** Crear cita desde calendario */
    public function createFromCalendar(Request $r)
    {
        $this->authorize('create', Cita::class);

        $data = $r->validate([
            'FK_COD_PACIENTE' => 'required|integer',
            'FK_COD_DOCTOR'   => 'required|integer',
            'start'           => 'required|date', // ISO-8601
            'MOT_CITA'        => 'nullable|string|max:255',
            'ESTADO_CITA'     => 'nullable|integer', // default pendiente
        ]);

        $start = Carbon::parse($data['start']); // 2025-11-12T09:00:00
        $cita = Cita::create([
            'FK_COD_PACIENTE' => $data['FK_COD_PACIENTE'],
            'FK_COD_DOCTOR'   => $data['FK_COD_DOCTOR'],
            'FEC_CITA'        => $start->toDateString(),
            'HOR_CITA'        => $start->format('H:i:s'),
            'MOT_CITA'        => $data['MOT_CITA'] ?? null,
            'ESTADO_CITA'     => $data['ESTADO_CITA'] ?? 1, // 1 = PENDIENTE
        ]);

        return response()->json(['ok'=>true, 'id'=>$cita->COD_CITA], 201);
    }

    /** Actualizar cita desde calendario (drag/drop o modal) */
    public function updateFromCalendar(Request $r, Cita $cita)
    {
        // Si sólo cambia estado, valida con changeStatus; si cambia fecha/hora, con update
        if ($r->filled('ESTADO_CITA') && !$r->filled('start')) {
            $this->authorize('changeStatus', $cita);
        } else {
            $this->authorize('update', $cita);
        }

        $rules = [
            'start'       => 'nullable|date',
            'MOT_CITA'    => 'nullable|string|max:255',
            'ESTADO_CITA' => 'nullable|integer',
        ];
        $data = $r->validate($rules);

        if (!empty($data['start'])) {
            $dt = Carbon::parse($data['start']);
            $cita->FEC_CITA = $dt->toDateString();
            $cita->HOR_CITA = $dt->format('H:i:s');
        }

        if (array_key_exists('MOT_CITA', $data)) {
            $cita->MOT_CITA = $data['MOT_CITA'];
        }

        if (array_key_exists('ESTADO_CITA', $data)) {
            $cita->ESTADO_CITA = (int)$data['ESTADO_CITA'];
        }

        $cita->save();

        return response()->json(['ok'=>true]);
    }
}
