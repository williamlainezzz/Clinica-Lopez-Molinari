<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorPaciente extends Model
{
    protected $table = 'tbl_doctor_paciente';
    protected $primaryKey = 'COD_DP';
    public $timestamps = false;

    protected $fillable = ['FK_COD_DOCTOR','FK_COD_PACIENTE','FEC_ASIGNACION','ACTIVO'];

    public function doctor() {
        return $this->belongsTo(Persona::class, 'FK_COD_DOCTOR', 'COD_PERSONA');
    }
    public function paciente() {
        return $this->belongsTo(Persona::class, 'FK_COD_PACIENTE', 'COD_PERSONA');
    }
}
