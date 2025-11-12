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

    // Tu tabla no tiene timestamps ni remember_token
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

    /** Password para Auth: está en PWD_USUARIO */
    public function getAuthPassword()
    {
        return $this->PWD_USUARIO;
    }

    /** Alias usado a veces por vistas: Auth::user()->name */
    public function getNameAttribute()
    {
        return $this->USR_USUARIO;
    }

    /** Email para reset (desde tbl_correo por persona) */
    public function getEmailForPasswordReset()
    {
        return DB::table('tbl_correo')
            ->where('FK_COD_PERSONA', $this->FK_COD_PERSONA)
            ->orderByDesc('COD_CORREO')
            ->value('CORREO');
    }

    /** Canal de notificación mail */
    public function routeNotificationForMail($notification = null)
    {
        return $this->getEmailForPasswordReset();
    }

    // ---------------- Relaciones ----------------
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

    /* ======= Helpers para Policies/Roles/Permisos ======= */

    /**
     * Verifica si el usuario tiene alguno de los roles indicados.
     * Acepta string ("ADMIN") o array(["ADMIN","DOCTOR"]).
     */
    public function esRol(string|array $roles): bool
    {
        $nom = strtoupper(trim(optional($this->rol)->NOM_ROL ?? ''));
        if ($nom === '') return false;

        $roles = (array)$roles;
        $roles = array_map(fn($r) => strtoupper(trim($r)), $roles);

        return in_array($nom, $roles, true);
    }

    /**
     * Verifica permiso contra TBL_PERMISO/TBL_OBJETO.
     * $obj puede ser string o array de nombres de objeto.
     * $accion: VER|CREAR|EDITAR|ELIMINAR
     */
    public function tienePermiso(string|array $obj, string $accion = 'VER'): bool
    {
        $accion = strtoupper($accion);
        if (!in_array($accion, ['VER','CREAR','EDITAR','ELIMINAR'])) {
            $accion = 'VER';
        }

        $objetos = (array)$obj;

        $count = DB::table('tbl_permiso as p')
            ->join('tbl_objeto as o', 'o.COD_OBJETO', '=', 'p.FK_COD_OBJETO')
            ->where('p.FK_COD_ROL', $this->FK_COD_ROL)
            ->whereIn('o.NOM_OBJETO', $objetos)
            ->where('p.ESTADO_PERMISO', 1)
            ->where("p.$accion", 1)
            ->count();

        return $count > 0;
    }

    public function personaId(): ?int
    {
        return optional($this->persona)->COD_PERSONA ?? null;
    }

    /** Para citas: id de doctor = COD_PERSONA si su rol es DOCTOR */
    public function doctorId(): ?int
    {
        return $this->esRol('DOCTOR') ? $this->personaId() : null;
    }

    /** Para citas: id de paciente = COD_PERSONA si su rol es PACIENTE */
    public function pacienteId(): ?int
    {
        return $this->esRol('PACIENTE') ? $this->personaId() : null;
    }
}
