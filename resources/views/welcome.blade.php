<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bienvenido — Clínica Dental</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    {{-- Tailwind por CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Alpine.js --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>[x-cloak]{display:none!important}</style>

    {{-- Estilos comunes de inputs dentro de los modales --}}
    <style type="text/tailwindcss">
        @layer components {
            .modal-panel input[type="text"],
            .modal-panel input[type="email"],
            .modal-panel input[type="password"],
            .modal-panel input[type="number"],
            .modal-panel textarea,
            .modal-panel select {
                @apply block w-full mt-1 rounded-md
                       border border-slate-300 bg-white
                       placeholder-slate-400 text-slate-800
                       shadow-sm
                       focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500;
            }

            .modal-panel label {
                @apply text-[13px] font-medium text-slate-700;
            }
        }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-sky-50 via-white to-teal-50 text-slate-800 relative">

    {{-- halos decorativos --}}
    <div class="pointer-events-none absolute inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-28 -left-24 h-96 w-96 rounded-full bg-sky-200/45 blur-3xl"></div>
        <div class="absolute -bottom-28 -right-24 h-96 w-96 rounded-full bg-teal-200/45 blur-3xl"></div>
    </div>

    {{-- HEADER --}}
    <header class="mx-auto max-w-6xl px-6 py-5">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                {{-- Icono redondo tipo logo --}}
                <div class="flex h-11 w-11 items-center justify-center rounded-full bg-sky-800 text-white shadow-sm ring-2 ring-white/70">
                    <span class="text-xl font-semibold">🦷</span>
                </div>
                <div class="flex flex-col leading-tight">
                    <span class="text-xl font-semibold tracking-tight">
                        Complejo Dental <span class="text-slate-700">López Molinari</span>
                    </span>
                    <span class="text-xs text-slate-500">
                        Sistema de gestión clínica y agenda de citas
                    </span>
                </div>
            </div>
            {{-- Sin botones extra en el header --}}
            <div></div>
        </div>
    </header>

    {{-- HERO --}}
    <main class="mx-auto max-w-6xl px-6 pb-10">
        <div class="grid items-center gap-10 lg:grid-cols-2">
            {{-- Lado izquierdo: texto y botones --}}
            <section>
                <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight leading-tight text-slate-900">
                    Gestión clínica <span class="text-sky-700">clara y rápida</span>
                </h1>

                <p class="mt-6 text-base text-slate-700 max-w-xl">
                    Administra citas, pacientes y agenda del <span class="font-semibold">Complejo Dental López Molinari</span>
                    desde un solo lugar. Accede como paciente o como doctor de forma segura.
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    {{-- botones que abren los modales mediante eventos globales --}}
                    <button
                        type="button"
                        data-open="login"
                        class="inline-flex items-center justify-center rounded-full bg-sky-700 px-7 py-3 text-white text-sm font-semibold shadow-sm ring-1 ring-sky-700/10 hover:bg-sky-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-sky-700">
                        Iniciar sesión
                    </button>

                    <button
                        type="button"
                        data-open="register"
                        class="inline-flex items-center justify-center rounded-full bg-white px-7 py-3 text-sm font-semibold text-sky-700 shadow-sm ring-1 ring-sky-200 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-sky-500">
                        Registrarse
                    </button>
                </div>

                <ul class="mt-8 space-y-2 text-sm text-slate-700">
                    <li>• Plataforma para gestionar citas, pacientes y agenda clínica en un mismo sistema.</li>
                    <li>• Acceso diferenciado para pacientes y doctores, con módulos específicos para cada uno.</li>
                    <li>• Consulta de citas, horarios y recordatorios desde cualquier dispositivo con Internet.</li>
                </ul>
            </section>

            {{-- Lado derecho: foto de la clínica --}}
            <section aria-label="Sede principal del Complejo Dental López Molinari">
                <div class="relative rounded-2xl bg-white/70 backdrop-blur p-3 shadow-sm ring-1 ring-slate-200">
                    <div class="absolute -inset-0.5 -z-10 rounded-2xl bg-gradient-to-tr from-sky-200/40 to-teal-200/40 blur-2xl"></div>
                    <figure class="overflow-hidden rounded-2xl">
                        <img
                            src="{{ asset('images/clinica_exterior.avif') }}"
                            alt="Fachada del Complejo Dental López Molinari — Tegucigalpa, Honduras."
                            class="h-[320px] w-full object-cover sm:h-[380px] lg:h-[420px]">
                        <figcaption class="bg-slate-900/70 text-[11px] text-slate-50 px-4 py-2">
                            <span class="font-semibold text-xs uppercase tracking-wide">Sede principal</span><br>
                            Complejo Dental López Molinari — Tegucigalpa, Honduras.
                        </figcaption>
                    </figure>
                </div>
            </section>
        </div>
    </main>

    <footer class="mx-auto max-w-6xl px-6 pb-10">
        <div class="rounded-2xl bg-white/60 backdrop-blur px-5 py-4 text-center text-xs sm:text-sm text-slate-500 ring-1 ring-slate-200">
            © {{ date('Y') }} Complejo Dental López Molinari. Sistema de gestión clínica desarrollado para uso académico y profesional.
        </div>
    </footer>

    {{-- CONTENEDOR DE MODALES (LOGIN + REGISTRO) --}}
    <div
        x-data="{ showLogin:false, showRegister:false }"
        x-on:open-login.window="showLogin = true"
        x-on:open-register.window="showRegister = true"
        x-on:close-modals.window="showLogin = false; showRegister = false"
        x-init="
            @if (session('modal') === 'login') showLogin = true; @endif
            @if (session('modal') === 'register') showRegister = true; @endif
            @if ($errors->login->any()) showLogin = true; @endif
            @if ($errors->register->any()) showRegister = true; @endif
        "
    >

        {{-- ====================== MODAL LOGIN ====================== --}}
        <div x-cloak x-show="showLogin" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-slate-900/60" @click="showLogin=false"></div>

            <div x-transition
                 class="modal-panel relative w-full max-w-md mx-4 rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200 overflow-hidden">
                {{-- Header degradado --}}
                <div class="bg-gradient-to-r from-sky-600 via-sky-500 to-blue-500 px-6 py-4 text-white">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-[11px] font-semibold tracking-[0.18em] uppercase text-sky-100/80">
                                Bienvenido
                            </p>
                            <h3 class="mt-1 text-base sm:text-lg font-semibold">
                                Inicia sesión en tu cuenta
                            </h3>
                            <p class="mt-1 text-[11px] sm:text-xs text-sky-50/90">
                                Accede a la gestión de citas del Complejo Dental López Molinari.
                            </p>
                        </div>
                        <button class="mt-1 rounded-full bg-white/15 p-1.5 hover:bg-white/25" @click="showLogin=false" aria-label="Cerrar">
                            ✕
                        </button>
                    </div>
                </div>

                <div class="p-5 max-h-[80vh] overflow-y-auto">
                    @if ($errors->login->any())
                        <div class="mb-3 text-xs sm:text-sm text-red-600">
                            No pudimos iniciar sesión con los datos ingresados. Verifica tu usuario o correo y tu contraseña e inténtalo nuevamente.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" novalidate x-data="{ showPwd:false }">
                        @csrf

                        {{-- Usuario o correo --}}
                        <div>
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
                        </div>

                        {{-- Contraseña --}}
                        <div class="mt-4">
                            <x-input-label for="password" :value="__('Contraseña')" />

                            <div class="relative">
                                <x-text-input
                                    id="password"
                                    name="password"
                                    x-bind:type="showPwd ? 'text' : 'password'"
                                    class="block mt-1 w-full pr-10"
                                    required
                                    autocomplete="current-password"
                                />

                                <button
                                    type="button"
                                    class="absolute inset-y-0 right-2 mt-1 flex items-center text-slate-500 hover:text-slate-700"
                                    @click="showPwd = !showPwd"
                                    :aria-label="showPwd ? 'Ocultar contraseña' : 'Mostrar contraseña'">
                                    <svg x-show="!showPwd" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <svg x-show="showPwd" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3l18 18M10.58 10.58A3 3 0 0012 15a3 3 0 002.42-4.42M9.88 5.09A9.96 9.96 0 0112 5c4.477 0 8.268 2.943 9.542 7-.39 1.24-1.02 2.36-1.85 3.33M6.27 6.27C4.39 7.58 3.03 9.54 2.46 12c1.274 4.057 5.065 7 9.542 7a9.96 9.96 0 004.12-.87"/></svg>
                                </button>
                            </div>
                        </div>

                        {{-- Recordarme --}}
                        <div class="block mt-4">
                            <label for="remember_me" class="inline-flex items-center">
                                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                                <span class="ms-2 text-sm text-gray-600">{{ __('Recordarme') }}</span>
                            </label>
                        </div>

                        <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            @if (Route::has('password.request'))
                                <a class="text-xs sm:text-sm text-sky-700 hover:text-sky-900 underline"
                                   href="{{ route('password.request') }}">
                                    {{ __('¿Olvidaste tu contraseña?') }}
                                </a>
                            @endif

                            <x-primary-button class="w-full sm:w-auto justify-center">
                                {{ __('Iniciar sesión') }}
                            </x-primary-button>
                        </div>

                        <p class="mt-4 text-center text-xs text-slate-500">
                            ¿Aún no tienes cuenta?
                            <button type="button" class="text-sky-700 font-semibold hover:underline"
                                    @click="$dispatch('close-modals'); window.dispatchEvent(new CustomEvent('open-register'));">
                                Regístrate aquí
                            </button>
                        </p>
                    </form>
                </div>
            </div>
        </div>

        {{-- ====================== MODAL REGISTRO ====================== --}}
        @php
            use App\Models\PreguntaSeguridad;
            $preguntasSeg = PreguntaSeguridad::where('ESTADO', 1)->orderBy('TEXTO_PREGUNTA')->get();
        @endphp

        <div x-cloak x-show="showRegister" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-slate-900/60" @click="showRegister=false"></div>

            <div x-transition class="modal-panel relative w-full max-w-5xl mx-4 rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200 overflow-hidden">
                {{-- Header degradado --}}
                <div class="bg-gradient-to-r from-sky-600 via-sky-500 to-blue-500 px-6 py-4 text-white">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-[11px] font-semibold tracking-[0.18em] uppercase text-sky-100/80">
                                Registro de paciente
                            </p>
                            <h3 class="mt-1 text-base sm:text-lg font-semibold">
                                Crea tu cuenta en el Complejo Dental López Molinari
                            </h3>
                            <p class="mt-1 text-[11px] sm:text-xs text-sky-50/90">
                                Completa tus datos para agendar y gestionar tus citas en línea.
                            </p>
                        </div>
                        <button class="mt-1 rounded-full bg-white/15 p-1.5 hover:bg-white/25" @click="showRegister=false" aria-label="Cerrar">
                            ✕
                        </button>
                    </div>
                </div>

                <div class="p-5 max-h-[85vh] overflow-y-auto">
                    <form method="POST" action="{{ route('register') }}" novalidate>
                        @csrf

                        {{-- ===== DATOS PERSONALES ===== --}}
                        <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-3">
                            Datos personales
                        </h2>

                        {{-- Nombres + Apellidos (combinados) --}}
                        <div class="grid gap-4 md:grid-cols-2">
                            {{-- Nombres visibles, campos reales ocultos --}}
                            <div>
                                <x-input-label for="NOMBRES_UI" :value="__('Nombres')" />
                                <input
                                    id="NOMBRES_UI"
                                    type="text"
                                    class="modal-panel block mt-1 w-full rounded-md border border-slate-300 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500"
                                    value="{{ trim(old('PRIMER_NOMBRE').' '.old('SEGUNDO_NOMBRE')) }}"
                                    autocomplete="given-name"
                                >
                                {{-- campos originales ocultos --}}
                                <input type="hidden" id="PRIMER_NOMBRE" name="PRIMER_NOMBRE" value="{{ old('PRIMER_NOMBRE') }}">
                                <input type="hidden" id="SEGUNDO_NOMBRE" name="SEGUNDO_NOMBRE" value="{{ old('SEGUNDO_NOMBRE') }}">

                                <x-input-error :messages="$errors->register->get('PRIMER_NOMBRE')" class="mt-2" />
                                <x-input-error :messages="$errors->register->get('SEGUNDO_NOMBRE')" class="mt-1" />
                            </div>

                            {{-- Apellidos --}}
                            <div>
                                <x-input-label for="APELLIDOS_UI" :value="__('Apellidos')" />
                                <input
                                    id="APELLIDOS_UI"
                                    type="text"
                                    class="modal-panel block mt-1 w-full rounded-md border border-slate-300 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500"
                                    value="{{ trim(old('PRIMER_APELLIDO').' '.old('SEGUNDO_APELLIDO')) }}"
                                    autocomplete="family-name"
                                >
                                <input type="hidden" id="PRIMER_APELLIDO" name="PRIMER_APELLIDO" value="{{ old('PRIMER_APELLIDO') }}">
                                <input type="hidden" id="SEGUNDO_APELLIDO" name="SEGUNDO_APELLIDO" value="{{ old('SEGUNDO_APELLIDO') }}">

                                <x-input-error :messages="$errors->register->get('PRIMER_APELLIDO')" class="mt-2" />
                                <x-input-error :messages="$errors->register->get('SEGUNDO_APELLIDO')" class="mt-1" />
                            </div>
                        </div>

                        {{-- Género + Teléfono --}}
                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            <div>
                                <x-input-label for="TIPO_GENERO" :value="__('Género')" />
                                <select id="TIPO_GENERO" name="TIPO_GENERO" class="block mt-1 w-full rounded-md border-slate-300" required>
                                    <option value="" disabled {{ old('TIPO_GENERO') ? '' : 'selected' }}>Seleccione...</option>
                                    <option value="1" {{ old('TIPO_GENERO')=='1' ? 'selected' : '' }}>Masculino</option>
                                    <option value="2" {{ old('TIPO_GENERO')=='2' ? 'selected' : '' }}>Femenino</option>
                                    <option value="3" {{ old('TIPO_GENERO')=='3' ? 'selected' : '' }}>Otro / Prefiero no decir</option>
                                </select>
                                <x-input-error :messages="$errors->register->get('TIPO_GENERO')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="NUM_TELEFONO" :value="__('Teléfono')" />
                                <x-text-input id="NUM_TELEFONO" class="block mt-1 w-full" type="text" name="NUM_TELEFONO"
                                              :value="old('NUM_TELEFONO')" />
                                <x-input-error :messages="$errors->register->get('NUM_TELEFONO')" class="mt-2" />
                            </div>
                        </div>

                        {{-- ===== DIRECCIÓN ===== --}}
                        <h2 class="mt-6 text-xs font-semibold text-slate-500 uppercase tracking-wide mb-3">
                            Dirección
                        </h2>

                        <div class="grid gap-4 md:grid-cols-2">
                            {{-- Departamento (select) --}}
                            <div>
                                <x-input-label for="DEPARTAMENTO_SELECT" :value="__('Departamento')" />
                                <select id="DEPARTAMENTO_SELECT" class="block mt-1 w-full rounded-md border-slate-300">
                                    <option value="">Seleccione...</option>
                                </select>

                                {{-- campo real --}}
                                <input type="hidden" id="DEPARTAMENTO" name="DEPARTAMENTO" value="{{ old('DEPARTAMENTO') }}">
                                <x-input-error :messages="$errors->register->get('DEPARTAMENTO')" class="mt-2" />
                            </div>

                            {{-- Ciudad (select dependiente) --}}
                            <div>
                                <x-input-label for="CIUDAD_SELECT" :value="__('Ciudad')" />
                                <select id="CIUDAD_SELECT" class="block mt-1 w-full rounded-md border-slate-300" {{ old('DEPARTAMENTO') ? '' : 'disabled' }}>
                                    <option value="">Seleccione un departamento primero</option>
                                </select>

                                {{-- campos reales --}}
                                <input type="hidden" id="CIUDAD" name="CIUDAD" value="{{ old('CIUDAD') }}">
                                <input type="hidden" id="MUNICIPIO" name="MUNICIPIO" value="{{ old('MUNICIPIO') }}">
                                <input type="hidden" id="COLONIA" name="COLONIA" value="{{ old('COLONIA') ?? 'N/A' }}">

                                <x-input-error :messages="$errors->register->get('MUNICIPIO')" class="mt-2" />
                                <x-input-error :messages="$errors->register->get('CIUDAD')" class="mt-1" />
                                <x-input-error :messages="$errors->register->get('COLONIA')" class="mt-1" />
                            </div>
                        </div>

                        {{-- Dirección / Referencia --}}
                        <div class="mt-4">
                            <x-input-label for="REFERENCIA" :value="__('Dirección / Referencia')" />
                            <textarea id="REFERENCIA" name="REFERENCIA" rows="3"
                                      class="mt-1 block w-full rounded-md border-slate-300 focus:border-cyan-500 focus:ring-cyan-500">{{ old('REFERENCIA') }}</textarea>
                            <x-input-error :messages="$errors->register->get('REFERENCIA')" class="mt-2" />
                        </div>

                        {{-- ===== CONTACTO Y RECUPERACIÓN ===== --}}
                        <h2 class="mt-6 text-xs font-semibold text-slate-500 uppercase tracking-wide mb-3">
                            Contacto y recuperación
                        </h2>

                        {{-- Correo --}}
                        <div>
                            <x-input-label for="CORREO" :value="__('Correo electrónico')" />
                            <x-text-input
                                id="CORREO"
                                type="email"
                                name="CORREO"
                                :value="old('CORREO')"
                                required
                                class="block mt-1 w-full {{ ($errors->register ?? $errors)->has('CORREO') ? 'is-invalid' : '' }}"
                            />
                            <x-input-error :messages="($errors->register ?? $errors)->get('CORREO')" class="mt-2" />
                        </div>

                        {{-- ===== PREGUNTAS DE SEGURIDAD ===== --}}
                        <h2 class="mt-8 text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">
                            Preguntas de seguridad
                        </h2>
                        <p class="text-xs text-slate-600 mb-3">
                            Elige dos preguntas y escribe tus respuestas. Se usarán para verificar tu identidad al restablecer la contraseña.
                        </p>

                        <div
                            x-data="{
                                q1: '{{ old('PREGUNTA_1') }}' || '',
                                q2: '{{ old('PREGUNTA_2') }}' || '',
                                same() { return this.q1 && this.q2 && this.q1 === this.q2; }
                            }"
                            class="grid gap-4 md:grid-cols-2"
                        >
                            {{-- Pregunta 1 --}}
                            <div>
                                <x-input-label for="PREGUNTA_1" :value="__('Pregunta 1')" />
                                <select id="PREGUNTA_1" name="PREGUNTA_1"
                                        class="mt-1 block w-full rounded-md border-slate-300"
                                        x-model="q1" required>
                                    <option value="" disabled {{ old('PREGUNTA_1') ? '' : 'selected' }}>Seleccione...</option>
                                    @foreach ($preguntasSeg as $p)
                                        <option value="{{ $p->COD_PREGUNTA }}" {{ old('PREGUNTA_1') == $p->COD_PREGUNTA ? 'selected' : '' }}>
                                            {{ $p->TEXTO_PREGUNTA }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->register->get('PREGUNTA_1')" class="mt-2" />

                                <x-input-label for="RESPUESTA_1" :value="__('Respuesta a la pregunta 1')" class="mt-3" />
                                <x-text-input id="RESPUESTA_1" name="RESPUESTA_1" type="text"
                                              class="block mt-1 w-full" required
                                              :value="old('RESPUESTA_1')" />
                                <x-input-error :messages="$errors->register->get('RESPUESTA_1')" class="mt-2" />
                            </div>

                            {{-- Pregunta 2 --}}
                            <div>
                                <x-input-label for="PREGUNTA_2" :value="__('Pregunta 2')" />
                                <select id="PREGUNTA_2" name="PREGUNTA_2"
                                        class="mt-1 block w-full rounded-md border-slate-300"
                                        x-model="q2" required>
                                    <option value="" disabled {{ old('PREGUNTA_2') ? '' : 'selected' }}>Seleccione...</option>
                                    @foreach ($preguntasSeg as $p)
                                        <option value="{{ $p->COD_PREGUNTA }}" {{ old('PREGUNTA_2') == $p->COD_PREGUNTA ? 'selected' : '' }}>
                                            {{ $p->TEXTO_PREGUNTA }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->register->get('PREGUNTA_2')" class="mt-2" />

                                <x-input-label for="RESPUESTA_2" :value="__('Respuesta a la pregunta 2')" class="mt-3" />
                                <x-text-input id="RESPUESTA_2" name="RESPUESTA_2" type="text"
                                              class="block mt-1 w-full" required
                                              :value="old('RESPUESTA_2')" />
                                <x-input-error :messages="$errors->register->get('RESPUESTA_2')" class="mt-2" />
                            </div>

                            {{-- Aviso si eligieron la misma pregunta --}}
                            <div class="md:col-span-2" x-show="same()">
                                <div class="rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-700">
                                    Las preguntas deben ser distintas.
                                </div>
                            </div>
                        </div>

                        {{-- ===== CREDENCIALES ===== --}}
                        <h2 class="mt-8 text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">
                            Usuario asignado y contraseña
                        </h2>

                        <div class="mb-3 text-xs text-slate-600 flex flex-wrap items-center justify-between gap-2">
                            <div>
                                Este será tu usuario para iniciar sesión:
                                <code class="font-semibold" id="username-preview-inline"></code>
                            </div>
                            <div id="username-pill" class="hidden text-xs bg-slate-50 border border-slate-200 rounded-md px-3 py-1.5 text-slate-700">
                                <span class="font-medium text-slate-600 mr-1">Usuario:</span>
                                <code id="username-preview" class="font-semibold"></code>
                            </div>
                        </div>

                        <div
                            x-data="{
                                pwd: '',
                                confirm: '',
                                showPwd: false,
                                showConfirm: false,
                                get hasLen()   { return this.pwd.length >= 10 },
                                get hasUpper() { return /[A-Z]/.test(this.pwd) },
                                get hasLower() { return /[a-z]/.test(this.pwd) },
                                get hasDigit() { return /\d/.test(this.pwd) },
                                get hasSym()   { return /[^A-Za-z0-9]/.test(this.pwd) },
                                get match()    { return this.pwd.length>0 && this.pwd === this.confirm },
                                get ok()       { return this.hasLen && this.hasUpper && this.hasLower && this.hasDigit && this.hasSym && this.match },
                            }"
                            class="space-y-4"
                        >
                            {{-- Contraseña --}}
                            <div>
                                <x-input-label for="password" :value="__('Contraseña')" />
                                <div class="relative">
                                    <x-text-input
                                        id="password"
                                        name="password"
                                        x-bind:type="showPwd ? 'text' : 'password'"
                                        class="block mt-1 w-full pr-10"
                                        required
                                        autocomplete="new-password"
                                        x-model="pwd"
                                    />
                                    <button
                                        type="button"
                                        class="absolute inset-y-0 right-2 mt-1 flex items-center text-slate-500 hover:text-slate-700"
                                        @click="showPwd = !showPwd"
                                        :aria-label="showPwd ? 'Ocultar contraseña' : 'Mostrar contraseña'">
                                        <svg x-show="!showPwd" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <svg x-show="showPwd" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3l18 18M10.58 10.58A3 3 0 0012 15a3 3 0 002.42-4.42M9.88 5.09A9.96 9.96 0 0112 5c4.477 0 8.268 2.943 9.542 7-.39 1.24-1.02 2.36-1.85 3.33M6.27 6.27C4.39 7.58 3.03 9.54 2.46 12c1.274 4.057 5.065 7 9.542 7a9.96 9.96 0 004.12-.87"/></svg>
                                    </button>
                                </div>

                                <ul class="mt-3 grid gap-1 text-sm">
                                    <li :class="hasLen ? 'text-green-600' : 'text-red-600'">
                                        <span class="font-semibold" x-text="hasLen ? '✓' : '•'"></span>
                                        Mínimo 10 caracteres
                                    </li>
                                    <li :class="(hasUpper && hasLower) ? 'text-green-600' : 'text-red-600'">
                                        <span class="font-semibold" x-text="(hasUpper && hasLower) ? '✓' : '•'"></span>
                                        Mayúsculas y minúsculas
                                    </li>
                                    <li :class="hasDigit ? 'text-green-600' : 'text-red-600'">
                                        <span class="font-semibold" x-text="hasDigit ? '✓' : '•'"></span>
                                        Al menos un número
                                    </li>
                                    <li :class="hasSym ? 'text-green-600' : 'text-red-600'">
                                        <span class="font-semibold" x-text="hasSym ? '✓' : '•'"></span>
                                        Al menos un símbolo
                                    </li>
                                </ul>
                            </div>

                            {{-- Confirmar contraseña --}}
                            <div>
                                <x-input-label for="password_confirmation" :value="__('Confirmar contraseña')" />
                                <div class="relative">
                                    <x-text-input
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        x-bind:type="showConfirm ? 'text' : 'password'"
                                        class="block mt-1 w-full pr-10"
                                        required
                                        autocomplete="new-password"
                                        x-model="confirm"
                                    />
                                    <button
                                        type="button"
                                        class="absolute inset-y-0 right-2 mt-1 flex items-center text-slate-500 hover:text-slate-700"
                                        @click="showConfirm = !showConfirm"
                                        :aria-label="showConfirm ? 'Ocultar confirmación' : 'Mostrar confirmación'">
                                        <svg x-show="!showConfirm" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <svg x-show="showConfirm" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3l18 18M10.58 10.58A3 3 0 0012 15a3 3 0 002.42-4.42M9.88 5.09A9.96 9.96 0 0112 5c4.477 0 8.268 2.943 9.542 7-.39 1.24-1.02 2.36-1.85 3.33M6.27 6.27C4.39 7.58 3.03 9.54 2.46 12c1.274 4.057 5.065 7 9.542 7a9.96 9.96 0 004.12-.87"/></svg>
                                    </button>
                                </div>

                                <div class="mt-1 text-sm" :class="match ? 'text-green-600' : 'text-red-600'">
                                    <span class="font-semibold" x-text="match ? '✓' : '•'"></span>
                                    Las contraseñas deben coincidir
                                </div>

                                <x-input-error :messages="$errors->register->get('password')" class="mt-2" />
                                <x-input-error :messages="$errors->register->get('password_confirmation')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button class="px-5">
                                Registrarme
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div> {{-- fin contenedor modales --}}

    {{-- Script: vista previa del usuario autogenerado (mismo de tu archivo original) --}}
    <script>
        (function () {
            const maxLen = 50;
            const $n = document.getElementById('PRIMER_NOMBRE');
            const $a = document.getElementById('PRIMER_APELLIDO');
            const $pill = document.getElementById('username-pill');
            const $out1 = document.getElementById('username-preview');
            const $out2 = document.getElementById('username-preview-inline');

            function strip(s){return (s||'').normalize('NFD').replace(/[\u0300-\u036f]/g,'');}
            function mk(n,a){
                const first=(n||'').trim().charAt(0);
                const last=(a||'').trim().replace(/\s+/g,'');
                let base=(first+last).toLowerCase();
                base=strip(base).replace(/[^a-z0-9]/g,'');
                return (base||'user').slice(0,maxLen);
            }
            function up(){
                const u = mk($n?.value,$a?.value);
                if(($n?.value?.trim() || $a?.value?.trim())){
                    $pill?.classList.remove('hidden');
                    if($out1) $out1.textContent=u;
                    if($out2) $out2.textContent=u;
                }else{
                    $pill?.classList.add('hidden');
                    if($out1) $out1.textContent='';
                    if($out2) $out2.textContent='';
                }
            }
            ['input','change'].forEach(e=>{ $n?.addEventListener(e,up); $a?.addEventListener(e,up); });
            up();
        })();
    </script>

    {{-- Script: abrir modales desde botones data-open --}}
    <script>
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('[data-open]');
            if (!btn) return;
            e.preventDefault();
            const which = btn.getAttribute('data-open');
            if (which === 'login')   window.dispatchEvent(new CustomEvent('open-login'));
            if (which === 'register') window.dispatchEvent(new CustomEvent('open-register'));
        });
    </script>

    {{-- Script: sincronizar campos combinados y selects de departamentos/ciudades --}}
    <script>
        // 1) Nombres / Apellidos -> campos ocultos
        (function () {
            function syncCombined(visibleId, firstId, secondId) {
                const v = document.getElementById(visibleId);
                const f = document.getElementById(firstId);
                const s = document.getElementById(secondId);
                if (!v || !f || !s) return;

                const update = () => {
                    const parts = (v.value || '').trim().split(/\s+/);
                    f.value = parts[0] || '';
                    s.value = parts.slice(1).join(' ') || '';
                };

                v.addEventListener('input', update);
                v.addEventListener('change', update);
                // inicial
                update();
            }

            syncCombined('NOMBRES_UI', 'PRIMER_NOMBRE', 'SEGUNDO_NOMBRE');
            syncCombined('APELLIDOS_UI', 'PRIMER_APELLIDO', 'SEGUNDO_APELLIDO');
        })();

        // 2) Departamento / Ciudad dependientes
        (function () {
            const data = {
                "Atlántida": ["La Ceiba", "Tela", "Jutiapa"],
                "Choluteca": ["Choluteca", "Pespire"],
                "Colón": ["Trujillo", "Tocoa"],
                "Comayagua": ["Comayagua", "Siguatepeque"],
                "Copán": ["Santa Rosa de Copán", "Copán Ruinas"],
                "Cortés": ["San Pedro Sula", "Puerto Cortés", "Choloma"],
                "El Paraíso": ["Yuscarán", "Danlí"],
                "Francisco Morazán": ["Tegucigalpa", "Valle de Ángeles", "Santa Lucía"],
                "Gracias a Dios": ["Puerto Lempira"],
                "Intibucá": ["La Esperanza"],
                "Islas de la Bahía": ["Roatán", "Utila"],
                "La Paz": ["La Paz"],
                "Lempira": ["Gracias"],
                "Ocotepeque": ["Nueva Ocotepeque"],
                "Olancho": ["Juticalpa", "Catacamas"],
                "Santa Bárbara": ["Santa Bárbara"],
                "Valle": ["Nacaome"],
                "Yoro": ["El Progreso", "Yoro"]
            };

            const depSelect = document.getElementById('DEPARTAMENTO_SELECT');
            const citySelect = document.getElementById('CIUDAD_SELECT');
            const depHidden = document.getElementById('DEPARTAMENTO');
            const muniHidden = document.getElementById('MUNICIPIO');
            const cityHidden = document.getElementById('CIUDAD');
            const colHidden = document.getElementById('COLONIA');

            if (!depSelect || !citySelect) return;

            // rellenar departamentos
            const oldDept = depHidden.value || '';
            const oldCity = cityHidden.value || '';

            Object.keys(data).forEach(dep => {
                const opt = document.createElement('option');
                opt.value = dep;
                opt.textContent = dep;
                if (dep === oldDept) opt.selected = true;
                depSelect.appendChild(opt);
            });

            function fillCities(dep) {
                citySelect.innerHTML = '';
                if (!dep || !data[dep]) {
                    const opt = document.createElement('option');
                    opt.value = '';
                    opt.textContent = 'Seleccione un departamento primero';
                    citySelect.appendChild(opt);
                    citySelect.disabled = true;
                    return;
                }
                citySelect.disabled = false;
                data[dep].forEach(city => {
                    const opt = document.createElement('option');
                    opt.value = city;
                    opt.textContent = city;
                    if (city === oldCity) opt.selected = true;
                    citySelect.appendChild(opt);
                });
            }

            // inicial
            fillCities(oldDept);
            if (oldDept) {
                depSelect.value = oldDept;
                citySelect.disabled = false;
            }

            depSelect.addEventListener('change', () => {
                const dep = depSelect.value || '';
                depHidden.value = dep;
                fillCities(dep);
                // reset ciudad
                const city = citySelect.value || '';
                cityHidden.value = city;
                muniHidden.value = city;
                if (!colHidden.value) colHidden.value = 'N/A';
            });

            citySelect.addEventListener('change', () => {
                const city = citySelect.value || '';
                cityHidden.value = city;
                muniHidden.value = city;
                if (!colHidden.value) colHidden.value = 'N/A';
            });
        })();
    </script>

</body>
</html>
