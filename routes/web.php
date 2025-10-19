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

// Seguridad: Objetos / Permisos (están en App\Http\Controllers\*)
use App\Http\Controllers\ObjetoController;
use App\Http\Controllers\PermisoController;

// Model para /db-check
use App\Models\Usuario;

/* =========================
|  Público / Dashboard
========================= */
Route::get('/', fn() => view('welcome'))->name('welcome');
Route::view('/dashboard', 'dashboard')->middleware('auth')->name('dashboard');

require __DIR__.'/auth.php';

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
|  2FA por Email (solo invitado)
========================= */
Route::middleware('guest')->group(function () {
    Route::get('/two-factor-challenge',  [TwoFactorEmailController::class, 'create'])->name('two-factor.challenge');
    Route::post('/two-factor-challenge', [TwoFactorEmailController::class, 'store'])->name('two-factor.challenge.store');
    Route::post('/two-factor-resend',    [TwoFactorEmailController::class, 'resend'])
        ->middleware('throttle:3,1')->name('two-factor.challenge.resend');
});

/* =========================
|  Perfil (autenticado)
========================= */
Route::middleware('auth')->group(function () {
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',[ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy');
});

/* =========================
|  Seguridad (CONTROLADORES) bajo auth
|  ⚠️ IMPORTANTE: NO hay vistas estáticas de /seguridad/objetos o /seguridad/permisos
|  aquí. Todo va con controladores + middleware `permiso:ACCION`.
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

    /* ---- Usuarios (sin objeto de permisos específico; queda solo con auth) ---- */
    Route::get('/usuarios',                [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('/usuarios/crear',         [UsuarioController::class, 'create'])->name('usuarios.create');
    Route::post('/usuarios',              [UsuarioController::class, 'store'])->name('usuarios.store');
    Route::get('/usuarios/{id}/editar',   [UsuarioController::class, 'edit'])->name('usuarios.edit');
    Route::put('/usuarios/{id}',          [UsuarioController::class, 'update'])->name('usuarios.update');
    Route::delete('/usuarios/{id}',       [UsuarioController::class, 'destroy'])->name('usuarios.destroy');

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
|  Alias de compatibilidad (si alguien usa usuarios.index "viejo")
========================= */
Route::middleware('auth')->get('/alias/usuarios', function () {
    return redirect()->route('seguridad.usuarios.index');
})->name('usuarios.index');

/* =========================
|  Diagnóstico rápido (dejar público o mover a auth si gustas)
========================= */
Route::get('/db-check', function () {
    $usuarios = Usuario::with(['persona', 'rol'])->limit(5)->get();

    return response()->json([
        'ok'    => true,
        'count' => $usuarios->count(),
        'data'  => $usuarios,
    ]);
});
