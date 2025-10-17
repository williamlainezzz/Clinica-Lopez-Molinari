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

class Persona extends Model
{
    protected $table = 'tbl_persona';
    protected $primaryKey = 'COD_PERSONA';
    public $timestamps = true;

    public function telefonos()
    {
        return $this->hasMany(Telefono::class, 'FK_COD_PERSONA', 'COD_PERSONA');
    }

    public function correos()
    {
        return $this->hasMany(Correo::class, 'FK_COD_PERSONA', 'COD_PERSONA');
    }

    public function getNombreCompletoAttribute()
    {
        return trim($this->PRIMER_NOMBRE.' '.($this->SEGUNDO_NOMBRE ?? '').' '.$this->PRIMER_APELLIDO);
    }
}
