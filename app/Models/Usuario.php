<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'tbl_usuario';
    protected $primaryKey = 'COD_USUARIO';
    public $timestamps = false;

    // La tabla no tiene remember_token
    protected $rememberTokenName = null;

    protected $fillable = [
        'USR_USUARIO',
        'PWD_USUARIO',
        'FK_COD_PERSONA',
        'FK_COD_ROL',
        'ESTADO_USUARIO',
    ];

    protected $hidden = ['PWD_USUARIO'];

    /**
     * Indica a Laravel dónde está la contraseña.
     */
    public function getAuthPassword()
    {
        return $this->PWD_USUARIO;
    }

    /**
     * Nombre "amigable" por si alguna vista usa Auth::user()->name.
     */
    public function getNameAttribute()
    {
        return $this->USR_USUARIO;
    }

    /**
     * Devuelve el correo asociado al usuario (desde tbl_correo por FK_COD_PERSONA).
     * Laravel usará este valor para enviar el enlace de restablecimiento.
     */
    public function getEmailForPasswordReset()
    {
        return DB::table('tbl_correo')
            ->where('FK_COD_PERSONA', $this->FK_COD_PERSONA)
            ->orderByDesc('COD_CORREO') // si hay varios, toma el más reciente
            ->value('CORREO');
    }

    /**
     * Hace que las notificaciones por correo usen el email anterior.
     */
    public function routeNotificationForMail($notification = null)
    {
        return $this->getEmailForPasswordReset();
    }

        /** Relación directa a la tabla puente (con los hashes) */
    public function usuarioPreguntas()
    {
        return $this->hasMany(UsuarioPregunta::class, 'FK_COD_USUARIO', 'COD_USUARIO');
    }

    /**
     * Relación many-to-many a preguntas, con acceso al hash en el pivot.
     * Útil si quieres traer las preguntas y además leer RESPUESTA_HASH desde ->pivot.
     */
    public function preguntasSeguridad()
    {
        return $this->belongsToMany(
            PreguntaSeguridad::class,
            'tbl_usuario_pregunta',
            'FK_COD_USUARIO',   // clave local en pivot
            'FK_COD_PREGUNTA'   // clave relacionada en pivot
        )->withPivot('RESPUESTA_HASH');
    }

}
