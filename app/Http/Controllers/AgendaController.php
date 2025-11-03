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
        $rol  = strtoupper(optional($user->rol)->NOM_ROL ?? '');   // ADMIN | DOCTOR | RECEPCIONISTA | PACIENTE

        // Mapeos legibles
        $labels = [
            'ADMIN'         => 'Admin',
            'DOCTOR'        => 'Doctor',
            'RECEPCIONISTA' => 'Recepción',
            'PACIENTE'      => 'Paciente',
        ];
        $rolLabel = $labels[$rol] ?? 'Admin';

        // Nombre de ruta actual
        $routeName = match (strtoupper($section)) {
            'CITAS'       => 'agenda.citas',
            'CALENDARIO'  => 'agenda.calendario',
            default       => 'agenda.reportes',
        };

        // Keys para ubicar parciales
        $sectionKey = match (strtoupper($section)) {
            'CITAS'       => 'citas',
            'CALENDARIO'  => 'calendario',
            default       => 'reportes',
        };
        $rolKey = strtolower($rol ?: 'admin'); // admin|doctor|recepcionista|paciente

        // Nombres de parciales banner/toolbar (se incluyen con @includeIf)
        $bannerPartial  = "modulo-citas.{$sectionKey}.banner-{$rolKey}";
        $toolbarPartial = "modulo-citas.{$sectionKey}.toolbar-{$rolKey}";

        // Filtros GET
        $filters = [
            'desde'  => $request->query('desde'),
            'hasta'  => $request->query('hasta'),
            'estado' => $request->query('estado'),   // Confirmada | Pendiente | Cancelada
            'doctor' => $request->query('doctor'),   // Dr. López | Dra. Molina
        ];

        // Catálogos de filtro DEMO (luego vendrán de la BD)
        $catalogoEstados = ['Confirmada','Pendiente','Cancelada'];
        $catalogoDoctores = ['Dr. López', 'Dra. Molina'];

        // Dataset DEMO (luego se reemplaza por consultas reales acordes a tu esquema)
        $rows = collect([
            ['fecha' => '2025-11-12', 'hora' => '08:30', 'paciente' => 'Ana Rivera',   'doctor' => 'Dr. López',   'estado' => 'Confirmada', 'motivo' => 'Limpieza'],
            ['fecha' => '2025-11-12', 'hora' => '09:00', 'paciente' => 'Carlos Pérez', 'doctor' => 'Dra. Molina', 'estado' => 'Pendiente',  'motivo' => 'Dolor de muela'],
            ['fecha' => '2025-11-12', 'hora' => '10:15', 'paciente' => 'María Gómez',  'doctor' => 'Dr. López',   'estado' => 'Cancelada',  'motivo' => 'Control'],
        ]);

        // Reglas visuales por rol
        $isAdmin  = ($rol === 'ADMIN');
        $isDoc    = ($rol === 'DOCTOR');
        $isRecep  = ($rol === 'RECEPCIONISTA');
        $isPac    = ($rol === 'PACIENTE');

        // Qué columnas/acciones mostrar
        $showDoctorColumn = $isAdmin || $isRecep;               // Admin/Recep ven columna "Doctor"
        $showActions      = $isAdmin || $isRecep || $isDoc;     // Paciente sin acciones
        $readOnly         = $isPac;

        // En DOCTOR filtramos DEMO para que vea "sus" citas (aquí simulamos Dr. López)
        if ($isDoc) {
            $rows = $rows->where('doctor', 'Dr. López');
        }

        // Aplicar filtros GET
        $rows = $rows->filter(function ($row) use ($filters) {
            if ($filters['estado'] && strcasecmp($row['estado'], $filters['estado']) !== 0) return false;
            if ($filters['doctor'] && strcasecmp($row['doctor'], $filters['doctor']) !== 0) return false;
            if ($filters['desde'] && $row['fecha'] < $filters['desde']) return false;
            if ($filters['hasta'] && $row['fecha'] > $filters['hasta']) return false;
            return true;
        })->values()->all();

        // Títulos de página
        $pageTitle = "{$section} · {$rolLabel}";
        $heading   = "{$section} {$rolLabel}";

        return view('modulo-citas.shared.lista', [
            'pageTitle'        => $pageTitle,
            'heading'          => $heading,
            'routeName'        => $routeName,
            'filters'          => $filters,
            'rows'             => $rows,
            'catalogoEstados'  => $catalogoEstados,
            'catalogoDoctores' => $catalogoDoctores,
            'bannerPartial'    => $bannerPartial,
            'toolbarPartial'   => $toolbarPartial,
            'rol'              => $rol,                 // para decidir acciones/etiquetas en Blade
            'showDoctorColumn' => $showDoctorColumn,
            'showActions'      => $showActions,
            'readOnly'         => $readOnly,
            'sectionKey'       => $sectionKey,          // por si lo necesitas en Blade
        ]);
    }
}
