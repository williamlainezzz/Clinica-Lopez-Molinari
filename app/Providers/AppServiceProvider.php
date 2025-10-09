<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Validation\Rules\Password;

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

   
    // Configuración de reglas de contraseña más estrictas
    public function boot(): void
{
    Password::defaults(function () {
        return Password::min(10)   // longitud mínima
            ->mixedCase()          // al menos 1 mayúscula y 1 minúscula
            ->numbers()            // al menos 1 dígito
            ->symbols()            // al menos 1 símbolo
            ->uncompromised();     // no aparezca en brechas conocidas
    });
}

}
