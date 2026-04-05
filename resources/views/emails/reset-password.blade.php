@component('mail::message')
# Recuperacion de contrasena

Hola,

Recibimos una solicitud para restablecer la contrasena asociada a tu cuenta en **{{ $appName }}**.

@component('mail::panel')
- **Correo asociado:** {{ $email }}
- **Tiempo de validez del enlace:** {{ $count }} minutos
@endcomponent

@component('mail::button', ['url' => $resetUrl])
Restablecer contrasena
@endcomponent

Si no solicitaste este cambio, puedes ignorar este mensaje con tranquilidad.

Gracias,  
**{{ $appName }}**
@endcomponent
