<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bienvenido - Clinica Dental</title>
  <link rel="icon" href="{{ asset('favicon.ico') }}">
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <style>[x-cloak]{display:none!important}</style>

  <style type="text/tailwindcss">
    @layer components {
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

  <header class="mx-auto max-w-7xl px-4 py-3 sm:px-6 lg:px-8">
    <div class="rounded-[28px] border border-white/80 bg-white/75 px-4 py-3 shadow-lg shadow-slate-900/5 backdrop-blur sm:rounded-full sm:px-5">
      <div class="flex items-center justify-between gap-3">
        <div class="flex min-w-0 items-center gap-3">
          <img src="{{ asset('images/logo-icon.jpg') }}" class="h-11 w-11 rounded-full object-cover ring-4 ring-blue-50" alt="Logo">
          <div class="min-w-0">
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-blue-700">Sistema Clinico</p>
            <p class="text-sm font-semibold leading-tight text-slate-900 sm:text-lg">Complejo Dental <span class="text-slate-500">Lopez Molinari</span></p>
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

      <div class="mt-3 grid grid-cols-2 gap-2 md:hidden">
        <button type="button" data-open="login" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-3 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">
          Iniciar sesion
        </button>
        <button type="button" data-open="register" class="inline-flex items-center justify-center rounded-full bg-blue-700 px-3 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-700/20 transition hover:bg-blue-800">
          Crear cuenta
        </button>
      </div>
    </div>
  </header>

  <main class="mx-auto max-w-7xl px-6 pb-3 pt-3 lg:px-8">
    @if (session('status'))
      <div class="mb-4 rounded-[22px] border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-800 shadow-sm">
        {{ session('status') }}
      </div>
    @endif

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
    <template x-if="showRegisterSuccess">
      @include('welcome.partials.register-success-modal')
    </template>

    <template x-if="showLogin">
      @include('welcome.partials.login-modal')
    </template>

    <template x-if="showRegister">
      @include('welcome.partials.register-modal', ['preguntasSeg' => $preguntasSeg])
    </template>
  </div>

  @include('welcome.partials.scripts')
</body>
</html>
