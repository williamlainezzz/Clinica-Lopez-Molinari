@php
    $brandName = config('mail.from.name', 'Complejo Dental Lopez Molinari');
    $appUrl = 'https://cdentallopezmolinari.com/';
    $host = parse_url((string) $appUrl, PHP_URL_HOST);
    $isLocalHost = in_array($host, ['127.0.0.1', 'localhost'], true);
    $logoUrl = !$isLocalHost ? asset('images/email-logo.jpg') : null;
@endphp

<x-mail::layout>
<x-slot:header>
<x-mail::header :url="$appUrl">
@if($logoUrl)
<img src="{{ $logoUrl }}" alt="{{ $brandName }}" class="brand-logo-image">
@else
<span class="brand-fallback">{{ $brandName }}</span>
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
Este mensaje fue enviado por **{{ $brandName }}**. Si necesitas ayuda, responde a este correo o comunicate con el equipo de la clinica.
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
