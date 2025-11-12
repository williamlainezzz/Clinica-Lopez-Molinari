<?php

namespace App\Http\Controllers\Citas;

use App\Http\Controllers\Controller;
use App\Models\InvitacionPaciente;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InvitacionController extends Controller
{
    public function store(Request $r)
    {
        $doctorId = $r->user()->doctorId();
        abort_if(!$doctorId, 403);

        $token = Str::random(64);

        $inv = InvitacionPaciente::create([
            'FK_COD_DOCTOR'   => $doctorId,
            'TOKEN'           => $token,
            'USOS_MAX'        => $r->integer('USOS_MAX', 1),
            'USOS_ACTUALES'   => 0,
            'EXPIRA_EN'       => now()->addDays($r->integer('dias', 7)),
            'ACTIVA'          => 1,
            'CREATED_AT'      => now(),
        ]);

        $url = route('signup.invite', ['token'=>$token]);

        return response()->json(['ok'=>true,'link'=>$url,'id'=>$inv->COD_INVITACION]);
    }

    public function qr(InvitacionPaciente $id)
    {
        $url = route('signup.invite', ['token'=>$id->TOKEN]);
        return response()->json(['link'=>$url]);
    }

    public function showSignup($token)
    {
        $inv = InvitacionPaciente::where('TOKEN',$token)
            ->where('ACTIVA',1)
            ->where('EXPIRA_EN','>=',now())
            ->firstOrFail();

        return view('public.registro-paciente', ['token'=>$token,'doctor'=>$inv->FK_COD_DOCTOR]);
    }

    public function submitSignup(Request $r, $token)
    {
        // Implementaremos el registro con asignación en el siguiente paso
        return response()->json(['ok'=>true]);
    }
}
