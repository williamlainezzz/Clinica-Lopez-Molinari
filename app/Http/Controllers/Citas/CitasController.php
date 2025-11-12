<?php

namespace App\Http\Controllers\Citas;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CitasController extends Controller
{
    public function index(Request $r)
    {
        $this->authorize('viewAny', Cita::class);

        $q = Cita::query()
            ->with(['paciente','doctor'])
            ->when($r->filled('doctor'), fn($x)=>$x->where('FK_COD_DOCTOR',$r->doctor))
            ->when($r->filled('paciente'), fn($x)=>$x->where('FK_COD_PACIENTE',$r->paciente))
            ->when($r->filled('fecha'), fn($x)=>$x->where('FEC_CITA',$r->fecha))
            ->when($r->filled('estado'), fn($x)=>$x->where('ESTADO_CITA',$r->estado));

        // Alcance por rol
        $user = $r->user();
        if ($user->esRol('DOCTOR'))   $q->where('FK_COD_DOCTOR', $user->doctorId());
        if ($user->esRol('PACIENTE')) $q->where('FK_COD_PACIENTE', $user->pacienteId());

        $citas = $q->orderBy('FEC_CITA')->orderBy('HOR_CITA')->paginate(15);

        // Catálogo de estados
        $estados = DB::table('tbl_estado_cita')
            ->pluck('NOM_ESTADO','COD_ESTADO')
            ->map(fn($v)=>strtoupper($v))
            ->toArray();

        return view('citas.index', compact('citas','estados'));
    }

    public function show(Cita $cita)
    {
        $this->authorize('view', $cita);
        return response()->json($cita->load(['paciente','doctor']));
    }

    public function exportCsv(Request $r)
    {
        $this->authorize('viewAny', Cita::class);

        $q = Cita::with(['paciente','doctor']);

        $user = $r->user();
        if ($user->esRol('DOCTOR'))   $q->where('FK_COD_DOCTOR', $user->doctorId());
        if ($user->esRol('PACIENTE')) $q->where('FK_COD_PACIENTE', $user->pacienteId());

        // mismos filtros que la tabla si vienen por query string
        if ($r->filled('doctor'))  $q->where('FK_COD_DOCTOR',$r->doctor);
        if ($r->filled('paciente'))$q->where('FK_COD_PACIENTE',$r->paciente);
        if ($r->filled('fecha'))   $q->where('FEC_CITA',$r->fecha);
        if ($r->filled('estado'))  $q->where('ESTADO_CITA',$r->estado);

        $rows = $q->orderBy('FEC_CITA')->orderBy('HOR_CITA')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="citas.csv"',
        ];

        return response()->stream(function() use ($rows) {
            $out = fopen('php://output', 'w');
            // BOM para Excel
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, ['Fecha','Hora','Paciente','Doctor','Estado (cód.)','Motivo']);
            foreach ($rows as $c) {
                fputcsv($out, [
                    $c->FEC_CITA,
                    $c->HOR_CITA,
                    trim(($c->paciente->PRIMER_NOMBRE ?? '').' '.($c->paciente->PRIMER_APELLIDO ?? '')),
                    trim(($c->doctor->PRIMER_NOMBRE ?? '').' '.($c->doctor->PRIMER_APELLIDO ?? '')),
                    $c->ESTADO_CITA,
                    $c->MOT_CITA,
                ]);
            }
            fclose($out);
        }, 200, $headers);
    }
}
