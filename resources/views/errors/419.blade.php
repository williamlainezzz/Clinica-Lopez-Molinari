<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>419 - Sesión expirada</title>
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
</head>
<body class="hold-transition layout-top-nav">
<div class="wrapper">
    <div class="content-wrapper">
        <section class="content pt-5">
            <div class="container">
                <div class="error-page">
                    <h2 class="headline text-info">419</h2>
                    <div class="error-content">
                        <h3><i class="fas fa-clock text-info"></i> Sesión expirada.</h3>
                        <p>Tu sesión o token de seguridad expiró. Por favor vuelve a iniciar sesión y repite la operación.</p>
                        <a href="{{ route('login') }}" class="btn btn-primary mr-2">Iniciar sesión</a>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Volver al inicio</a>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
</body>
</html>
