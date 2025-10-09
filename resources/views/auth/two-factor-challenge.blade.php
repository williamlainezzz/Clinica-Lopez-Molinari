<x-guest-layout>
    {{-- Mensaje / estado --}}
    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif

    <h1 class="text-lg font-semibold mb-2">Verificación en dos pasos</h1>
    <p class="text-sm text-gray-600 mb-4">
        Ingresa el código de 6 dígitos que enviamos a tu correo.
    </p>

    <form method="POST" action="{{ route('two-factor.challenge.store') }}">
        @csrf
        <div class="mb-4">
            <x-input-label for="code" :value="__('Código')" />
            <x-text-input id="code" name="code" type="text"
                inputmode="numeric" pattern="[0-9]*" maxlength="6"
                class="block mt-1 w-full"
                placeholder="000000" required autofocus />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900">
                {{ __('Cancelar e iniciar sesión de nuevo') }}
            </a>

            <x-primary-button>
                {{ __('Verificar código') }}
            </x-primary-button>
        </div>
    </form>

    <form method="POST" action="{{ route('two-factor.challenge.resend') }}" class="mt-4">
        @csrf
        <button type="submit" class="text-sm underline hover:no-underline">
            {{ __('Reenviar código') }}
        </button>
    </form>
</x-guest-layout>

  
