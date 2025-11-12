<?php

namespace App\Http\Controllers\Citas;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use Illuminate\Http\Request;

class CalendarioController extends Controller
{
    public function view(Request $r)
    {
        return view('citas.calendario'); // Blade simple
    }

    public function events(Request $r)
    {
        $q = Cita::query();
        $user = $r->user();
        if ($user->esRol('DOCTOR'))   $q->where('FK_COD_DOCTOR', $user->doctorId());
        if ($user->esRol('PACIENTE')) $q->where('FK_COD_PACIENTE', $user->pacienteId());
        if ($r->filled('start') && $r->filled('end')) {
            $q->whereBetween('FEC_CITA', [substr($r->start,0,10), substr($r->end,0,10)]);
        }
        $citas = $q->with(['paciente','doctor'])->get();

        $events = $citas->map(function($c){
            return [
                'id'    => $c->COD_CITA,
                'title' => trim(($c->paciente->PRIMER_NOMBRE ?? '').' '.($c->paciente->PRIMER_APELLIDO ?? '')),
                'start' => $c->inicio,
                'end'   => $c->fin ?? null,
                'extendedProps' => [
                    'doctor'   => $c->doctor->PRIMER_APELLIDO ?? null,
                    'estado'   => $c->ESTADO_CITA,
                    'paciente' => $c->FK_COD_PACIENTE,
                ],
            ];
        });

        return response()->json($events);
    }

    public function createFromCalendar(Request $r)
    {
        return app(CitasController::class)->store($r);
    }

    public function updateFromCalendar(Request $r, Cita $cita)
    {
        return app(CitasController::class)->update($r, $cita);
    }
}
