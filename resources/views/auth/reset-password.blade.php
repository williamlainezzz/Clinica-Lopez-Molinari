<x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <x-input-label for="email" :value="__('Correo electrónico')" />
            <x-text-input
                id="email"
                class="auth-input mt-2"
                type="email"
                name="email"
                :value="old('email', $request->email)"
                required
                autofocus
                autocomplete="username"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        @if (!empty($secQuestion))
            <div class="space-y-2">
                <x-input-label for="security_answer" :value="__('Pregunta de seguridad')" />

                <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-700">
                    {{ $secQuestion }}
                </div>

                <input type="hidden" name="security_question_id" value="{{ $secQuestionId }}" />

                <x-text-input
                    id="security_answer"
                    class="auth-input"
                    type="text"
                    name="security_answer"
                    :value="old('security_answer')"
                    required
                />
                <x-input-error :messages="$errors->get('security_answer')" class="mt-2" />

                <p class="text-xs leading-5 text-slate-600">
                    Si no recuerda su pregunta de seguridad, por favor contacte a soporte:
                    <strong>{{ config('mail.from.address') }}</strong>
                </p>
            </div>
        @endif

        <div class="space-y-2">
            <x-input-label for="password" :value="__('Nueva contraseña')" />
            <div class="relative">
                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="auth-input pr-10"
                    required
                    autocomplete="new-password"
                />
                <button
                    type="button"
                    class="absolute inset-y-0 right-3 flex items-center text-slate-500 transition hover:text-slate-700"
                    aria-label="Mostrar u ocultar contraseña"
                    data-eye-for="password"
                >
                    <svg class="eye-open h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        <circle cx="12" cy="12" r="3" stroke-width="1.8" />
                    </svg>
                    <svg class="eye-closed hidden h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3l18 18M10.584 10.587A3 3 0 0113.414 13.41M9.88 4.6A9.98 9.98 0 0112 4c4.477 0 8.268 2.943 9.542 7a10.05 10.05 0 01-2.082 3.354M6.7 6.704A10.05 10.05 0 004.458 11 10.05 10.05 0 007.7 15.296M12 7a5 5 0 015 5" />
                    </svg>
                </button>
            </div>

            <div id="pw-rules" class="space-y-1 text-sm">
                <div class="pw-rule text-red-600" data-rule="len">Mínimo 10 caracteres</div>
                <div class="pw-rule text-red-600" data-rule="upper">Incluye una mayúscula</div>
                <div class="pw-rule text-red-600" data-rule="lower">Incluye una minúscula</div>
                <div class="pw-rule text-red-600" data-rule="num">Incluye un número</div>
                <div class="pw-rule text-red-600" data-rule="sym">Incluye un símbolo</div>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="space-y-2">
            <x-input-label for="password_confirmation" :value="__('Confirmar contraseña')" />
            <div class="relative">
                <x-text-input
                    id="password_confirmation"
                    class="auth-input pr-10"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                />
                <button
                    type="button"
                    class="absolute inset-y-0 right-3 flex items-center text-slate-500 transition hover:text-slate-700"
                    aria-label="Mostrar u ocultar confirmación"
                    data-eye-for="password_confirmation"
                >
                    <svg class="eye-open h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        <circle cx="12" cy="12" r="3" stroke-width="1.8" />
                    </svg>
                    <svg class="eye-closed hidden h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3l18 18M10.584 10.587A3 3 0 0113.414 13.41M9.88 4.6A9.98 9.98 0 0112 4c4.477 0 8.268 2.943 9.542 7a10.05 10.05 0 01-2.082 3.354M6.7 6.704A10.05 10.05 0 004.458 11 10.05 10.05 0 007.7 15.296M12 7a5 5 0 015 5" />
                    </svg>
                </button>
            </div>

            <div id="match-msg" class="text-sm text-red-600">
                La contraseña debe coincidir.
            </div>
        </div>

        <div class="flex items-center justify-between gap-3 pt-2">
            <a href="{{ url('/') }}" class="auth-link">
                {{ __('Volver al inicio') }}
            </a>

            <button type="submit" class="auth-action">
                {{ __('RESTABLECER CONTRASEÑA') }}
            </button>
        </div>
    </form>

    <script>
        (function () {
            const $pw = document.getElementById('password');
            const $pc = document.getElementById('password_confirmation');
            const $match = document.getElementById('match-msg');
            const rulesEls = {
                len: document.querySelector('[data-rule="len"]'),
                upper: document.querySelector('[data-rule="upper"]'),
                lower: document.querySelector('[data-rule="lower"]'),
                num: document.querySelector('[data-rule="num"]'),
                sym: document.querySelector('[data-rule="sym"]'),
            };

            const tests = {
                len: v => v.length >= 10,
                upper: v => /[A-Z]/.test(v),
                lower: v => /[a-z]/.test(v),
                num: v => /\d/.test(v),
                sym: v => /[^A-Za-z0-9]/.test(v),
            };

            function setColor(el, ok) {
                if (!el) return;
                el.classList.toggle('text-green-600', ok);
                el.classList.toggle('text-red-600', !ok);
            }

            function updateRules() {
                const v = $pw?.value || '';
                Object.keys(tests).forEach(k => setColor(rulesEls[k], tests[k](v)));

                const same = v !== '' && v === ($pc?.value || '');
                setColor($match, same);
                if ($match) {
                    $match.textContent = same ? 'Las contraseñas coinciden.' : 'La contraseña debe coincidir.';
                }
            }

            $pw?.addEventListener('input', updateRules);
            $pc?.addEventListener('input', updateRules);
            updateRules();

            document.querySelectorAll('[data-eye-for]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.getAttribute('data-eye-for');
                    const input = document.getElementById(id);
                    if (!input) return;

                    const open = btn.querySelector('.eye-open');
                    const closed = btn.querySelector('.eye-closed');

                    if (input.type === 'password') {
                        input.type = 'text';
                        open?.classList.add('hidden');
                        closed?.classList.remove('hidden');
                    } else {
                        input.type = 'password';
                        open?.classList.remove('hidden');
                        closed?.classList.add('hidden');
                    }
                });
            });
        })();
    </script>
</x-guest-layout>
