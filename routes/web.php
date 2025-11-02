<?php

use Illuminate\Support\Facades\Route;

// Auth / Reset / 2FA
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\TwoFactorEmailController;

// Seguridad (controladores reales)
use App\Http\Controllers\Seguridad\RolController;
use App\Http\Controllers\Seguridad\BitacoraController;
use App\Http\Controllers\Seguridad\BackupController;
use App\Http\Controllers\Seguridad\UsuarioController;

// Perfil
use App\Http\Controllers\ProfileController;

// Seguridad: Objetos / Permisos
use App\Http\Controllers\ObjetoController;
use App\Http\Controllers\PermisoController;

// Model para /db-check
use App\Models\Usuario;

// Agregar imports necesarios para la ruta closure (evitan usar el facade Request por error)
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
//use Illuminate\Support\Facades\DB; // ya importado pero aseguramos su disponibilidad

// Definir función global para resolver id de rol (evita dependencias de scope con variable)
if (!function_exists('resolveRoleId')) {
    function resolveRoleId(array $keywords, $defaultIfNone = null) {
        if (!Schema::hasTable('tbl_rol')) {
            Log::warning('tbl_rol no existe, usando role id por defecto: ' . ($defaultIfNone ?? 'null'));
            return $defaultIfNone;
        }

        // Intentar detectar la columna PK
        $pkCandidates = ['COD_ROL','ID','id','ROL_ID'];
        $pkCol = null;
        foreach ($pkCandidates as $c) {
            if (Schema::hasColumn('tbl_rol', $c)) { $pkCol = $c; break; }
        }
        if (!$pkCol) {
            $cols = DB::getSchemaBuilder()->getColumnListing('tbl_rol');
            $pkCol = $cols[0] ?? null;
            if (!$pkCol) {
                Log::error('No se pudo determinar PK de tbl_rol');
                return $defaultIfNone;
            }
        }

        // Columnas que pueden contener el nombre del rol
        $nameCandidates = ['NOMBRE','NOM_ROL','DESCRIPCION','ROL','NOMBRE_ROL','NOM','nombre'];
        foreach ($nameCandidates as $nameCol) {
            if (!Schema::hasColumn('tbl_rol', $nameCol)) continue;
            foreach ($keywords as $kw) {
                $row = DB::table('tbl_rol')->whereRaw("LOWER({$nameCol}) LIKE ?", ['%'.mb_strtolower($kw).'%'])->first();
                if ($row && isset($row->{$pkCol})) {
                    return $row->{$pkCol};
                }
            }
        }

        // Fallback: primer rol disponible
        $firstRole = DB::table('tbl_rol')->orderBy($pkCol, 'asc')->first();
        if ($firstRole && isset($firstRole->{$pkCol})) {
            Log::warning('No se encontró rol por keywords ('.implode(',', $keywords).'), usando primer rol disponible id='.$firstRole->{$pkCol});
            return $firstRole->{$pkCol};
        }

        Log::error('No se pudo resolver role id en tbl_rol y no hay filas.');
        return $defaultIfNone;
    }
}

/* =========================
|  Público / Dashboard
========================= */
Route::get('/', fn() => view('welcome'))->name('welcome');
Route::view('/dashboard', 'dashboard')->middleware('auth')->name('dashboard');

require __DIR__ . '/auth.php';

/* =========================
|  Export (demo CSV)
========================= */
Route::get('/export/citas.csv', function () {
    $rows = [
        ['Fecha','Hora','Paciente','Doctor','Estado','Motivo'],
        ['2025-08-12','08:30','Ana Rivera','Dr. López','Confirmada','Limpieza'],
        ['2025-08-12','09:00','Carlos Pérez','Dra. Molina','Pendiente','Dolor muela'],
    ];
    $tmp = fopen('temp://memory', 'r+');
    foreach ($rows as $r) fputcsv($tmp, $r);
    rewind($tmp);

    return response(stream_get_contents($tmp), 200, [
        'Content-Type'        => 'text/csv',
        'Content-Disposition' => 'attachment; filename="citas.csv"',
    ]);
})->name('export.citas.csv');

/* =========================
|  Agenda (vistas)
========================= */
Route::prefix('agenda')->group(function () {
    Route::view('/citas',           'citas.index')->name('citas.index');
    Route::view('/disponibilidad',  'disponibilidad.index')->name('disponibilidad.index');
    Route::view('/estado-cita',     'estado-cita.index')->name('estado-cita.index');
});

/* =========================
|  Personas (vistas)
========================= */
Route::prefix('personas')->group(function () {
    Route::view('/doctores',        'doctores.index')->name('doctores.index');
    Route::view('/pacientes',       'pacientes.index')->name('pacientes.index');
    Route::view('/recepcionistas',  'recepcionistas.index')->name('recepcionistas.index');
    Route::view('/administradores', 'administradores.index')->name('administradores.index');
});

/* =========================
|  Notificaciones / Reportes (vistas)
========================= */
Route::view('/notificaciones', 'notificaciones.index')->name('notificaciones.index');

Route::view('/reportes', 'reportes.index')->name('reportes.index');
Route::prefix('reportes')->group(function () {
    Route::view('/citas-rango',        'reportes.citas-rango')->name('reportes.citas_rango');
    Route::view('/citas-estado',       'reportes.citas-estado')->name('reportes.citas_estado');
    Route::view('/agenda-doctor',      'reportes.agenda-doctor')->name('reportes.agenda_doctor');
    Route::view('/pacientes-estado',   'reportes.pacientes-estado')->name('reportes.pacientes_estado');
    Route::view('/usuarios-rol',       'reportes.usuarios-rol')->name('reportes.usuarios_rol');
    Route::view('/citas-no-atendidas', 'reportes.citas-no-atendidas')->name('reportes.citas_no_atendidas');
    Route::view('/procesos',           'reportes.procesos')->name('reportes.procesos');
    Route::view('/seguridad-permisos', 'reportes.seguridad-permisos')->name('reportes.seguridad_permisos');
});

