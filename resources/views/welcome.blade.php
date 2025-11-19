<!doctype html>
<html lang="es" x-data="{ showLogin:false, showRegister:false }">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bienvenido — Clínica Dental</title>
  <link rel="icon" href="{{ asset('favicon.ico') }}">
  <!-- Tailwind por CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Alpine.js para abrir/cerrar modales (sin archivos nuevos) -->
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <style>[x-cloak]{display:none!important}</style>

  <!-- Estilos del formulario dentro de modales -->
  <style type="text/tailwindcss">
    @layer components {
      /* Hace que inputs/textarea/select dentro de .modal-panel se vean definidos */
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
  <style>
    .modal-animated {
      animation: floatIn 320ms ease;
    }
    @keyframes floatIn {
      0% {opacity: 0; transform: translateY(16px) scale(.98);} 
      100% {opacity: 1; transform: translateY(0) scale(1);} 
    }
  </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-sky-50 via-white to-teal-50 text-slate-800 relative">

  <!-- halos decorativos -->
  <div class="pointer-events-none absolute inset-0 -z-10 overflow-hidden">
    <div class="absolute -top-28 -left-24 h-96 w-96 rounded-full bg-sky-200/45 blur-3xl"></div>
    <div class="absolute -bottom-28 -right-24 h-96 w-96 rounded-full bg-teal-200/45 blur-3xl"></div>
  </div>

  <!-- header -->
  <header class="mx-auto max-w-6xl px-6 py-5">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-3">
        <img src="{{ asset('images/logo_clinica.avif') }}" class="h-10 w-10 rounded-full shadow-sm ring-1 ring-white/60" alt="Logo">
        <span class="text-lg sm:text-xl font-semibold tracking-tight">
          Complejo Dental <span class="text-slate-500">López Molinari</span>
        </span>
      </div>
      {{-- nav superior eliminado a petición --}}
      <div></div>
    </div>
  </header>

 <!-- hero -->
<main class="mx-auto max-w-6xl px-6 py-6">
  <div class="grid items-center gap-10 lg:grid-cols-2">
    <section>
      <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight leading-tight text-slate-900">
        Gestión clínica <span class="text-sky-700">simple</span>, clara y rápida
      </h1>

      <div class="mt-8 flex flex-wrap gap-3">
        {{-- Abrir modales por eventos globales (funciona fuera del x-data de los modales) --}}
        <button
          type="button"
          data-open="login"
          class="inline-flex items-center justify-center rounded-xl bg-sky-600 px-6 py-3 text-white shadow-sm ring-1 ring-sky-600/10 hover:bg-sky-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-sky-600">
          Iniciar sesión
        </button>

        <button
          type="button"
          data-open="register"
          class="inline-flex items-center justify-center rounded-xl bg-white px-6 py-3 text-slate-800 shadow-sm ring-1 ring-slate-200 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-slate-400">
          Registrarse
        </button>
      </div>
    </section>


      <!-- tarjetón ilustrativo -->
      <section aria-label="Ilustración clínica">
        <div class="relative rounded-2xl bg-white/70 backdrop-blur p-3 shadow-sm ring-1 ring-slate-200">
          <div class="absolute -inset-0.5 -z-10 rounded-2xl bg-gradient-to-tr from-sky-200/40 to-teal-200/40 blur-2xl"></div>
          <img
            src="https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?q=80&w=1200&auto=format&fit=crop"
            alt="Ilustración clínica"
            class="h-[320px] w-full rounded-xl object-cover sm:h-[380px] lg:h-[420px]">
        </div>
      </section>
    </div>
  </main>

  <footer class="mx-auto max-w-6xl px-6 py-10">
    <div class="rounded-2xl bg-white/60 backdrop-blur px-5 py-4 text-center text-sm text-slate-500 ring-1 ring-slate-200">
      © {{ date('Y') }} Complejo Dental López Molinari
    </div>
  </footer>





  
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



  <!-- ===== MODAL: LOGIN (mismo archivo) ===== -->
  <div x-cloak x-show="showLogin" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-slate-900/60" @click="showLogin=false"></div>

    <!-- Añadido: modal-panel -->
    <div x-transition
         class="modal-panel modal-animated relative w-full max-w-md mx-4 rounded-2xl bg-white shadow-xl ring-1 ring-slate-200">
      <div class="flex items-center justify-between px-5 py-4 border-b">
        <h3 class="text-base font-semibold text-slate-800">Iniciar sesión</h3>
        <button class="p-2 rounded-md hover:bg-slate-100" @click="showLogin=false" aria-label="Cerrar">✕</button>
      </div>
      <div class="p-5 max-h-[80vh] overflow-y-auto">

        {{-- (Opcional) Mensaje general del modal de login --}}
        @if ($errors->login->any())
          <div class="mb-3 text-sm text-red-600">No pudimos iniciar sesión con los datos ingresados. Verifica tu usuario o correo y tu contraseña e inténtalo nuevamente.</div>
        @endif

        {{-- FORM LOGIN (usuario o correo + password) --}}
        <form method="POST" action="{{ route('login') }}"novalidate x-data="{ showPwd:false }">
          @csrf

          <!-- Usuario o correo -->
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

          <!-- Contraseña -->
<div class="mt-4">
  <x-input-label for="password" :value="__('Contraseña')" />

  <div class="relative">
    <x-text-input
      id="password"
      name="password"
      x-bind:type="showPwd ? 'text' : 'password'"   {{-- alterna texto/oculto --}}
      class="block mt-1 w-full pr-10"               {{-- pr-10 deja espacio al botón --}}
      required
      autocomplete="current-password"
    />

    <!-- Botón ojo -->
    <button
      type="button"
      class="absolute inset-y-0 right-2 mt-1 flex items-center text-slate-500 hover:text-slate-700"
      @click="showPwd = !showPwd"
      :aria-label="showPwd ? 'Ocultar contraseña' : 'Mostrar contraseña'"
    >
      <!-- ojo abierto -->
      <svg x-show="!showPwd" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
      <!-- ojo tachado -->
      <svg x-show="showPwd" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3l18 18M10.58 10.58A3 3 0 0012 15a3 3 0 002.42-4.42M9.88 5.09A9.96 9.96 0 0112 5c4.477 0 8.268 2.943 9.542 7-.39 1.24-1.02 2.36-1.85 3.33M6.27 6.27C4.39 7.58 3.03 9.54 2.46 12c1.274 4.057 5.065 7 9.542 7a9.96 9.96 0 004.12-.87"/></svg>
    </button>
  </div>
</div>


          <!-- Remember -->
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

  <!-- ===== MODAL: REGISTRO ===== -->
  <div x-cloak x-show="showRegister" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-slate-900/60" @click="showRegister=false"></div>

  <div x-transition class="modal-panel modal-animated relative w-full max-w-5xl mx-4 rounded-2xl bg-white shadow-xl ring-1 ring-slate-200">
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

        @php
            $nombresCompletos = trim((old('PRIMER_NOMBRE') ?? '') . ' ' . (old('SEGUNDO_NOMBRE') ?? ''));
            $apellidosCompletos = trim((old('PRIMER_APELLIDO') ?? '') . ' ' . (old('SEGUNDO_APELLIDO') ?? ''));
        @endphp

        {{-- Header: usuario autogenerado --}}
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-sm font-semibold text-slate-700">Datos personales</h2>
          <div id="username-pill" class="hidden text-xs bg-sky-50 border border-sky-100 rounded-md px-3 py-2 text-sky-800 shadow-sm">
            <span class="font-medium mr-1">Usuario asignado:</span>
            <code id="username-preview" class="font-semibold"></code>
          </div>
        </div>

        {{-- Nombres y apellidos combinados --}}
        <input type="hidden" name="PRIMER_NOMBRE" id="PRIMER_NOMBRE" value="{{ old('PRIMER_NOMBRE') }}">
        <input type="hidden" name="SEGUNDO_NOMBRE" id="SEGUNDO_NOMBRE" value="{{ old('SEGUNDO_NOMBRE') }}">
        <input type="hidden" name="PRIMER_APELLIDO" id="PRIMER_APELLIDO" value="{{ old('PRIMER_APELLIDO') }}">
        <input type="hidden" name="SEGUNDO_APELLIDO" id="SEGUNDO_APELLIDO" value="{{ old('SEGUNDO_APELLIDO') }}">

        <div class="grid gap-4 md:grid-cols-2">
          <div>
            <x-input-label for="NOMBRES_COMPLETOS" :value="__('Nombres (primer y segundo)')" />
            <x-text-input id="NOMBRES_COMPLETOS" class="block mt-1 w-full" type="text" name="NOMBRES_COMPLETOS"
                          :value="$nombresCompletos" required autofocus />
            <x-input-error :messages="array_filter([$errors->register->first('PRIMER_NOMBRE'), $errors->register->first('SEGUNDO_NOMBRE')])" class="mt-2" />
          </div>
          <div>
            <x-input-label for="APELLIDOS_COMPLETOS" :value="__('Apellidos (primero y segundo)')" />
            <x-text-input id="APELLIDOS_COMPLETOS" class="block mt-1 w-full" type="text" name="APELLIDOS_COMPLETOS"
                          :value="$apellidosCompletos" required />
            <x-input-error :messages="array_filter([$errors->register->first('PRIMER_APELLIDO'), $errors->register->first('SEGUNDO_APELLIDO')])" class="mt-2" />
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

        {{-- Departamento + Ciudad --}}
        <div class="mt-4 grid gap-4 md:grid-cols-2">
          <div>
            <x-input-label for="DEPARTAMENTO" :value="__('Departamento')" />
            <select id="DEPARTAMENTO" name="DEPARTAMENTO" class="block mt-1 w-full rounded-md border-slate-300">
              <option value="">Selecciona un departamento</option>
            </select>
            <x-input-error :messages="$errors->register->get('DEPARTAMENTO')" class="mt-2" />
          </div>
          <div>
            <x-input-label for="CIUDAD" :value="__('Ciudad')" />
            <select id="CIUDAD" name="CIUDAD" class="block mt-1 w-full rounded-md border-slate-300" disabled>
              <option value="">Selecciona un departamento primero</option>
            </select>
            <x-input-error :messages="$errors->register->get('CIUDAD')" class="mt-2" />
          </div>
        </div>

        <input type="hidden" name="MUNICIPIO" id="MUNICIPIO" value="{{ old('MUNICIPIO') }}">
        <input type="hidden" name="COLONIA" id="COLONIA" value="">

        {{-- Dirección / Referencia --}}
        <div class="mt-4">
          <x-input-label for="REFERENCIA" :value="__('Dirección / Referencia')" />
          <textarea id="REFERENCIA" name="REFERENCIA" rows="3"
                    class="mt-1 block w-full rounded-md border-slate-300 focus:border-cyan-500 focus:ring-cyan-500">{{ old('REFERENCIA') }}</textarea>
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
      required
      class="block mt-1 w-full {{ ($errors->register ?? $errors)->has('CORREO') ? 'is-invalid' : '' }}"
  />

  <x-input-error :messages="($errors->register ?? $errors)->get('CORREO')" class="mt-2" />
</div>


{{-- ===== PREGUNTAS DE SEGURIDAD ===== --}}
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


        {{-- ====== CREDENCIALES ====== --}}
        <h3 class="text-sm font-semibold text-slate-700 mt-8 mb-2">Credenciales</h3>
        <div class="mb-3 text-xs text-slate-600">
          Tu usuario se generará automáticamente con los datos ingresados: <code class="font-semibold" id="username-preview-inline"></code>
        </div>

        {{-- Contraseña + Confirmación (una sola columna) --}}
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
                :aria-label="showPwd ? 'Ocultar contraseña' : 'Mostrar contraseña'"
                title=""
              >
                {{-- ojo abierto --}}
                <svg x-show="!showPwd" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                {{-- ojo tachado --}}
                <svg x-show="showPwd" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3l18 18M10.58 10.58A3 3 0 0012 15a3 3 0 002.42-4.42M9.88 5.09A9.96 9.96 0 0112 5c4.477 0 8.268 2.943 9.542 7-.39 1.24-1.02 2.36-1.85 3.33M6.27 6.27C4.39 7.58 3.03 9.54 2.46 12c1.274 4.057 5.065 7 9.542 7a9.96 9.96 0 004.12-.87"/></svg>
              </button>
            </div>

            {{-- Checklist dinámico debajo --}}
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
                :aria-label="showConfirm ? 'Ocultar confirmación' : 'Mostrar confirmación'"
                title=""
              >
                <svg x-show="!showConfirm" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <svg x-show="showConfirm" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3l18 18M10.58 10.58A3 3 0 0012 15a3 3 0 002.42-4.42M9.88 5.09A9.96 9.96 0 0112 5c4.477 0 8.268 2.943 9.542 7-.39 1.24-1.02 2.36-1.85 3.33M6.27 6.27C4.39 7.58 3.03 9.54 2.46 12c1.274 4.057 5.065 7 9.542 7a9.96 9.96 0 004.12-.87"/></svg>
              </button>
            </div>

            <div class="mt-1 text-sm" :class="match ? 'text-green-600' : 'text-red-600'">
              <span class="font-semibold" x-text="match ? '✓' : '•'"></span>
              Las contraseñas deben coincidir
            </div>

            {{-- Errores del servidor (bag register) --}}
            <x-input-error :messages="$errors->register->get('password')" class="mt-2" />
            <x-input-error :messages="$errors->register->get('password_confirmation')" class="mt-2" />
          </div>
        </div>

        <div class="flex items-center justify-end mt-6">
          <x-primary-button class="px-5" id="btn-register">Registrarme</x-primary-button>
        </div>
      </form>
    </div>
  </div>
</div>


          {{-- Script: vista previa del usuario autogenerado y normalización de nombres --}}
<script>
  (function () {
    const maxLen = 50;
    const $n = document.getElementById('PRIMER_NOMBRE');
    const $a = document.getElementById('PRIMER_APELLIDO');
    const $namesFull = document.getElementById('NOMBRES_COMPLETOS');
    const $lastFull = document.getElementById('APELLIDOS_COMPLETOS');
    const $pill = document.getElementById('username-pill');
    const $out1 = document.getElementById('username-preview');
    const $out2 = document.getElementById('username-preview-inline');

    function splitName(value) {
      const parts = (value || '').trim().split(/\s+/).filter(Boolean);
      const first = parts.shift() || '';
      const rest = parts.join(' ');
      return [first, rest];
    }

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

    function syncNames(){
      const [pName, sName] = splitName($namesFull?.value || '');
      const [pLast, sLast] = splitName($lastFull?.value || '');
      if($n) $n.value = pName;
      if($a) $a.value = pLast;
      const $sN = document.getElementById('SEGUNDO_NOMBRE');
      const $sA = document.getElementById('SEGUNDO_APELLIDO');
      if($sN) $sN.value = sName;
      if($sA) $sA.value = sLast;
      up();
    }

    ['input','change'].forEach(e=>{
      $namesFull?.addEventListener(e, syncNames);
      $lastFull?.addEventListener(e, syncNames);
    });
    ['input','change'].forEach(e=>{ $n?.addEventListener(e,up); $a?.addEventListener(e,up); });

    const registerForm = document.querySelector('form[action="{{ route('register') }}"]');
    registerForm?.addEventListener('submit', () => {
      syncNames();
      const city = document.getElementById('CIUDAD')?.value || '';
      const municipio = document.getElementById('MUNICIPIO');
      if(municipio) municipio.value = city;
    });

    syncNames();
    up();
  })();
</script>

{{-- Script: cargar departamentos y ciudades de Honduras --}}
<script>
  (function(){
    const data = {
      "Atlántida": ["La Ceiba", "Tela", "Arizona", "Esparta", "Jutiapa", "La Masica", "San Francisco", "El Porvenir"],
      "Choluteca": ["Choluteca", "Apacilagua", "Concepción de María", "Duyure", "El Corpus", "Marcovia", "Namasigüe", "Orocuina", "Pespire", "San Antonio de Flores", "San Isidro", "San José", "San Marcos de Colón", "Santa Ana de Yusguare"],
      "Colón": ["Trujillo", "Balfate", "Iriona", "Limón", "Saba", "Santa Fe", "Santa Rosa de Aguán", "Sonaguera", "Tocoa", "Bonito Oriental"],
      "Comayagua": ["Comayagua", "Ajuterique", "El Rosario", "Esquías", "Humuya", "La Libertad", "Lamaní", "Las Lajas", "Lejamaní", "Meámbar", "Minas de Oro", "Ojos de Agua", "San Jerónimo", "San José de Comayagua", "San José del Potrero", "San Luis", "San Sebastián", "Siguatepeque", "Taulabé", "Villa de San Antonio"],
      "Copán": ["Santa Rosa de Copán", "Cabañas", "Concepción", "Copán Ruinas", "Corquín", "Cucuyagua", "Dolores", "Dulce Nombre", "El Paraíso", "Florida", "La Jigua", "La Unión", "Nueva Arcadia", "San Agustín", "San Antonio", "San Jerónimo", "San José", "San Juan de Opoa", "San Nicolás", "San Pedro", "Santa Rita", "Trinidad de Copán", "Veracruz"],
      "Cortés": ["San Pedro Sula", "Choloma", "La Lima", "Villanueva", "Puerto Cortés", "Omoa", "Pimienta", "Potrerillos", "San Antonio de Cortés", "San Francisco de Yojoa", "Santa Cruz de Yojoa"],
      "El Paraíso": ["Yuscarán", "Alauca", "Danlí", "El Paraíso", "Güinope", "Jacaleapa", "Liure", "Morocelí", "Oropolí", "Potrerillos", "San Antonio de Flores", "San Lucas", "San Matías", "Soledad", "Teupasenti", "Texiguat", "Vado Ancho"],
      "Francisco Morazán": ["Distrito Central", "Alubarén", "Cedros", "Curarén", "El Porvenir", "Guaimaca", "La Libertad", "La Venta", "Lepaterique", "Maraita", "Marale", "Nueva Armenia", "Ojojona", "Orica", "Reitoca", "Sabanagrande", "San Antonio de Oriente", "San Buenaventura", "San Ignacio", "San Juan de Flores", "San Miguelito", "Santa Ana", "Santa Lucía", "Talanga", "Tatumbla", "Valle de Ángeles", "Villa de San Francisco", "Vallecillo"],
      "Gracias a Dios": ["Puerto Lempira", "Ahuas", "Brus Laguna", "Juan Francisco Bulnes", "Ramón Villeda Morales", "Wampusirpi"],
      "Intibucá": ["La Esperanza", "Camasca", "Colomoncagua", "Concepción", "Dolores", "Intibucá", "Jesús de Otoro", "Magdalena", "Masaguara", "San Antonio", "San Isidro", "San Juan", "San Marcos de la Sierra", "San Miguelito", "Santa Lucía", "Yamaranguila"],
      "Islas de la Bahía": ["Roatán", "José Santos Guardiola", "Utila", "Guanaja"],
      "La Paz": ["La Paz", "Aguanqueterique", "Cabañas", "Cane", "Chinacla", "Guajiquiro", "Lauterique", "Marcala", "Mercedes de Oriente", "Opatoro", "San Antonio del Norte", "San José", "San Juan", "San Pedro de Tutule", "Santa Ana", "Santa Elena", "Santa María", "Santiago de Puringla", "Yarula"],
      "Lempira": ["Gracias", "Belén", "Candelaria", "Cololaca", "Erandique", "Gualcince", "Guarita", "La Campa", "La Iguala", "Las Flores", "La Unión", "La Virtud", "Lepaera", "Mapulaca", "Piraera", "San Andrés", "San Francisco", "San Juan Guarita", "San Manuel Colohete", "San Marcos de Caiquín", "San Rafael", "San Sebastián", "Santa Cruz", "Talgua", "Tambla", "Tomalá", "Valladolid", "Virginia"],
      "Ocotepeque": ["Nueva Ocotepeque", "Belén Gualcho", "Concepción", "Dolores Merendón", "Fraternidad", "La Encarnación", "La Labor", "Lucerna", "Mercedes", "San Fernando", "San Francisco del Valle", "San Jorge", "San Marcos", "Santa Fe", "Sensenti", "Sinuapa"],
      "Olancho": ["Juticalpa", "Campamento", "Catacamas", "Concordia", "Dulce Nombre de Culmí", "El Rosario", "Esquipulas del Norte", "Gualaco", "Guarizama", "Guata", "Guayape", "Jano", "La Unión", "Mangulile", "Manto", "Salamá", "San Esteban", "San Francisco de Becerra", "San Francisco de la Paz", "Santa María del Real", "Silca", "Yocón"],
      "Santa Bárbara": ["Santa Bárbara", "Arada", "Atima", "Azacualpa", "Ceguaca", "Concepción del Norte", "Concepción del Sur", "Chinda", "El Níspero", "Gualala", "Ilama", "Las Vegas", "Macuelizo", "Naranjito", "Nuevo Celilac", "Petoa", "Protección", "Quimistán", "San Francisco de Ojuera", "San José de las Colinas", "San Luis", "San Marcos", "San Nicolás", "San Pedro Zacapa", "San Vicente Centenario", "Santa Rita", "Trinidad"],
      "Valle": ["Nacaome", "Alianza", "Amapala", "Aramecina", "Caridad", "Goascorán", "Langue", "San Francisco de Coray", "San Lorenzo"] ,
      "Yoro": ["Yoro", "Arenal", "El Negrito", "El Progreso", "Jocón", "Morazán", "Olanchito", "Santa Rita", "Sulaco", "Victoria", "Yorito"]
    };

    const deptSelect = document.getElementById('DEPARTAMENTO');
    const citySelect = document.getElementById('CIUDAD');

    function fillDepartments() {
      if(!deptSelect) return;
      Object.keys(data).sort().forEach(dep => {
        const opt = document.createElement('option');
        opt.value = dep;
        opt.textContent = dep;
        if(dep === "{{ old('DEPARTAMENTO') }}") opt.selected = true;
        deptSelect.appendChild(opt);
      });
    }

    function fillCities(dep) {
      if(!citySelect) return;
      citySelect.innerHTML = '';
      if(!dep || !data[dep]) {
        citySelect.disabled = true;
        const opt = document.createElement('option');
        opt.value = '';
        opt.textContent = 'Selecciona un departamento primero';
        citySelect.appendChild(opt);
        return;
      }
      citySelect.disabled = false;
      data[dep].forEach(city => {
        const opt = document.createElement('option');
        opt.value = city;
        opt.textContent = city;
        if(city === "{{ old('CIUDAD') }}") opt.selected = true;
        citySelect.appendChild(opt);
      });
    }

    deptSelect?.addEventListener('change', (e) => {
      fillCities(e.target.value);
    });

    fillDepartments();
    if(deptSelect?.value) {
      fillCities(deptSelect.value);
    } else {
      fillCities('');
    }
  })();
</script>

{{-- Modal emergente de bienvenida al registrar --}}
<div id="registerOverlay" class="hidden fixed inset-0 z-50 items-center justify-center bg-slate-900/70 px-4">
  <div class="modal-panel modal-animated max-w-md w-full bg-white rounded-2xl shadow-xl ring-1 ring-slate-200 overflow-hidden">
    <div class="bg-gradient-to-r from-sky-500 to-teal-500 px-5 py-4 text-white">
      <h4 class="text-lg font-semibold">¡Bienvenido a la clínica!</h4>
      <p class="text-sm opacity-90">Estamos preparando tu cuenta.</p>
    </div>
    <div class="p-5 space-y-2 text-slate-700">
      <p class="text-sm">Tu usuario asignado será:</p>
      <div class="rounded-lg bg-slate-50 border border-slate-200 px-4 py-3 flex items-center justify-between">
        <span class="text-slate-500 text-sm">Usuario</span>
        <code id="overlay-username" class="font-semibold text-slate-800"></code>
      </div>
      <p class="text-xs text-slate-500">Guárdalo para tu próximo inicio de sesión.</p>
    </div>
  </div>
</div>

<script>
  // Delegación: cualquier elemento con data-open="login" o "register" abre el modal correcto
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-open]');
    if (!btn) return;
    e.preventDefault();
    const which = btn.getAttribute('data-open');
    if (which === 'login')   window.dispatchEvent(new CustomEvent('open-login'));
    if (which === 'register') window.dispatchEvent(new CustomEvent('open-register'));
  });

  (function(){
    const overlay = document.getElementById('registerOverlay');
    const overlayUsername = document.getElementById('overlay-username');
    const btnRegister = document.getElementById('btn-register');
    const registerForm = document.querySelector('form[action="{{ route('register') }}"]');
    const userPreview = document.getElementById('username-preview-inline');

    function showOverlay(){
      if(!overlay) return;
      if(overlayUsername && userPreview){
        overlayUsername.textContent = userPreview.textContent || '';
      }
      overlay.classList.remove('hidden');
      overlay.classList.add('flex');
    }

    registerForm?.addEventListener('submit', () => {
      showOverlay();
    });

    btnRegister?.addEventListener('click', () => {
      if(userPreview && userPreview.textContent === ''){
        const fallback = document.getElementById('PRIMER_NOMBRE')?.value || 'usuario';
        if(overlayUsername) overlayUsername.textContent = fallback;
      }
    });
  })();
</script>

</body>
</html>
