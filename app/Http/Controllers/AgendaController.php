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
        $dataset = collect([
            ['fecha' => '2025-11-12', 'hora' => '08:30', 'paciente' => 'Ana Rivera',   'doctor' => 'Dr. López',   'estado' => 'Confirmada', 'motivo' => 'Limpieza dental'],
            ['fecha' => '2025-11-12', 'hora' => '09:00', 'paciente' => 'Carlos Pérez', 'doctor' => 'Dra. Molina', 'estado' => 'Pendiente',  'motivo' => 'Dolor de muela'],
            ['fecha' => '2025-11-12', 'hora' => '10:15', 'paciente' => 'María Gómez',  'doctor' => 'Dr. López',   'estado' => 'Cancelada',  'motivo' => 'Control general'],
            ['fecha' => '2025-11-13', 'hora' => '11:00', 'paciente' => 'Pedro Díaz',   'doctor' => 'Dra. Molina', 'estado' => 'Confirmada', 'motivo' => 'Ortodoncia'],
            ['fecha' => '2025-11-13', 'hora' => '13:30', 'paciente' => 'Ana Rivera',   'doctor' => 'Dr. López',   'estado' => 'Pendiente',  'motivo' => 'Revisión anual'],
        ]);

        // Reglas visuales por rol
        $isAdmin  = ($rol === 'ADMIN');
        $isDoc    = ($rol === 'DOCTOR');
        $isRecep  = ($rol === 'RECEPCIONISTA');
        $isPac    = ($rol === 'PACIENTE');

        // En DOCTOR filtramos DEMO para que vea "sus" citas (aquí simulamos Dr. López)
        if ($isDoc) {
            $dataset = $dataset->where('doctor', 'Dr. López');
        }

        // En PACIENTE mostramos únicamente sus citas (simulamos paciente "Ana Rivera")
        if ($isPac) {
            $dataset = $dataset->where('paciente', 'Ana Rivera');
        }

        // Aplicar filtros GET
        $filtered = $dataset->filter(function ($row) use ($filters) {
            if ($filters['estado'] && strcasecmp($row['estado'], $filters['estado']) !== 0) return false;
            if ($filters['doctor'] && strcasecmp($row['doctor'], $filters['doctor']) !== 0) return false;
            if ($filters['desde'] && $row['fecha'] < $filters['desde']) return false;
            if ($filters['hasta'] && $row['fecha'] > $filters['hasta']) return false;
            return true;
        })->values()->all();

        $rows = collect($filtered);

        // Qué columnas/acciones mostrar
        $showDoctorColumn = $isAdmin || $isRecep;         // Admin/Recep ven columna "Doctor"
        $canManage        = $isRecep || $isDoc;           // Pueden crear/editar
        $canViewDetail    = true;                         // Todos pueden abrir el detalle
        $showActions      = $canManage || $canViewDetail; // Mostrar columna acciones
        $readOnly         = !$canManage;

        $stats = [
            'total'       => $rows->count(),
            'confirmadas' => $rows->where('estado', 'Confirmada')->count(),
            'pendientes'  => $rows->where('estado', 'Pendiente')->count(),
            'canceladas'  => $rows->where('estado', 'Cancelada')->count(),
        ];

        $nextAppointment = $rows
            ->sortBy(fn ($row) => sprintf('%s %s', $row['fecha'], $row['hora']))
            ->first();

        $rows = $rows->values()->all();

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
            'canManage'        => $canManage,
            'canViewDetail'    => $canViewDetail,
            'stats'            => $stats,
            'nextAppointment'  => $nextAppointment,
            'sectionKey'       => $sectionKey,          // por si lo necesitas en Blade
        ]);
    }
}
