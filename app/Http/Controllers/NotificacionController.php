<?php

namespace App\Http\Controllers;

use App\Services\NotificacionCitaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NotificacionController extends Controller
{
    private array $tipos = [
        'CREACION'        => 'Creación de cita',
        'RECORDATORIO_24H'=> 'Recordatorio 24 horas',
        'RECORDATORIO_1H' => 'Recordatorio 1 hora',
        'MANUAL'          => 'Notificación manual',
    ];

    public function index(Request $request, NotificacionCitaService $service)
    {
        $user = $request->user();

        if (!Schema::hasTable('tbl_notificacion') || !Schema::hasTable('tbl_cita')) {
            return view('notificaciones.index', [
                'notificaciones' => collect(),
                'filtros'        => $request->all(),
                'tipos'          => $this->tipos,
                'estados'        => $this->estadosCita(),
                'paginacion'     => null,
            ]);
        }

        $query = $service->baseNotificacionQuery()
            ->select([
                'n.COD_NOTIFICACION',
                'n.FEC_ENVIO',
                'n.MSG_NOTIFICACION',
                'n.TIPO_NOTIFICACION',
                'n.LEIDA',
                'c.COD_CITA',
                'c.FEC_CITA',
                'c.HOR_CITA',
                'c.ESTADO_CITA',
                'e.NOM_ESTADO as estado_cita_nombre',
                DB::raw("CONCAT(p.PRIMER_NOMBRE,' ',p.PRIMER_APELLIDO) as paciente_nombre"),
                DB::raw("CONCAT(d.PRIMER_NOMBRE,' ',d.PRIMER_APELLIDO) as doctor_nombre"),
            ])
            ->orderByDesc('n.FEC_ENVIO');

        $service->aplicarFiltroPorRol($query, $user);
        $this->aplicarFiltros($query, $request);

        $idsPorMarcar = (clone $query)
            ->where('n.LEIDA', 0)
            ->pluck('n.COD_NOTIFICACION');

        if ($idsPorMarcar->isNotEmpty()) {
            DB::table('tbl_notificacion')
                ->whereIn('COD_NOTIFICACION', $idsPorMarcar)
                ->update(['LEIDA' => 1]);
        }

        $notificaciones = $query->paginate(10)->appends($request->query());

        return view('notificaciones.index', [
            'notificaciones' => $notificaciones,
            'filtros'        => $request->all(),
            'tipos'          => $this->tipos,
            'estados'        => $this->estadosCita(),
            'paginacion'     => $notificaciones,
        ]);
    }

    private function aplicarFiltros($query, Request $request): void
    {
        if ($request->filled('fecha_desde')) {
            $query->whereDate('n.FEC_ENVIO', '>=', $request->input('fecha_desde'));
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('n.FEC_ENVIO', '<=', $request->input('fecha_hasta'));
        }

        if ($request->filled('estado_cita')) {
            $query->where('c.ESTADO_CITA', $request->input('estado_cita'));
        }

        if ($request->filled('tipo') && array_key_exists($request->input('tipo'), $this->tipos)) {
            $query->where('n.TIPO_NOTIFICACION', $request->input('tipo'));
        }

        if ($request->filled('q')) {
            $texto = '%' . trim($request->input('q')) . '%';
            $query->where(function ($q) use ($texto) {
                $q->where('n.MSG_NOTIFICACION', 'like', $texto)
                    ->orWhere(DB::raw("CONCAT(p.PRIMER_NOMBRE,' ',p.PRIMER_APELLIDO)"), 'like', $texto)
                    ->orWhere(DB::raw("CONCAT(d.PRIMER_NOMBRE,' ',d.PRIMER_APELLIDO)"), 'like', $texto);
            });
        }
    }

    private function estadosCita(): array
    {
        if (!Schema::hasTable('tbl_estado_cita')) {
            return [];
        }

        return DB::table('tbl_estado_cita')
            ->orderBy('NOM_ESTADO')
            ->pluck('NOM_ESTADO', 'COD_ESTADO')
            ->toArray();
    }
}
