<?php
namespace App\Http\Controllers;

class AgendaController extends Controller
{
    public function citas()      { return $this->render('Citas'); }
    public function calendario() { return $this->render('Calendario'); }
    public function reportes()   { return $this->render('Reportes'); }

    private function render(string $section)
    {
        $rol = strtoupper(auth()->user()->rol->NOM_ROL ?? '');

        $titulos = [
            'ADMIN' => ['Citas'=>'Citas Admin','Calendario'=>'Calendario Admin','Reportes'=>'Reportes · Admin'],
            'DOCTOR' => ['Citas'=>'Citas · Doctor','Calendario'=>'Calendario Doctor','Reportes'=>'Reportes Doctor'],
            'RECEPCIONISTA' => ['Citas'=>'Citas Recepción','Calendario'=>'Calendario Recepción','Reportes'=>'Reportes Recepción'],
            'PACIENTE' => ['Citas'=>'Citas Paciente','Calendario'=>'Calendario Paciente','Reportes'=>'Historial Paciente'],
        ];

        $titulo = $titulos[$rol][$section] ?? $section;
        return view('modulo-citas.shared.lista', ['titulo' => $titulo]);
    }
}
