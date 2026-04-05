@component('mail::message')
# Tu contrasena esta proxima a vencer

Hola,

Queremos recordarte que la contrasena de tu cuenta en **{{ $appName }}** debe renovarse pronto.

@component('mail::panel')
- **Usuario:** {{ $username }}
- **Tiempo recomendado para cambiarla:** antes de {{ $days }} dias
@endcomponent

Actualizala con tiempo para evitar bloqueos o interrupciones en tu acceso.

@component('mail::button', ['url' => $profileUrl])
Cambiar contrasena
@endcomponent

Si no puedes ingresar o detectas algo inusual, comunicate con soporte.

Gracias,  
**{{ $appName }}**
@endcomponent
