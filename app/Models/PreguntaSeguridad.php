<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreguntaSeguridad extends Model
{
    protected $table      = 'tbl_pregunta_seguridad';
    protected $primaryKey = 'COD_PREGUNTA';
    public $timestamps    = false; // la tabla no tiene created_at/updated_at

    protected $fillable = [
        'TEXTO_PREGUNTA',
        'ESTADO', // 1=activa, 0=inactiva
    ];

    /** Relación: una pregunta puede estar asociada a muchos usuarios (pivot con respuesta hash) */
    public function usuarioPreguntas()
    {
        return $this->hasMany(UsuarioPregunta::class, 'FK_COD_PREGUNTA', 'COD_PREGUNTA');
    }
}
