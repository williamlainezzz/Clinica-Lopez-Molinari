<!DOCTYPE html>
<html lang="es" x-data="landingPage()" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bienvenido — Clínica Dental</title>

    {{-- TailwindCSS desde CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Alpine.js --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Favicon con el logo de la clínica --}}
    <link rel="icon" type="image/avif" href="{{ asset('images/logo_clinica.avif') }}">
</head>
<body class="h-full bg-slate-50 text-slate-900 antialiased">

    {{-- Contenedor principal --}}
    <div class="min-h-screen flex flex-col">

        {{-- Barra superior --}}
        <header class="w-full border-b border-slate-200 bg-white/80 backdrop-blur">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    {{-- Logo de la clínica en el header (más grande) --}}
                    <img
                        src="{{ asset('images/logo_clinica.avif') }}"
                        alt="Complejo Dental López Molinari"
                        class="h-12 w-12 rounded-full object-contain bg-white"
                    >
                    <div>
                        <p class="text-base sm:text-lg font-semibold text-slate-900 leading-tight">
                            Complejo Dental <span class="text-sky-800">López Molinari</span>
                        </p>
                        <p class="text-[11px] sm:text-xs text-slate-500">
                            Sistema de gestión clínica y agenda de citas
                        </p>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    @if (Route::has('login'))
                        @auth
                            {{-- Solo cuando YA está autenticado mostramos el acceso al panel --}}
                            <a href="{{ url('/dashboard') }}"
                               class="inline-flex items-center rounded-full border border-sky-700 px-4 py-2 text-sm font-semibold text-sky-800 hover:bg-sky-50 transition">
                                Ir al panel
                            </a>
                        @endauth
                    @endif
                </div>
            </div>
        </header>

        {{-- Sección de hero / portada --}}
        <main class="flex-1">
            <section class="relative overflow-hidden">
                {{-- Fondo suave en azules --}}
                <div class="absolute inset-0 bg-gradient-to-br from-sky-50 via-slate-50 to-slate-100 pointer-events-none"></div>

                <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-20 grid gap-12 lg:grid-cols-2 items-center">
                    {{-- Texto principal --}}
                    <div>
                        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 mb-4 leading-tight">
                            Gestión clínica <span class="text-sky-800">clara y rápida</span>
                        </h1>
                        <p class="text-base sm:text-lg text-slate-600 mb-6">
                            Administra citas, pacientes y agenda del <strong>Complejo Dental López Molinari</strong> desde un solo lugar.
                            Accede como paciente o como doctor de forma segura.
                        </p>

                        <div class="flex flex-wrap items-center gap-3 mb-6">
                            {{-- ÚNICOS botones principales para invitados --}}
                            <button type="button"
                                    @click="openLogin()"
                                    class="inline-flex items-center rounded-full bg-sky-800 px-6 py-2.5 text-sm font-semibold text-white shadow-md hover:bg-sky-900 transition">
                                Iniciar sesión
                            </button>

                            @if (Route::has('register'))
                                <button type="button"
                                        @click="openRegister()"
                                        class="inline-flex items-center rounded-full border border-sky-800 px-6 py-2.5 text-sm font-semibold text-sky-800 hover:bg-sky-50 transition">
                                    Registrarse
                                </button>
                            @endif
                        </div>

                        {{-- Mensajes sobre el sistema --}}
                        <ul class="space-y-2 text-sm text-slate-600">
                            <li class="flex items-start space-x-2">
                                <span class="mt-1 h-1.5 w-1.5 rounded-full bg-sky-700"></span>
                                <span>Plataforma para gestionar citas, pacientes y agenda clínica en un mismo sistema.</span>
                            </li>
                            <li class="flex items-start space-x-2">
                                <span class="mt-1 h-1.5 w-1.5 rounded-full bg-sky-700"></span>
                                <span>Acceso diferenciado para pacientes y doctores, con módulos específicos para cada uno.</span>
                            </li>
                            <li class="flex items-start space-x-2">
                                <span class="mt-1 h-1.5 w-1.5 rounded-full bg-sky-700"></span>
                                <span>Consulta de citas, horarios y recordatorios desde cualquier dispositivo con Internet.</span>
                            </li>
                        </ul>
                    </div>

                    {{-- Foto real de la clínica (más alta) --}}
                    <div class="relative">
                        <div class="relative h-64 sm:h-72 lg:h-80 rounded-3xl overflow-hidden bg-slate-200 shadow-xl shadow-sky-100/50 border border-slate-100">
                            <img
                                src="{{ asset('images/clinica_exterior.avif') }}"
                                alt="Fachada del Complejo Dental López Molinari"
                                class="w-full h-full object-cover"
                            >
                            {{-- Overlay con texto descriptivo --}}
                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-900/80 via-slate-900/20 to-transparent px-5 py-4">
                                <p class="text-xs font-semibold text-sky-100 uppercase tracking-wide">
                                    Sede principal
                                </p>
                                <p class="text-sm text-slate-50">
                                    Complejo Dental López Molinari — Tegucigalpa, Honduras.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        {{-- Pie de página --}}
        <footer class="border-t border-slate-200 bg-white">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-2">
                <span>© {{ date('Y') }} Complejo Dental López Molinari. Todos los derechos reservados.</span>
                <span>Sistema de gestión clínica desarrollado para uso académico y profesional.</span>
            </div>
        </footer>
    </div>

    {{-- ===================== MODAL LOGIN ===================== --}}
    <div
        x-cloak
        x-show="showLogin"
        x-transition.opacity
        class="fixed inset-0 z-40 flex items-center justify-center bg-slate-900/60 px-4 sm:px-6"
        aria-modal="true"
        role="dialog"
    >
        <div
            x-transition
            class="relative w-full max-w-md rounded-3xl bg-white shadow-2xl border border-slate-100 overflow-hidden"
        >
            {{-- Banner superior en azules --}}
            <div class="bg-gradient-to-r from-sky-800 via-sky-700 to-indigo-600 px-6 py-4">
                <p class="text-xs font-semibold tracking-wide text-sky-100 uppercase">Bienvenido</p>
                <h2 class="text-lg font-bold text-white">
                    Inicia sesión en tu cuenta
                </h2>
                <p class="mt-1 text-xs text-sky-100/90">
                    Accede a la gestión de citas del Complejo Dental López Molinari.
                </p>
            </div>

            {{-- Botón cerrar --}}
            <button
                type="button"
                @click="showLogin = false"
                class="absolute top-3 right-3 inline-flex h-8 w-8 items-center justify-center rounded-full bg-white/90 text-slate-500 shadow-sm hover:bg-slate-50 hover:text-slate-700 border border-slate-200"
            >
                <span class="sr-only">Cerrar</span>
                ✕
            </button>

            {{-- Contenido --}}
            <div class="px-6 pt-6 pb-5 space-y-5">
                {{-- Mensajes de estado / error --}}
                @if (session('status'))
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs text-emerald-700">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs text-rose-700">
                        <p class="font-semibold mb-1">Error al iniciar sesión</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-4" x-data="{ showPwd:false }">
                    @csrf

                    {{-- Usuario o correo --}}
                    <div>
                        <label for="login" class="block text-xs font-semibold text-slate-700 mb-1">
                            Usuario o correo
                        </label>
                        <input
                            id="login"
                            type="text"
                            name="login"
                            value="{{ old('login') }}"
                            autocomplete="username email"
                            required
                            class="block w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-600 focus:border-sky-600"
                        >
                    </div>

                    {{-- Contraseña --}}
                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">
                            Contraseña
                        </label>
                        <div class="relative">
                            <input
                                id="password"
                                name="password"
                                x-bind:type="showPwd ? 'text' : 'password'"
                                required
                                autocomplete="current-password"
                                class="block w-full rounded-xl border border-slate-300 px-3 py-2.5 pr-10 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-600 focus:border-sky-600"
                            >
                            <button
                                type="button"
                                @click="showPwd = !showPwd"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600"
                                tabindex="-1"
                            >
                                <span class="sr-only">Mostrar u ocultar contraseña</span>
                                👁
                            </button>
                        </div>
                    </div>

                    {{-- Recordarme + Olvidé contraseña --}}
                    <div class="flex items-center justify-between text-xs">
                        <label class="inline-flex items-center space-x-2 text-slate-600">
                            <input
                                type="checkbox"
                                name="remember"
                                class="rounded border-slate-300 text-sky-700 shadow-sm focus:ring-sky-600"
                            >
                            <span>Recordarme</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="font-medium text-sky-700 hover:text-sky-900">
                                ¿Olvidaste tu contraseña?
                            </a>
                        @endif
                    </div>

                    {{-- Botón --}}
                    <div class="pt-1">
                        <button
                            type="submit"
                            class="inline-flex w-full items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 transition"
                        >
                            INICIAR SESIÓN
                        </button>
                    </div>
                </form>

                {{-- Enlace a registro --}}
                @if (Route::has('register'))
                    <p class="text-[11px] text-center text-slate-500">
                        ¿Aún no tienes cuenta?
                        <button type="button"
                                @click="switchToRegister()"
                                class="font-semibold text-sky-700 hover:text-sky-900">
                            Regístrate aquí
                        </button>
                    </p>
                @endif
            </div>
        </div>
    </div>

    {{-- ===================== MODAL REGISTRO ===================== --}}
    <div
        x-cloak
        x-show="showRegister"
        x-transition.opacity
        class="fixed inset-0 z-40 flex items-center justify-center bg-slate-900/60 px-4 sm:px-6"
        aria-modal="true"
        role="dialog"
    >
        <div
            x-transition
            class="relative w-full max-w-4xl rounded-3xl bg-white shadow-2xl border border-slate-100 overflow-hidden"
        >
            {{-- Banner superior en azules --}}
            <div class="bg-gradient-to-r from-indigo-600 via-sky-700 to-sky-500 px-6 py-4">
                <p class="text-xs font-semibold tracking-wide text-sky-100 uppercase">Registro de paciente</p>
                <h2 class="text-lg font-bold text-white">
                    Crea tu cuenta en el Complejo Dental López Molinari
                </h2>
                <p class="mt-1 text-xs text-sky-50/95">
                    Completa tus datos para agendar y gestionar tus citas en línea.
                </p>
            </div>

            {{-- Botón cerrar --}}
            <button
                type="button"
                @click="showRegister = false"
                class="absolute top-3 right-3 inline-flex h-8 w-8 items-center justify-center rounded-full bg-white/90 text-slate-500 shadow-sm hover:bg-slate-50 hover:text-slate-700 border border-slate-200"
            >
                <span class="sr-only">Cerrar</span>
                ✕
            </button>

            <div class="px-6 pt-6 pb-6 max-h-[85vh] overflow-y-auto text-sm">
                {{-- Errores --}}
                @if ($errors->any())
                    <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs text-rose-700">
                        <p class="font-semibold mb-1">Revisa la información ingresada</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @php
                    use App\Models\PreguntaSeguridad;
                    $preguntasSeg = PreguntaSeguridad::where('ESTADO', 1)
                        ->orderBy('TEXTO_PREGUNTA')
                        ->get();
                @endphp

                <form
                    method="POST"
                    action="{{ route('register') }}"
                    x-data="registroPaciente()"
                    @submit="beforeSubmit"
                    class="space-y-6"
                >
                    @csrf

                    {{-- Hidden para nombres/apellidos que espera el backend --}}
                    <input type="hidden" name="PRIMER_NOMBRE" x-model="primerNombre">
                    <input type="hidden" name="SEGUNDO_NOMBRE" x-model="segundoNombre">
                    <input type="hidden" name="PRIMER_APELLIDO" x-model="primerApellido">
                    <input type="hidden" name="SEGUNDO_APELLIDO" x-model="segundoApellido">

                    {{-- MUNICIPIO / COLONIA vacíos si el backend los requiere --}}
                    <input type="hidden" name="MUNICIPIO" value="">
                    <input type="hidden" name="COLONIA" value="">

                    {{-- ====== Bloque 1: Datos personales ====== --}}
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-3">
                            Datos personales
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Nombres
                                </label>
                                <input
                                    type="text"
                                    x-model="nombresCompletos"
                                    @input="syncNames()"
                                    class="block w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-600 focus:border-sky-600"
                                    required
                                >
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Apellidos
                                </label>
                                <input
                                    type="text"
                                    x-model="apellidosCompletos"
                                    @input="syncNames()"
                                    class="block w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-600 focus:border-sky-600"
                                    required
                                >
                            </div>
                            <div>
                                <label for="TIPO_GENERO" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Género
                                </label>
                                <select
                                    id="TIPO_GENERO"
                                    name="TIPO_GENERO"
                                    class="block w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-600 focus:border-sky-600"
                                    required
                                >
                                    <option value="">Seleccione...</option>
                                    <option value="1">Masculino</option>
                                    <option value="2">Femenino</option>
                                    <option value="3">Prefiero no decirlo</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        Teléfono
                                    </label>
                                    <input
                                        type="text"
                                        name="NUM_TELEFONO"
                                        class="block w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-600 focus:border-sky-600"
                                        required
                                    >
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        Tipo de teléfono
                                    </label>
                                    <select
                                        name="TIPO_TELEFONO"
                                        class="block w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-600 focus:border-sky-600"
                                    >
                                        <option value="Móvil">Móvil</option>
                                        <option value="Casa">Casa</option>
                                        <option value="Trabajo">Trabajo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ====== Bloque 2: Dirección ====== --}}
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-3">
                            Dirección
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Departamento
                                </label>
                                <select
                                    name="DEPARTAMENTO"
                                    x-model="departamento"
                                    @change="updateCiudades()"
                                    class="block w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-600 focus:border-sky-600"
                                    required
                                >
                                    <option value="">Seleccione...</option>
                                    <template x-for="(ciudades, dep) in departamentos" :key="dep">
                                        <option :value="dep" x-text="dep"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Ciudad
                                </label>
                                <select
                                    name="CIUDAD"
                                    x-model="ciudad"
                                    class="block w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-600 focus:border-sky-600"
                                    required
                                >
                                    <option value="">Seleccione un departamento primero</option>
                                    <template x-for="c in ciudadesDisponibles" :key="c">
                                        <option :value="c" x-text="c"></option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Dirección / Referencia
                            </label>
                            <textarea
                                name="REFERENCIA"
                                rows="2"
                                class="block w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-600 focus:border-sky-600 resize-y"
                            ></textarea>
                        </div>
                    </div>

                    {{-- ====== Bloque 3: Contacto y seguridad ====== --}}
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-3">
                            Contacto y recuperación
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Correo electrónico
                                </label>
                                <input
                                    type="email"
                                    name="CORREO"
                                    class="block w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-600 focus:border-sky-600"
                                    required
                                >
                            </div>
                        </div>

                        {{-- Preguntas de seguridad --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Pregunta 1
                                </label>
                                <select
                                    name="PREGUNTA1"
                                    class="block w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-600 focus:border-sky-600"
                                    required
                                >
                                    <option value="">Seleccione...</option>
                                    @foreach($preguntasSeg as $pregunta)
                                        <option value="{{ $pregunta->COD_PREGUNTA }}">
                                            {{ $pregunta->TEXTO_PREGUNTA }}
                                        </option>
                                    @endforeach
                                </select>

                                <label class="block text-xs font-semibold text-slate-700 mt-2 mb-1">
                                    Respuesta a la pregunta 1
                                </label>
                                <input
                                    type="text"
                                    name="RESPUESTA1"
                                    class="block w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-600 focus:border-sky-600"
                                    required
                                >
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Pregunta 2
                                </label>
                                <select
                                    name="PREGUNTA2"
                                    class="block w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-600 focus:border-sky-600"
                                    required
                                >
                                    <option value="">Seleccione...</option>
                                    @foreach($preguntasSeg as $pregunta)
                                        <option value="{{ $pregunta->COD_PREGUNTA }}">
                                            {{ $pregunta->TEXTO_PREGUNTA }}
                                        </option>
                                    @endforeach
                                </select>

                                <label class="block text-xs font-semibold text-slate-700 mt-2 mb-1">
                                    Respuesta a la pregunta 2
                                </label>
                                <input
                                    type="text"
                                    name="RESPUESTA2"
                                    class="block w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-600 focus:border-sky-600"
                                    required
                                >
                            </div>
                        </div>
                    </div>

                    {{-- ====== Bloque 4: Usuario asignado y contraseña ====== --}}
                    <div class="space-y-4">
                        <div class="rounded-2xl border border-sky-100 bg-sky-50 px-4 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div>
                                <p class="text-xs font-semibold text-sky-800">
                                    Usuario asignado
                                </p>
                            <p class="text-[11px] text-sky-700">
                                    Este será el usuario con el que iniciarás sesión en el sistema.
                                </p>
                            </div>
                            <div class="px-3 py-1 rounded-full bg-white border border-sky-200 text-xs font-mono font-semibold text-sky-800">
                                <span class="text-slate-500 mr-1">Usuario:</span>
                                <span id="username-preview" x-text="usuarioGenerado || 'pendiente...'"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-start">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Contraseña
                                </label>
                                <div class="relative">
                                    <input
                                        id="password_reg"
                                        type="password"
                                        name="password"
                                        x-model="password"
                                        @input="validatePassword()"
                                        class="block w-full rounded-xl border border-slate-300 px-3 py-2.5 pr-10 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-600 focus:border-sky-600"
                                        required
                                    >
                                    <button
                                        type="button"
                                        @click="togglePwd('password_reg')"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600"
                                        tabindex="-1"
                                    >
                                        👁
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Confirmar contraseña
                                </label>
                                <div class="relative">
                                    <input
                                        id="password_conf"
                                        type="password"
                                        name="password_confirmation"
                                        x-model="passwordConfirm"
                                        @input="validatePassword()"
                                        class="block w-full rounded-xl border border-slate-300 px-3 py-2.5 pr-10 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-600 focus:border-sky-600"
                                        required
                                    >
                                    <button
                                        type="button"
                                        @click="togglePwd('password_conf')"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600"
                                        tabindex="-1"
                                    >
                                        👁
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Reglas de contraseña --}}
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                            <p class="text-xs font-semibold text-slate-700 mb-1">
                                La contraseña debe cumplir con:
                            </p>
                            <ul class="text-[11px] space-y-1">
                                <li :class="rules.length ? 'text-emerald-700' : 'text-rose-600'">
                                    • Mínimo 10 caracteres
                                </li>
                                <li :class="rules.case ? 'text-emerald-700' : 'text-rose-600'">
                                    • Mayúsculas y minúsculas
                                </li>
                                <li :class="rules.number ? 'text-emerald-700' : 'text-rose-600'">
                                    • Al menos un número
                                </li>
                                <li :class="rules.symbol ? 'text-emerald-700' : 'text-rose-600'">
                                    • Al menos un símbolo
                                </li>
                                <li :class="rules.match ? 'text-emerald-700' : 'text-rose-600'">
                                    • Las contraseñas deben coincidir
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="pt-2 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <button
                            type="submit"
                            class="inline-flex w-full sm:w-auto items-center justify-center rounded-xl bg-slate-900 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 transition"
                        >
                            REGISTRARME
                        </button>

                        <p class="text-[11px] text-slate-500 text-center sm:text-right">
                            ¿Ya tienes una cuenta?
                            <button
                                type="button"
                                @click="switchToLogin()"
                                class="font-semibold text-sky-700 hover:text-sky-900"
                            >
                                Inicia sesión aquí
                            </button>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ===================== SCRIPTS AUXILIARES ===================== --}}
    <script>
        function landingPage() {
            return {
                showLogin: false,
                showRegister: false,

                openLogin() {
                    this.showRegister = false;
                    this.showLogin = true;
                },
                openRegister() {
                    this.showLogin = false;
                    this.showRegister = true;
                },
                switchToRegister() {
                    this.showLogin = false;
                    this.showRegister = true;
                },
                switchToLogin() {
                    this.showRegister = false;
                    this.showLogin = true;
                }
            };
        }

        function registroPaciente() {
            return {
                // Nombres
                nombresCompletos: '',
                apellidosCompletos: '',
                primerNombre: '',
                segundoNombre: '',
                primerApellido: '',
                segundoApellido: '',

                // Usuario generado
                usuarioGenerado: '',

                // Dirección
                departamentos: {
                    'Atlántida': ['La Ceiba', 'Tela', 'El Porvenir'],
                    'Choluteca': ['Choluteca', 'San Marcos de Colón'],
                    'Colón': ['Trujillo', 'Tocoa'],
                    'Comayagua': ['Comayagua', 'Siguatepeque', 'La Libertad'],
                    'Copán': ['Santa Rosa de Copán', 'Copán Ruinas'],
                    'Cortés': ['San Pedro Sula', 'Puerto Cortés', 'Choloma', 'Villanueva'],
                    'El Paraíso': ['Yuscarán', 'Danlí'],
                    'Francisco Morazán': ['Tegucigalpa', 'Comayagüela', 'Valle de Ángeles'],
                    'Gracias a Dios': ['Puerto Lempira'],
                    'Intibucá': ['La Esperanza', 'Intibucá'],
                    'Islas de la Bahía': ['Roatán', 'Coxen Hole'],
                    'La Paz': ['La Paz', 'Marcala'],
                    'Lempira': ['Gracias', 'La Campa'],
                    'Ocotepeque': ['Nueva Ocotepeque'],
                    'Olancho': ['Juticalpa', 'Catacamas'],
                    'Santa Bárbara': ['Santa Bárbara', 'Ilama'],
                    'Valle': ['Nacaome', 'San Lorenzo'],
                    'Yoro': ['Yoro', 'El Progreso', 'Olanchito']
                },
                departamento: '',
                ciudad: '',
                ciudadesDisponibles: [],

                // Password
                password: '',
                passwordConfirm: '',
                rules: {
                    length: false,
                    case: false,
                    number: false,
                    symbol: false,
                    match: false,
                },

                syncNames() {
                    const clean = (str) => (str || '').trim().replace(/\s+/g, ' ');
                    const nombres = clean(this.nombresCompletos).split(' ');
                    const apellidos = clean(this.apellidosCompletos).split(' ');

                    this.primerNombre = nombres[0] || '';
                    this.segundoNombre = nombres.slice(1).join(' ') || '';

                    this.primerApellido = apellidos[0] || '';
                    this.segundoApellido = apellidos.slice(1).join(' ') || '';

                    this.generateUsername();
                },

                generateUsername() {
                    const removeAccents = (str) => str.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
                    const pn = removeAccents((this.primerNombre || '').toLowerCase());
                    const pa = removeAccents((this.primerApellido || '').toLowerCase());

                    if (!pn && !pa) {
                        this.usuarioGenerado = '';
                        return;
                    }

                    let base = pn && pa ? `${pn}.${pa}` : (pn || pa);
                    base = base.replace(/[^a-z0-9.]/g, '');

                    this.usuarioGenerado = base;
                    const preview = document.getElementById('username-preview');
                    if (preview) preview.textContent = this.usuarioGenerado;
                },

                updateCiudades() {
                    this.ciudadesDisponibles = this.departamentos[this.departamento] || [];
                    if (!this.ciudadesDisponibles.includes(this.ciudad)) {
                        this.ciudad = '';
                    }
                },

                validatePassword() {
                    const pwd = this.password || '';
                    const conf = this.passwordConfirm || '';

                    this.rules.length = pwd.length >= 10;
                    this.rules.case = /[a-z]/.test(pwd) && /[A-Z]/.test(pwd);
                    this.rules.number = /[0-9]/.test(pwd);
                    this.rules.symbol = /[^A-Za-z0-9]/.test(pwd);
                    this.rules.match = pwd.length > 0 && pwd === conf;
                },

                togglePwd(id) {
                    const el = document.getElementById(id);
                    if (!el) return;
                    el.type = el.type === 'password' ? 'text' : 'password';
                },

                beforeSubmit() {
                    // Aseguramos sincronización final
                    this.syncNames();
                    this.validatePassword();
                }
            };
        }
    </script>
</body>
</html>
