<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CitasApiController extends Controller
{
    /**
     * GET /api/agenda/citas
     * Lista de citas (mock). Acepta filtros opcionales por ?estado= y ?doctor=
     */
    public function index(Request $request): JsonResponse
    {
        // NO asumimos usuario logueado en la API pública
        $estado = $request->query('estado');
        $doctor = $request->query('doctor');

        // Dataset de ejemplo (evita errores mientras no usemos BD aquí)
        $rows = collect([
            ['id' => 1, 'fecha' => '2025-11-12', 'hora' => '08:30', 'paciente' => 'Ana Rivera',   'doctor' => 'Dr. López',   'estado' => 'Confirmada', 'motivo' => 'Limpieza'],
            ['id' => 2, 'fecha' => '2025-11-12', 'hora' => '09:00', 'paciente' => 'Carlos Pérez', 'doctor' => 'Dra. Molina', 'estado' => 'Pendiente',  'motivo' => 'Dolor de muela'],
            ['id' => 3, 'fecha' => '2025-11-12', 'hora' => '10:15', 'paciente' => 'María Gómez',  'doctor' => 'Dr. López',   'estado' => 'Cancelada',  'motivo' => 'Control'],
        ]);

        if ($estado) {
            $rows = $rows->where('estado', $estado);
        }
        if ($doctor) {
            $rows = $rows->where('doctor', $doctor);
        }

        return response()->json([
            'ok'    => true,
            'total' => $rows->count(),
            'data'  => $rows->values()->all(),
        ]);
    }

    /**
     * GET /api/agenda/citas/{id}
     * Detalle de una cita (mock).
     */
    public function show(int $id): JsonResponse
    {
        $rows = [
            1 => ['id' => 1, 'fecha' => '2025-11-12', 'hora' => '08:30', 'paciente' => 'Ana Rivera',   'doctor' => 'Dr. López',   'estado' => 'Confirmada', 'motivo' => 'Limpieza',       'observaciones' => '—'],
            2 => ['id' => 2, 'fecha' => '2025-11-12', 'hora' => '09:00', 'paciente' => 'Carlos Pérez', 'doctor' => 'Dra. Molina', 'estado' => 'Pendiente',  'motivo' => 'Dolor de muela', 'observaciones' => 'Trae Rx.'],
            3 => ['id' => 3, 'fecha' => '2025-11-12', 'hora' => '10:15', 'paciente' => 'María Gómez',  'doctor' => 'Dr. López',   'estado' => 'Cancelada',  'motivo' => 'Control',        'observaciones' => 'Canceló por viaje'],
        ];

        if (! isset($rows[$id])) {
            return response()->json(['ok' => false, 'message' => 'Cita no encontrada'], 404);
        }

        return response()->json(['ok' => true, 'data' => $rows[$id]]);
    }

    /**
     * GET /api/agenda/doctores
     * Lista de doctores (mock).
     */
    public function doctores(): JsonResponse
    {
        return response()->json([
            'ok'   => true,
            'data' => [
                ['id' => 1, 'nombre' => 'Dr. López'],
                ['id' => 2, 'nombre' => 'Dra. Molina'],
            ],
        ]);
    }

    /**
     * GET /api/agenda/estados
     * Estados de cita (mock).
     */
    public function estados(): JsonResponse
    {
        return response()->json([
            'ok'   => true,
            'data' => [
                ['id' => 'Confirmada', 'nombre' => 'Confirmada'],
                ['id' => 'Pendiente',  'nombre' => 'Pendiente'],
                ['id' => 'Cancelada',  'nombre' => 'Cancelada'],
            ],
        ]);
    }
}
