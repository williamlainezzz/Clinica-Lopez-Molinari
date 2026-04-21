<x-guest-layout>
    @php
        $estadoConfig = [
            'pendiente' => [
                'badge' => 'Pendiente de confirmacion',
                'title' => 'Confirme su asistencia',
                'message' => 'Revise los datos de su cita y confirme si podra asistir en la fecha programada.',
                'tone' => 'bg-amber-50 text-amber-800 border-amber-200',
            ],
            'confirmada' => [
                'badge' => 'Cita confirmada',
                'title' => 'Su asistencia fue confirmada',
                'message' => 'Gracias. Hemos notificado al doctor y su cita ya aparece como confirmada en la agenda.',
                'tone' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
            ],
            'no_disponible' => [
                'badge' => 'No disponible',
                'title' => 'Esta cita no puede confirmarse',
                'message' => 'La cita ya fue cancelada, completada o marcada como no disponible. Si necesita ayuda, comuniquese con la clinica.',
                'tone' => 'bg-slate-100 text-slate-700 border-slate-200',
            ],
            'vencida' => [
                'badge' => 'Enlace vencido',
                'title' => 'La cita ya no puede confirmarse por este medio',
                'message' => 'La fecha u hora de la cita ya paso. Para recibir asistencia, comuniquese directamente con la clinica.',
                'tone' => 'bg-slate-100 text-slate-700 border-slate-200',
            ],
            'error' => [
                'badge' => 'No se pudo confirmar',
                'title' => 'Ocurrio un inconveniente',
                'message' => 'No fue posible registrar la confirmacion en este momento. Intente nuevamente o comuniquese con la clinica.',
                'tone' => 'bg-rose-50 text-rose-800 border-rose-200',
            ],
        ];

        $config = $estadoConfig[$estado] ?? $estadoConfig['error'];
        $hora = $cita->HOR_CITA ? \Illuminate\Support\Str::of($cita->HOR_CITA)->substr(0, 5) : '';
        $horaFin = $cita->HOR_FIN ? \Illuminate\Support\Str::of($cita->HOR_FIN)->substr(0, 5) : null;
    @endphp

    <div class="space-y-5">
        <div class="rounded-xl border px-3 py-2 text-xs font-semibold {{ $config['tone'] }}">
            {{ $config['badge'] }}
        </div>

        <div>
            <h1 class="text-2xl font-semibold text-slate-900">{{ $config['title'] }}</h1>
            <p class="mt-2 text-sm leading-6 text-slate-600">{{ $config['message'] }}</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700">
            <dl class="space-y-3">
                <div>
                    <dt class="font-semibold text-slate-900">Paciente</dt>
                    <dd>{{ $cita->paciente_nombre }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-slate-900">Doctor(a)</dt>
                    <dd>{{ $cita->doctor_nombre }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-slate-900">Fecha y hora</dt>
                    <dd>
                        {{ \Carbon\Carbon::parse($cita->FEC_CITA)->translatedFormat('d/m/Y') }}
                        {{ $hora }}@if($horaFin) - {{ $horaFin }}@endif
                    </dd>
                </div>
                <div>
                    <dt class="font-semibold text-slate-900">Motivo</dt>
                    <dd>{{ $cita->MOT_CITA ?: 'Consulta programada' }}</dd>
                </div>
            </dl>
        </div>

        @if($estado === 'pendiente' && $confirmUrl)
            <form method="POST" action="{{ $confirmUrl }}" class="space-y-3">
                @csrf
                <button type="submit" class="auth-action w-full">
                    Si, confirmo mi asistencia
                </button>
                <p class="text-center text-xs leading-5 text-slate-500">
                    Esta accion actualizara el estado de la cita y notificara al doctor.
                </p>
            </form>
        @else
            <a href="{{ route('welcome') }}" class="auth-action w-full">
                Volver al inicio
            </a>
        @endif
    </div>
</x-guest-layout>
