<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 - Error interno</title>
    @auth
        <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endauth
</head>
<body class="@auth hold-transition layout-top-nav @else min-h-screen bg-gradient-to-br from-sky-50 via-white to-teal-50 text-slate-800 @endauth">
@auth
<div class="wrapper">
    <div class="content-wrapper">
        <section class="content pt-5">
            <div class="container">
                <div class="error-page">
                    <h2 class="headline text-danger">500</h2>
                    <div class="error-content">
                        <h3><i class="fas fa-bug text-danger"></i> Error interno del sistema.</h3>
                        <p>Ocurrió un problema inesperado, posiblemente temporal de conexión. Intenta nuevamente en unos minutos.</p>
                        <a href="{{ route('welcome') }}" class="btn btn-primary mr-2">Volver al inicio</a>
                        <a href="{{ url('/?modal=login') }}" class="btn btn-outline-secondary">Iniciar sesión</a>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@else
<main class="mx-auto max-w-2xl px-6 py-20">
    <div class="rounded-2xl bg-white/80 shadow-sm ring-1 ring-slate-200 p-8 text-center">
        <p class="text-5xl font-black text-rose-700">500</p>
        <h1 class="mt-2 text-2xl font-semibold">Error interno del sistema</h1>
        <p class="mt-2 text-slate-600">Estamos presentando un inconveniente temporal. Por favor inténtalo nuevamente en unos minutos.</p>
        <div class="mt-6 flex flex-wrap justify-center gap-3">
            <a href="{{ route('welcome') }}" class="rounded-xl bg-sky-600 px-5 py-2 text-white hover:bg-sky-700">Volver al inicio</a>
            <a href="{{ url('/?modal=login') }}" class="rounded-xl border border-slate-300 px-5 py-2 text-slate-700 hover:bg-slate-50">Iniciar sesión</a>
        </div>
    </div>
</main>
@endauth
</body>
</html>
