<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AgendaController extends Controller
{
    public function citas(Request $request)      { return $this->render('Citas', $request); }
    public function calendario(Request $request)  { return $this->render('Calendario', $request); }
    public function reportes(Request $request)    { return $this->render('Reportes', $request); }

    private function render(string $section, Request $request)
    {
        $user = auth()->user();
        $rol  = strtoupper(optional($user->rol)->NOM_ROL ?? '');

        // Etiqueta legible por rol para títulos
        $labels = [
            'ADMIN'         => 'Admin',
            'DOCTOR'        => 'Doctor',
            'RECEPCIONISTA' => 'Recepción',
            'PACIENTE'      => 'Paciente',
        ];
        $rolLabel = $labels[$rol] ?? 'Admin';

        // Nombre de ruta actual (por si luego lo usas en filtros GET)
        $routeName = match ($section) {
            'Citas', 'CITAS', 'citas'                => 'agenda.citas',
            'Calendario', 'CALENDARIO', 'calendario' => 'agenda.calendario',
            default                                  => 'agenda.reportes',
        };

        // Lee posibles filtros (aunque tu vista actual no los usa aún)
        $filters = [
            'desde'  => $request->query('desde'),
            'hasta'  => $request->query('hasta'),
            'estado' => $request->query('estado'),
            'doctor' => $request->query('doctor'),
        ];

        // DEMO de filas (sin cambios sustanciales)
        $rows = collect([
            ['fecha' => '2025-11-12', 'hora' => '08:30', 'paciente' => 'Ana Rivera',    'doctor' => 'Dr. López',   'estado' => 'Confirmada', 'motivo' => 'Limpieza'],
            ['fecha' => '2025-11-12', 'hora' => '09:00', 'paciente' => 'Carlos Pérez',  'doctor' => 'Dra. Molina', 'estado' => 'Pendiente',  'motivo' => 'Dolor de muela'],
            ['fecha' => '2025-11-12', 'hora' => '10:15', 'paciente' => 'María Gómez',   'doctor' => 'Dr. López',   'estado' => 'Cancelada',  'motivo' => 'Control'],
        ])
        ->filter(function ($row) use ($filters) {
            if ($filters['estado'] && strcasecmp($row['estado'], $filters['estado']) !== 0) return false;
            if ($filters['doctor'] && strcasecmp($row['doctor'], $filters['doctor']) !== 0) return false;
            if ($filters['desde']  && $row['fecha'] < $filters['desde']) return false;
            if ($filters['hasta']  && $row['fecha'] > $filters['hasta']) return false;
            return true;
        })
        ->values()
        ->all();

        // Variables que tu vista usa
        $titulo  = "{$section} · {$rolLabel}";
        $seccion = strtolower($section);      // citas | calendario | reportes

        return view('modulo-citas.shared.lista', [
            'titulo'    => $titulo,
            'seccion'   => $seccion,
            'routeName' => $routeName,
            'filters'   => $filters,
            'rows'      => $rows,
            'rol'       => strtolower($rol),  // <- para includeIf (admin|doctor|recepcionista|paciente)
        ]);
    }
}
