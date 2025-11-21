@extends('adminlte::page')
@section('title','Permisos por rol')
@section('content_header')<h1>Permisos por rol</h1>@endsection
@section('content')
<div class="card">
  <div class="card-body">
    @forelse($roles as $rol)
      <h5 class="mt-3">{{ $rol->NOM_ROL }}</h5>
      <ul class="mb-2">
        @forelse(($permisos[$rol->NOM_ROL] ?? collect()) as $p)
          @php
            $acciones = [];
            if ($p->VER) $acciones[] = 'Ver';
            if ($p->CREAR) $acciones[] = 'Crear';
            if ($p->EDITAR) $acciones[] = 'Editar';
            if ($p->ELIMINAR) $acciones[] = 'Eliminar';
          @endphp
          <li>• {{ $p->NOM_OBJETO }}: {{ empty($acciones) ? 'Sin permisos' : implode(', ', $acciones) }}</li>
        @empty
          <li class="text-muted">Sin permisos registrados.</li>
        @endforelse
      </ul>
    @empty
      <p class="text-muted">No hay roles definidos.</p>
    @endforelse
  </div>
</div>
@endsection
