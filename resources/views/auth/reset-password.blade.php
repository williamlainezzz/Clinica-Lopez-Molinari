<x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        {{-- Token de restablecimiento (una sola vez) --}}
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        {{-- Correo --}}
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

        {{-- Pregunta de seguridad (si existe) --}}
        @if (!empty($secQuestion))
            <div class="mt-4">
                <x-input-label for="security_answer" :value="__('Pregunta de seguridad')" />

                <div class="mt-1 rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700">
                    {{ $secQuestion }}
                </div>

                <input type="hidden" name="security_question_id" value="{{ $secQuestionId }}"/>

                <x-text-input
                    id="security_answer"
                    class="block mt-2 w-full"
                    type="text"
                    name="security_answer"
                    :value="old('security_answer')"
                    required
                />
                <x-input-error :messages="$errors->get('security_answer')" class="mt-2" />

                <p class="mt-2 text-xs text-slate-600">
                    Si no recuerda su pregunta de seguridad, por favor contacte a soporte:
                    <strong>{{ config('mail.from.address') }}</strong>
                </p>
            </div>
        @endif

        {{-- Nueva contraseña + ojo SVG --}}
        <div class="mt-4">
            <x-input-label for="password" :value="__('Nueva contraseña')" />
            <div class="relative">
  <x-text-input
      id="password"
      name="password"
      type="password"
      class="block mt-1 w-full pr-12"   {{-- <— deja espacio para el ojo --}}
      required
      autocomplete="new-password"
  />
  <button type="button"
          class="text-gray-500 hover:text-gray-700"
          aria-label="Mostrar u ocultar contraseña"
          data-eye-for="password"
          style="position:absolute; right:10px; top:50%; transform:translateY(-50%);">
    {{-- eye open --}}
    <svg class="h-5 w-5 eye-open" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
      <circle cx="12" cy="12" r="3" stroke-width="1.8" />
    </svg>
    {{-- eye closed --}}
    <svg class="h-5 w-5 eye-closed hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
            d="M3 3l18 18M10.584 10.587A3 3 0 0113.414 13.41M9.88 4.6A9.98 9.98 0 0112 4c4.477 0 8.268 2.943 9.542 7a10.05 10.05 0 01-2.082 3.354M6.7 6.704A10.05 10.05 0 004.458 11 10.05 10.05 0 007.7 15.296M12 7a5 5 0 015 5" />
    </svg>
  </button>
</div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />

            {{-- Indicadores fijos (no cambian el alto) --}}
            <div id="pw-rules" class="mt-2 text-sm space-y-1" style="min-height: 80px;">
                <div class="pw-rule text-red-600" data-rule="len">Mínimo 10 caracteres</div>
                <div class="pw-rule text-red-600" data-rule="upper">Incluye una mayúscula</div>
                <div class="pw-rule text-red-600" data-rule="lower">Incluye una minúscula</div>
                <div class="pw-rule text-red-600" data-rule="num">Incluye un número</div>
                <div class="pw-rule text-red-600" data-rule="sym">Incluye un símbolo</div>
            </div>
        </div>

        {{-- Confirmación + ojo SVG --}}
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirmar contraseña')" />
            <div class="relative">
  <x-text-input
      id="password_confirmation"
      class="block mt-1 w-full pr-12"  {{-- <— espacio para el ojo --}}
      type="password"
      name="password_confirmation"
      required
      autocomplete="new-password"
  />
  <button type="button"
          class="text-gray-500 hover:text-gray-700"
          aria-label="Mostrar u ocultar confirmación"
          data-eye-for="password_confirmation"
          style="position:absolute; right:10px; top:50%; transform:translateY(-50%);">
    {{-- eye open --}}
    <svg class="h-5 w-5 eye-open" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
      <circle cx="12" cy="12" r="3" stroke-width="1.8" />
    </svg>
    {{-- eye closed --}}
    <svg class="h-5 w-5 eye-closed hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
            d="M3 3l18 18M10.584 10.587A3 3 0 0113.414 13.41M9.88 4.6A9.98 9.98 0 0112 4c4.477 0 8.268 2.943 9.542 7a10.05 10.05 0 01-2.082 3.354M6.7 6.704A10.05 10.05 0 004.458 11 10.05 10.05 0 007.7 15.296M12 7a5 5 0 015 5" />
    </svg>
  </button>
</div>

            <div id="match-msg" class="text-sm mt-2 text-red-600" style="min-height: 20px;">
                La contraseña debe coincidir.
            </div>
        </div>

        <div class="flex items-center justify-between mt-6">
            <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900">
                {{ __('Volver a iniciar sesión') }}
            </a>

            <x-primary-button>
                {{ __('Restablecer contraseña') }}
            </x-primary-button>
        </div>
    </form>

    {{-- Script de validación en vivo + ojos --}}
    <script>
      (function () {
        const $pw = document.getElementById('password');
        const $pc = document.getElementById('password_confirmation');
        const $match = document.getElementById('match-msg');
        const rulesEls = {
          len:   document.querySelector('[data-rule="len"]'),
          upper: document.querySelector('[data-rule="upper"]'),
          lower: document.querySelector('[data-rule="lower"]'),
          num:   document.querySelector('[data-rule="num"]'),
          sym:   document.querySelector('[data-rule="sym"]'),
        };

        const tests = {
          len:   v => v.length >= 10,
          upper: v => /[A-Z]/.test(v),
          lower: v => /[a-z]/.test(v),
          num:   v => /\d/.test(v),
          sym:   v => /[^A-Za-z0-9]/.test(v),
        };

        function setColor(el, ok) {
          el.classList.toggle('text-green-600', ok);
          el.classList.toggle('text-red-600', !ok);
        }

        function updateRules() {
          const v = $pw.value || '';
          Object.keys(tests).forEach(k => setColor(rulesEls[k], tests[k](v)));

          const same = v !== '' && v === ($pc.value || '');
          setColor($match, same);
          $match.textContent = same ? 'Las contraseñas coinciden.' : 'La contraseña debe coincidir.';
        }

        $pw.addEventListener('input', updateRules);
        $pc.addEventListener('input', updateRules);
        updateRules();

        // Ojitos: alterna tipo e iconos (open/closed)
        document.querySelectorAll('[data-eye-for]').forEach(btn => {
          btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-eye-for');
            const input = document.getElementById(id);
            if (!input) return;

            const open = btn.querySelector('.eye-open');
            const closed = btn.querySelector('.eye-closed');

            if (input.type === 'password') {
              input.type = 'text';
              if (open) open.classList.add('hidden');
              if (closed) closed.classList.remove('hidden');
            } else {
              input.type = 'password';
              if (open) open.classList.remove('hidden');
              if (closed) closed.classList.add('hidden');
            }
          });
        });
      })();
    </script>
</x-guest-layout>
