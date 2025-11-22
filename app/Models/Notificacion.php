<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $table = 'tbl_notificacion';
    protected $primaryKey = 'COD_NOTIFICACION';
    public $timestamps = false;

    protected $fillable = [
        'FK_COD_CITA',
        'MSG_NOTIFICACION',
        'FEC_ENVIO',
        'TIPO_NOTIFICACION',
        'LEIDA',
    ];
}
