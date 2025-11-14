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

    // ❗ Tu tabla NO tiene created_at/updated_at
    public $timestamps = false;
    const CREATED_AT = null;
    const UPDATED_AT = null;

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

    /** Password para Auth: está en PWD_USUARIO */
    public function getAuthPassword()
    {
        return $this->PWD_USUARIO;
    }

    /** Alias por si alguna vista usa Auth::user()->name */
    public function getNameAttribute()
    {
        return $this->USR_USUARIO;
    }

    /** Email a usar para reset de contraseña (desde tbl_correo por persona) */
    public function getEmailForPasswordReset()
    {
        return DB::table('tbl_correo')
            ->where('FK_COD_PERSONA', $this->FK_COD_PERSONA)
            ->orderByDesc('COD_CORREO')
            ->value('CORREO');
    }

    /** Canal de notificaciones por mail -> usa el correo anterior */
    public function routeNotificationForMail($notification = null)
    {
        return $this->getEmailForPasswordReset();
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

    /**
     * Verifica si el usuario tiene permiso sobre un objeto / acción
     * usando la función MySQL fn_tiene_permiso(FK_COD_ROL, OBJETO, ACCION).
     *
     * Ejemplo de uso:
     *   auth()->user()->tienePermiso('SEGURIDAD_USUARIOS', 'VER');
     */
    public function tienePermiso(string $objeto, string $accion): bool
    {
        // Si quieres que el rol ADMIN (COD_ROL = 1) tenga acceso total:
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
