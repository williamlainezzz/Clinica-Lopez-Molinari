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

        <div id="webauthn-login-message" class="hidden mt-4 rounded-md border px-3 py-2 text-sm"></div>

        <div class="flex flex-col gap-3 mt-4 sm:flex-row sm:items-center sm:justify-end">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('¿Olvidaste tu contraseña?') }}
                </a>
            @endif

            <button
                id="webauthn-login-button"
                type="button"
                class="hidden inline-flex items-center justify-center px-4 py-2 bg-white border border-indigo-300 rounded-md font-semibold text-xs text-indigo-700 uppercase tracking-widest shadow-sm hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed sm:ms-3"
            >
                Entrar con biometria
            </button>

            <x-primary-button class="ms-3">{{ __('Iniciar sesión') }}</x-primary-button>
        </div>
    </form>

    <script>
    (() => {
        const button = document.getElementById('webauthn-login-button');
        const loginInput = document.getElementById('login');
        const message = document.getElementById('webauthn-login-message');
        const csrf = document.querySelector('input[name="_token"]')?.value;

        if (!button || !loginInput || !message || !csrf || !window.PublicKeyCredential) {
            return;
        }

        button.classList.remove('hidden');

        function showMessage(text, type = 'info') {
            message.textContent = text;
            message.className = 'mt-4 rounded-md border px-3 py-2 text-sm';

            if (type === 'error') {
                message.classList.add('border-red-200', 'bg-red-50', 'text-red-700');
                return;
            }

            message.classList.add('border-indigo-200', 'bg-indigo-50', 'text-indigo-700');
        }

        function isInvalidWebAuthnHost(hostname) {
            return /^(?:\d{1,3}\.){3}\d{1,3}$/.test(hostname) || hostname.includes(':');
        }

        if (isInvalidWebAuthnHost(window.location.hostname)) {
            button.disabled = true;
            showMessage('Para usar biometria en pruebas locales, abre el sistema como http://localhost:8000 en lugar de 127.0.0.1.', 'error');
            return;
        }

        function base64UrlToBuffer(value) {
            const base64 = value.replace(/-/g, '+').replace(/_/g, '/');
            const padded = base64.padEnd(base64.length + ((4 - base64.length % 4) % 4), '=');
            const binary = atob(padded);
            const bytes = new Uint8Array(binary.length);

            for (let i = 0; i < binary.length; i += 1) {
                bytes[i] = binary.charCodeAt(i);
            }

            return bytes.buffer;
        }

        function bufferToBase64Url(buffer) {
            const bytes = new Uint8Array(buffer);
            let binary = '';

            bytes.forEach(byte => {
                binary += String.fromCharCode(byte);
            });

            return btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/g, '');
        }

        async function postJson(url, body) {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                body: JSON.stringify(body),
            });
            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                throw new Error(data.message || 'No se pudo completar la verificacion biometrica.');
            }

            if (!response.headers.get('content-type')?.includes('application/json')) {
                throw new Error('La sesion cambio mientras se solicitaba la biometria. Recarga la pagina e intenta de nuevo.');
            }

            return data;
        }

        button.addEventListener('click', async () => {
            const login = loginInput.value.trim();

            button.disabled = true;
            showMessage(login
                ? 'Solicitando verificacion biometrica del dispositivo...'
                : 'Buscando biometria registrada en este dispositivo...');

            try {
                const optionsResponse = await postJson('{{ route('webauthn.authentication-options') }}', login ? { login } : {});
                const publicKey = optionsResponse.publicKey;

                if (!publicKey || !publicKey.challenge) {
                    throw new Error(optionsResponse.message || 'No se recibio el reto biometrico. Recarga la pagina e intenta de nuevo.');
                }

                publicKey.challenge = base64UrlToBuffer(publicKey.challenge);
                if (Array.isArray(publicKey.allowCredentials)) {
                    publicKey.allowCredentials = publicKey.allowCredentials.map(credential => ({
                        ...credential,
                        id: base64UrlToBuffer(credential.id),
                    }));
                }

                const assertion = await navigator.credentials.get({ publicKey });

                const result = await postJson('{{ route('webauthn.authenticate') }}', {
                    id: assertion.id,
                    rawId: bufferToBase64Url(assertion.rawId),
                    type: assertion.type,
                    response: {
                        clientDataJSON: bufferToBase64Url(assertion.response.clientDataJSON),
                        authenticatorData: bufferToBase64Url(assertion.response.authenticatorData),
                        signature: bufferToBase64Url(assertion.response.signature),
                    },
                });

                window.location.href = result.redirect || '{{ route('dashboard') }}';
            } catch (error) {
                showMessage(error.message || 'No se pudo iniciar sesion con biometria.', 'error');
                button.disabled = false;
            }
        });
    })();
    </script>
</x-guest-layout>
