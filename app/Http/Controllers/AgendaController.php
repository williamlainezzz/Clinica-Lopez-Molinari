<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AgendaController extends Controller
{
    public function citas()
    {
        return $this->render('Citas');
    }

    public function calendario()
    {
        return $this->render('Calendario');
    }

    public function reportes()
    {
        return $this->render('Reportes');
    }

    private function render(string $section)
    {
        // Lee el rol del usuario logueado
        $rol = strtoupper(auth()->user()->rol->NOM_ROL ?? '');

        // Etiquetas bonitas para el título
        $roleLabel = match ($rol) {
            'ADMIN'         => 'Admin',
            'DOCTOR'        => 'Doctor',
            'RECEPCIONISTA' => 'Recepción',
            'PACIENTE'      => 'Paciente',
            default         => 'Admin',
        };

        // Vista única con parciales compartidos
        return view('modulo-citas.page', [
            'section'    => $section,   // 'Citas' | 'Calendario' | 'Reportes'
            'roleLabel'  => $roleLabel, // 'Admin' | 'Doctor' | 'Recepción' | 'Paciente'
        ]);
    }
}
