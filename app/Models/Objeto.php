<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Objeto extends Model
{
    protected $table = 'tbl_objeto';
    protected $primaryKey = 'COD_OBJETO';
    public $timestamps = false;

    protected $fillable = [
        'NOM_OBJETO','DESC_OBJETO','URL_OBJETO','ESTADO_OBJETO'
    ];
}
