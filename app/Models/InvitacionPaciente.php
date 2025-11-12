<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvitacionPaciente extends Model
{
    protected $table = 'tbl_invitacion_paciente';
    protected $primaryKey = 'COD_INVITACION';
    public $timestamps = false;

    protected $fillable = [
        'FK_COD_DOCTOR','TOKEN','USOS_MAX','USOS_ACTUALES','EXPIRA_EN','ACTIVA','CREATED_AT'
    ];

    public function doctor() {
        return $this->belongsTo(Persona::class, 'FK_COD_DOCTOR', 'COD_PERSONA');
    }
}
