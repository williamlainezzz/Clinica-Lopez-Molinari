@component('mail::message')
# {{ $titulo ?? 'Recordatorio de su cita' }}

@isset($saludo)
{{ $saludo }}
@else
Hola,
@endisset

@component('mail::panel')
- **Paciente:** {{ $paciente ?? 'Paciente' }}
- **Doctor(a):** {{ $doctor ?? 'Doctor' }}
- **Clinica:** {{ $clinica ?? 'Clinica Dental' }}
- **Fecha:** {{ $fecha ?? '' }}
- **Hora:** {{ $hora ?? '' }}
- **Tipo de notificacion:** {{ $tipo_legible ?? $tipo ?? 'Cita' }}
@endcomponent

{{ $mensaje ?? 'Le esperamos en la clinica para su cita programada.' }}

@isset($nota)
> {{ $nota }}
@endisset

@component('mail::button', ['url' => $url ?? url('/')])
Ver detalles
@endcomponent

Gracias por confiar en nosotros,  
**{{ $clinica ?? config('app.name') }}**
@endcomponent
