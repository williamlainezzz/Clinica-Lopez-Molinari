<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    @php
        $isWideAuthRoute = request()->routeIs('register') || request()->routeIs('registro.paciente');
    @endphp
    <body class="font-sans antialiased {{ $isWideAuthRoute ? 'register-shell' : 'text-gray-900' }}">
        <div class="{{ $isWideAuthRoute ? 'min-h-screen' : 'auth-shell' }}">
            @if ($isWideAuthRoute)
                <div class="mx-auto min-h-screen max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
                    <div class="mb-5 flex items-center justify-end gap-4">
                        <a href="{{ route('welcome') }}" class="inline-flex items-center gap-2 rounded-full border border-blue-200/80 bg-white/95 px-4 py-2 text-sm font-semibold text-blue-900 shadow-[0_8px_20px_rgba(18,58,133,0.10)] transition hover:border-blue-300 hover:bg-blue-50/80">
                            Volver al inicio
                        </a>
                    </div>

                    {{ $slot }}
                </div>
            @else
                <div class="mx-auto flex min-h-screen max-w-md flex-col items-center justify-center px-4 py-10">
                    <a href="/" class="mb-6 inline-flex justify-center">
                        <x-application-logo class="w-auto" />
                    </a>

                    <div class="auth-card mx-auto max-w-[420px] px-5 py-5 sm:px-6">
                        {{ $slot }}
                    </div>
                </div>
            @endif
        </div>
    </body>
</html>
