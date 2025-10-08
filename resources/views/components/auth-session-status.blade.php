@if ($status)
  <div {{ $attributes->merge(['class' => 'mb-4 font-medium text-sm text-green-600']) }}>
    {{ __($status) }}  {{-- no uses $status a pelo --}}
  </div>
@endif
