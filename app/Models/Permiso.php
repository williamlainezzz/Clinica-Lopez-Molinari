<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    protected $table = 'tbl_permiso';
    protected $primaryKey = 'COD_PERMISO';
    public $timestamps = false;

    protected $fillable = [
        'FK_COD_ROL','FK_COD_OBJETO','ESTADO_PERMISO',
        'VER','CREAR','EDITAR','ELIMINAR',
        // por convivencia con las columnas legadas
        'PER_SELECT','PER_INSERTAR','PER_UPDATE'
    ];
}
