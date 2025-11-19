<x-guest-layout>
    <div class="space-y-6">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Acceso seguro</p>
                <h1 class="text-2xl font-semibold text-slate-800">Inicia sesión</h1>
                <p class="text-sm text-slate-600 mt-1">Organiza tus citas y usuarios desde una interfaz clara y responsiva.</p>
            </div>
            <div class="px-3 py-2 rounded-xl bg-sky-50 border border-sky-100 text-xs text-sky-700 shadow-sm">
                <span class="font-semibold">Portal clínico</span>
                <div class="text-[11px] text-sky-600">Modo administrador</div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-white to-sky-50 border border-slate-100 rounded-2xl p-6 shadow-sm">
            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div class="space-y-2">
                    <x-input-label for="login" :value="__('Usuario o correo')" />
                    <x-text-input
                        id="login"
                        class="block mt-1 w-full"
                        type="text"
                        name="login"
                        :value="old('login')"
                        required
                        autofocus
                        autocomplete="username email"
                    />
                    <x-input-error :messages="$errors->get('login')" class="mt-2" />
                    <x-input-error :messages="$errors->get('USR_USUARIO')" class="mt-2" />
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <x-input-label for="password" :value="__('Contraseña')" />
                        @if (Route::has('password.request'))
                            <a class="text-xs text-slate-600 hover:text-slate-900 underline" href="{{ route('password.request') }}">
                                {{ __('¿Olvidaste tu contraseña?') }}
                            </a>
                        @endif
                    </div>
                    <x-text-input
                        id="password"
                        class="block mt-1 w-full"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                    />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between">
                    <label for="remember_me" class="inline-flex items-center text-sm text-slate-600">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                        <span class="ms-2">{{ __('Recordarme') }}</span>
                    </label>
                    <x-primary-button class="px-5">{{ __('Iniciar sesión') }}</x-primary-button>
                </div>
            </form>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 text-sm text-slate-600">
            <div class="flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                <span>Diseño animado para modales y formularios.</span>
            </div>
            <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:text-indigo-700">Crear cuenta nueva</a>
        </div>
    </div>
</x-guest-layout>
