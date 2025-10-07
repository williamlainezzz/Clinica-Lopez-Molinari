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
}
