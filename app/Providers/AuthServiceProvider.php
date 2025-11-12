<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

// ✅ añade estos use para mapear la policy
use App\Models\Cita;
use App\Policies\CitaPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Mapea modelos => policies.
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // \App\Models\Model::class => \App\Policies\ModelPolicy::class,
        Cita::class => CitaPolicy::class,
    ];

    /**
     * ID del rol que consideramos súper-administrador por defecto.
     * (se mantiene 1 por compatibilidad)
     */
    private int $ADMIN_ROLE_ID = 1;

    public function boot(): void
    {
        $this->registerPolicies();

        // ------- Helpers locales -------
        $isAdmin = function ($user): bool {
            $rolId = (int)($user->FK_COD_ROL ?? 0);

            // 1) Por ID
            if ($rolId === $this->ADMIN_ROLE_ID) {
                return true;
            }

            // 2) Por nombre de rol (tolerante a mayúsculas/minúsculas)
            $nom = DB::table('tbl_rol')->where('COD_ROL', $rolId)->value('NOM_ROL');
            return $nom && strtoupper(trim($nom)) === 'ADMIN';
        };

        $roleIs = function ($user, array $roles) {
            $rolId = (int)($user->FK_COD_ROL ?? 0);
            if ($rolId === 0) {
                return false;
            }

            $nom = DB::table('tbl_rol')->where('COD_ROL', $rolId)->value('NOM_ROL');
            if (!$nom) {
                return false;
            }

            $nom = strtoupper(trim($nom));
            $roles = array_map(fn ($r) => strtoupper(trim($r)), $roles);

            return in_array($nom, $roles, true);
        };

        $has = function ($user, string $objeto, string $accion = 'VER'): bool {
            // Usa helper puede() si existe
            if (function_exists('puede')) {
                return puede($objeto, $accion);
            }

            // Fallback directo a la BD
            $rolId  = (int)($user->FK_COD_ROL ?? 0);
            $accion = strtoupper($accion);
            $row = DB::selectOne(
                "SELECT fn_tiene_permiso(?, ?, ?) AS ok",
                [$rolId, $objeto, $accion]
            );
            return $row && (int)$row->ok === 1;
        };

        // ==============================
        // Gate para mostrar el bloque Seguridad en menú
        // ==============================
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

        // ==============================
        // Gates por pantalla (Personas)
        // ==============================
        Gate::define('personas.menu', function ($user) use ($isAdmin, $has, $roleIs) {
            if ($isAdmin($user)) return true;
            if ($roleIs($user, ['RECEPCIONISTA'])) return true;

            $objetos = [
                'PERSONAS_DOCTORES',
                'PERSONAS_PACIENTES',
                'PERSONAS_RECEPCIONISTAS',
                'PERSONAS_ADMINISTRADORES',
            ];

            foreach ($objetos as $obj) {
                if ($has($user, $obj, 'VER')) {
                    return true;
                }
            }
            return false;
        });

        Gate::define('personas.doctores.ver', function ($user) use ($isAdmin, $has, $roleIs) {
            if ($isAdmin($user) || $roleIs($user, ['RECEPCIONISTA'])) return true;
            return $has($user, 'PERSONAS_DOCTORES', 'VER');
        });

        Gate::define('personas.pacientes.ver', function ($user) use ($isAdmin, $has, $roleIs) {
            if ($isAdmin($user) || $roleIs($user, ['RECEPCIONISTA'])) return true;
            return $has($user, 'PERSONAS_PACIENTES', 'VER');
        });

        Gate::define('personas.recepcionistas.ver', fn ($user) =>
            $isAdmin($user) || $has($user, 'PERSONAS_RECEPCIONISTAS', 'VER')
        );

        Gate::define('personas.administradores.ver', fn ($user) =>
            $isAdmin($user) || $has($user, 'PERSONAS_ADMINISTRADORES', 'VER')
        );
    }
}
