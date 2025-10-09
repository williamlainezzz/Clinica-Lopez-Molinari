<x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        {{-- Token de restablecimiento --}}
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

<!-- Correo -->
<div>
    <x-input-label for="email" :value="__('Correo electrónico')" />
    <x-text-input
        id="email"
        class="block mt-1 w-full"
        type="email"
        name="email"
        :value="old('email', $request->email)"
        required
        autofocus
        autocomplete="username"
        placeholder="{{ __('tucorreo@ejemplo.com') }}"
    />
    <x-input-error :messages="$errors->get('email')" class="mt-2" />
</div>

        {{-- PASO 5.1 — Pregunta de seguridad (si existe) --}}
@if (!empty($secQuestion))
    <div class="mt-4">
        <x-input-label for="security_answer" :value="__('Pregunta de seguridad')" />

        {{-- texto de la pregunta --}}
        <div class="mt-1 rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700">
            {{ $secQuestion }}
        </div>

        {{-- ID de la pregunta mostrada --}}
        <input type="hidden" name="security_question_id" value="{{ $secQuestionId }}">

        {{-- respuesta del usuario --}}
        <x-text-input id="security_answer" class="block mt-2 w-full"
                      type="text" name="security_answer" :value="old('security_answer')" required />
        <x-input-error :messages="$errors->get('security_answer')" class="mt-2" />
    </div>

    {{-- Aviso de soporte --}}
    <p class="mt-2 text-xs text-slate-600">
        Si no recuerda su pregunta de seguridad, por favor contactarse a soporte:
        <strong>{{ config('mail.from.address') }}</strong>
    </p>
@endif


        {{-- Nueva contraseña --}}
        <div class="mt-4">
            <x-input-label for="password" :value="__('Nueva contraseña')" />
<x-text-input
    id="password"
    name="password"
    type="password"
    class="block mt-1 w-full"
    required
    autocomplete="new-password"
    minlength="10"
    pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).{10,}"
/>
<small class="text-gray-600">
  Mínimo 10 caracteres, con <strong>mayúsculas</strong>, <strong>minúsculas</strong>,
  <strong>número</strong> y <strong>símbolo</strong>.
</small>
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
