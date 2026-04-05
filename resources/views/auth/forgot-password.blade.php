<x-guest-layout>
    <div class="space-y-4">
        <p class="text-sm leading-6 text-slate-600">
            ¿Olvidaste tu contraseña? No hay problema. Ingresa tu correo y te enviaremos un enlace para restablecerla.
        </p>

        @if (session('status'))
            <div class="text-sm font-medium text-emerald-600">
                {{ __(session('status')) }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <div>
                <x-input-label for="email" :value="__('Correo electrónico')" />
                <x-text-input
                    id="email"
                    class="auth-input mt-2"
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

            <div class="flex items-center justify-between gap-3 pt-1">
                <a href="{{ url('/') }}" class="auth-link">
                    {{ __('Volver al inicio') }}
                </a>

                <button type="submit" class="auth-action">
                    {{ __('ENVIAR ENLACE DE RESTABLECIMIENTO') }}
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
