@php
    $emailLogoPath = public_path('images/email-logo.jpg');
    $emailLogo = file_exists($emailLogoPath)
        ? 'data:image/jpeg;base64,'.base64_encode(file_get_contents($emailLogoPath))
        : null;
@endphp

<x-mail::layout>
<x-slot:header>
<x-mail::header :url="config('app.url')">
@if($emailLogo)
<img src="{{ $emailLogo }}" alt="{{ config('app.name') }}" class="brand-logo-image">
@else
{{ config('app.name') }}
@endif
</x-mail::header>
</x-slot:header>

{!! $slot !!}

@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{!! $subcopy !!}
</x-mail::subcopy>
</x-slot:subcopy>
@endisset

<x-slot:footer>
<x-mail::footer>
Este mensaje fue enviado por **{{ config('app.name') }}**. Si necesitas ayuda, responde a este correo o comunicate con el equipo de la clinica.
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
