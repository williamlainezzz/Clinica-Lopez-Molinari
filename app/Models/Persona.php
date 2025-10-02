<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $table = 'tbl_persona';
    protected $primaryKey = 'COD_PERSONA';
    public $timestamps = false;

    protected $fillable = [
        'PRIMER_NOMBRE',
        'SEGUNDO_NOMBRE',
        'PRIMER_APELLIDO',
        'SEGUNDO_APELLIDO',
        'TIPO_GENERO', // <- obligatorio en tu esquema
    ];
}
