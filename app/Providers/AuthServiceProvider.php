<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

// usamos tu helper "puede()" que llama a fn_tiene_permiso()
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Model::class => Policy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Grupo Seguridad (muestra el grupo si el rol tiene VER en algún submódulo)
        Gate::define('seguridad.menu', function ($user) {
            return puede('SEGURIDAD_BACKUPS','VER')
                || puede('SEGURIDAD_BITACORA','VER')
                || puede('SEGURIDAD_ROLES','VER')
                || puede('SEGURIDAD_OBJETOS','VER')
                || puede('SEGURIDAD_PERMISOS','VER');
        });

        // Subopciones (cada item del menú)
        Gate::define('seguridad.backups.ver',   fn($u) => puede('SEGURIDAD_BACKUPS','VER'));
        Gate::define('seguridad.bitacora.ver',  fn($u) => puede('SEGURIDAD_BITACORA','VER'));
        Gate::define('seguridad.roles.ver',     fn($u) => puede('SEGURIDAD_ROLES','VER'));
        Gate::define('seguridad.objetos.ver',   fn($u) => puede('SEGURIDAD_OBJETOS','VER'));
        Gate::define('seguridad.permisos.ver',  fn($u) => puede('SEGURIDAD_PERMISOS','VER'));

        // (Opcional) si quieres controlar “Usuarios” por permisos también:
        // Gate::define('seguridad.usuarios.ver', fn($u) => puede('SEGURIDAD_USUARIOS','VER'));
    }
}
