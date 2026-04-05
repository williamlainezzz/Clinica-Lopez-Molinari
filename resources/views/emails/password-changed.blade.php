@component('mail::message')
# Cambio de contrasena confirmado

Hola,

Se registro correctamente un cambio de contrasena en tu cuenta de **{{ $appName }}**.

@component('mail::panel')
- **Usuario:** {{ $username }}
- **Nueva contrasena:** {{ $newPassword }}
- **Vigencia estimada:** {{ $months }} meses
@endcomponent

Te recomendamos guardar esta informacion en un lugar seguro y no compartirla con terceros.

Si no reconoces este cambio, comunicate de inmediato con soporte.

Gracias,  
**{{ $appName }}**
@endcomponent
