<?php

namespace App\Models;

use App\Notifications\ResetPasswordEs;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'tbl_usuario';
    protected $primaryKey = 'COD_USUARIO';

    public $timestamps = false;
    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $rememberTokenName = null;

    protected $fillable = [
        'USR_USUARIO',
        'PWD_USUARIO',
        'FK_COD_PERSONA',
        'FK_COD_ROL',
        'ESTADO_USUARIO',
    ];

    protected $hidden = ['PWD_USUARIO'];

    public function getAuthPassword()
    {
        return $this->PWD_USUARIO;
    }

    public function getNameAttribute()
    {
        return $this->USR_USUARIO;
    }

    public function getEmailForPasswordReset()
    {
        return DB::table('tbl_correo')
            ->where('FK_COD_PERSONA', $this->FK_COD_PERSONA)
            ->orderByDesc('COD_CORREO')
            ->value('CORREO');
    }

    public function routeNotificationForMail($notification = null)
    {
        return $this->getEmailForPasswordReset();
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordEs($token));
    }

    public function usuarioPreguntas()
    {
        return $this->hasMany(UsuarioPregunta::class, 'FK_COD_USUARIO', 'COD_USUARIO');
    }

    public function preguntasSeguridad()
    {
        return $this->belongsToMany(
            PreguntaSeguridad::class,
            'tbl_usuario_pregunta',
            'FK_COD_USUARIO',
            'FK_COD_PREGUNTA'
        )->withPivot('RESPUESTA_HASH');
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'FK_COD_PERSONA', 'COD_PERSONA');
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'FK_COD_ROL', 'COD_ROL');
    }

    public function tienePermiso(string $objeto, string $accion): bool
    {
        if ((int) $this->FK_COD_ROL === 1) {
            return true;
        }

        $row = DB::selectOne(
            'SELECT fn_tiene_permiso(?, ?, ?) AS ok',
            [
                (int) $this->FK_COD_ROL,
                $objeto,
                $accion,
            ]
        );

        if (!$row) {
            return false;
        }

        return (int) $row->ok === 1;
    }
}
