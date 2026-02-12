<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Notifications\PasswordChangedNotification;
use App\Support\PasswordSecurityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $q      = trim($request->input('q', ''));
        $rolId  = $request->input('rol_id');
        $estado = $request->input('estado');

        $correoSub = DB::table('tbl_correo')
            ->select('FK_COD_PERSONA', DB::raw('MIN(CORREO) as CORREO'))
            ->groupBy('FK_COD_PERSONA');

        $telSub = DB::table('tbl_telefono')
            ->select('FK_COD_PERSONA', DB::raw('MIN(NUM_TELEFONO) as TELEFONO'))
            ->groupBy('FK_COD_PERSONA');

        $nombreCompleto = "CONCAT_WS(' ', p.PRIMER_NOMBRE, NULLIF(p.SEGUNDO_NOMBRE,''), p.PRIMER_APELLIDO, NULLIF(p.SEGUNDO_APELLIDO,''))";

        $usuarios = DB::table('tbl_usuario as u')
            ->join('tbl_persona as p', 'p.COD_PERSONA', '=', 'u.FK_COD_PERSONA')
            ->leftJoin('tbl_rol as r', 'r.COD_ROL', '=', 'u.FK_COD_ROL')
            ->leftJoinSub($correoSub, 'c', fn ($join) => $join->on('c.FK_COD_PERSONA', '=', 'p.COD_PERSONA'))
            ->leftJoinSub($telSub, 't', fn ($join) => $join->on('t.FK_COD_PERSONA', '=', 'p.COD_PERSONA'))
            ->select([
                'u.COD_USUARIO',
                'u.USR_USUARIO',
                DB::raw("$nombreCompleto as nombre"),
                'u.FK_COD_ROL',
                'r.NOM_ROL as rol',
                DB::raw("COALESCE(c.CORREO, '') as correo"),
                DB::raw("COALESCE(c.CORREO, '') as EMAIL"),
                DB::raw("COALESCE(t.TELEFONO, '') as telefono"),
                'u.ESTADO_USUARIO',
                'u.ESTADO_USUARIO as estado_id',
                DB::raw("CASE u.ESTADO_USUARIO WHEN 1 THEN 'ACTIVO' WHEN 0 THEN 'INACTIVO' ELSE CONCAT('EST-', u.ESTADO_USUARIO) END as estado"),
            ])
            ->when($q !== '', function ($qb) use ($q, $nombreCompleto) {
                $qb->where(function ($w) use ($q, $nombreCompleto) {
                    $w->where('u.USR_USUARIO', 'like', "%{$q}%")
                        ->orWhere(DB::raw($nombreCompleto), 'like', "%{$q}%")
                        ->orWhere('c.CORREO', 'like', "%{$q}%");
                });
            })
            ->when(!empty($rolId), fn ($qb) => $qb->where('u.FK_COD_ROL', $rolId))
            ->when($estado !== null && $estado !== '', fn ($qb) => $qb->where('u.ESTADO_USUARIO', $estado))
            ->orderBy('p.PRIMER_APELLIDO')
            ->orderBy('p.PRIMER_NOMBRE')
            ->paginate(10)
            ->appends($request->query());

        $roles = DB::table('tbl_rol')->select('COD_ROL', 'NOM_ROL')->orderBy('NOM_ROL')->get();

        $estados = collect([
            (object) ['COD_ESTADO_USUARIO' => 1, 'ESTADO_USUARIO' => 'ACTIVO'],
            (object) ['COD_ESTADO_USUARIO' => 0, 'ESTADO_USUARIO' => 'INACTIVO'],
        ]);

        $filtros = ['q' => $q, 'rol_id' => $rolId, 'estado' => $estado];

        return view('seguridad.usuarios.index', compact('usuarios', 'roles', 'estados', 'filtros'));
    }

    public function create()
    {
        $personas = DB::table('tbl_persona')
            ->select('COD_PERSONA', DB::raw("CONCAT_WS(' ', PRIMER_APELLIDO, SEGUNDO_APELLIDO, PRIMER_NOMBRE, SEGUNDO_NOMBRE) as nombre"))
            ->orderBy('PRIMER_APELLIDO')->orderBy('PRIMER_NOMBRE')->get();

        $roles = DB::table('tbl_rol')->select('COD_ROL', 'NOM_ROL')->orderBy('NOM_ROL')->get();

        $estados = collect([(object) ['id' => 1, 'txt' => 'ACTIVO'], (object) ['id' => 0, 'txt' => 'INACTIVO']]);

        return view('seguridad.usuarios.create', compact('personas', 'roles', 'estados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'FK_COD_PERSONA' => ['required', 'integer', 'exists:tbl_persona,COD_PERSONA'],
            'USR_USUARIO'    => ['required', 'string', 'max:50', 'unique:tbl_usuario,USR_USUARIO'],
            'FK_COD_ROL'     => ['required', 'integer', 'exists:tbl_rol,COD_ROL'],
            'ESTADO_USUARIO' => ['required', 'in:0,1'],
            'password'       => [\Illuminate\Validation\Rules\Password::defaults()],
        ]);

        DB::table('tbl_usuario')->insert([
            'FK_COD_PERSONA' => (int) $request->FK_COD_PERSONA,
            'USR_USUARIO' => $request->USR_USUARIO,
            'PWD_USUARIO' => Hash::make($request->password),
            'FK_COD_ROL' => (int) $request->FK_COD_ROL,
            'ESTADO_USUARIO' => (int) $request->ESTADO_USUARIO,
        ]);

        return redirect()->route('seguridad.usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit($id)
    {
        $u = DB::table('tbl_usuario')->where('COD_USUARIO', $id)->first();
        abort_if(!$u, 404);

        $personas = DB::table('tbl_persona')
            ->select('COD_PERSONA', DB::raw("CONCAT_WS(' ', PRIMER_APELLIDO, SEGUNDO_APELLIDO, PRIMER_NOMBRE, SEGUNDO_NOMBRE) as nombre"))
            ->orderBy('PRIMER_APELLIDO')->orderBy('PRIMER_NOMBRE')->get();

        $roles = DB::table('tbl_rol')->select('COD_ROL', 'NOM_ROL')->orderBy('NOM_ROL')->get();
        $estados = collect([(object) ['id' => 1, 'txt' => 'ACTIVO'], (object) ['id' => 0, 'txt' => 'INACTIVO']]);

        return view('seguridad.usuarios.edit', compact('u', 'personas', 'roles', 'estados'));
    }

    public function update(Request $request, $id, PasswordSecurityService $passwordSecurityService)
    {
        $request->validate([
            'FK_COD_PERSONA' => ['required', 'integer', 'exists:tbl_persona,COD_PERSONA'],
            'USR_USUARIO' => ['required', 'string', 'max:50', "unique:tbl_usuario,USR_USUARIO,{$id},COD_USUARIO"],
            'FK_COD_ROL' => ['required', 'integer', 'exists:tbl_rol,COD_ROL'],
            'ESTADO_USUARIO' => ['required', 'in:0,1'],
            'password' => ['nullable', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $plainPassword = null;

        DB::transaction(function () use ($request, $id, &$plainPassword, $passwordSecurityService) {
            $data = [
                'FK_COD_PERSONA' => (int) $request->FK_COD_PERSONA,
                'USR_USUARIO' => $request->USR_USUARIO,
                'FK_COD_ROL' => (int) $request->FK_COD_ROL,
                'ESTADO_USUARIO' => (int) $request->ESTADO_USUARIO,
            ];

            if ($request->filled('password')) {
                $plainPassword = (string) $request->password;
                $data['PWD_USUARIO'] = Hash::make($plainPassword);
            }

            DB::table('tbl_usuario')->where('COD_USUARIO', $id)->update($data);

            if ($plainPassword !== null) {
                $passwordSecurityService->markPasswordChanged((int) $id);
            }
        });

        if ($plainPassword !== null) {
            $usuario = Usuario::where('COD_USUARIO', $id)->first();
            if ($usuario) {
                $usuario->notify(new PasswordChangedNotification($usuario->USR_USUARIO, $plainPassword));
            }

            session()->flash('password_updated_modal', [
                'usuario' => $request->USR_USUARIO,
                'password' => $plainPassword,
            ]);
        }

        return redirect()->route('seguridad.usuarios.edit', $id)->with('success', 'Usuario actualizado.');
    }

    public function destroy($id)
    {
        DB::table('tbl_usuario')->where('COD_USUARIO', $id)->update(['ESTADO_USUARIO' => 0]);

        return redirect()->route('seguridad.usuarios.index')->with('success', 'Usuario desactivado.');
    }
}
