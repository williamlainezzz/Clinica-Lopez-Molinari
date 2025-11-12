<?php

namespace App\Http\Controllers\Citas;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CalendarioController extends Controller
{
    public function view()
    {
        // 👉 Enviar catálogo de estados a la vista del calendario
        $estados = DB::table('tbl_estado_cita')
            ->pluck('NOM_ESTADO', 'COD_ESTADO')
            ->map(fn ($v) => strtoupper($v))
            ->toArray();

        return view('citas.calendario', compact('estados'));
    }

    public function events(Request $r)
    {
        $this->authorize('viewAny', Cita::class);

        $q = Cita::with(['paciente','doctor']);

        // Alcance por rol
        $u = $r->user();
        if ($u->esRol('DOCTOR'))   $q->where('FK_COD_DOCTOR', $u->doctorId());
        if ($u->esRol('PACIENTE')) $q->where('FK_COD_PACIENTE', $u->pacienteId());

        // Rango que envía FullCalendar
        if ($r->filled('start')) $q->where('FEC_CITA', '>=', substr($r->start, 0, 10));
        if ($r->filled('end'))   $q->where('FEC_CITA', '<=', substr($r->end,   0, 10));

        $rows    = $q->orderBy('FEC_CITA')->orderBy('HOR_CITA')->get();
        $estados = DB::table('tbl_estado_cita')->pluck('NOM_ESTADO','COD_ESTADO')->map(fn($v)=>strtoupper($v));

        $events = $rows->map(function (Cita $c) use ($estados) {
            $pac = trim(($c->paciente->PRIMER_NOMBRE ?? '').' '.($c->paciente->PRIMER_APELLIDO ?? ''));
            $doc = trim(($c->doctor->PRIMER_NOMBRE   ?? '').' '.($c->doctor->PRIMER_APELLIDO   ?? ''));
            return [
                'id'    => $c->COD_CITA,
                'title' => ($pac ?: 'Paciente').' · '.($estados[$c->ESTADO_CITA] ?? '—'),
                'start' => "{$c->FEC_CITA}T{$c->HOR_CITA}",
                'extendedProps' => [
                    'paciente' => $pac,
                    'doctor'   => $doc,
                    'motivo'   => $c->MOT_CITA,
                    'estado'   => $estados[$c->ESTADO_CITA] ?? null,
                ],
            ];
        });

        return response()->json($events);
    }

    public function createFromCalendar(Request $r)
    {
        $this->authorize('create', Cita::class);

        // Acepta "start" (ISO) o los campos separados
        $start = $r->input('start');
        if ($start && strlen($start) >= 16) {
            $fec = substr($start, 0, 10);        // YYYY-MM-DD
            $hor = substr($start, 11, 5);        // HH:MM
            $r->merge(['FEC_CITA' => $fec, 'HOR_CITA' => $hor]);
        }

        $data = $r->validate([
            'FK_COD_PACIENTE' => 'required|integer',
            'FK_COD_DOCTOR'   => 'required|integer',
            'FEC_CITA'        => 'required|date',
            'HOR_CITA'        => 'required|date_format:H:i',
            'MOT_CITA'        => 'nullable|string|max:255',
            'ESTADO_CITA'     => 'required|integer',
        ]);

        $cita = Cita::create($data); // respeta UNIQUE uq_cita_slot

        return response()->json(['ok' => true, 'id' => $cita->COD_CITA], 201);
    }

    public function updateFromCalendar(Cita $cita, Request $r)
    {
        $this->authorize('update', $cita);

        // Si llega "start" (ISO) desde drag&drop, separa
        if ($r->filled('start')) {
            $iso = $r->input('start');
            if (strlen($iso) >= 16) {
                $r->merge([
                    'FEC_CITA' => substr($iso, 0, 10),
                    'HOR_CITA' => substr($iso, 11, 5),
                ]);
            }
        }

        $data = $r->validate([
            'FEC_CITA'    => 'nullable|date',
            'HOR_CITA'    => 'nullable|date_format:H:i',
            'ESTADO_CITA' => 'nullable|integer',
            'MOT_CITA'    => 'nullable|string|max:255',
        ]);

        $cita->fill(array_filter($data, fn ($v) => !is_null($v)))->save();

        return response()->json(['ok' => true]);
    }
}
