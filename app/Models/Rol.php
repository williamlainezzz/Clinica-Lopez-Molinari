<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'tbl_rol';
    protected $primaryKey = 'COD_ROL';
    public $timestamps = false;

    // columnas reales
    protected $fillable = ['NOM_ROL', 'DESCRIPCION'];
}
