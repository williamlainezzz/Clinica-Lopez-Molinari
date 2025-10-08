<x-guest-layout>
    {{-- Mensaje introductorio --}}
    <div class="mb-4 text-sm text-gray-600">
        {{ __('¿Olvidaste tu contraseña? No hay problema. Ingresa tu correo y te enviaremos un enlace para restablecerla.') }}
    </div>

    {{-- Estado de sesión (ej. "Te enviamos el enlace...") --}}
    <x-auth-session-status :status="session('status') ? __(session('status')) : null" />


    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        {{-- Correo --}}
        <div>
            <x-input-label for="email" :value="__('Correo electrónico')" />
            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="email"
                placeholder="{{ __('tucorreo@ejemplo.com') }}"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900">
                {{ __('Volver a iniciar sesión') }}
            </a>

            <x-primary-button>
                {{ __('Enviar enlace de restablecimiento') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
