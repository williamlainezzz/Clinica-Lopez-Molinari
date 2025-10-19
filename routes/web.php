<?php

use Illuminate\Support\Facades\Route;

// Auth / Reset / 2FA
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\TwoFactorEmailController;

// Seguridad
use App\Http\Controllers\Seguridad\RolController;
use App\Http\Controllers\Seguridad\BitacoraController;
use App\Http\Controllers\Seguridad\BackupController;
use App\Http\Controllers\Seguridad\UsuarioController;

// Model para /db-check
use App\Models\Usuario;

use App\Http\Controllers\ProfileController;

use App\Http\Controllers\ObjetoController;
use App\Http\Controllers\PermisoController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::view('/dashboard', 'dashboard')->middleware('auth')->name('dashboard');

require __DIR__.'/auth.php';

/* =========================
|  Export de ejemplo (CSV)
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
|  Agenda
========================= */
Route::prefix('agenda')->group(function () {
    Route::view('/citas',           'citas.index')->name('citas.index');
    Route::view('/disponibilidad',  'disponibilidad.index')->name('disponibilidad.index');
    Route::view('/estado-cita',     'estado-cita.index')->name('estado-cita.index');
});

/* =========================
|  Personas & Usuarios (vistas informativas)
========================= */
Route::prefix('personas')->group(function () {
    Route::view('/doctores',        'doctores.index')->name('doctores.index');
    Route::view('/pacientes',       'pacientes.index')->name('pacientes.index');
    Route::view('/recepcionistas',  'recepcionistas.index')->name('recepcionistas.index');
    Route::view('/administradores', 'administradores.index')->name('administradores.index');
});

/* =========================
|  Seguridad (vistas estáticas que ya tenías)
========================= */
Route::prefix('seguridad')->group(function () {
    Route::view('/objetos',  'objetos.index')->name('objetos.index');
    Route::view('/permisos', 'permisos.index')->name('permisos.index');
    // ¡Ojo! Estamos dentro de prefix('seguridad'), por eso aquí va solo '/auditoria'
    Route::view('/auditoria', 'seguridad.auditoria')->name('seguridad.auditoria');
    // ⚠️ No definir aquí /usuarios como view para no chocar con la ruta real
});

/* =========================
|  Notificaciones
========================= */
Route::view('/notificaciones', 'notificaciones.index')->name('notificaciones.index');

/* =========================
|  Reportes
========================= */
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
|  Endpoints Auth: reset password
========================= */
Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
    ->middleware('guest')
    ->name('password.request');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware(['guest','throttle:6,1'])
    ->name('password.email');

Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
    ->middleware('guest')
    ->name('password.reset');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.store');

/* =========================
|  2FA por Email
========================= */
Route::middleware('guest')->group(function () {
    Route::get('/two-factor-challenge', [TwoFactorEmailController::class, 'create'])
        ->name('two-factor.challenge');

    Route::post('/two-factor-challenge', [TwoFactorEmailController::class, 'store'])
        ->name('two-factor.challenge.store');

    Route::post('/two-factor-resend', [TwoFactorEmailController::class, 'resend'])
        ->middleware('throttle:3,1')
        ->name('two-factor.challenge.resend');


        // Perfil de usuario (como en Breeze/Jetstream)
Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/* =========================
|  Seguridad REAL (controladores) bajo auth
========================= */
Route::middleware(['auth'])->group(function () {

    // Grupo real de Seguridad
    Route::prefix('seguridad')->name('seguridad.')->group(function () {
        // Usuarios (LISTADO) — nombre correcto
        Route::get('/usuarios', [UsuarioController::class, 'index'])
            ->name('usuarios.index');

            // LISTA (ya la tienes, no dupliques)
Route::get('/usuarios', [\App\Http\Controllers\Seguridad\UsuarioController::class, 'index'])
    ->name('usuarios.index');

// NUEVO / GUARDAR
Route::get('/usuarios/crear', [\App\Http\Controllers\Seguridad\UsuarioController::class, 'create'])
    ->name('usuarios.create');
Route::post('/usuarios', [\App\Http\Controllers\Seguridad\UsuarioController::class, 'store'])
    ->name('usuarios.store');

// EDITAR / ACTUALIZAR
Route::get('/usuarios/{id}/editar', [\App\Http\Controllers\Seguridad\UsuarioController::class, 'edit'])
    ->name('usuarios.edit');
Route::put('/usuarios/{id}', [\App\Http\Controllers\Seguridad\UsuarioController::class, 'update'])
    ->name('usuarios.update');

// ELIMINAR (suave: marcar inactivo)
Route::delete('/usuarios/{id}', [\App\Http\Controllers\Seguridad\UsuarioController::class, 'destroy'])
    ->name('usuarios.destroy');


        // Backups
        Route::get('/backups',                [BackupController::class, 'index'])->name('backups.index');
        Route::post('/backups',               [BackupController::class, 'store'])->name('backups.store');
        Route::get('/backups/{id}/descargar', [BackupController::class, 'download'])->name('backups.download');

        // Roles
        Route::get('/roles',           [RolController::class, 'index'])->name('roles.index');
        Route::get('/roles/crear',     [RolController::class, 'create'])->name('roles.create');
        Route::post('/roles',          [RolController::class, 'store'])->name('roles.store');
        Route::get('/roles/{id}/edit', [RolController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{id}',      [RolController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{id}',   [RolController::class, 'destroy'])->name('roles.destroy');

        // Bitácora
        Route::get('/bitacora', [BitacoraController::class, 'index'])->name('bitacora.index');
    });

    // 🔁 Alias de compatibilidad:
    // Si alguna parte del sistema aún llama route('usuarios.index'),
    // esta ruta redirige a la real sin romper nada.
    Route::get('/alias/usuarios', function () {
        return redirect()->route('seguridad.usuarios.index');
    })->name('usuarios.index');
});

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

// =========================
//  Rutas de Seguridad: Objetos y Permisos
// =========================
Route::prefix('seguridad')->group(function () {
    // Objetos
    Route::get('/objetos',  [ObjetoController::class,'index'])
        ->middleware('permiso:VER')->name('seguridad.objetos.index');

    Route::post('/objetos', [ObjetoController::class,'store'])
        ->middleware('permiso:EDITAR')->name('seguridad.objetos.store');

    Route::delete('/objetos/{id}', [ObjetoController::class,'destroy'])
        ->middleware('permiso:ELIMINAR')->name('seguridad.objetos.destroy');

    // Permisos
    Route::get('/permisos',  [PermisoController::class,'index'])
        ->middleware('permiso:VER')->name('seguridad.permisos.index');

    Route::post('/permisos', [PermisoController::class,'update'])
        ->middleware('permiso:EDITAR')->name('seguridad.permisos.update');
});