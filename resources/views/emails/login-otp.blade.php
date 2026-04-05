@component('mail::message')
# Verificacion en dos pasos

Hola,

Recibimos una solicitud para validar tu acceso en **{{ $appName }}**. Usa este codigo de seguridad para continuar:

@component('mail::panel')
<h2 style="margin: 0; text-align: center; letter-spacing: 6px; font-size: 32px; color: #1d4ed8;">
    {{ $code }}
</h2>
<p style="margin: 12px 0 0; text-align: center; font-size: 13px; color: #64748b;">
    Codigo de un solo uso
</p>
@endcomponent

Este codigo vence en **{{ $ttlMinutes }} minutos** y solo puede utilizarse una vez.

Si no reconoces este intento de acceso, ignora este correo y cambia tu contrasena desde el sistema.

Gracias por cuidar tu cuenta,  
**{{ $appName }}**
@endcomponent
