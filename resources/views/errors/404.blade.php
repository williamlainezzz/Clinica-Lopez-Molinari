<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>404 - Página no encontrada</title>

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
                        <h2 class="headline text-warning">404</h2>

                        <div class="error-content">
                            <h3>Página no encontrada.</h3>
                            <p>
                                No fue posible localizar el recurso solicitado. Verifica la dirección o vuelve a una opción segura.
                            </p>

                            <div class="mt-3">
                                <a href="{{ url()->previous() }}" class="btn btn-outline-primary mr-2">Volver</a>
                                <a href="{{ route('welcome') }}" class="btn btn-primary">Ir al inicio</a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@else
    <main class="mx-auto max-w-2xl px-6 py-20">
        <div class="rounded-2xl bg-white/80 shadow-sm ring-1 ring-slate-200 p-8 text-center">
            <p class="text-5xl font-black text-sky-700">404</p>
            <h1 class="mt-2 text-2xl font-semibold">Página no encontrada</h1>
            <p class="mt-2 text-slate-600">
                No pudimos encontrar el recurso solicitado. Revisa la dirección o vuelve al inicio.
            </p>

            <div class="mt-6 flex flex-wrap justify-center gap-3">
                <a href="{{ url()->previous() }}"
                   class="rounded-xl border border-slate-300 px-5 py-2 text-slate-700 hover:bg-slate-50">
                    Volver
                </a>
                <a href="{{ route('welcome') }}"
                   class="rounded-xl bg-sky-600 px-5 py-2 text-white hover:bg-sky-700">
                    Volver al inicio
                </a>
                <a href="{{ url('/?modal=login') }}"
                   class="rounded-xl border border-slate-300 px-5 py-2 text-slate-700 hover:bg-slate-50">
                    Iniciar sesión
                </a>
            </div>
        </div>
    </main>
@endauth

</body>
</html>