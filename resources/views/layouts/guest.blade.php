<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Complejo Dental López Molinari</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Favicon -->
        <link rel="icon" type="image/avif" href="{{ asset('images/logo_clinica.avif') }}">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center py-6 sm:py-0 bg-slate-100">
            {{-- Logo de la clínica --}}
            <div class="mb-4">
                <a href="{{ url('/') }}">
                    <x-application-logo class="h-20 w-auto mx-auto" />
                </a>
            </div>

            {{-- Tarjeta de contenido (login, forgot, reset, etc.) --}}
            <div class="w-full sm:max-w-md px-6 py-6 bg-white shadow-xl border border-slate-100 rounded-2xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
