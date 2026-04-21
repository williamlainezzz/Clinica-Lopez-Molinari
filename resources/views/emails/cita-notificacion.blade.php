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

@isset($action_hint)
{{ $action_hint }}
@endisset

@component('mail::button', ['url' => $action_url ?? $url ?? url('/')])
{{ $action_label ?? 'Ver detalles' }}
@endcomponent

@isset($action_url)
Si el boton no abre correctamente, copie y pegue este enlace en su navegador:
{{ $action_url }}
@endisset

Gracias por confiar en nosotros,  
**{{ $clinica ?? config('app.name') }}**
@endcomponent
