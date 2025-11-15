<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1) Usar estilos de paginación de Bootstrap en TODO el sistema
        Paginator::useBootstrap();

        // 2) Reglas globales de contraseña
        Password::defaults(function () {
            return Password::min(10)   // longitud mínima
                ->mixedCase()          // al menos 1 mayúscula y 1 minúscula
                ->numbers()            // al menos 1 dígito
                ->symbols()            // al menos 1 símbolo
                ->uncompromised();     // no aparezca en brechas conocidas
        });

        // 3) Directiva Blade para verificar permisos de objetos
        //
        //    Uso en Blade:
        //       @permiso('AGENDA_CITAS', 'VER')
        //           ... enlace / sección ...
        //       @endpermiso
        //
        Blade::if('permiso', function (string $objeto, string $accion = 'VER') {
            $user = Auth::user();

            if (!$user) {
                return false;
            }

            // Usa el método que agregamos en App\Models\Usuario
            return $user->tienePermiso($objeto, $accion);
        });
    }
}
