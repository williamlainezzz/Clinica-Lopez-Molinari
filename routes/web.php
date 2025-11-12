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

// Personas
use App\Http\Controllers\Personas\PersonaController;

// Perfil
use App\Http\Controllers\ProfileController;

// Seguridad: Objetos / Permisos
use App\Http\Controllers\ObjetoController;
use App\Http\Controllers\PermisoController;

// Model para /db-check
use App\Models\Usuario;

// --- AGENDA (vistas stub)
use App\Http\Controllers\AgendaController;

// --- Citas (controladores reales que ya creamos)
use App\Http\Controllers\Citas\CitasController;
use App\Http\Controllers\Citas\CalendarioController;
use App\Http\Controllers\Citas\MisPacientesController;
use App\Http\Controllers\Citas\InvitacionController;

/* =========================
|  Público / Dashboard
========================= */
Route::get('/', fn() => view('welcome'))->name('welcome');
Route::view('/dashboard', 'dashboard')->middleware('auth')->name('dashboard');

require __DIR__ . '/auth.php';

/* =========================
|  Personas (controlador)
========================= */
Route::middleware('auth')->prefix('personas')->group(function () {
    Route::get('/doctores', [PersonaController::class, 'doctores'])
        ->middleware('can:personas.doctores.ver')
        ->name('doctores.index');

    Route::get('/pacientes', [PersonaController::class, 'pacientes'])
        ->middleware('can:personas.pacientes.ver')
        ->name('pacientes.index');

    Route::get('/recepcionistas', [PersonaController::class, 'recepcionistas'])
        ->middleware('can:personas.recepcionistas.ver')
        ->name('recepcionistas.index');

    Route::get('/administradores', [PersonaController::class, 'administradores'])
        ->middleware('can:personas.administradores.ver')
        ->name('administradores.index');
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

/* =======================================================
|  AGENDA: Citas / Calendario / Reportes (por ROL)
|  (vistas stub; la lógica real la vamos activando por partes)
======================================================= */
Route::middleware(['auth'])->prefix('agenda')->group(function () {
    Route::get('/citas', [AgendaController::class, 'citas'])->name('agenda.citas');

    // IMPORTANTE: /agenda/calendario redirige al calendario real
    Route::get('/calendario', fn() => redirect()->route('citas.calendario'))
        ->name('agenda.calendario');

    Route::get('/reportes', [AgendaController::class, 'reportes'])->name('agenda.reportes');
});

/* =========================
|  CITAS (rutas reales)
|  index, show, exportCsv
========================= */
Route::middleware(['auth'])->group(function () {
    Route::get('/citas', [CitasController::class, 'index'])->name('citas.index');
    Route::get('/citas/{cita}', [CitasController::class, 'show'])->name('citas.show');
    Route::get('/citas/export/csv', [CitasController::class, 'exportCsv'])->name('export.citas.csv');

    // Calendario (FullCalendar)
    Route::get('/citas/calendario', [CalendarioController::class, 'view'])
        ->name('citas.calendario');

    Route::get('/citas/calendario/events', [CalendarioController::class, 'events'])
        ->name('citas.events');

    Route::post('/citas/calendario/event', [CalendarioController::class, 'createFromCalendar'])
        ->name('citas.calendar.create');

    Route::patch('/citas/calendario/event/{cita}', [CalendarioController::class, 'updateFromCalendar'])
        ->name('citas.calendar.update');
});

/* =========================
|  (Reservado) Flujo Doctor/Paciente
|  (cuando activemos esos controladores)
========================= */
// Route::get('/mis-pacientes', [MisPacientesController::class, 'index'])->name('doctor.pacientes');
// Route::post('/mis-pacientes/asignar', [MisPacientesController::class, 'asignarExistente'])->name('doctor.pacientes.asignar');
// Route::post('/invitar-paciente', [InvitacionController::class, 'store'])->name('doctor.invitar');
// Route::get('/invitar-paciente/{id}/qr', [InvitacionController::class, 'qr'])->name('doctor.invitar.qr');
// Route::get('/registro/invitacion/{token}', [InvitacionController::class, 'showSignup'])->name('signup.invite');
// Route::post('/registro/invitacion/{token}', [InvitacionController::class, 'submitSignup'])->name('signup.invite.submit');

