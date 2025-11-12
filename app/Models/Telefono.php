<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Telefono extends Model
{
    protected $table = 'tbl_telefono';
    protected $primaryKey = 'COD_TELEFONO';

    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'FK_COD_PERSONA',
        'NUM_TELEFONO',
        'TIPO_TELEFONO',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'FK_COD_PERSONA', 'COD_PERSONA');
    }
}
