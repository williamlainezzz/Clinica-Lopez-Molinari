@component('mail::message')
# {{ $titulo ?? 'Recordatorio de su cita' }}

@isset($saludo)
{{ $saludo }}
@endisset

@component('mail::panel')
- **Paciente:** {{ $paciente ?? 'Paciente' }}
- **Doctor(a):** {{ $doctor ?? 'Doctor' }}
- **Clínica:** {{ $clinica ?? 'Clínica Dental' }}
- **Fecha:** {{ $fecha ?? '' }}
- **Hora:** {{ $hora ?? '' }}
- **Tipo de notificación:** {{ $tipo_legible ?? $tipo ?? 'Cita' }}
@endcomponent

{{ $mensaje ?? 'Le esperamos en la clínica para su cita programada.' }}

@isset($nota)
> {{ $nota }}
@endisset

@component('mail::button', ['url' => $url ?? url('/')])
Ver detalles
@endcomponent

Gracias por confiar en nosotros,
{{ $clinica ?? config('app.name') }}
@endcomponent
