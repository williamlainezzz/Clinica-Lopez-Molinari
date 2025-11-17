<?php

namespace App\Http\Controllers;

use App\Models\PreguntaSeguridad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistroPacienteController extends Controller
{
    public function create(Request $request)
    {
        $preguntas = PreguntaSeguridad::where('ESTADO', 1)
            ->orderBy('TEXTO_PREGUNTA')
            ->get();

        $doctorInfo = $this->resolveDoctorInfo($request);

        return view('registro.paciente', [
            'preguntasSeg' => $preguntas,
            'doctorInfo'   => $doctorInfo,
        ]);
    }

    private function resolveDoctorInfo(Request $request): ?array
    {
        $username = trim((string) $request->query('doctor', ''));
        if ($username !== '') {
            $record = DB::table('tbl_usuario as u')
                ->join('tbl_persona as p', 'p.COD_PERSONA', '=', 'u.FK_COD_PERSONA')
                ->join('tbl_rol as r', 'r.COD_ROL', '=', 'u.FK_COD_ROL')
                ->select(
                    'u.FK_COD_PERSONA as persona_id',
                    'u.USR_USUARIO as usuario',
                    'p.PRIMER_NOMBRE',
                    'p.PRIMER_APELLIDO',
                    'r.NOM_ROL'
                )
                ->whereRaw('UPPER(u.USR_USUARIO) = ?', [strtoupper($username)])
                ->whereRaw('UPPER(r.NOM_ROL) LIKE ?', ['%DOCTOR%'])
                ->first();

            if ($record) {
                return [
                    'persona_id' => (int) $record->persona_id,
                    'usuario'    => $record->usuario,
                    'nombre'     => trim(($record->PRIMER_NOMBRE ?? '') . ' ' . ($record->PRIMER_APELLIDO ?? '')),
                ];
            }
        }

        $doctorId = (int) $request->query('doctor_id', 0);
        if ($doctorId > 0) {
            $record = DB::table('tbl_persona as p')
                ->leftJoin('tbl_usuario as u', 'u.FK_COD_PERSONA', '=', 'p.COD_PERSONA')
                ->leftJoin('tbl_rol as r', 'r.COD_ROL', '=', 'u.FK_COD_ROL')
                ->select(
                    'p.COD_PERSONA as persona_id',
                    'u.USR_USUARIO as usuario',
                    'p.PRIMER_NOMBRE',
                    'p.PRIMER_APELLIDO'
                )
                ->where('p.COD_PERSONA', $doctorId)
                ->first();

            if ($record) {
                return [
                    'persona_id' => (int) $record->persona_id,
                    'usuario'    => $record->usuario,
                    'nombre'     => trim(($record->PRIMER_NOMBRE ?? '') . ' ' . ($record->PRIMER_APELLIDO ?? '')),
                ];
            }
        }

        return null;
    }
}
