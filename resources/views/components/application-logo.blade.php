@props(['class' => 'h-20 w-auto'])

<img
    src="{{ asset('images/logo_clinica.avif') }}"
    alt="Complejo Dental López Molinari"
    {{ $attributes->merge(['class' => $class . ' object-contain']) }}
/>
