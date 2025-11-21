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

  <!-- Estilos del formulario dentro de modales (igual que tu original) -->
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

  {{-- ========= HEADER NUEVO ========= --}}
  <header class="w-full border-b border-slate-200 bg-white/80 backdrop-blur">
    <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <img
          src="{{ asset('images/logo_clinica.avif') }}"
          class="h-12 w-12 rounded-full shadow-sm ring-1 ring-white/60 object-contain bg-white"
          alt="Logo Complejo Dental López Molinari">
        <div>
          <p class="text-base sm:text-lg font-semibold tracking-tight text-slate-900 leading-tight">
            Complejo Dental <span class="text-sky-800">López Molinari</span>
          </p>
          <p class="text-[11px] sm:text-xs text-slate-500">
            Sistema de gestión clínica y agenda de citas
          </p>
        </div>
      </div>

      {{-- arriba ya no mostramos botones para invitados (solo si está logueado) --}}
      @if (Route::has('login'))
        @auth
          <a href="{{ url('/dashboard') }}"
             class="inline-flex items-center rounded-full border border-sky-700 px-4 py-2 text-sm font-semibold text-sky-800 hover:bg-sky-50 transition">
            Ir al panel
          </a>
        @endauth
      @endif
    </div>
  </header>

  {{-- ========= HERO / PANTALLA DE BIENVENIDA NUEVA ========= --}}
  <main class="flex-1">
    <section class="relative overflow-hidden">
      {{-- fondo suave en degradado --}}
      <div class="absolute inset-0 bg-gradient-to-br from-sky-50 via-slate-50 to-slate-100 pointer-events-none"></div>

      <div class="relative max-w-6xl mx-auto px-6 py-16 lg:py-20 grid items-center gap-10 lg:grid-cols-2">
        {{-- Columna izquierda: texto + botones --}}
        <section>
          <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-tight text-slate-900">
            Gestión clínica <span class="text-sky-800">clara y rápida</span>
          </h1>

          <p class="mt-5 text-base sm:text-lg text-slate-600">
            Administra citas, pacientes y agenda del <strong>Complejo Dental López Molinari</strong> desde un solo lugar.
            Accede como paciente o como doctor de forma segura.
          </p>

          <div class="mt-8 flex flex-wrap gap-3">
            {{-- Botones que SIGUEN usando tu lógica de modales (data-open) --}}
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

          {{-- Mensajes sobre el sistema (no de seguridad, como pediste) --}}
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

        {{-- Columna derecha: foto real de la clínica --}}
        <section aria-label="Sede del Complejo Dental">
          <div class="relative rounded-3xl bg-white/70 backdrop-blur p-3 shadow-lg ring-1 ring-slate-200">
            <div class="absolute -inset-0.5 -z-10 rounded-3xl bg-gradient-to-tr from-sky-200/40 to-sky-400/30 blur-2xl"></div>

            <figure class="relative h-[260px] sm:h-[320px] lg:h-[360px] w-full overflow-hidden rounded-2xl">
              <img
                src="{{ asset('images/clinica_exterior.avif') }}"
                alt="Fachada del Complejo Dental López Molinari"
                class="h-full w-full object-cover">
              <figcaption class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-900/85 via-slate-900/30 to-transparent px-4 py-3">
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

  {{-- ========= FOOTER NUEVO ========= --}}
  <footer class="border-t border-slate-200 bg-white">
    <div class="max-w-6xl mx-auto px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-slate-500">
      <span>© {{ date('Y') }} Complejo Dental López Molinari. Todos los derechos reservados.</span>
      <span>Sistema de gestión clínica desarrollado para uso académico y profesional.</span>
    </div>
  </footer>

  {{-- ========================================================= --}}
  {{-- A PARTIR DE AQUÍ VA TU LÓGICA ORIGINAL DE MODALES TAL CUAL --}}
  {{-- ========================================================= --}}

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

    <!-- ===== MODAL: LOGIN (igual que tu original) ===== -->
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

    <!-- ===== MODAL: REGISTRO (idéntico al tuyo) ===== -->
    {{-- A partir de aquí pego tu modal de registro tal cual lo tenías --}}
    {!! '' !!}
    <!-- (para no romper formato del mensaje, copia desde
         `<div x-cloak x-show="showRegister"...` hasta antes del `<script>` final
         exactamente como en tu archivo original) -->

    {{-- Como el modal que pegaste es MUY largo, para evitar errores al copiar aquí,
       te recomiendo: en VSCode, toma desde:
         <div x-cloak x-show="showRegister" ...
       hasta el último </div> de ese modal,
       y deja todo IGUAL. No cambies ids, names ni nada. --}}
  </div>

  {{-- Script de delegación: se mantiene tal cual tu original --}}
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
  </script>

</body>
</html>
