<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\Pagination\Paginator;

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
        // Usar las vistas de paginación de Bootstrap (se integra con AdminLTE)
        // Si en algún momento migras a Bootstrap 5 puedes cambiar a:
        // Paginator::useBootstrapFive();
        Paginator::useBootstrapFour();

        // Configuración de reglas de contraseña más estrictas
        Password::defaults(function () {
            return Password::min(10)   // longitud mínima
                ->mixedCase()          // al menos 1 mayúscula y 1 minúscula
                ->numbers()            // al menos 1 dígito
                ->symbols()            // al menos 1 símbolo
                ->uncompromised();     // no aparezca en brechas conocidas
        });
    }
}
