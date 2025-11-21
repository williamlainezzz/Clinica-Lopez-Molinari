<!doctype html>
<html lang="es" x-data="{ showLogin:false, showRegister:false }">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bienvenido — Clínica Dental</title>
  <link rel="icon" href="{{ asset('favicon.ico') }}">

  <!-- Tailwind por CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Alpine.js -->
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <style>[x-cloak]{display:none!important}</style>

  <!-- Estilos de inputs dentro de los modales (igual que antes) -->
  <style type="text/tailwindcss">
    @layer components {
      .modal-panel input[type="text"],
      .modal-panel input[type="email"],
      .modal-panel input[type="password"],
      .modal-panel input[type="number"],
      .modal-panel input[type="tel"],
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
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">

  {{-- ================= HEADER NUEVO ================= --}}
  <header class="w-full border-b border-slate-200 bg-white/90 backdrop-blur">
    <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <img
          src="{{ asset('images/logo_clinica.avif') }}"
          class="h-12 w-12 rounded-full shadow-sm ring-1 ring-white/60 bg-white object-contain"
          alt="Logo Complejo Dental López Molinari">

        <div>
          <p class="text-lg sm:text-xl font-semibold tracking-tight leading-tight">
            Complejo Dental <span class="text-sky-800">López Molinari</span>
          </p>
          <p class="text-[11px] sm:text-xs text-slate-500">
            Sistema de gestión clínica y agenda de citas
          </p>
        </div>
      </div>

      {{-- Si el usuario ya está autenticado, mostramos acceso al panel (esto ya es opcional) --}}
      @if (Route::has('login'))
        @auth
          <a href="{{ url('/dashboard') }}"
             class="inline-flex items-center rounded-full border border-sky-700 px-4 py-1.5 text-xs sm:text-sm font-semibold text-sky-800 hover:bg-sky-50 transition">
            Ir al panel
          </a>
        @endauth
      @endif
    </div>
  </header>

  {{-- ================= HERO NUEVO ================= --}}
  <main class="flex-1">
    <section class="relative overflow-hidden">
      {{-- Fondo en degradado suave --}}
      <div class="absolute inset-0 bg-gradient-to-br from-sky-50 via-slate-50 to-slate-100 pointer-events-none"></div>

      <div class="relative max-w-6xl mx-auto px-6 py-16 lg:py-20 grid items-center gap-10 lg:grid-cols-2">
        {{-- Columna izquierda: texto + botones --}}
        <section>
          <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-tight text-slate-900">
            Gestión clínica <span class="text-sky-800">clara y rápida</span>
          </h1>

          <p class="mt-5 text-base sm:text-lg text-slate-600">
            Administra citas, pacientes y agenda del
            <strong>Complejo Dental López Molinari</strong> desde un solo lugar.
            Accede como paciente o como doctor de forma segura.
          </p>

          <div class="mt-8 flex flex-wrap gap-3">
            {{-- IMPORTANTE: estos botones usan la MISMA lógica que antes (data-open) --}}
            <button
              type="button"
              data-open="login"
              class="inline-flex items-center justify-center rounded-full bg-sky-800 px-6 py-2.5 text-sm font-semibold text-white shadow-md ring-1 ring-sky-700/20 hover:bg-sky-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-sky-700">
              Iniciar sesión
            </button>

            <button
              type="button"
              data-open="register"
              class="inline-flex items-center justify-center rounded-full bg-white px-6 py-2.5 text-sm font-semibold text-sky-800 shadow-sm ring-1 ring-sky-700/40 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-sky-700/70">
              Registrarse
            </button>
          </div>

          {{-- Mensajes sobre el sistema (no sobre seguridad) --}}
          <ul class="mt-6 space-y-2 text-sm text-slate-600">
            <li class="flex items-start gap-2">
              <span class="mt-1 h-1.5 w-1.5 rounded-full bg-sky-700"></span>
              <span>Plataforma para gestionar citas, pacientes y agenda clínica en un mismo sistema.</span>
            </li>
            <li class="flex items-start gap-2">
              <span class="mt-1 h-1.5 w-1.5 rounded-full bg-sky-700"></span>
              <span>Acceso diferenciado para pacientes y doctores, con módulos específicos para cada uno.</span>
            </li>
            <li class="flex items-start gap-2">
              <span class="mt-1 h-1.5 w-1.5 rounded-full bg-sky-700"></span>
              <span>Consulta de citas, horarios y recordatorios desde cualquier dispositivo con Internet.</span>
            </li>
          </ul>
        </section>

        {{-- Columna derecha: foto de la clínica --}}
        <section aria-label="Sede del Complejo Dental">
          <div class="relative rounded-3xl bg-white/70 backdrop-blur p-3 shadow-lg ring-1 ring-slate-200">
            <div class="absolute -inset-0.5 -z-10 rounded-3xl bg-gradient-to-tr from-sky-200/40 to-sky-400/30 blur-2xl"></div>

            <figure class="relative h-[260px] sm:h-[320px] lg:h-[360px] w-full overflow-hidden rounded-2xl">
              <img
                src="{{ asset('images/clinica_exterior.avif') }}"
                alt="Fachada del Complejo Dental López Molinari"
                class="h-full w-full object-cover">
              <figcaption
                class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-900/85 via-slate-900/35 to-transparent px-4 py-3">
                <p class="text-[11px] font-semibold text-sky-100 uppercase tracking-wide">
                  Sede principal
                </p>
                <p class="text-xs sm:text-sm text-slate-50">
                  Complejo Dental López Molinari — Tegucigalpa, Honduras.
                </p>
              </figcaption>
            </figure>
          </div>
        </section>
      </div>
    </section>
  </main>

  {{-- ================= FOOTER NUEVO ================= --}}
  <footer class="border-t border-slate-200 bg-white">
    <div class="max-w-6xl mx-auto px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-slate-500">
      <span>© {{ date('Y') }} Complejo Dental López Molinari. Todos los derechos reservados.</span>
      <span>Sistema de gestión clínica desarrollado para uso académico y profesional.</span>
    </div>
  </footer>


  {{-- ================================================================= --}}
  {{--  DE AQUÍ PARA ABAJO VA **TU LÓGICA ORIGINAL** DE MODALES Y SCRIPT  --}}
  {{--  (NO LA HE TOCADO, SOLO LA HE PEGADO TAL CUAL LA ENVIASTE)        --}}
  {{-- ================================================================= --}}

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

    {{-- ===== MODAL: LOGIN (TU CÓDIGO ORIGINAL) ===== --}}
    <div x-cloak x-show="showLogin" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center">
      <div class="absolute inset-0 bg-slate-900/60" @click="showLogin=false"></div>

      <div x-transition
           class="modal-panel relative w-full max-w-md mx-4 rounded-2xl bg-white shadow-xl ring-1 ring-slate-200">
        <div class="flex items-center justify-between px-5 py-4 border-b">
          <h3 class="text-base font-semibold text-slate-800">Iniciar sesión</h3>
          <button class="p-2 rounded-md hover:bg-slate-100" @click="showLogin=false" aria-label="Cerrar">✕</button>
        </div>
        <div class="p-5 max-h-[80vh] overflow-y-auto">

          @if ($errors->login->any())
            <div class="mb-3 text-sm text-red-600">
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
                  {{-- ojo abierto --}}
                  <svg x-show="!showPwd" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                  {{-- ojo tachado --}}
                  <svg x-show="showPwd" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3l18 18M10.58 10.58A3 3 0 0012 15a3 3 0 002.42-4.42M9.88 5.09A9.96 9.96 0 0112 5c4.477 0 8.268 2.943 9.542 7-.39 1.24-1.02 2.36-1.85 3.33M6.27 6.27C4.39 7.58 3.03 9.54 2.46 12c1.274 4.057 5.065 7 9.542 7a9.96 9.96 0 004.12-.87"/></svg>
                </button>
              </div>
            </div>

            {{-- Remember --}}
            <div class="block mt-4">
              <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Recordarme') }}</span>
              </label>
            </div>

            <div class="flex items-center justify-between mt-6">
              @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                   href="{{ route('password.request') }}">
                  {{ __('¿Olvidaste tu contraseña?') }}
                </a>
              @endif

              <x-primary-button>
                {{ __('Iniciar sesión') }}
              </x-primary-button>
            </div>
          </form>
        </div>
      </div>
    </div>

    {{-- ===== MODAL: REGISTRO (TU CÓDIGO ORIGINAL COMPLETO) ===== --}}
    {{-- TODO lo que viene a continuación es exactamente lo que pegaste,
         no lo he tocado para no romper nada. --}}

    <!-- ===== MODAL: REGISTRO ===== -->
    <div x-cloak x-show="showRegister" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center">
      <div class="absolute inset-0 bg-slate-900/60" @click="showRegister=false"></div>

      <div x-transition class="modal-panel relative w-full max-w-5xl mx-4 rounded-2xl bg-white shadow-xl ring-1 ring-slate-200">
        <div class="flex items-center justify-between px-5 py-4 border-b">
          <h3 class="text-base font-semibold text-slate-800">Crear cuenta</h3>
          <button class="p-2 rounded-md hover:bg-slate-100" @click="showRegister=false" aria-label="Cerrar">✕</button>
        </div>

        <div class="p-5 max-h-[85vh] overflow-y-auto">

        @php
        use App\Models\PreguntaSeguridad;
        $preguntasSeg = PreguntaSeguridad::where('ESTADO', 1)->orderBy('TEXTO_PREGUNTA')->get();
        @endphp

          {{-- FORM REGISTRO COMPLETO --}}
          <form method="POST" action="{{ route('register') }}" novalidate>
            @csrf

            {{-- Header: usuario autogenerado (discreto) --}}
            <div class="flex items-center justify-between mb-4">
              <h2 class="text-sm font-semibold text-slate-700">Datos personales</h2>
              <div id="username-pill" class="hidden text-xs bg-slate-50 border border-slate-200 rounded-md px-3 py-2 text-slate-700">
                <span class="font-medium text-slate-600 mr-1">Usuario:</span>
                <code id="username-preview" class="font-semibold"></code>
              </div>
            </div>

            {{-- Nombres --}}
            <div class="grid gap-4 md:grid-cols-2">
              <div>
                <x-input-label for="PRIMER_NOMBRE" :value="__('Primer nombre')" />
                <x-text-input id="PRIMER_NOMBRE" class="block mt-1 w-full" type="text" name="PRIMER_NOMBRE"
                              :value="old('PRIMER_NOMBRE')" placeholder="Ej. Ana" required autofocus />
                <x-input-error :messages="$errors->register->get('PRIMER_NOMBRE')" class="mt-2" />
              </div>
              <div>
                <x-input-label for="SEGUNDO_NOMBRE" :value="__('Segundo nombre (opcional)')" />
                <x-text-input id="SEGUNDO_NOMBRE" class="block mt-1 w-full" type="text" name="SEGUNDO_NOMBRE"
                              :value="old('SEGUNDO_NOMBRE')" placeholder="Ej. María" />
                <x-input-error :messages="$errors->register->get('SEGUNDO_NOMBRE')" class="mt-2" />
              </div>
            </div>

            {{-- Apellidos --}}
            <div class="mt-4 grid gap-4 md:grid-cols-2">
              <div>
                <x-input-label for="PRIMER_APELLIDO" :value="__('Primer apellido')" />
                <x-text-input id="PRIMER_APELLIDO" class="block mt-1 w-full" type="text" name="PRIMER_APELLIDO"
                              :value="old('PRIMER_APELLIDO')" placeholder="Ej. Rivera" required />
                <x-input-error :messages="$errors->register->get('PRIMER_APELLIDO')" class="mt-2" />
              </div>
              <div>
                <x-input-label for="SEGUNDO_APELLIDO" :value="__('Segundo apellido (opcional)')" />
                <x-text-input id="SEGUNDO_APELLIDO" class="block mt-1 w-full" type="text" name="SEGUNDO_APELLIDO"
                              :value="old('SEGUNDO_APELLIDO')" placeholder="Ej. López" />
                <x-input-error :messages="$errors->register->get('SEGUNDO_APELLIDO')" class="mt-2" />
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
                              :value="old('NUM_TELEFONO')" placeholder="99991234" />
                <x-input-error :messages="$errors->register->get('NUM_TELEFONO')" class="mt-2" />
              </div>
            </div>

            {{-- Departamento + Municipio --}}
            <div class="mt-4 grid gap-4 md:grid-cols-2">
              <div>
                <x-input-label for="DEPARTAMENTO" :value="__('Departamento')" />
                <x-text-input id="DEPARTAMENTO" class="block mt-1 w-full" type="text" name="DEPARTAMENTO"
                              :value="old('DEPARTAMENTO')" placeholder="Ej. Cortés" />
                <x-input-error :messages="$errors->register->get('DEPARTAMENTO')" class="mt-2" />
              </div>
              <div>
                <x-input-label for="MUNICIPIO" :value="__('Municipio')" />
                <x-text-input id="MUNICIPIO" class="block mt-1 w-full" type="text" name="MUNICIPIO"
                              :value="old('MUNICIPIO')" placeholder="Ej. San Pedro Sula" />
                <x-input-error :messages="$errors->register->get('MUNICIPIO')" class="mt-2" />
              </div>
            </div>

            {{-- Ciudad + Colonia --}}
            <div class="mt-4 grid gap-4 md:grid-cols-2">
              <div>
                <x-input-label for="CIUDAD" :value="__('Ciudad')" />
                <x-text-input id="CIUDAD" class="block mt-1 w-full" type="text" name="CIUDAD"
                              :value="old('CIUDAD')" placeholder="Ej. San Pedro Sula" />
                <x-input-error :messages="$errors->register->get('CIUDAD')" class="mt-2" />
              </div>
              <div>
                <x-input-label for="COLONIA" :value="__('Colonia')" />
                <x-text-input id="COLONIA" class="block mt-1 w-full" type="text" name="COLONIA"
                              :value="old('COLONIA')" placeholder="Ej. Rivera Hernández" />
                <x-input-error :messages="$errors->register->get('COLONIA')" class="mt-2" />
              </div>
            </div>

            {{-- Dirección / Referencia --}}
            <div class="mt-4">
              <x-input-label for="REFERENCIA" :value="__('Dirección / Referencia')" />
              <textarea id="REFERENCIA" name="REFERENCIA" rows="3"
                        class="mt-1 block w-full rounded-md border-slate-300 focus:border-cyan-500 focus:ring-cyan-500"
                        placeholder="Col. Centro, Calle 1 #123">{{ old('REFERENCIA') }}</textarea>
              <x-input-error :messages="$errors->register->get('REFERENCIA')" class="mt-2" />
            </div>

            {{-- Correo --}}
            <div class="mt-6">
              <x-input-label for="CORREO" :value="__('Correo electrónico')" />
              <x-text-input
                  id="CORREO"
                  type="email"
                  name="CORREO"
                  :value="old('CORREO')"
                  placeholder="tucorreo@ejemplo.com"
                  required
                  class="block mt-1 w-full {{ ($errors->register ?? $errors)->has('CORREO') ? 'is-invalid' : '' }}"
              />
              <x-input-error :messages="($errors->register ?? $errors)->get('CORREO')" class="mt-2" />
            </div>

            {{-- PREGUNTAS DE SEGURIDAD (igual que original) --}}
            <h3 class="text-sm font-semibold text-slate-700 mt-8 mb-2">Preguntas de seguridad</h3>
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

            {{-- CREDENCIALES (usuario + contraseña) --}}
            <h3 class="text-sm font-semibold text-slate-700 mt-8 mb-2">Usuario asignado</h3>
            <div class="mb-3 text-xs text-slate-600">
              Este será tu usuario para iniciar sesión: <code class="font-semibold" id="username-preview-inline"></code>
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
              <x-primary-button class="px-5">Registrarme</x-primary-button>
            </div>
          </form>
        </div>
      </div>
    </div>

  </div> {{-- cierre del x-data de modales --}}

  {{-- Script: vista previa del usuario autogenerado (igual que original) --}}
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

  {{-- Delegación de botones data-open="login/register" (igual que original) --}}
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

</body>
</html>
