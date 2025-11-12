<?php

namespace App\Providers;

use App\Services\Permissions\PermissionChecker;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
     * Registra gates/abilities.
     */
    public function boot(PermissionChecker $permissions): void
    {
        $this->registerPolicies();

        Gate::before(static function ($user) use ($permissions) {
            return $permissions->isAdmin($user) ? true : null;
        });

        $this->registerMenuGate('seguridad.menu', [
            'SEGURIDAD_PERMISOS',
            'SEGURIDAD_OBJETOS',
            'SEGURIDAD_ROLES',
            'SEGURIDAD_BITACORA',
            'SEGURIDAD_BACKUPS',
            'SEGURIDAD_USUARIOS',
        ], $permissions);

        $this->registerMenuGate('personas.menu', [
            'PERSONAS_DOCTORES',
            'PERSONAS_PACIENTES',
            'PERSONAS_RECEPCIONISTAS',
            'PERSONAS_ADMINISTRADORES',
        ], $permissions);

        $this->registerObjectGates([
            'seguridad.permisos.ver'       => 'SEGURIDAD_PERMISOS',
            'seguridad.objetos.ver'        => 'SEGURIDAD_OBJETOS',
            'seguridad.roles.ver'          => 'SEGURIDAD_ROLES',
            'seguridad.bitacora.ver'       => 'SEGURIDAD_BITACORA',
            'seguridad.backups.ver'        => 'SEGURIDAD_BACKUPS',
            'seguridad.usuarios.ver'       => 'SEGURIDAD_USUARIOS',
            'personas.doctores.ver'        => 'PERSONAS_DOCTORES',
            'personas.pacientes.ver'       => 'PERSONAS_PACIENTES',
            'personas.recepcionistas.ver'  => 'PERSONAS_RECEPCIONISTAS',
            'personas.administradores.ver' => 'PERSONAS_ADMINISTRADORES',
        ], $permissions);
    }

    private function registerMenuGate(string $ability, array $objects, PermissionChecker $permissions): void
    {
        Gate::define($ability, static function ($user) use ($permissions, $objects) {
            foreach ($objects as $object) {
                if ($permissions->hasPermission($user, $object, 'VER')) {
                    return true;
                }
            }

            return false;
        });
    }

    private function registerObjectGates(array $definitions, PermissionChecker $permissions): void
    {
        foreach ($definitions as $ability => $definition) {
            $object = is_array($definition)
                ? ($definition['object'] ?? ($definition[0] ?? null))
                : $definition;

            $action = is_array($definition)
                ? strtoupper($definition['action'] ?? ($definition[1] ?? 'VER'))
                : 'VER';

            if (!$object) {
                continue;
            }

            Gate::define($ability, static function ($user) use ($permissions, $object, $action) {
                return $permissions->hasPermission($user, $object, $action);
            });
        }
    }
}
