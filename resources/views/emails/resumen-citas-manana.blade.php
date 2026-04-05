@component('mail::message')
# Resumen de citas para manana

Fecha: **{{ $fecha_legible ?? $fecha ?? '' }}**

@isset($intro)
{{ $intro }}
@else
Este es el listado de citas programadas para manana.
@endisset

@component('mail::panel')
@forelse($citas as $cita)
- **Hora:** {{ $cita['hora'] ?? '' }}
- **Paciente:** {{ $cita['paciente'] ?? '' }}
- **Doctor(a):** {{ $cita['doctor'] ?? '' }}
- **Estado:** {{ $cita['estado'] ?? 'Pendiente' }}

---
@empty
No hay citas programadas para manana.
@endforelse
@endcomponent

Gracias por coordinar la agenda de la clinica,  
**{{ $clinica ?? config('app.name') }}**
@endcomponent
