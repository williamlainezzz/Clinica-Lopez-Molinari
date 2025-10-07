<x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        {{-- Token de restablecimiento --}}
        <input type="hidden" name="token" value="{{ $token }}">

        {{-- Correo --}}
        <div>
            <x-input-label for="email" :value="__('Correo electrónico')" />
            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email', $email ?? request('email'))"
                required
                autofocus
                autocomplete="email"
                placeholder="{{ __('tucorreo@ejemplo.com') }}"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Nueva contraseña --}}
        <div class="mt-4">
            <x-input-label for="password" :value="__('Nueva contraseña')" />
            <x-text-input
                id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                required
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Confirmación --}}
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirmar contraseña')" />
            <x-text-input
                id="password_confirmation"
                class="block mt-1 w-full"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900">
                {{ __('Volver a iniciar sesión') }}
            </a>

            <x-primary-button>
                {{ __('Restablecer contraseña') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
