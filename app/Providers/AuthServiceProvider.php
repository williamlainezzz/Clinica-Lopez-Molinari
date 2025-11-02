<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Si usas policies, mapea aquí tus modelos => policies.
     * No las necesitamos por ahora.
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // \App\Models\Model::class => \App\Policies\ModelPolicy::class,
    ];

    /**
     * ID del rol que consideramos súper-administrador por defecto.
     * (se mantiene 1 por compatibilidad, pero ya no dependemos sólo de él)
     */
    private int $ADMIN_ROLE_ID = 1;

    /**
     * Registra gates/abilities.
     */
    public function boot(): void
    {
        $this->registerPolicies();

                // ---- Mapeo de códigos de rol por submenú (ajusta según tus códigos reales) ----
                // Ejemplo: 1 = Admin, 2 = Doctor, 3 = Paciente, 4 = Recepcionista
                $personasMap = [
                    'doctores'       => [1, 2], // quién ve Doctores
                    'pacientes'      => [1, 3], // quién ve Pacientes
                    'recepcionistas' => [1, 4], // quién ve Recepcionistas
                    'administradores'=> [1],    // quién ve Administradores
                ];
        
                // ------- Helpers locales -------
                $isAdmin = function ($user) use ($personasMap): bool {
                    $rolId = (int)($user->FK_COD_ROL ?? session('FK_COD_ROL') ?? 0);
                    return in_array($rolId, $personasMap['administradores'], true);
                };
        
                $has = function ($user, string $objeto, string $accion = 'VER'): bool {
                    // Usa helper puede() si está cargado
                    if (function_exists('puede')) {
                        return puede($objeto, $accion);
                    }
        
                    // Fallback directo a la BD si no está el helper
                    $rolId  = (int)($user->FK_COD_ROL ?? session('FK_COD_ROL') ?? 0);
                    $accion = strtoupper($accion);
                    $row = DB::selectOne(
                        "SELECT fn_tiene_permiso(?, ?, ?) AS ok",
                        [$rolId, $objeto, $accion]
                    );
                    return $row && (int)$row->ok === 1;
                };
        
                // Gates por submenú: usan primero session('FK_COD_ROL') si existe, sino $user->FK_COD_ROL
                Gate::define('personas.doctores', function ($user) use ($personasMap, $isAdmin) {
                    $rolId = (int)(session('FK_COD_ROL') ?? $user->FK_COD_ROL ?? 0);
                    return $isAdmin($user) || in_array($rolId, $personasMap['doctores'], true);
                });
        
                Gate::define('personas.pacientes', function ($user) use ($personasMap, $isAdmin) {
                    $rolId = (int)(session('FK_COD_ROL') ?? $user->FK_COD_ROL ?? 0);
                    return $isAdmin($user) || in_array($rolId, $personasMap['pacientes'], true);
                });
        
                Gate::define('personas.recepcionistas', function ($user) use ($personasMap, $isAdmin) {
                    $rolId = (int)(session('FK_COD_ROL') ?? $user->FK_COD_ROL ?? 0);
                    return $isAdmin($user) || in_array($rolId, $personasMap['recepcionistas'], true);
                });
        
                Gate::define('personas.administradores', function ($user) use ($personasMap, $isAdmin) {
                    $rolId = (int)(session('FK_COD_ROL') ?? $user->FK_COD_ROL ?? 0);
                    return $isAdmin($user) || in_array($rolId, $personasMap['administradores'], true);
                });
        
                // Gate padre que controla visibilidad del bloque "Personas & Usuarios"
                Gate::define('personas.menu', function ($user) use ($personasMap, $isAdmin, $has) {
                    // Mostrar si admin
                    if ($isAdmin($user)) return true;
        
                    // Mostrar si rol está en alguna lista
                    $rolId = (int)(session('FK_COD_ROL') ?? $user->FK_COD_ROL ?? 0);
                    foreach ($personasMap as $list) {
                        if (in_array($rolId, $list, true)) return true;
                    }
        
                    // Fallback por permisos finos (si existe)
                    return $has($user, 'PERSONAS_USUARIOS', 'VER');
                });
        

        // ==========================================================
        // Gate para mostrar el BLOQUE "Seguridad" en el menú
        // Visible si: es ADMIN o tiene VER en CUALQUIER objeto de seguridad.
        // ==========================================================
        Gate::define('seguridad.menu', function ($user) use ($isAdmin, $has) {
            if ($isAdmin($user)) {
                return true;
            }
 
            $objetos = [
                'SEGURIDAD_PERMISOS',
                'SEGURIDAD_OBJETOS',
                'SEGURIDAD_ROLES',
                'SEGURIDAD_BITACORA',
                'SEGURIDAD_BACKUPS',
                'SEGURIDAD_USUARIOS',
            ];
 
            foreach ($objetos as $obj) {
                if ($has($user, $obj, 'VER')) {
                    return true;
                }
            }
            return false;
        });
 
        // ==========================================================
        // Gates por pantalla
        // ==========================================================
        Gate::define('seguridad.permisos.ver', fn ($user) => $isAdmin($user) || $has($user, 'SEGURIDAD_PERMISOS', 'VER'));
        Gate::define('seguridad.objetos.ver',  fn ($user) => $isAdmin($user) || $has($user, 'SEGURIDAD_OBJETOS',  'VER'));
        Gate::define('seguridad.roles.ver',    fn ($user) => $isAdmin($user) || $has($user, 'SEGURIDAD_ROLES',    'VER'));
        Gate::define('seguridad.bitacora.ver', fn ($user) => $isAdmin($user) || $has($user, 'SEGURIDAD_BITACORA', 'VER'));
        Gate::define('seguridad.backups.ver',  fn ($user) => $isAdmin($user) || $has($user, 'SEGURIDAD_BACKUPS',  'VER'));
        Gate::define('seguridad.usuarios.ver', fn ($user) => $isAdmin($user) || $has($user, 'SEGURIDAD_USUARIOS', 'VER'));
    }
}
