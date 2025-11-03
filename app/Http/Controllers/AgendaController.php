<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AgendaController extends Controller
{
    public function citas(Request $request)      { return $this->render('citas', $request); }
    public function calendario(Request $request)  { return $this->render('calendario', $request); }
    public function reportes(Request $request)    { return $this->render('reportes', $request); }

    /**
     * Renderiza la vista compartida con título dinámico por rol/sección y filtros GET.
     */
    private function render(string $seccion, Request $request)
    {
        $user = auth()->user();
        $rol  = strtoupper(optional($user->rol)->NOM_ROL ?? '');

        // Mapa de títulos por ROL y SECCIÓN
        $map = [
            'ADMIN' => [
                'citas'      => 'Citas · Admin',
                'calendario' => 'Calendario · Admin',
                'reportes'   => 'Reportes · Admin',
            ],
            'DOCTOR' => [
                'citas'      => 'Citas · Doctor',
                'calendario' => 'Calendario · Doctor',
                'reportes'   => 'Reportes · Doctor',
            ],
            'RECEPCIONISTA' => [
                'citas'      => 'Citas Recepción',
                'calendario' => 'Calendario Recepción',
                'reportes'   => 'Reportes Recepción',
            ],
            'PACIENTE' => [
                'citas'      => 'Citas Paciente',
                'calendario' => 'Calendario Paciente',
                'reportes'   => 'Historial Paciente',
            ],
        ];

        $titulo = $map[$rol][$seccion] ?? 'Agenda';

        // Nombre de ruta actual: para que el form GET envíe a sí mismo
        $routeName = match (strtolower($seccion)) {
            'citas'      => 'agenda.citas',
            'calendario' => 'agenda.calendario',
            default      => 'agenda.reportes',
        };

        // Filtros desde la query
        $filters = [
            'desde'  => $request->query('desde'),
            'hasta'  => $request->query('hasta'),
            'estado' => $request->query('estado'), // Confirmada | Pendiente | Cancelada | null
            'doctor' => $request->query('doctor'), // Dr. López | Dra. Molina | null
        ];

        // --- DATASET DEMO (reemplazar luego por consultas reales) ---
        $rows = collect([
            ['fecha' => '2025-11-12', 'hora' => '08:30', 'paciente' => 'Ana Rivera',    'doctor' => 'Dr. López',   'estado' => 'Confirmada', 'motivo' => 'Limpieza'],
            ['fecha' => '2025-11-12', 'hora' => '09:00', 'paciente' => 'Carlos Pérez',  'doctor' => 'Dra. Molina', 'estado' => 'Pendiente',  'motivo' => 'Dolor de muela'],
            ['fecha' => '2025-11-12', 'hora' => '10:15', 'paciente' => 'María Gómez',   'doctor' => 'Dr. López',   'estado' => 'Cancelada',  'motivo' => 'Control'],
        ])
        ->filter(function ($row) use ($filters) {
            if ($filters['estado'] && strcasecmp($row['estado'], $filters['estado']) !== 0) return false;
            if ($filters['doctor'] && strcasecmp($row['doctor'], $filters['doctor']) !== 0) return false;
            if ($filters['desde'] && $row['fecha'] < $filters['desde']) return false;
            if ($filters['hasta'] && $row['fecha'] > $filters['hasta']) return false;
            return true;
        })
        ->values()
        ->all();

        return view('modulo-citas.shared.lista', [
            'titulo'    => $titulo,
            'seccion'   => strtolower($seccion),
            'routeName' => $routeName,
            'filters'   => $filters,
            'rows'      => $rows,
        ]);
    }
}
