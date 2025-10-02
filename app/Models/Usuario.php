<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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

    // Indica a Laravel dónde está la contraseña
    public function getAuthPassword()
    {
        return $this->PWD_USUARIO;
    }

    // Opcional: para usar Auth::user()->name sin romper vistas
    public function getNameAttribute()
    {
        return $this->USR_USUARIO;
    }
}
