@extends('layouts.app')

@section('content')
<h1>Permisos por rol / objeto</h1>

<form method="GET" action="{{ route('seguridad.permisos.index') }}" class="mb-3">
  <label>Rol:</label>
  <select name="rol_id" onchange="this.form.submit()">
    @foreach($roles as $r)
      <option value="{{ $r->COD_ROL }}" {{ request('rol_id', $roles->first()->COD_ROL)==$r->COD_ROL?'selected':'' }}>
        {{ $r->NOM_ROL }}
      </option>
    @endforeach
  </select>
</form>

@php $rolId = (int) request('rol_id', $roles->first()->COD_ROL); @endphp

<form method="POST" action="{{ route('seguridad.permisos.update') }}">
@csrf
<input type="hidden" name="rol_id" value="{{ $rolId }}">

<table border="1" cellpadding="6">
  <thead>
    <tr>
      <th>Objeto</th>
      <th>Ver</th>
      <th>Crear</th>
      <th>Editar</th>
      <th>Eliminar</th>
    </tr>
  </thead>
  <tbody>
    @foreach($objetos as $o)
      @php
        $p = optional(optional($permisos->get($rolId))->get($o->COD_OBJETO));
      @endphp
      <tr>
        <td>{{ $o->NOM_OBJETO }}</td>
        <td><input type="checkbox" name="permisos[{{ $o->COD_OBJETO }}][VER]"      {{ $p && $p->VER ? 'checked':'' }}></td>
        <td><input type="checkbox" name="permisos[{{ $o->COD_OBJETO }}][CREAR]"    {{ $p && $p->CREAR ? 'checked':'' }}></td>
        <td><input type="checkbox" name="permisos[{{ $o->COD_OBJETO }}][EDITAR]"   {{ $p && $p->EDITAR ? 'checked':'' }}></td>
        <td><input type="checkbox" name="permisos[{{ $o->COD_OBJETO }}][ELIMINAR]" {{ $p && $p->ELIMINAR ? 'checked':'' }}></td>
      </tr>
    @endforeach
  </tbody>
</table>

<button type="submit" class="mt-3">Guardar cambios</button>
</form>
@endsection
