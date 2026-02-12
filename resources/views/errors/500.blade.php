<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 - Error interno</title>
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
</head>
<body class="hold-transition layout-top-nav">
<div class="wrapper">
    <div class="content-wrapper">
        <section class="content pt-5">
            <div class="container">
                <div class="error-page">
                    <h2 class="headline text-danger">500</h2>
                    <div class="error-content">
                        <h3><i class="fas fa-bug text-danger"></i> Error interno del sistema.</h3>
                        <p>Ocurrió un problema inesperado, posiblemente temporal de conexión. Intenta nuevamente en unos minutos.</p>
                        <a href="{{ route('dashboard') }}" class="btn btn-primary mr-2">Ir al inicio</a>
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary">Iniciar sesión</a>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
</body>
</html>
