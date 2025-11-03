<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoCita extends Model
{
    // AJUSTA si tu catálogo tiene otro nombre
    protected $table      = 'tbl_estado_cita';
    protected $primaryKey = 'COD_ESTADO_CITA';
    public    $timestamps = false;

    protected $fillable = [
        'COD_ESTADO_CITA',
        'NOM_ESTADO_CITA',  // Confirmada / Pendiente / Cancelada
    ];
}
