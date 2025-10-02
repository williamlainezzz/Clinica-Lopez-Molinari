<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');   // pantalla inicial de Laravel
})->name('welcome');

Route::view('/dashboard', 'dashboard')->middleware('auth')->name('dashboard');
require __DIR__.'/auth.php';

Route::get('/export/citas.csv', function () {
    $rows = [
        ['Fecha','Hora','Paciente','Doctor','Estado','Motivo'],
        ['2025-08-12','08:30','Ana Rivera','Dr. López','Confirmada','Limpieza'],
        ['2025-08-12','09:00','Carlos Pérez','Dra. Molina','Pendiente','Dolor muela'],
    ];
    $tmp = fopen('php://temp', 'r+');
    foreach ($rows as $r) fputcsv($tmp, $r);
    rewind($tmp);
    return response(stream_get_contents($tmp), 200, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="citas.csv"',
    ]);
})->name('export.citas.csv');


// Agenda
Route::prefix('agenda')->group(function () {
    Route::view('/citas', 'citas.index')->name('citas.index');
    Route::view('/disponibilidad', 'disponibilidad.index')->name('disponibilidad.index');
    Route::view('/estado-cita', 'estado-cita.index')->name('estado-cita.index');
});

// Personas & Usuarios
Route::prefix('personas')->group(function () {
    Route::view('/doctores',        'doctores.index')->name('doctores.index');
    Route::view('/pacientes',       'pacientes.index')->name('pacientes.index');
    Route::view('/recepcionistas',  'recepcionistas.index')->name('recepcionistas.index');
    Route::view('/administradores', 'administradores.index')->name('administradores.index');
});


// Seguridad
Route::prefix('seguridad')->group(function () {
    Route::view('/objetos', 'objetos.index')->name('objetos.index');
    Route::view('/permisos', 'permisos.index')->name('permisos.index');
   Route::view('/usuarios', 'usuarios.index')->name('usuarios.index');
   Route::view('/seguridad/auditoria', 'seguridad.auditoria')->name('seguridad.auditoria');

});

// Notificaciones
Route::view('/notificaciones', 'notificaciones.index')->name('notificaciones.index');

// Reportes
// Página general (si ya la tienes, déjala igual)
Route::view('/reportes', 'reportes.index')->name('reportes.index');

// Subrutas de tipos de reporte
Route::prefix('reportes')->group(function () {
    Route::view('/citas-rango',        'reportes.citas-rango')->name('reportes.citas_rango');
    Route::view('/citas-estado',       'reportes.citas-estado')->name('reportes.citas_estado');
    Route::view('/agenda-doctor',      'reportes.agenda-doctor')->name('reportes.agenda_doctor');
    Route::view('/pacientes-estado',   'reportes.pacientes-estado')->name('reportes.pacientes_estado');
    Route::view('/usuarios-rol',       'reportes.usuarios-rol')->name('reportes.usuarios_rol');
    Route::view('/citas-no-atendidas', 'reportes.citas-no-atendidas')->name('reportes.citas_no_atendidas');
    Route::view('/reportes/procesos', 'reportes.procesos')->name('reportes.procesos');
    Route::view('/reportes/seguridad-permisos', 'reportes.seguridad-permisos')->name('reportes.seguridad_permisos');

});




