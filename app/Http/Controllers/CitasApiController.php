<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita;

class CitasApiController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $rol  = strtoupper(optional($user->rol)->NOM_ROL ?? '');

        $desde  = $request->query('desde');
        $hasta  = $request->query('hasta');
        $estado = $request->query('estado');
        $doctor = $request->query('doctor'); // nombre completo; si prefieres ID, lo ajustamos

        $q = Cita::with(['paciente','doctor','estado'])
                 ->entreFechas($desde, $hasta)
                 ->porEstado($estado)
                 ->porDoctorNombre($doctor);

        // Restricciones por ROL
        switch ($rol) {
            case 'PACIENTE':
                // asumiendo user->persona_id existe; si es otro campo me dices
                $personaId = optional($user->persona)->ID_PERSONA ?? null;
                if ($personaId) $q->where('ID_PACIENTE', $personaId);
                break;

            case 'DOCTOR':
                $personaId = optional($user->persona)->ID_PERSONA ?? null;
                if ($personaId) $q->where('ID_DOCTOR', $personaId);
                break;

            case 'RECEPCIONISTA':
                // ve todo (o por clínica/sucursal si tienes ese campo)
                break;

            case 'ADMIN':
            default:
                // ve todo
                break;
        }

        $rows = $q->orderBy('FECHA')->orderBy('HORA')->get()
            ->map(function ($c) {
                return [
                    'id'       => $c->ID_CITA,
                    'fecha'    => $c->FECHA,
                    'hora'     => substr((string)$c->HORA, 0, 5),
                    'paciente' => optional($c->paciente)->nombre_completo ?? '—',
                    'doctor'   => optional($c->doctor)->nombre_completo ?? '—',
                    'estado'   => optional($c->estado)->NOMBRE ?? '—',
                    'motivo'   => $c->MOTIVO ?? '—',
                ];
            });

        return response()->json([
            'ok'     => true,
            'count'  => $rows->count(),
            'items'  => $rows,
        ]);
    }

    public function show($id)
    {
        $c = Cita::with(['paciente','doctor','estado'])->find($id);
        if (!$c) {
            return response()->json(['ok'=>false,'msg'=>'No encontrada'], 404);
        }
        return response()->json([
            'ok'   => true,
            'item' => [
                'id'       => $c->ID_CITA,
                'fecha'    => $c->FECHA,
                'hora'     => substr((string)$c->HORA,0,5),
                'paciente' => optional($c->paciente)->nombre_completo ?? '—',
                'doctor'   => optional($c->doctor)->nombre_completo ?? '—',
                'estado'   => optional($c->estado)->NOMBRE ?? '—',
                'motivo'   => $c->MOTIVO ?? '—',
            ],
        ]);
    }
}

