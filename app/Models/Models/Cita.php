<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    protected $table = 'tbl_cita';
    protected $primaryKey = 'COD_CITA';
    public $timestamps = false;

    protected $fillable = [
        'FK_COD_PACIENTE',
        'FK_COD_DOCTOR',
        'FEC_CITA',
        'HOR_CITA',
        'MOT_CITA',
        'ESTADO_CITA',
    ];

    // Relaciones a persona (paciente/doctor)
    public function paciente()
    {
        return $this->belongsTo(Persona::class, 'FK_COD_PACIENTE', 'COD_PERSONA');
    }

    public function doctor()
    {
        return $this->belongsTo(Persona::class, 'FK_COD_DOCTOR', 'COD_PERSONA');
    }

    // Útil para calendario: ISO-8601 (YYYY-MM-DDTHH:MM)
    public function getInicioAttribute(): string
    {
        return "{$this->FEC_CITA}T{$this->HOR_CITA}";
    }
}
