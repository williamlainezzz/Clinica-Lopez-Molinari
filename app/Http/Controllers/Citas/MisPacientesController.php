<?php

namespace App\Http\Controllers\Citas;

use App\Http\Controllers\Controller;
use App\Models\DoctorPaciente;
use Illuminate\Http\Request;

class MisPacientesController extends Controller
{
    public function index(Request $r)
    {
        $doctorId = $r->user()->doctorId();
        abort_if(!$doctorId, 403);

        $asignados = DoctorPaciente::with('paciente')
            ->where('FK_COD_DOCTOR', $doctorId)
            ->where('ACTIVO', 1)
            ->orderBy('FEC_ASIGNACION','desc')
            ->paginate(15);

        return view('doctor.pacientes', compact('asignados'));
    }

    public function asignarExistente(Request $r)
    {
        $doctorId = $r->user()->doctorId();
        abort_if(!$doctorId, 403);

        $data = $r->validate(['FK_COD_PACIENTE' => 'required|integer']);

        DoctorPaciente::firstOrCreate([
            'FK_COD_DOCTOR'   => $doctorId,
            'FK_COD_PACIENTE' => $data['FK_COD_PACIENTE'],
        ], ['ACTIVO'=>1]);

        return back()->with('ok','Paciente asignado');
    }
}
