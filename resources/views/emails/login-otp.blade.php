@component('mail::message')

# Verificación en dos pasos

Para continuar con tu inicio de sesión en **{{ $appName }}**, ingresa el siguiente código:

@component('mail::panel')
<h2 style="margin:0; text-align:center; letter-spacing:2px;">
  {{ $code }}
</h2>
@endcomponent

Este código es de **un solo uso** y caduca en **{{ $ttlMinutes }} minutos**.

Si tú no iniciaste este proceso, puedes ignorar este mensaje.

Gracias,  
Equipo {{ $appName }}

@endcomponent

