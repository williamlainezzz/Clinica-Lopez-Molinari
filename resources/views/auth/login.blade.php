<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if(session('registro_exitoso') && session('username_generado'))
        <div x-data="{ open: true }" x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-gray-900/60"></div>
            <div class="relative bg-white w-full max-w-md rounded-lg shadow-xl p-6">
                <h3 class="text-lg font-semibold text-gray-900">Bienvenido</h3>
                <p class="mt-2 text-sm text-gray-700">Tu cuenta fue creada correctamente. Para continuar, ingresa tus credenciales manualmente.</p>
                <p class="mt-3 text-sm"><strong>Usuario generado:</strong> <span class="font-mono">{{ session('username_generado') }}</span></p>
                <div class="mt-5 text-right">
                    <button type="button" @click="open = false" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Aceptar</button>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div>
            <x-input-label for="login" :value="__('Usuario o correo')" />
            <x-text-input id="login" class="block mt-1 w-full" type="text" name="login" :value="old('login')" required autofocus autocomplete="username email" />
            <x-input-error :messages="$errors->get('login')" class="mt-2" />
            <x-input-error :messages="$errors->get('USR_USUARIO')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Contraseña')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Recordarme') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('¿Olvidaste tu contraseña?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">{{ __('Iniciar sesión') }}</x-primary-button>
        </div>
    </form>
</x-guest-layout>
