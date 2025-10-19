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
     * ID del rol que consideramos súper-administrador.
     * Ajusta si tu ADMIN no es 1.
     */
    private int $ADMIN_ROLE_ID = 1;

    /**
     * Registra gates/abilities.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // ------- Helpers locales -------
        $isAdmin = function ($user): bool {
            $rol = (int)($user->FK_COD_ROL ?? 0);
            return $rol === $this->ADMIN_ROLE_ID;
        };

        $has = function ($user, string $objeto, string $accion = 'VER'): bool {
            // Usa helper puede() si está disponible.
            if (function_exists('puede')) {
                return puede($objeto, $accion);
            }

            // Fallback directo a la BD si no está el helper.
            $rolId = (int)($user->FK_COD_ROL ?? 0);
            $accion = strtoupper($accion);
            $row = DB::selectOne(
                "SELECT fn_tiene_permiso(?, ?, ?) AS ok",
                [$rolId, $objeto, $accion]
            );
            return $row && (int)$row->ok === 1;
        };

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
                'SEGURIDAD_USUARIOS', // opcional si lo manejas como objeto
            ];

            foreach ($objetos as $obj) {
                if ($has($user, $obj, 'VER')) {
                    return true;
                }
            }
            return false;
        });

        // ==========================================================
        // Gates por pantalla (útiles si quieres @can() en las vistas)
        // ==========================================================
        Gate::define('seguridad.permisos.ver',   fn ($user) => $isAdmin($user) || $has($user, 'SEGURIDAD_PERMISOS', 'VER'));
        Gate::define('seguridad.objetos.ver',    fn ($user) => $isAdmin($user) || $has($user, 'SEGURIDAD_OBJETOS',  'VER'));
        Gate::define('seguridad.roles.ver',      fn ($user) => $isAdmin($user) || $has($user, 'SEGURIDAD_ROLES',    'VER'));
        Gate::define('seguridad.bitacora.ver',   fn ($user) => $isAdmin($user) || $has($user, 'SEGURIDAD_BITACORA', 'VER'));
        Gate::define('seguridad.backups.ver',    fn ($user) => $isAdmin($user) || $has($user, 'SEGURIDAD_BACKUPS',  'VER'));
        Gate::define('seguridad.usuarios.ver',   fn ($user) => $isAdmin($user) || $has($user, 'SEGURIDAD_USUARIOS', 'VER'));
    }
}
