<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioPregunta extends Model
{
    protected $table      = 'tbl_usuario_pregunta';
    protected $primaryKey = 'COD_USR_PREG';
    public $timestamps    = false; // la columna CREATED_AT la maneja la BD por defecto

    protected $fillable = [
        'FK_COD_USUARIO',
        'FK_COD_PREGUNTA',
        'RESPUESTA_HASH',
        // 'CREATED_AT' se llena por default CURRENT_TIMESTAMP en la BD
    ];

    /** Usuario dueño de esta pregunta+respuesta */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'FK_COD_USUARIO', 'COD_USUARIO');
    }

    /** Pregunta de seguridad asociada */
    public function pregunta()
    {
        return $this->belongsTo(PreguntaSeguridad::class, 'FK_COD_PREGUNTA', 'COD_PREGUNTA');
    }
}
