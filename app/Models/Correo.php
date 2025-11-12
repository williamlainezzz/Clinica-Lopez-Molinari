<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Correo extends Model
{
    protected $table = 'tbl_correo';
    protected $primaryKey = 'COD_CORREO';

    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'FK_COD_PERSONA',
        'CORREO',
        'TIPO_CORREO',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'FK_COD_PERSONA', 'COD_PERSONA');
    }
}