/* =========================
|  Reset Password
========================= */
Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
    ->middleware('guest')->name('password.request');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware(['guest','throttle:6,1'])->name('password.email');

Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
    ->middleware('guest')->name('password.reset');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')->name('password.store');

/* =========================
|  2FA por Email (guest)
========================= */
Route::middleware('guest')->group(function () {
    Route::get('/two-factor-challenge',  [TwoFactorEmailController::class, 'create'])->name('two-factor.challenge');
    Route::post('/two-factor-challenge', [TwoFactorEmailController::class, 'store'])->name('two-factor.challenge.store');
    Route::post('/two-factor-resend',    [TwoFactorEmailController::class, 'resend'])
        ->middleware('throttle:3,1')->name('two-factor.challenge.resend');
});

/* =========================
|  Perfil (auth)
========================= */
Route::middleware('auth')->group(function () {
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',[ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy');
});

/* =========================
|  Seguridad (CONTROLADORES) bajo auth + permiso
|  ⚠️ No hay vistas estáticas tipo Route::view('/seguridad/…').
========================= */
Route::middleware(['auth'])->prefix('seguridad')->name('seguridad.')->group(function () {

    /* ---- Backups (objeto: SEGURIDAD_BACKUPS) ---- */
    Route::get('/backups',                [BackupController::class, 'index'])
        ->middleware('permiso:VER')->name('backups.index');
    Route::post('/backups',               [BackupController::class, 'store'])
        ->middleware('permiso:CREAR')->name('backups.store');
    Route::get('/backups/{id}/descargar', [BackupController::class, 'download'])
        ->middleware('permiso:VER')->name('backups.download');

    /* ---- Bitácora (objeto: SEGURIDAD_BITACORA) ---- */
    Route::get('/bitacora', [BitacoraController::class, 'index'])
        ->middleware('permiso:VER')->name('bitacora.index');

    /* ---- Roles (objeto: SEGURIDAD_ROLES) ---- */
    Route::get('/roles',           [RolController::class, 'index'])
        ->middleware('permiso:VER')->name('roles.index');
    Route::get('/roles/crear',     [RolController::class, 'create'])
        ->middleware('permiso:CREAR')->name('roles.create');
    Route::post('/roles',          [RolController::class, 'store'])
        ->middleware('permiso:CREAR')->name('roles.store');
    Route::get('/roles/{id}/edit', [RolController::class, 'edit'])
        ->middleware('permiso:EDITAR')->name('roles.edit');
    Route::put('/roles/{id}',      [RolController::class, 'update'])
        ->middleware('permiso:EDITAR')->name('roles.update');
    Route::delete('/roles/{id}',   [RolController::class, 'destroy'])
        ->middleware('permiso:ELIMINAR')->name('roles.destroy');

    /* ---- Usuarios (queda solo con auth) ---- */
    Route::get('/usuarios',              [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('/usuarios/crear',       [UsuarioController::class, 'create'])->name('usuarios.create');
    Route::post('/usuarios',            [UsuarioController::class, 'store'])->name('usuarios.store');
    Route::get('/usuarios/{id}/editar', [UsuarioController::class, 'edit'])->name('usuarios.edit');
    Route::put('/usuarios/{id}',        [UsuarioController::class, 'update'])->name('usuarios.update');
    Route::delete('/usuarios/{id}',     [UsuarioController::class, 'destroy'])->name('usuarios.destroy');

    /* ---- Objetos (objeto: SEGURIDAD_OBJETOS) ---- */
    Route::get('/objetos',  [ObjetoController::class,'index'])
        ->middleware('permiso:VER')->name('objetos.index');
    Route::post('/objetos', [ObjetoController::class,'store'])
        ->middleware('permiso:EDITAR')->name('objetos.store');
    Route::delete('/objetos/{id}', [ObjetoController::class,'destroy'])
        ->middleware('permiso:ELIMINAR')->name('objetos.destroy');

    /* ---- Permisos (objeto: SEGURIDAD_PERMISOS) ---- */
    Route::get('/permisos',  [PermisoController::class,'index'])
        ->middleware('permiso:VER')->name('permisos.index');
    Route::post('/permisos', [PermisoController::class,'update'])
        ->middleware('permiso:EDITAR')->name('permisos.update');
});

/* =========================
|  Alias de compatibilidad
========================= */
Route::middleware('auth')->get('/alias/usuarios', function () {
    return redirect()->route('seguridad.usuarios.index');
})->name('usuarios.index');

/* =========================
|  Diagnóstico rápido
========================= */
Route::get('/db-check', function () {
    $usuarios = Usuario::with(['persona', 'rol'])->limit(5)->get();
    return response()->json([
        'ok'    => true,
        'count' => $usuarios->count(),
        'data'  => $usuarios,
    ]);
});

/* =========================
|  Registro Público (Pacientes)
========================= */
use App\Http\Controllers\PublicRegisterController;

Route::post('personas', [PublicRegisterController::class, 'store'])->name('personas.store');
Route::get('personas/{id}/json', [PublicRegisterController::class, 'show'])->name('personas.show');
Route::put('personas/{id}', [PublicRegisterController::class, 'update'])->name('personas.update');

/* =========================
|  Administradores
========================= */
use App\Http\Controllers\AdministradorController;

Route::post('/administradores', function(HttpRequest $request){
    // Validación básica (ajusta según tu partial)
    $validated = $request->validate([
        'PRIMER_NOMBRE' => 'required|string|max:255',
        'SEGUNDO_NOMBRE' => 'nullable|string|max:255',
        'PRIMER_APELLIDO' => 'required|string|max:255',
        'SEGUNDO_APELLIDO' => 'nullable|string|max:255',
        'TIPO_GENERO' => 'nullable',
        'NUM_TELEFONO' => 'nullable|string|max:30',
        'TIPO_TELEFONO' => 'nullable', // <--- añadido: tipo de teléfono opcional
        'DEPARTAMENTO' => 'nullable|string|max:255',
        'MUNICIPIO' => 'nullable|string|max:255',
        'CIUDAD' => 'nullable|string|max:255',
        'COLONIA' => 'nullable|string|max:255',
        'REFERENCIA' => 'nullable|string',
        'CORREO' => 'required|email|max:255',
        'password' => 'required|string|min:6|confirmed',
    ]);

    DB::beginTransaction();
    try {
        // helper local: añadir timestamps sólo si existen las columnas
        $addTimestampsIfExist = function(string $table, array &$data) {
            if (Schema::hasColumn($table, 'created_at')) {
                $data['created_at'] = now();
            }
            if (Schema::hasColumn($table, 'updated_at')) {
                $data['updated_at'] = now();
            }
        };

        // Insertar persona
        $personaData = [
            'PRIMER_NOMBRE' => $validated['PRIMER_NOMBRE'],
            'SEGUNDO_NOMBRE' => $validated['SEGUNDO_NOMBRE'] ?? null,
            'PRIMER_APELLIDO' => $validated['PRIMER_APELLIDO'],
            'SEGUNDO_APELLIDO' => $validated['SEGUNDO_APELLIDO'] ?? null,
            'TIPO_GENERO' => $validated['TIPO_GENERO'] ?? null,
        ];
        $addTimestampsIfExist('tbl_persona', $personaData);
        $personaId = DB::table('tbl_persona')->insertGetId($personaData);

        // Insertar correo (tipo 1 = principal)
        if (Schema::hasTable('tbl_correo')) {
            $correoData = [
                'FK_COD_PERSONA' => $personaId,
                'CORREO' => $validated['CORREO'],
                'TIPO_CORREO' => 1,
            ];
            $addTimestampsIfExist('tbl_correo', $correoData);
            DB::table('tbl_correo')->insert($correoData);
        }

        // Insertar teléfono (si viene)
        if (!empty($validated['NUM_TELEFONO']) && Schema::hasTable('tbl_telefono')) {
            $telefonoData = [
                'FK_COD_PERSONA' => $personaId,
                'NUM_TELEFONO' => $validated['NUM_TELEFONO'],
            ];
            // Añadir TIPO_TELEFONO si la columna existe (usar valor enviado o 1 por defecto)
            if (Schema::hasColumn('tbl_telefono', 'TIPO_TELEFONO')) {
                $telefonoData['TIPO_TELEFONO'] = $validated['TIPO_TELEFONO'] ?? 1;
            }
            $addTimestampsIfExist('tbl_telefono', $telefonoData);
            DB::table('tbl_telefono')->insert($telefonoData);
        }

        // Insertar dirección (si hay alguno de los campos) 
        $direccionKeys = ['DEPARTAMENTO','MUNICIPIO','CIUDAD','COLONIA','REFERENCIA'];
        $hasDireccion = false;
        foreach ($direccionKeys as $k) {
            if (!empty($validated[$k] ?? null)) {
                $hasDireccion = true;
                break;
            }
        }
        if ($hasDireccion && Schema::hasTable('tbl_direccion')) {
            $direccionData = [
                'FK_COD_PERSONA' => $personaId,
                'DEPARTAMENTO' => $validated['DEPARTAMENTO'] ?? null,
                'MUNICIPIO' => $validated['MUNICIPIO'] ?? null,
                'CIUDAD' => $validated['CIUDAD'] ?? null,
                'COLONIA' => $validated['COLONIA'] ?? null,
                'REFERENCIA' => $validated['REFERENCIA'] ?? null,
            ];
            $addTimestampsIfExist('tbl_direccion', $direccionData);
            DB::table('tbl_direccion')->insert($direccionData);
        }

        // Insertar usuario en tbl_usuario (sin tocar migraciones). Ajusta FK_COD_ROL si tu sistema usa otro id para administradores.
        if (Schema::hasTable('tbl_usuario')) {
            // generar username legible (inicial + primer apellido)
            $first = $validated['PRIMER_NOMBRE'];
            $last  = $validated['PRIMER_APELLIDO'];
            $base = Str::ascii(strtolower(substr(trim($first),0,1) . preg_replace('/\s+/', '', trim($last))));
            $base = preg_replace('/[^a-z0-9]/', '', $base);
            if (!$base) $base = 'user' . Str::random(4);

            // detectar id de rol para administradores
            $adminRoleId = resolveRoleId(['administrador','admin','administrator','adm'], 1);
            $usuarioData = [
                'FK_COD_PERSONA' => $personaId,
                'FK_COD_ROL' => $adminRoleId,
                'ESTADO_USUARIO' => 1,
            ];

            // Columnas posibles para "usuario" en diferentes esquemas
            $possibleUsernameCols = ['USERNAME','USR_USUARIO','USUARIO','USER','USR_NOMBRE','NOMBRE_USUARIO'];
            foreach ($possibleUsernameCols as $col) {
                if (Schema::hasColumn('tbl_usuario', $col)) {
                    $usuarioData[$col] = $base;
                    break;
                }
            }

            // Columnas posibles para "password" en diferentes esquemas (añadimos PWD_USUARIO y variantes)
            $possiblePasswordCols = [
                'PASSWORD','USR_CLAVE','PASSWORD_HASH','CONTRASENA','CLAVE','PASS',
                'PWD_USUARIO','USR_PWD','PWD','PASSWORD_USR'
            ];
            $passwordAssigned = false;
            foreach ($possiblePasswordCols as $col) {
                if (Schema::hasColumn('tbl_usuario', $col)) {
                    $usuarioData[$col] = Hash::make($validated['password']);
                    $passwordAssigned = true;
                    break;
                }
            }

            // Fallback adicional: si no detectamos ninguna columna de password, intentar asignar a PWD_USUARIO si existe
            if (!$passwordAssigned && Schema::hasColumn('tbl_usuario', 'PWD_USUARIO')) {
                $usuarioData['PWD_USUARIO'] = Hash::make($validated['password']);
                $passwordAssigned = true;
            }

            // Asegurar timestamps si la tabla los soporta
            $addTimestampsIfExist('tbl_usuario', $usuarioData);

            DB::table('tbl_usuario')->insert($usuarioData);
        }

        DB::commit();
        return response()->json(['success' => true, 'message' => 'Administrador creado correctamente', 'persona_id' => $personaId], 201);
    } catch (\Throwable $e) {
        DB::rollBack();
        // Log para depuración
        Log::error('Error creando administrador: '.$e->getMessage(), ['exception' => $e]);
        $msg = config('app.debug') ? $e->getMessage() : 'Error al crear administrador';
        return response()->json(['success' => false, 'message' => $msg], 500);
    }
})->name('administradores.store');

// Actualizar administrador por usuario (closure, sin controlador)
Route::put('/administradores/{usuario}', function(HttpRequest $request, $usuario){
    $payload = $request->validate([
        'nombre'  => 'required|string|max:500',
        'correo'  => 'nullable|email|max:255',
        'usuario' => 'required|string|max:255',
        'telefono'=> 'nullable|string|max:30',
        'estado'  => 'nullable|string|max:50',
    ]);

    DB::beginTransaction();
    try {
        // Determinar columna real que guarda el usuario en tbl_usuario
        $usernameCandidates = ['USERNAME','USR_USUARIO','USUARIO','USER','USR_NOMBRE','NOMBRE_USUARIO'];
        $usernameCol = null;
        foreach ($usernameCandidates as $col) {
            if (Schema::hasColumn('tbl_usuario', $col)) {
                $usernameCol = $col;
                break;
            }
        }
        if (!$usernameCol) {
            return response()->json(['success'=>false,'message'=>'No se encontró columna de usuario en tbl_usuario'], 500);
        }

        // Buscar registro de usuario por la columna detectada
        $usuarioRow = DB::table('tbl_usuario')->where($usernameCol, $usuario)->first();
        if (!$usuarioRow) {
            return response()->json(['success'=>false,'message'=>'Usuario no encontrado'], 404);
        }
        $personaId = $usuarioRow->FK_COD_PERSONA ?? null;
        if (!$personaId) {
            return response()->json(['success'=>false,'message'=>'Persona no enlazada al usuario'], 500);
        }

        // Actualizar tbl_persona: separar nombre completo
        $full = trim($payload['nombre']);
        $parts = preg_split('/\s+/', $full);
        $primerNombre = $parts[0] ?? $full;
        $primerApellido = count($parts) > 1 ? $parts[count($parts)-1] : '';

        $personaUpdate = [];
        if ($primerNombre !== '') $personaUpdate['PRIMER_NOMBRE'] = $primerNombre;
        if ($primerApellido !== '') $personaUpdate['PRIMER_APELLIDO'] = $primerApellido;
        if (!empty($personaUpdate) && Schema::hasTable('tbl_persona')) {
            DB::table('tbl_persona')->where('COD_PERSONA', $personaId)->update($personaUpdate);
        }

        // Actualizar correo principal (TIPO_CORREO = 1)
        if (!empty($payload['correo']) && Schema::hasTable('tbl_correo')) {
            $exists = DB::table('tbl_correo')->where('FK_COD_PERSONA', $personaId)->where('TIPO_CORREO', 1)->exists();
            $correoData = ['CORREO' => $payload['correo']];
            if ($exists) {
                DB::table('tbl_correo')->where('FK_COD_PERSONA', $personaId)->where('TIPO_CORREO', 1)->update($correoData);
            } else {
                $correoData['FK_COD_PERSONA'] = $personaId;
                $correoData['TIPO_CORREO'] = 1;
                if (Schema::hasColumn('tbl_correo','created_at')) $correoData['created_at'] = now();
                if (Schema::hasColumn('tbl_correo','updated_at')) $correoData['updated_at'] = now();
                DB::table('tbl_correo')->insert($correoData);
            }
        }

        // Actualizar teléfono: primer registro o insertar nuevo
        if (isset($payload['telefono']) && Schema::hasTable('tbl_telefono')) {
            $telRow = DB::table('tbl_telefono')->where('FK_COD_PERSONA', $personaId)->orderBy('COD_TELEFONO')->first();
            $telefonoData = ['NUM_TELEFONO' => $payload['telefono']];
            if (Schema::hasColumn('tbl_telefono','TIPO_TELEFONO') && !isset($payload['TIPO_TELEFONO'])) {
                $telefonoData['TIPO_TELEFONO'] = 1;
            }
            if ($telRow) {
                DB::table('tbl_telefono')->where('COD_TELEFONO', $telRow->COD_TELEFONO)->update($telefonoData);
            } else {
                $telefonoData['FK_COD_PERSONA'] = $personaId;
                if (Schema::hasColumn('tbl_telefono','created_at')) $telefonoData['created_at'] = now();
                if (Schema::hasColumn('tbl_telefono','updated_at')) $telefonoData['updated_at'] = now();
                DB::table('tbl_telefono')->insert($telefonoData);
            }
        }

        // Actualizar estado y/o username en tbl_usuario según columnas disponibles
        $usuarioUpdate = [];
        if (!empty($payload['estado']) && Schema::hasColumn('tbl_usuario','ESTADO_USUARIO')) {
            // normalizar valor a entero si es 'Activo'/'Inactivo' o dejar tal cual
            if (strtolower($payload['estado']) === 'activo') $usuarioUpdate['ESTADO_USUARIO'] = 1;
            elseif (strtolower($payload['estado']) === 'inactivo') $usuarioUpdate['ESTADO_USUARIO'] = 0;
            else $usuarioUpdate['ESTADO_USUARIO'] = $payload['estado'];
        }
        // permitir cambio de nombre de usuario
        if (!empty($payload['usuario'])) {
            if (Schema::hasColumn('tbl_usuario', $usernameCol)) {
                $usuarioUpdate[$usernameCol] = $payload['usuario'];
            }
        }
        if (!empty($usuarioUpdate)) {
            DB::table('tbl_usuario')->where($usernameCol, $usuario)->update($usuarioUpdate);
        }

        DB::commit();
        return response()->json(['success'=>true,'message'=>'Administrador actualizado correctamente']);
    } catch (\Throwable $e) {
        DB::rollBack();
        Log::error('Error actualizando administrador: '.$e->getMessage(), ['exception'=>$e]);
        $msg = config('app.debug') ? $e->getMessage() : 'Error al actualizar administrador';
        return response()->json(['success'=>false,'message'=>$msg], 500);
    }
})->name('administradores.update');

// Ruta para guardar doctores (closure, sin controlador)
Route::post('/doctores', function(HttpRequest $request){
    // Resolver dinámicamente el id de rol para "doctor" o según role_keywords enviado
    // si el cliente envía role_keywords (string "recepcionista,recepcionistas" o array), usarlo
    $rk = $request->input('role_keywords');
    if (is_string($rk) && trim($rk) !== '') {
        $keywords = array_values(array_filter(array_map('trim', explode(',', $rk))));
    } elseif (is_array($rk) && count($rk)) {
        $keywords = $rk;
    } else {
        $keywords = ['doctor','médico','medico','odontologo','odontólogo'];
    }
    $roleId = resolveRoleId($keywords, 2);

    $validated = $request->validate([
        'PRIMER_NOMBRE' => 'required|string|max:255',
        'SEGUNDO_NOMBRE' => 'nullable|string|max:255',
        'PRIMER_APELLIDO' => 'required|string|max:255',
        'SEGUNDO_APELLIDO' => 'nullable|string|max:255',
        'TIPO_GENERO' => 'nullable',
        'NUM_TELEFONO' => 'nullable|string|max:30',
        'TIPO_TELEFONO' => 'nullable',
        'DEPARTAMENTO' => 'nullable|string|max:255',
        'MUNICIPIO' => 'nullable|string|max:255',
        'CIUDAD' => 'nullable|string|max:255',
        'COLONIA' => 'nullable|string|max:255',
        'REFERENCIA' => 'nullable|string',
        'CORREO' => 'required|email|max:255',
        'password' => 'required|string|min:6|confirmed',
    ]);

    DB::beginTransaction();
    try {
        $addTimestampsIfExist = function(string $table, array &$data) {
            if (Schema::hasColumn($table, 'created_at')) $data['created_at'] = now();
            if (Schema::hasColumn($table, 'updated_at')) $data['updated_at'] = now();
        };

        // persona
        $personaData = [
            'PRIMER_NOMBRE' => $validated['PRIMER_NOMBRE'],
            'SEGUNDO_NOMBRE' => $validated['SEGUNDO_NOMBRE'] ?? null,
            'PRIMER_APELLIDO' => $validated['PRIMER_APELLIDO'],
            'SEGUNDO_APELLIDO' => $validated['SEGUNDO_APELLIDO'] ?? null,
            'TIPO_GENERO' => $validated['TIPO_GENERO'] ?? null,
        ];
        $addTimestampsIfExist('tbl_persona', $personaData);
        $personaId = DB::table('tbl_persona')->insertGetId($personaData);

        // correo principal
        if (Schema::hasTable('tbl_correo')) {
            $correoData = [
                'FK_COD_PERSONA' => $personaId,
                'CORREO' => $validated['CORREO'],
                'TIPO_CORREO' => 1,
            ];
            $addTimestampsIfExist('tbl_correo', $correoData);
            DB::table('tbl_correo')->insert($correoData);
        }

        // telefono
        if (!empty($validated['NUM_TELEFONO']) && Schema::hasTable('tbl_telefono')) {
            $telefonoData = [
                'FK_COD_PERSONA' => $personaId,
                'NUM_TELEFONO' => $validated['NUM_TELEFONO'],
            ];
            if (Schema::hasColumn('tbl_telefono', 'TIPO_TELEFONO')) {
                $telefonoData['TIPO_TELEFONO'] = $validated['TIPO_TELEFONO'] ?? 1;
            }
            $addTimestampsIfExist('tbl_telefono', $telefonoData);
            DB::table('tbl_telefono')->insert($telefonoData);
        }

        // direccion
        $direccionKeys = ['DEPARTAMENTO','MUNICIPIO','CIUDAD','COLONIA','REFERENCIA'];
        $hasDireccion = false;
        foreach ($direccionKeys as $k) {
            if (!empty($validated[$k] ?? null)) { $hasDireccion = true; break; }
        }
        if ($hasDireccion && Schema::hasTable('tbl_direccion')) {
            $direccionData = [
                'FK_COD_PERSONA' => $personaId,
                'DEPARTAMENTO' => $validated['DEPARTAMENTO'] ?? null,
                'MUNICIPIO' => $validated['MUNICIPIO'] ?? null,
                'CIUDAD' => $validated['CIUDAD'] ?? null,
                'COLONIA' => $validated['COLONIA'] ?? null,
                'REFERENCIA' => $validated['REFERENCIA'] ?? null,
            ];
            $addTimestampsIfExist('tbl_direccion', $direccionData);
            DB::table('tbl_direccion')->insert($direccionData);
        }

        // usuario (tbl_usuario) — detectar columnas de usuario/clave
        if (Schema::hasTable('tbl_usuario')) {
            $first = $validated['PRIMER_NOMBRE'];
            $last  = $validated['PRIMER_APELLIDO'];
            $base = Str::ascii(strtolower(substr(trim($first),0,1) . preg_replace('/\s+/', '', trim($last))));
            $base = preg_replace('/[^a-z0-9]/', '', $base);
            if (!$base) $base = 'user' . Str::random(4);

            $usuarioData = [
                'FK_COD_PERSONA' => $personaId,
                'FK_COD_ROL' => $roleId,
                'ESTADO_USUARIO' => 1,
            ];

            $possibleUsernameCols = ['USERNAME','USR_USUARIO','USUARIO','USER','USR_NOMBRE','NOMBRE_USUARIO'];
            foreach ($possibleUsernameCols as $col) {
                if (Schema::hasColumn('tbl_usuario', $col)) {
                    $usuarioData[$col] = $base;
                    break;
                }
            }

            $possiblePasswordCols = [
                'PASSWORD','USR_CLAVE','PASSWORD_HASH','CONTRASENA','CLAVE','PASS',
                'PWD_USUARIO','USR_PWD','PWD','PASSWORD_USR'
            ];
            foreach ($possiblePasswordCols as $col) {
                if (Schema::hasColumn('tbl_usuario', $col)) {
                    $usuarioData[$col] = Hash::make($validated['password']);
                    break;
                }
            }

            $addTimestampsIfExist('tbl_usuario', $usuarioData);
            DB::table('tbl_usuario')->insert($usuarioData);
        }

        DB::commit();
        return response()->json(['success'=>true,'message'=>'Doctor creado correctamente','persona_id'=>$personaId], 201);
    } catch (\Throwable $e) {
        DB::rollBack();
        Log::error('Error creando doctor: '.$e->getMessage(), ['exception'=>$e]);
        $msg = config('app.debug') ? $e->getMessage() : 'Error al crear doctor';
        return response()->json(['success'=>false,'message'=>$msg], 500);
    }
})->name('doctores.store');

// Actualizar doctor por usuario (closure, sin controlador)
Route::put('/doctores/{usuario}', function(HttpRequest $request, $usuario){
    $payload = $request->validate([
        'nombre'  => 'required|string|max:500',
        'correo'  => 'nullable|email|max:255',
        'usuario' => 'required|string|max:255',
        'telefono'=> 'nullable|string|max:30',
        'estado'  => 'nullable|string|max:50',
    ]);

    DB::beginTransaction();
    try {
        // Determinar columna real que guarda el usuario en tbl_usuario
        $usernameCandidates = ['USERNAME','USR_USUARIO','USUARIO','USER','USR_NOMBRE','NOMBRE_USUARIO'];
        $usernameCol = null;
        foreach ($usernameCandidates as $col) {
            if (Schema::hasColumn('tbl_usuario', $col)) {
                $usernameCol = $col;
                break;
            }
        }
        if (!$usernameCol) {
            return response()->json(['success'=>false,'message'=>'No se encontró columna de usuario en tbl_usuario'], 500);
        }

        // Buscar registro de usuario por la columna detectada
        $usuarioRow = DB::table('tbl_usuario')->where($usernameCol, $usuario)->first();
        if (!$usuarioRow) {
            return response()->json(['success'=>false,'message'=>'Usuario no encontrado'], 404);
        }
        $personaId = $usuarioRow->FK_COD_PERSONA ?? null;
        if (!$personaId) {
            return response()->json(['success'=>false,'message'=>'Persona no enlazada al usuario'], 500);
        }

        // Actualizar tbl_persona: separar nombre completo en primer nombre / primer apellido
        $full = trim($payload['nombre']);
        $parts = preg_split('/\s+/', $full);
        $primerNombre = $parts[0] ?? $full;
        $primerApellido = count($parts) > 1 ? $parts[count($parts)-1] : '';

        $personaUpdate = [];
        if ($primerNombre !== '') $personaUpdate['PRIMER_NOMBRE'] = $primerNombre;
        if ($primerApellido !== '') $personaUpdate['PRIMER_APELLIDO'] = $primerApellido;
        if (!empty($personaUpdate) && Schema::hasTable('tbl_persona')) {
            DB::table('tbl_persona')->where('COD_PERSONA', $personaId)->update($personaUpdate);
        }

        // Actualizar correo principal (TIPO_CORREO = 1)
        if (!empty($payload['correo']) && Schema::hasTable('tbl_correo')) {
            $exists = DB::table('tbl_correo')->where('FK_COD_PERSONA', $personaId)->where('TIPO_CORREO', 1)->exists();
            $correoData = ['CORREO' => $payload['correo']];
            if ($exists) {
                DB::table('tbl_correo')->where('FK_COD_PERSONA', $personaId)->where('TIPO_CORREO', 1)->update($correoData);
            } else {
                $correoData['FK_COD_PERSONA'] = $personaId;
                $correoData['TIPO_CORREO'] = 1;
                if (Schema::hasColumn('tbl_correo','created_at')) $correoData['created_at'] = now();
                if (Schema::hasColumn('tbl_correo','updated_at')) $correoData['updated_at'] = now();
                DB::table('tbl_correo')->insert($correoData);
            }
        }

        // Actualizar teléfono: primer registro o insertar nuevo
        if (isset($payload['telefono']) && Schema::hasTable('tbl_telefono')) {
            $telRow = DB::table('tbl_telefono')->where('FK_COD_PERSONA', $personaId)->orderBy('COD_TELEFONO')->first();
            $telefonoData = ['NUM_TELEFONO' => $payload['telefono']];
            if (Schema::hasColumn('tbl_telefono','TIPO_TELEFONO') && !isset($payload['TIPO_TELEFONO'])) {
                $telefonoData['TIPO_TELEFONO'] = 1;
            }
            if ($telRow) {
                DB::table('tbl_telefono')->where('COD_TELEFONO', $telRow->COD_TELEFONO)->update($telefonoData);
            } else {
                $telefonoData['FK_COD_PERSONA'] = $personaId;
                if (Schema::hasColumn('tbl_telefono','created_at')) $telefonoData['created_at'] = now();
                if (Schema::hasColumn('tbl_telefono','updated_at')) $telefonoData['updated_at'] = now();
                DB::table('tbl_telefono')->insert($telefonoData);
            }
        }

        // Actualizar estado y/o username en tbl_usuario según columnas disponibles
        $usuarioUpdate = [];
        if (!empty($payload['estado']) && Schema::hasColumn('tbl_usuario','ESTADO_USUARIO')) {
            if (strtolower($payload['estado']) === 'activo') $usuarioUpdate['ESTADO_USUARIO'] = 1;
            elseif (strtolower($payload['estado']) === 'inactivo') $usuarioUpdate['ESTADO_USUARIO'] = 0;
            else $usuarioUpdate['ESTADO_USUARIO'] = $payload['estado'];
        }
        if (!empty($payload['usuario'])) {
            if (Schema::hasColumn('tbl_usuario', $usernameCol)) {
                $usuarioUpdate[$usernameCol] = $payload['usuario'];
            }
        }
        if (!empty($usuarioUpdate)) {
            DB::table('tbl_usuario')->where($usernameCol, $usuario)->update($usuarioUpdate);
        }

        DB::commit();
        return response()->json(['success'=>true,'message'=>'Doctor actualizado correctamente']);
    } catch (\Throwable $e) {
        DB::rollBack();
        Log::error('Error actualizando doctor: '.$e->getMessage(), ['exception'=>$e]);
        $msg = config('app.debug') ? $e->getMessage() : 'Error al actualizar doctor';
        return response()->json(['success'=>false,'message'=>$msg], 500);
    }
})->name('doctores.update');

/* =========================
|  Recepcionistas
========================= */
use App\Http\Controllers\RecepcionistaController;

// Guardar recepcionista (closure, sin controlador)
Route::post('/recepcionistas', function(HttpRequest $request){
    $validated = $request->validate([
        'PRIMER_NOMBRE' => 'required|string|max:255',
        'SEGUNDO_NOMBRE' => 'nullable|string|max:255',
        'PRIMER_APELLIDO' => 'required|string|max:255',
        'SEGUNDO_APELLIDO' => 'nullable|string|max:255',
        'TIPO_GENERO' => 'nullable',
        'NUM_TELEFONO' => 'nullable|string|max:30',
        'TIPO_TELEFONO' => 'nullable',
        'DEPARTAMENTO' => 'nullable|string|max:255',
        'MUNICIPIO' => 'nullable|string|max:255',
        'CIUDAD' => 'nullable|string|max:255',
        'COLONIA' => 'nullable|string|max:255',
        'REFERENCIA' => 'nullable|string',
        'CORREO' => 'required|email|max:255',
        'password' => 'required|string|min:6|confirmed',
    ]);

    DB::beginTransaction();
    try {
        $addTimestampsIfExist = function(string $table, array &$data) {
            if (Schema::hasColumn($table, 'created_at')) $data['created_at'] = now();
            if (Schema::hasColumn($table, 'updated_at')) $data['updated_at'] = now();
        };

        // persona
        $personaData = [
            'PRIMER_NOMBRE' => $validated['PRIMER_NOMBRE'],
            'SEGUNDO_NOMBRE' => $validated['SEGUNDO_NOMBRE'] ?? null,
            'PRIMER_APELLIDO' => $validated['PRIMER_APELLIDO'],
            'SEGUNDO_APELLIDO' => $validated['SEGUNDO_APELLIDO'] ?? null,
            'TIPO_GENERO' => $validated['TIPO_GENERO'] ?? null,
        ];
        $addTimestampsIfExist('tbl_persona', $personaData);
        $personaId = DB::table('tbl_persona')->insertGetId($personaData);

        // correo principal
        if (Schema::hasTable('tbl_correo')) {
            $correoData = [
                'FK_COD_PERSONA' => $personaId,
                'CORREO' => $validated['CORREO'],
                'TIPO_CORREO' => 1,
            ];
            $addTimestampsIfExist('tbl_correo', $correoData);
            DB::table('tbl_correo')->insert($correoData);
        }

        // telefono
        if (!empty($validated['NUM_TELEFONO']) && Schema::hasTable('tbl_telefono')) {
            $telefonoData = [
                'FK_COD_PERSONA' => $personaId,
                'NUM_TELEFONO' => $validated['NUM_TELEFONO'],
            ];
            if (Schema::hasColumn('tbl_telefono', 'TIPO_TELEFONO')) {
                $telefonoData['TIPO_TELEFONO'] = $validated['TIPO_TELEFONO'] ?? 1;
            }
            $addTimestampsIfExist('tbl_telefono', $telefonoData);
            DB::table('tbl_telefono')->insert($telefonoData);
        }

        // direccion
        $direccionKeys = ['DEPARTAMENTO','MUNICIPIO','CIUDAD','COLONIA','REFERENCIA'];
        $hasDireccion = false;
        foreach ($direccionKeys as $k) {
            if (!empty($validated[$k] ?? null)) { $hasDireccion = true; break; }
        }
        if ($hasDireccion && Schema::hasTable('tbl_direccion')) {
            $direccionData = [
                'FK_COD_PERSONA' => $personaId,
                'DEPARTAMENTO' => $validated['DEPARTAMENTO'] ?? null,
                'MUNICIPIO' => $validated['MUNICIPIO'] ?? null,
                'CIUDAD' => $validated['CIUDAD'] ?? null,
                'COLONIA' => $validated['COLONIA'] ?? null,
                'REFERENCIA' => $validated['REFERENCIA'] ?? null,
            ];
            $addTimestampsIfExist('tbl_direccion', $direccionData);
            DB::table('tbl_direccion')->insert($direccionData);
        }

        // usuario (tbl_usuario) — detectar columnas de usuario/clave
        if (Schema::hasTable('tbl_usuario')) {
            $first = $validated['PRIMER_NOMBRE'];
            $last  = $validated['PRIMER_APELLIDO'];
            $base = Str::ascii(strtolower(substr(trim($first),0,1) . preg_replace('/\s+/', '', trim($last))));
            $base = preg_replace('/[^a-z0-9]/', '', $base);
            if (!$base) $base = 'user' . Str::random(4);

            $usuarioData = [
                'FK_COD_PERSONA' => $personaId,
                'FK_COD_ROL' => 3, // Asignar rol de recepcionista (ajustar si es necesario)
                'ESTADO_USUARIO' => 1,
            ];

            $possibleUsernameCols = ['USERNAME','USR_USUARIO','USUARIO','USER','USR_NOMBRE','NOMBRE_USUARIO'];
            foreach ($possibleUsernameCols as $col) {
                if (Schema::hasColumn('tbl_usuario', $col)) {
                    $usuarioData[$col] = $base;
                    break;
                }
            }

            $possiblePasswordCols = [
                'PASSWORD','USR_CLAVE','PASSWORD_HASH','CONTRASENA','CLAVE','PASS',
                'PWD_USUARIO','USR_PWD','PWD','PASSWORD_USR'
            ];
            foreach ($possiblePasswordCols as $col) {
                if (Schema::hasColumn('tbl_usuario', $col)) {
                    $usuarioData[$col] = Hash::make($validated['password']);
                    break;
                }
            }

            $addTimestampsIfExist('tbl_usuario', $usuarioData);
            DB::table('tbl_usuario')->insert($usuarioData);
        }

        DB::commit();
        return response()->json(['success'=>true,'message'=>'Recepcionista creada correctamente','persona_id'=>$personaId], 201);
    } catch (\Throwable $e) {
        DB::rollBack();
        Log::error('Error creando recepcionista: '.$e->getMessage(), ['exception'=>$e]);
        $msg = config('app.debug') ? $e->getMessage() : 'Error al crear recepcionista';
        return response()->json(['success'=>false,'message'=>$msg], 500);
    }
})->name('recepcionistas.store');

// Actualizar recepcionista por usuario (closure, sin controlador)
Route::put('/recepcionistas/{usuario}', function(HttpRequest $request, $usuario){
    $payload = $request->validate([
        'nombre'  => 'required|string|max:500',
        'correo'  => 'nullable|email|max:255',
        'usuario' => 'required|string|max:255',
        'telefono'=> 'nullable|string|max:30',
        'estado'  => 'nullable|string|max:50',
    ]);

    DB::beginTransaction();
    try {
        // detectar columna de username en tbl_usuario
        $usernameCandidates = ['USERNAME','USR_USUARIO','USUARIO','USER','USR_NOMBRE','NOMBRE_USUARIO'];
        $usernameCol = null;
        foreach ($usernameCandidates as $col) {
            if (Schema::hasColumn('tbl_usuario', $col)) { $usernameCol = $col; break; }
        }
        if (!$usernameCol) return response()->json(['success'=>false,'message'=>'No se encontró columna de usuario en tbl_usuario'], 500);

        // buscar usuario por la columna detectada
        $usuarioRow = DB::table('tbl_usuario')->where($usernameCol, $usuario)->first();
        if (!$usuarioRow) return response()->json(['success'=>false,'message'=>'Usuario no encontrado'], 404);

        $personaId = $usuarioRow->FK_COD_PERSONA ?? null;
        if (!$personaId) return response()->json(['success'=>false,'message'=>'Persona no enlazada al usuario'], 500);

        // actualizar tbl_persona
        $full = trim($payload['nombre']);
        $parts = preg_split('/\s+/', $full);
        $primerNombre = $parts[0] ?? $full;
        $primerApellido = count($parts) > 1 ? $parts[count($parts)-1] : '';
        $personaUpdate = [];
        if ($primerNombre !== '') $personaUpdate['PRIMER_NOMBRE'] = $primerNombre;
        if ($primerApellido !== '') $personaUpdate['PRIMER_APELLIDO'] = $primerApellido;
        if (!empty($personaUpdate) && Schema::hasTable('tbl_persona')) {
            DB::table('tbl_persona')->where('COD_PERSONA', $personaId)->update($personaUpdate);
        }

        // correo principal
        if (!empty($payload['correo']) && Schema::hasTable('tbl_correo')) {
            $exists = DB::table('tbl_correo')->where('FK_COD_PERSONA', $personaId)->where('TIPO_CORREO', 1)->exists();
            $correoData = ['CORREO' => $payload['correo']];
            if ($exists) {
                DB::table('tbl_correo')->where('FK_COD_PERSONA', $personaId)->where('TIPO_CORREO', 1)->update($correoData);
            } else {
                $correoData['FK_COD_PERSONA'] = $personaId;
                $correoData['TIPO_CORREO'] = 1;
                if (Schema::hasColumn('tbl_correo','created_at')) $correoData['created_at'] = now();
                if (Schema::hasColumn('tbl_correo','updated_at')) $correoData['updated_at'] = now();
                DB::table('tbl_correo')->insert($correoData);
            }
        }

        // telefono
        if (isset($payload['telefono']) && Schema::hasTable('tbl_telefono')) {
            $telRow = DB::table('tbl_telefono')->where('FK_COD_PERSONA', $personaId)->orderBy('COD_TELEFONO')->first();
            $telefonoData = ['NUM_TELEFONO' => $payload['telefono']];
            if (Schema::hasColumn('tbl_telefono','TIPO_TELEFONO') && !isset($payload['TIPO_TELEFONO'])) {
                $telefonoData['TIPO_TELEFONO'] = 1;
            }
            if ($telRow) {
                DB::table('tbl_telefono')->where('COD_TELEFONO', $telRow->COD_TELEFONO)->update($telefonoData);
            } else {
                $telefonoData['FK_COD_PERSONA'] = $personaId;
                if (Schema::hasColumn('tbl_telefono','created_at')) $telefonoData['created_at'] = now();
                if (Schema::hasColumn('tbl_telefono','updated_at')) $telefonoData['updated_at'] = now();
                DB::table('tbl_telefono')->insert($telefonoData);
            }
        }

        // estado / username en tbl_usuario
        $usuarioUpdate = [];
        if (!empty($payload['estado']) && Schema::hasColumn('tbl_usuario','ESTADO_USUARIO')) {
            if (strtolower($payload['estado']) === 'activo') $usuarioUpdate['ESTADO_USUARIO'] = 1;
            elseif (strtolower($payload['estado']) === 'inactivo') $usuarioUpdate['ESTADO_USUARIO'] = 0;
            else $usuarioUpdate['ESTADO_USUARIO'] = $payload['estado'];
        }
        if (!empty($payload['usuario'])) {
            if (Schema::hasColumn('tbl_usuario', $usernameCol)) $usuarioUpdate[$usernameCol] = $payload['usuario'];
        }
        if (!empty($usuarioUpdate)) {
            DB::table('tbl_usuario')->where($usernameCol, $usuario)->update($usuarioUpdate);
        }

        DB::commit();
        return response()->json(['success'=>true,'message'=>'Recepcionista actualizada correctamente']);
    } catch (\Throwable $e) {
        DB::rollBack();
        Log::error('Error actualizando recepcionista: '.$e->getMessage(), ['exception'=>$e]);
        $msg = config('app.debug') ? $e->getMessage() : 'Error al actualizar recepcionista';
        return response()->json(['success'=>false,'message'=>$msg], 500);
    }
})->name('recepcionistas.update');
