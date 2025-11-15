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
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // \App\Models\Model::class => \App\Policies\ModelPolicy::class,
    ];

    /**
     * ID del rol que consideramos súper-administrador por defecto.
     */
    private int $ADMIN_ROLE_ID = 1;

    /**
     * Registra gates/abilities.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // ------- Helpers locales -------

        // ¿Es ADMIN?
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

        // ¿El rol está en un conjunto de nombres?
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

        // Verificación de permiso contra fn_tiene_permiso / helper puede()
        $has = function ($user, string $objeto, string $accion = 'VER'): bool {
            // Usa helper puede() si está disponible
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

        // ==========================================================
        // SEGURIDAD
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

        Gate::define('seguridad.permisos.ver', fn ($user) => $isAdmin($user) || $has($user, 'SEGURIDAD_PERMISOS', 'VER'));
        Gate::define('seguridad.objetos.ver',  fn ($user) => $isAdmin($user) || $has($user, 'SEGURIDAD_OBJETOS',  'VER'));
        Gate::define('seguridad.roles.ver',    fn ($user) => $isAdmin($user) || $has($user, 'SEGURIDAD_ROLES',    'VER'));
        Gate::define('seguridad.bitacora.ver', fn ($user) => $isAdmin($user) || $has($user, 'SEGURIDAD_BITACORA', 'VER'));
        Gate::define('seguridad.backups.ver',  fn ($user) => $isAdmin($user) || $has($user, 'SEGURIDAD_BACKUPS',  'VER'));
        Gate::define('seguridad.usuarios.ver', fn ($user) => $isAdmin($user) || $has($user, 'SEGURIDAD_USUARIOS', 'VER'));

        // ==========================================================
        // PERSONAS
        // ==========================================================
        Gate::define('personas.menu', function ($user) use ($isAdmin, $has, $roleIs) {
            if ($isAdmin($user)) {
                return true;
            }

            // Por ejemplo RECEPCIONISTA ve siempre el menú de personas
            if ($roleIs($user, ['RECEPCIONISTA'])) {
                return true;
            }

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
            if ($isAdmin($user) || $roleIs($user, ['RECEPCIONISTA'])) {
                return true;
            }

            return $has($user, 'PERSONAS_DOCTORES', 'VER');
        });

        Gate::define('personas.pacientes.ver', function ($user) use ($isAdmin, $has, $roleIs) {
            if ($isAdmin($user) || $roleIs($user, ['RECEPCIONISTA'])) {
                return true;
            }

            return $has($user, 'PERSONAS_PACIENTES', 'VER');
        });

        Gate::define('personas.recepcionistas.ver', function ($user) use ($isAdmin, $has) {
            if ($isAdmin($user)) {
                return true;
            }

            return $has($user, 'PERSONAS_RECEPCIONISTAS', 'VER');
        });

        Gate::define('personas.administradores.ver', function ($user) use ($isAdmin, $has) {
            if ($isAdmin($user)) {
                return true;
            }

            return $has($user, 'PERSONAS_ADMINISTRADORES', 'VER');
        });

        // ==========================================================
        // AGENDA / CITAS
        // ==========================================================
        // Menú "Citas" visible si:
        //  - Es ADMIN, o
        //  - Rol DOCTOR / PACIENTE / RECEPCIONISTA, o
        //  - Tiene VER en cualquier objeto de agenda
        Gate::define('agenda.menu', function ($user) use ($isAdmin, $has, $roleIs) {
            if ($isAdmin($user) || $roleIs($user, ['DOCTOR', 'PACIENTE', 'RECEPCIONISTA'])) {
                return true;
            }

            $objetos = [
                'AGENDA_CITAS',
                'AGENDA_CALENDARIO',
                'AGENDA_REPORTES',
            ];

            foreach ($objetos as $obj) {
                if ($has($user, $obj, 'VER')) {
                    return true;
                }
            }

            return false;
        });

        // Ver Citas
        Gate::define('agenda.citas.ver', function ($user) use ($isAdmin, $has, $roleIs) {
            if ($isAdmin($user) || $roleIs($user, ['DOCTOR', 'PACIENTE', 'RECEPCIONISTA'])) {
                return true;
            }

            return $has($user, 'AGENDA_CITAS', 'VER');
        });

        // Calendario
        Gate::define('agenda.calendario.ver', function ($user) use ($isAdmin, $has, $roleIs) {
            if ($isAdmin($user) || $roleIs($user, ['DOCTOR', 'RECEPCIONISTA'])) {
                return true;
            }

            return $has($user, 'AGENDA_CALENDARIO', 'VER');
        });

        // Historial / Reportes de agenda
        Gate::define('agenda.reportes.ver', function ($user) use ($isAdmin, $has, $roleIs) {
            if ($isAdmin($user) || $roleIs($user, ['DOCTOR', 'RECEPCIONISTA'])) {
                return true;
            }

            return $has($user, 'AGENDA_REPORTES', 'VER');
        });
    }
}
