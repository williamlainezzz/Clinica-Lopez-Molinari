<!doctype html>
<html lang="es" x-data="{ showLogin:false, showRegister:false, showRegisterSuccess:false }">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bienvenido - Clinica Dental</title>
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
        @apply block w-full mt-1 rounded-xl
               border border-slate-300 bg-white
               placeholder-slate-400 text-slate-800
               shadow-sm
               focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500;
      }

      .modal-panel label {
        @apply text-[13px] font-semibold tracking-[0.02em] text-slate-700;
      }

      .modal-card {
        @apply rounded-[28px] border border-white/70 bg-white/95 shadow-2xl shadow-slate-900/10 backdrop-blur;
      }

      .section-kicker {
        @apply text-[11px] font-semibold uppercase tracking-[0.24em] text-blue-700;
      }
    }
  </style>
</head>
<body class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(29,78,216,0.12),_transparent_28%),radial-gradient(circle_at_bottom_right,_rgba(59,130,246,0.14),_transparent_34%),linear-gradient(180deg,_#f8fbff_0%,_#eef4ff_48%,_#f8fbff_100%)] text-slate-800 relative">

  <div class="pointer-events-none absolute inset-0 -z-10 overflow-hidden">
    <div class="absolute left-[-8rem] top-[-7rem] h-80 w-80 rounded-full bg-blue-200/40 blur-3xl"></div>
    <div class="absolute right-[-6rem] top-28 h-72 w-72 rounded-full bg-sky-200/40 blur-3xl"></div>
    <div class="absolute bottom-[-8rem] left-1/3 h-96 w-96 rounded-full bg-indigo-100/50 blur-3xl"></div>
  </div>

  <header class="mx-auto max-w-7xl px-6 py-3 lg:px-8">
    <div class="flex items-center justify-between rounded-full border border-white/80 bg-white/75 px-5 py-3 shadow-lg shadow-slate-900/5 backdrop-blur">
      <div class="flex items-center gap-3">
        <img src="{{ asset('images/logo-icon.jpg') }}" class="h-11 w-11 rounded-full object-cover ring-4 ring-blue-50" alt="Logo">
        <div>
          <p class="text-xs font-semibold uppercase tracking-[0.28em] text-blue-700">Sistema Clinico</p>
          <p class="text-base font-semibold text-slate-900 sm:text-lg">Complejo Dental <span class="text-slate-500">Lopez Molinari</span></p>
        </div>
      </div>
      <div class="hidden items-center gap-3 md:flex">
        <button type="button" data-open="login" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">
          Iniciar sesion
        </button>
        <button type="button" data-open="register" class="inline-flex items-center justify-center rounded-full bg-blue-700 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-blue-700/20 transition hover:bg-blue-800">
          Crear cuenta
        </button>
      </div>
    </div>
  </header>

  <main class="mx-auto max-w-7xl px-6 pb-3 pt-3 lg:px-8">
    <section class="rounded-[26px] border border-blue-200/80 bg-white/90 p-5 shadow-[0_18px_50px_rgba(15,23,42,0.08)] backdrop-blur sm:min-h-[880px] sm:p-6">
      <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_390px] lg:items-center">
        <div class="space-y-5 pt-3">
          <div class="space-y-4">
            <div class="flex items-center">
              <img src="{{ asset('images/logo-horizontal.jpg') }}" alt="Complejo Dental Lopez Molinari" class="h-18 w-auto sm:h-24">
            </div>

            <span class="inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-4 py-2 text-xs font-semibold uppercase tracking-[0.24em] text-blue-800">
              <span class="h-2 w-2 rounded-full bg-blue-500"></span>
              Clinica odontologica
            </span>

            <div class="space-y-3 pt-2">
              <h1 class="max-w-3xl text-[3.2rem] font-black leading-[0.97] tracking-tight text-slate-950 sm:text-[3.7rem]">
                Tu Sonrisa en un
                <span class="text-blue-600">Santuario</span>
                de Precision.
              </h1>
              <p class="max-w-2xl text-base leading-8 text-slate-600">
                Combinamos tecnologia dental de vanguardia con un entorno disenado para tu tranquilidad. Experimenta el futuro de la salud oral en Complejo Dental Lopez Molinari.
              </p>
            </div>
          </div>
        </div>

        <section aria-label="Foto del local" class="pt-4">
          <div class="relative rounded-[26px] border border-blue-100 bg-white p-3 shadow-lg shadow-slate-900/8">
            <img src="{{ asset('images/clinic-local.png') }}" alt="Fachada del local" class="h-[280px] w-full rounded-[22px] object-cover sm:h-[350px]">

            <a href="https://maps.app.goo.gl/c1FejDNUYQRaAQUS9" target="_blank" rel="noopener noreferrer" class="absolute bottom-2 right-2 flex items-center gap-2.5 rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-xl shadow-slate-900/10 transition hover:-translate-y-0.5 hover:shadow-2xl">
              <span class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-50 text-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2a7 7 0 0 0-7 7c0 4.97 5.24 11.91 6.05 12.97a1.2 1.2 0 0 0 1.9 0C13.76 20.91 19 13.97 19 9a7 7 0 0 0-7-7Zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5Z"/></svg>
              </span>
              <span>
                <span class="block text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-400">Ubicacion</span>
                <span class="block text-sm font-bold text-slate-900">Haz clic para ver la direccion de la clinica</span>
              </span>
            </a>
          </div>
        </section>
      </div>

      <div class="mt-10 grid gap-4 lg:grid-cols-[minmax(0,1fr)_270px]">
        <section class="rounded-[22px] border border-blue-100 bg-white p-5 shadow-sm">
          <div class="flex items-center justify-between gap-4">
            <div>
              <h2 class="text-[1.95rem] font-bold text-slate-950">Nuestros Servicios Especializados</h2>
            </div>
            <span class="flex h-9 w-9 items-center justify-center rounded-xl border border-blue-100 bg-blue-50 text-blue-700">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v16m8-8H4"/></svg>
            </span>
          </div>

          <div class="mt-5 grid gap-4 sm:grid-cols-2">
            <div class="flex gap-4">
              <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 13c0-1.657 2.239-3 5-3s5 1.343 5 3-2.239 6-5 6-5-4.343-5-6Zm3.5-5.5c0-.828.672-1.5 1.5-1.5s1.5.672 1.5 1.5S12.828 9 12 9s-1.5-.672-1.5-1.5Z"/></svg>
              </span>
              <div>
                <h3 class="font-semibold text-slate-900">Implantes Dentales</h3>
                <p class="mt-1 text-sm leading-5 text-slate-600">Restauracion permanente con estetica natural y duradera.</p>
              </div>
            </div>

            <div class="flex gap-4">
              <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7h8M9 12h6M8 17h8"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 5h.01M6 12h.01M6 19h.01M18 5h.01M18 12h.01M18 19h.01"/></svg>
              </span>
              <div>
                <h3 class="font-semibold text-slate-900">Ortodoncia</h3>
                <p class="mt-1 text-sm leading-5 text-slate-600">Alineacion perfecta con tecnicas invisibles y tradicionales.</p>
              </div>
            </div>

            <div class="flex gap-4">
              <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 8l8 8M16 8l-8 8"/></svg>
              </span>
              <div>
                <h3 class="font-semibold text-slate-900">Estetica Dental</h3>
                <p class="mt-1 text-sm leading-5 text-slate-600">Carillas y blanqueamiento para una sonrisa radiante.</p>
              </div>
            </div>

            <div class="flex gap-4">
              <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3l7 4v5c0 5-3.5 7.5-7 9-3.5-1.5-7-4-7-9V7l7-4Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 12l1.5 1.5L14 11"/></svg>
              </span>
              <div>
                <h3 class="font-semibold text-slate-900">Odontologia Preventiva</h3>
                <p class="mt-1 text-sm leading-5 text-slate-600">El cuidado proactivo es la base de una salud oral optima.</p>
              </div>
            </div>
          </div>
        </section>

        <section class="rounded-[22px] bg-[#0b2c61] p-5 text-white shadow-xl shadow-blue-900/20">
          <h2 class="text-[1.95rem] font-bold leading-tight">Horarios de Atencion</h2>
          <p class="mt-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-blue-200">Atencion personalizada</p>

          <div class="mt-6 space-y-4 text-sm">
            <div class="flex items-center justify-between gap-4 border-b border-white/10 pb-3">
              <span class="font-semibold">Lunes - Viernes</span>
              <span class="font-bold">08:00 - 19:00</span>
            </div>
            <div class="flex items-center justify-between gap-4 border-b border-white/10 pb-3">
              <span class="font-semibold">Sabados</span>
              <span class="font-bold">08:00 - 19:00</span>
            </div>
            <div class="flex items-center justify-between gap-4 border-b border-white/10 pb-3 text-blue-100">
              <span class="font-semibold">Domingos</span>
              <span class="font-bold">CERRADO</span>
            </div>
          </div>

          <div class="mt-6 border-t border-white/10 pt-4">
            <p class="text-xs text-blue-200">Telefono de contacto</p>
            <p class="mt-2 text-[1.9rem] font-bold tracking-tight">+504 9985-5489</p>
          </div>
        </section>
      </div>
    </section>
  </main>

  <div
    x-data="{ showLogin:false, showRegister:false, showRegisterSuccess:false }"
    x-on:open-login.window="showLogin = true"
    x-on:open-register.window="showRegister = true"
    x-on:close-modals.window="showLogin = false; showRegister = false; showRegisterSuccess = false"
    x-init="
      @if (session('modal') === 'login' || request()->query('modal') === 'login') showLogin = true; @endif
      @if (session('modal') === 'register' || request()->query('modal') === 'register') showRegister = true; @endif
      @if (session('modal') === 'welcome-register-success') showRegisterSuccess = true; @endif
      @if ($errors->login->any()) showLogin = true; @endif
      @if ($errors->register->any()) showRegister = true; @endif
    "
  >

    <div x-cloak x-show="showRegisterSuccess" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm" @click="showRegisterSuccess=false"></div>

      <div x-transition class="modal-panel modal-card relative w-full max-w-lg">
        <div class="border-b border-slate-200 px-6 py-5">
          <div class="flex items-start justify-between gap-4">
            <div>
              <p class="section-kicker">Cuenta creada</p>
              <h3 class="mt-2 text-xl font-bold text-slate-900">Bienvenido al sistema</h3>
            </div>
            <button class="rounded-xl p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700" @click="showRegisterSuccess=false" aria-label="Cerrar">x</button>
          </div>
        </div>
        <div class="space-y-4 px-6 py-6">
          <div class="rounded-2xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-800">Tu cuenta fue creada correctamente.</div>
          <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Usuario generado</p>
            <p class="mt-2 font-mono text-lg font-semibold text-slate-900">{{ session('username_generado') }}</p>
          </div>
          <p class="text-sm leading-6 text-slate-600">Por seguridad, ahora inicia sesion con tus credenciales para continuar.</p>
        </div>
        <div class="flex justify-end border-t border-slate-200 px-6 py-4">
          <button type="button" class="inline-flex items-center justify-center rounded-2xl bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-800" @click="showRegisterSuccess=false">Aceptar</button>
        </div>
      </div>
    </div>

    <div x-cloak x-show="showLogin" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm" @click="showLogin=false"></div>

      <div x-transition class="modal-panel modal-card relative w-full max-w-md overflow-hidden">
        <div class="bg-gradient-to-r from-blue-800 to-blue-600 px-6 py-5 text-white">
          <div class="flex items-start justify-between gap-4">
            <div>
              <p class="text-xs font-semibold uppercase tracking-[0.24em] text-blue-100">Acceso seguro</p>
              <h3 class="mt-2 text-xl font-bold">Iniciar sesion</h3>
              <p class="mt-2 text-sm text-blue-50/90">Ingresa con tu usuario o correo y continua en el sistema.</p>
            </div>
            <button class="rounded-xl p-2 text-white/80 transition hover:bg-white/10 hover:text-white" @click="showLogin=false" aria-label="Cerrar">x</button>
          </div>
        </div>
        <div class="p-6">
          @if ($errors->login->any())
            <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">No pudimos iniciar sesion con los datos ingresados. Verifica tu usuario o correo y tu contrasena e intentalo nuevamente.</div>
          @endif

          <form method="POST" action="{{ route('login') }}" novalidate x-data="{ showPwd:false }" class="space-y-5">
            @csrf

            <div>
              <x-input-label for="login" :value="__('Usuario o correo')" />
              <x-text-input id="login" class="block mt-1 w-full" type="text" name="login" :value="old('login')" required autofocus autocomplete="username email" />
            </div>

            <div>
              <x-input-label for="password" :value="__('Contrasena')" />
              <div class="relative">
                <x-text-input id="password" name="password" x-bind:type="showPwd ? 'text' : 'password'" class="block mt-1 w-full pr-10" required autocomplete="current-password" />
                <button type="button" class="absolute inset-y-0 right-3 flex items-center text-slate-500 transition hover:text-slate-700" @click="showPwd = !showPwd" :aria-label="showPwd ? 'Ocultar contrasena' : 'Mostrar contrasena'">
                  <svg x-show="!showPwd" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                  <svg x-show="showPwd" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3l18 18M10.58 10.58A3 3 0 0012 15a3 3 0 002.42-4.42M9.88 5.09A9.96 9.96 0 0112 5c4.477 0 8.268 2.943 9.542 7-.39 1.24-1.02 2.36-1.85 3.33M6.27 6.27C4.39 7.58 3.03 9.54 2.46 12c1.274 4.057 5.065 7 9.542 7a9.96 9.96 0 004.12-.87"/></svg>
                </button>
              </div>
            </div>

            <div class="flex items-center justify-between gap-3">
              <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-slate-600">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-700 shadow-sm focus:ring-blue-500" name="remember">
                <span>{{ __('Recordarme') }}</span>
              </label>

              @if (Route::has('password.request'))
                <a class="text-sm font-medium text-blue-700 transition hover:text-blue-800" href="{{ route('password.request') }}">
                  {{ __('Olvide mi contrasena') }}
                </a>
              @endif
            </div>

            <div class="pt-2">
              <x-primary-button class="w-full justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold shadow-lg shadow-slate-900/15 hover:bg-slate-800">
                {{ __('Iniciar sesion') }}
              </x-primary-button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- ===== MODAL: REGISTRO ===== -->
    <div x-cloak x-show="showRegister" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm" @click="showRegister=false"></div>

      <div x-transition class="modal-panel modal-card relative w-full max-w-6xl overflow-hidden">
        <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-5">
          <div><p class="section-kicker">Nuevo registro</p><h3 class="mt-2 text-xl font-bold text-slate-900">Crear cuenta</h3></div>
          <button class="rounded-xl p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700" @click="showRegister=false" aria-label="Cerrar">x</button>
        </div>

        <div class="max-h-[85vh] overflow-y-auto p-6">

          @php
            use App\Models\PreguntaSeguridad;
            $preguntasSeg = PreguntaSeguridad::where('ESTADO', 1)->orderBy('TEXTO_PREGUNTA')->get();
          @endphp

          {{-- FORM REGISTRO COMPLETO --}}
          <form method="POST" action="{{ route('register') }}" novalidate>
            @csrf

            {{-- Header: usuario autogenerado (discreto) --}}
            <div class="mb-5 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
              <div><p class="section-kicker">Seccion 1</p><h2 class="mt-2 text-lg font-bold text-slate-900">Datos personales</h2><p class="mt-1 text-sm text-slate-600">Ingresa tu informacion principal para crear tu perfil dentro del sistema.</p></div>
              <div id="username-pill" class="hidden rounded-2xl border border-blue-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm">
                <span class="block text-[11px] font-semibold uppercase tracking-[0.2em] text-blue-700">Usuario asignado</span>
                <code id="username-preview" class="font-semibold"></code>
              </div>
            </div>

            {{-- Nombres --}}
            <div class="grid gap-4 md:grid-cols-2">
              <div>
                <x-input-label for="PRIMER_NOMBRE" :value="__('Primer nombre')" />
                  <x-text-input id="PRIMER_NOMBRE" class="block mt-1 w-full" type="text" name="PRIMER_NOMBRE"
                                :value="old('PRIMER_NOMBRE')" required autofocus />
                <x-input-error :messages="$errors->register->get('PRIMER_NOMBRE')" class="mt-2" />
              </div>
              <div>
                <x-input-label for="SEGUNDO_NOMBRE" :value="__('Segundo nombre (opcional)')" />
                  <x-text-input id="SEGUNDO_NOMBRE" class="block mt-1 w-full" type="text" name="SEGUNDO_NOMBRE"
                                :value="old('SEGUNDO_NOMBRE')" />
                <x-input-error :messages="$errors->register->get('SEGUNDO_NOMBRE')" class="mt-2" />
              </div>
            </div>

            {{-- Apellidos --}}
            <div class="mt-4 grid gap-4 md:grid-cols-2">
              <div>
                <x-input-label for="PRIMER_APELLIDO" :value="__('Primer apellido')" />
                  <x-text-input id="PRIMER_APELLIDO" class="block mt-1 w-full" type="text" name="PRIMER_APELLIDO"
                                :value="old('PRIMER_APELLIDO')" required />
                <x-input-error :messages="$errors->register->get('PRIMER_APELLIDO')" class="mt-2" />
              </div>
              <div>
                <x-input-label for="SEGUNDO_APELLIDO" :value="__('Segundo apellido (opcional)')" />
                  <x-text-input id="SEGUNDO_APELLIDO" class="block mt-1 w-full" type="text" name="SEGUNDO_APELLIDO"
                                :value="old('SEGUNDO_APELLIDO')" />
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
                                :value="old('NUM_TELEFONO')" />
                <x-input-error :messages="$errors->register->get('NUM_TELEFONO')" class="mt-2" />
              </div>
            </div>

            {{-- Departamento + Municipio --}}
            <div class="mt-4 grid gap-4 md:grid-cols-2">
              <div>
                <x-input-label for="DEPARTAMENTO" :value="__('Departamento')" />
                  <x-text-input id="DEPARTAMENTO" class="block mt-1 w-full" type="text" name="DEPARTAMENTO"
                                :value="old('DEPARTAMENTO')" />
                <x-input-error :messages="$errors->register->get('DEPARTAMENTO')" class="mt-2" />
              </div>
              <div>
                <x-input-label for="MUNICIPIO" :value="__('Municipio')" />
                  <x-text-input id="MUNICIPIO" class="block mt-1 w-full" type="text" name="MUNICIPIO"
                                :value="old('MUNICIPIO')" />
                <x-input-error :messages="$errors->register->get('MUNICIPIO')" class="mt-2" />
              </div>
            </div>

            {{-- Ciudad + Colonia --}}
            <div class="mt-4 grid gap-4 md:grid-cols-2">
              <div>
                <x-input-label for="CIUDAD" :value="__('Ciudad')" />
                  <x-text-input id="CIUDAD" class="block mt-1 w-full" type="text" name="CIUDAD"
                                :value="old('CIUDAD')" />
                <x-input-error :messages="$errors->register->get('CIUDAD')" class="mt-2" />
              </div>
              <div>
                <x-input-label for="COLONIA" :value="__('Colonia')" />
                  <x-text-input id="COLONIA" class="block mt-1 w-full" type="text" name="COLONIA"
                                :value="old('COLONIA')" />
                <x-input-error :messages="$errors->register->get('COLONIA')" class="mt-2" />
              </div>
            </div>

            {{-- Dirección / Referencia --}}
            <div class="mt-4">
              <x-input-label for="REFERENCIA" :value="__('Dirección / Referencia')" />
                  <textarea id="REFERENCIA" name="REFERENCIA" rows="3"
                        class="mt-1 block w-full rounded-md border-slate-300 focus:border-blue-500 focus:ring-blue-500">{{ old('REFERENCIA') }}</textarea>
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
            <h3 class="text-sm font-semibold text-slate-700 mt-8 mb-2">Usuario asignado</h3>
            <div class="mb-3 text-xs text-slate-600">
              Este será tu usuario para iniciar sesión: <code class="font-semibold" id="username-preview-inline"></code>
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

    {{-- Script: vista previa del usuario autogenerado --}}
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
  </script>

</body>
</html>
