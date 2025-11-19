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

        <style>
            .floating-gradient {
                position: absolute;
                border-radius: 9999px;
                filter: blur(70px);
                opacity: 0.45;
            }
            .animate-panel {
                animation: panelPop 420ms ease;
            }
            .animate-modal {
                animation: modalFloat 360ms ease;
            }
            @keyframes panelPop {
                0% { opacity: 0; transform: translateY(18px) scale(.98); }
                100% { opacity: 1; transform: translateY(0) scale(1); }
            }
            @keyframes modalFloat {
                0% { opacity: 0; transform: translateY(-8px) scale(.96); }
                100% { opacity: 1; transform: translateY(0) scale(1); }
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen relative overflow-hidden bg-gradient-to-br from-sky-50 via-white to-indigo-50">
            <div class="floating-gradient w-64 h-64 bg-sky-200 top-10 left-8"></div>
            <div class="floating-gradient w-72 h-72 bg-indigo-200 bottom-6 right-10"></div>
            <div class="floating-gradient w-52 h-52 bg-cyan-100 top-1/2 left-1/2"></div>

            <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
                <div class="grid gap-6 lg:grid-cols-5 items-stretch">
                    <div class="lg:col-span-2 bg-white/70 backdrop-blur border border-white/70 shadow-lg rounded-3xl p-6 md:p-8 flex flex-col justify-between animate-panel">
                        <div class="flex items-center justify-between mb-8">
                            <div class="flex items-center gap-3">
                                <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-sky-500 to-indigo-600 flex items-center justify-center text-white shadow-lg">
                                    <x-application-logo class="h-8 w-8 fill-current text-white" />
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Portal Clínico</p>
                                    <p class="text-lg font-semibold text-slate-800">Clinica López Molinari</p>
                                </div>
                            </div>
                            <div class="hidden md:flex flex-col items-end text-right text-xs text-slate-500">
                                <span class="font-semibold text-slate-700">Accesos seguros</span>
                                <span>UI enfocada en claridad y precisión</span>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <h1 class="text-xl font-semibold text-slate-800">Gestiona tu salud con confianza</h1>
                            <p class="text-sm leading-relaxed text-slate-600">
                                Accede a un diseño pensado para administradores, doctores y pacientes. Una experiencia ordenada, elegante y responsiva para cualquier dispositivo.
                            </p>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div class="p-3 rounded-2xl bg-sky-50 border border-sky-100 text-sky-800">Seguridad reforzada</div>
                                <div class="p-3 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-800">Interfaces limpias</div>
                            </div>
                        </div>
                        <div class="mt-6 flex items-center gap-3 text-xs text-slate-500">
                            <div class="h-10 w-10 rounded-full bg-slate-900/80 flex items-center justify-center text-white shadow">UI</div>
                            <div>
                                <p class="font-semibold text-slate-700">Actualizado para login y registro</p>
                                <p>Animaciones suaves y mensajes claros en cada modal.</p>
                            </div>
                        </div>
                    </div>
                    <div class="lg:col-span-3">
                        <div class="bg-white/90 backdrop-blur border border-white/80 shadow-2xl rounded-3xl p-6 md:p-8 animate-panel">
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
