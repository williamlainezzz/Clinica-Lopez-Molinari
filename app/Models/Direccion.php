<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    protected $table = 'tbl_direccion';
    protected $primaryKey = 'COD_DIRECCION';

    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'FK_COD_PERSONA',
        'DEPARTAMENTO',
        'MUNICIPIO',
        'CIUDAD',
        'COLONIA',
        'REFERENCIA',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'FK_COD_PERSONA', 'COD_PERSONA');
    }
}
