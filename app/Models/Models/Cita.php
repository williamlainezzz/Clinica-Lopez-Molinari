<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    protected $table = 'tbl_cita';
    protected $primaryKey = 'COD_CITA';
    public $timestamps = false;

    protected $fillable = [
        'FK_COD_PACIENTE','FK_COD_DOCTOR','FEC_CITA','HOR_CITA','HOR_FIN',
        'MOT_CITA','OBSERVACIONES','ESTADO_CITA','ORIGEN','USUARIO_CREA','USUARIO_MOD'
    ];

    // Relaciones
    public function paciente() {  // persona
        return $this->belongsTo(Persona::class, 'FK_COD_PACIENTE', 'COD_PERSONA');
    }

    public function doctor() {    // persona
        return $this->belongsTo(Persona::class, 'FK_COD_DOCTOR', 'COD_PERSONA');
    }

    // (opcional) estado por catálogo, si luego decides FK:
    // public function estado() {
    //     return $this->belongsTo(EstadoCita::class, 'ESTADO_CITA', 'COD_ESTADO');
    // }

    // Helpers de fecha/hora para calendario
    public function getInicioAttribute() {
        return "{$this->FEC_CITA} {$this->HOR_CITA}";
    }
    public function getFinAttribute() {
        return $this->HOR_FIN ? "{$this->FEC_CITA} {$this->HOR_FIN}" : null;
    }
}
