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

        // Etiqueta legible para títulos
        $labels = [
            'ADMIN'         => 'Admin',
            'DOCTOR'        => 'Doctor',
            'RECEPCIONISTA' => 'Recepción',
            'PACIENTE'      => 'Paciente',
        ];
        $rolLabel = $labels[$rol] ?? 'Admin';

        // Ruta de la sección (para que el form GET haga submit sobre sí mismo)
        $routeName = match ($section) {
            'Citas', 'CITAS', 'citas'                => 'agenda.citas',
            'Calendario', 'CALENDARIO', 'calendario' => 'agenda.calendario',
            default                                  => 'agenda.reportes',
        };

        // Filtros desde la query
        $filters = [
            'desde'  => $request->query('desde'),
            'hasta'  => $request->query('hasta'),
            'estado' => $request->query('estado'),
            'doctor' => $request->query('doctor'),
        ];

        // --- Dataset DEMO (luego se cambia por queries reales) ---
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

        // Título y sección
        $titulo  = "{$section} · {$rolLabel}";
        $seccion = strtolower($section);

        // Permisos por rol (qué botones mostrar)
        $perms = match ($rol) {
            'ADMIN' =>        ['view'=>true,'edit'=>true,'delete'=>true,'schedule'=>true],
            'RECEPCIONISTA' =>['view'=>true,'edit'=>true,'delete'=>true,'schedule'=>true],
            'DOCTOR' =>       ['view'=>true,'edit'=>true,'delete'=>false,'schedule'=>false],
            'PACIENTE' =>     ['view'=>true,'edit'=>false,'delete'=>false,'schedule'=>false],
            default =>        ['view'=>true,'edit'=>false,'delete'=>false,'schedule'=>false],
        };

        // Columnas por rol + sección
        // claves disponibles: fecha, hora, paciente, doctor, estado, motivo
        $columns = $this->columnsFor($rol, $seccion);

        return view('modulo-citas.shared.lista', [
            'titulo'    => $titulo,
            'seccion'   => $seccion,
            'routeName' => $routeName,
            'filters'   => $filters,
            'rows'      => $rows,
            'perms'     => $perms,
            'columns'   => $columns,   // <<— NUEVO
        ]);
    }

    /**
     * Devuelve las columnas a mostrar según rol + sección.
     * Cada item: ['key' => 'fecha', 'label' => 'Fecha']
     */
    private function columnsFor(string $rol, string $seccion): array
    {
        // Admin y Recepción ven todo en cualquier sección
        if (in_array($rol, ['ADMIN','RECEPCIONISTA'])) {
            return [
                ['key'=>'fecha','label'=>'Fecha'],
                ['key'=>'hora','label'=>'Hora'],
                ['key'=>'paciente','label'=>'Paciente'],
                ['key'=>'doctor','label'=>'Doctor'],
                ['key'=>'estado','label'=>'Estado'],
                ['key'=>'motivo','label'=>'Motivo'],
            ];
        }

        // Doctor
        if ($rol === 'DOCTOR') {
            // El doctor no necesita columna "Doctor" (ya es él)
            $base = [
                ['key'=>'fecha','label'=>'Fecha'],
                ['key'=>'hora','label'=>'Hora'],
                ['key'=>'paciente','label'=>'Paciente'],
                ['key'=>'estado','label'=>'Estado'],
            ];
            // En reportes agregamos motivo
            if ($seccion === 'reportes') {
                $base[] = ['key'=>'motivo','label'=>'Motivo'];
            }
            return $base;
        }

        // Paciente
        if ($rol === 'PACIENTE') {
            // El paciente no necesita columna "Paciente" (es él)
            $base = [
                ['key'=>'fecha','label'=>'Fecha'],
                ['key'=>'hora','label'=>'Hora'],
                ['key'=>'doctor','label'=>'Doctor'],
                ['key'=>'estado','label'=>'Estado'],
            ];
            // En reportes también mostramos motivo
            if ($seccion === 'reportes') {
                $base[] = ['key'=>'motivo','label'=>'Motivo'];
            }
            return $base;
        }

        // Default (seguro)
        return [
            ['key'=>'fecha','label'=>'Fecha'],
            ['key'=>'hora','label'=>'Hora'],
            ['key'=>'paciente','label'=>'Paciente'],
            ['key'=>'doctor','label'=>'Doctor'],
            ['key'=>'estado','label'=>'Estado'],
        ];
    }
}
