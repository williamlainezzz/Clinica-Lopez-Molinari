<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bienvenido — Clínica Dental</title>
  <link rel="icon" href="{{ asset('favicon.ico') }}">
  <!-- Tailwind por CDN: perfecto para prototipo, no requiere build -->
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-sky-50 via-white to-teal-50 relative text-slate-800">
  <!-- decoraciones suaves -->
  <div class="absolute inset-0 -z-10 overflow-hidden pointer-events-none">
    <div class="absolute -top-24 -left-24 h-96 w-96 rounded-full bg-sky-200 blur-3xl opacity-40"></div>
    <div class="absolute -bottom-24 -right-24 h-96 w-96 rounded-full bg-teal-200 blur-3xl opacity-40"></div>
  </div>

  <!-- header -->
  <header class="max-w-7xl mx-auto px-6 py-6 flex items-center justify-between">
  <div class="flex items-center gap-3">
    <img src="{{ asset('images/logo_clinica.avif') }}" class="h-10 w-10 rounded-full shadow" alt="Logo">
    <span class="text-xl font-semibold">
      Complejo Dental <span class="text-slate-500">López Molinari</span>
    </span>
  </div>
  {{-- nav superior eliminado a petición --}}
</header>


  <!-- hero -->
  <main class="max-w-7xl mx-auto px-6 py-8 grid lg:grid-cols-2 gap-10 items-center">
    <section>
      <h1 class="text-4xl font-bold leading-tight">
        Gestión clínica simple, clara y rápida
      </h1>
      <p class="mt-4 text-slate-600">
        Agenda de citas, pacientes, disponibilidad de doctores, notificaciones y reportes — todo en un solo lugar.
      </p>

      <div class="mt-8 flex flex-wrap gap-3">
        @auth
          <a href="{{ route('dashboard') }}" class="px-6 py-3 rounded-xl bg-sky-600 text-white hover:bg-sky-700 shadow">
            Entrar al panel
          </a>
        @else
          @if (Route::has('login'))
            <a href="{{ route('login') }}" class="px-6 py-3 rounded-xl bg-sky-600 text-white hover:bg-sky-700 shadow">
              Iniciar sesión
            </a>
          @endif
          @if (Route::has('register'))
            <a href="{{ route('register') }}" class="px-6 py-3 rounded-xl bg-white border border-slate-300 hover:bg-slate-50">
              Registrarse
            </a>
          @endif
        @endauth
      </div>

      <div class="mt-10 grid grid-cols-2 sm:grid-cols-3 gap-4">
        <div class="p-4 bg-white/70 backdrop-blur rounded-xl shadow-sm border">
          <div class="text-sm text-slate-500">Agenda</div>
          <div class="font-semibold">Citas & Disponibilidad</div>
        </div>
        <div class="p-4 bg-white/70 backdrop-blur rounded-xl shadow-sm border">
          <div class="text-sm text-slate-500">Usuarios</div>
          <div class="font-semibold">Doctores & Pacientes</div>
        </div>
        <div class="p-4 bg-white/70 backdrop-blur rounded-xl shadow-sm border">
          <div class="text-sm text-slate-500">Seguridad</div>
          <div class="font-semibold">Permisos</div>
        </div>
      </div>
    </section>

    <!-- tarjetón ilustrativo -->
    <section>
      <div class="rounded-2xl bg-white/80 backdrop-blur p-6 shadow border">
        <img
          src="https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?q=80&w=1200&auto=format&fit=crop"
          alt="Ilustración clínica"
          class="rounded-xl w-full h-72 object-cover">
        <div class="mt-4 grid grid-cols-3 gap-3">
          <div class="p-3 rounded-lg bg-sky-50 text-sky-700 text-sm">Recordatorios de cita</div>
          <div class="p-3 rounded-lg bg-teal-50 text-teal-700 text-sm">Reportes listos</div>
          <div class="p-3 rounded-lg bg-amber-50 text-amber-700 text-sm">Diseño responsivo</div>
        </div>
      </div>
    </section>
  </main>

  <footer class="text-center text-slate-500 text-sm py-8">
    © {{ date('Y') }} Complejo Dental López Molinari — Prototipo
  </footer>
</body>
</html>
