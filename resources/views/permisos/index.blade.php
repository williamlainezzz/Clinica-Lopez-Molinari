{{-- resources/views/permisos/index.blade.php --}}
@extends('adminlte::page')

@section('title', 'Permisos')

@section('content_header')
  <div class="d-flex align-items-center justify-content-between flex-wrap">
    <h1 class="m-0">Permisos por rol / objeto</h1>

    <div class="d-flex align-items-center gap-2">
      {{-- Selector de Rol (GET para cambiar de rol) --}}
      <form method="GET" action="{{ route('seguridad.permisos.index') }}" class="form-inline">
        <label class="mr-2 mb-0 font-weight-bold">Rol:</label>
        <select name="rol_id" class="form-control" onchange="this.form.submit()">
          @foreach($roles as $r)
            <option value="{{ $r->COD_ROL }}" {{ (int)$rolId === (int)$r->COD_ROL ? 'selected' : '' }}>
              {{ $r->NOM_ROL }}
            </option>
          @endforeach
        </select>
        @if (request('q'))
          <input type="hidden" name="q" value="{{ request('q') }}">
        @endif
      </form>

      {{-- Buscar objeto (opcional) --}}
      <form method="GET" action="{{ route('seguridad.permisos.index') }}" class="form-inline ml-2">
        <input type="hidden" name="rol_id" value="{{ $rolId }}">
        <div class="input-group">
          <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Filtrar objetos...">
          <div class="input-group-append">
            <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
          </div>
        </div>
      </form>

      {{-- (Opcional) botón a gestión de objetos --}}
      <a href="{{ route('seguridad.objetos.index') }}" class="btn btn-outline-secondary ml-2">
        <i class="fas fa-cubes"></i> Gestionar objetos
      </a>
    </div>
  </div>
@endsection

@section('content')

  @if (session('ok'))
    <div class="alert alert-success">{{ session('ok') }}</div>
  @endif
  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="card">
    <div class="card-body table-responsive p-0">
      <form method="POST" action="{{ route('seguridad.permisos.update') }}">
        @csrf
        <input type="hidden" name="rol_id" value="{{ $rolId }}">

        <table class="table table-striped mb-0">
          <thead>
            <tr>
              <th style="min-width: 280px">Objeto</th>
              <th class="text-center" style="width:120px">Ver</th>
              <th class="text-center" style="width:120px">Crear</th>
              <th class="text-center" style="width:120px">Editar</th>
              <th class="text-center" style="width:120px">Eliminar</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($objetos as $obj)
              @php
                $p = $permisosPorObjeto[$obj->COD_OBJETO] ?? null; // row permiso existente
                $isOn = fn($k) => (!empty($p) && (int)($p->{$k} ?? 0) === 1);
              @endphp
              <tr>
                <td class="align-middle">
                  <strong>{{ $obj->NOM_OBJETO }}</strong>
                </td>

                {{-- VER --}}
                <td class="text-center align-middle">
                  <input type="hidden" name="permisos[{{ $obj->COD_OBJETO }}][VER]" value="0">
                  <div class="custom-control custom-switch d-inline-block">
                    <input type="checkbox"
                           class="custom-control-input"
                           id="ver_{{ $obj->COD_OBJETO }}"
                           name="permisos[{{ $obj->COD_OBJETO }}][VER]"
                           value="1" {{ $isOn('VER') ? 'checked' : '' }}>
                    <label class="custom-control-label" for="ver_{{ $obj->COD_OBJETO }}"></label>
                  </div>
                </td>

                {{-- CREAR --}}
                <td class="text-center align-middle">
                  <input type="hidden" name="permisos[{{ $obj->COD_OBJETO }}][CREAR]" value="0">
                  <div class="custom-control custom-switch d-inline-block">
                    <input type="checkbox"
                           class="custom-control-input"
                           id="crear_{{ $obj->COD_OBJETO }}"
                           name="permisos[{{ $obj->COD_OBJETO }}][CREAR]"
                           value="1" {{ $isOn('CREAR') ? 'checked' : '' }}>
                    <label class="custom-control-label" for="crear_{{ $obj->COD_OBJETO }}"></label>
                  </div>
                </td>

                {{-- EDITAR --}}
                <td class="text-center align-middle">
                  <input type="hidden" name="permisos[{{ $obj->COD_OBJETO }}][EDITAR]" value="0">
                  <div class="custom-control custom-switch d-inline-block">
                    <input type="checkbox"
                           class="custom-control-input"
                           id="editar_{{ $obj->COD_OBJETO }}"
                           name="permisos[{{ $obj->COD_OBJETO }}][EDITAR]"
                           value="1" {{ $isOn('EDITAR') ? 'checked' : '' }}>
                    <label class="custom-control-label" for="editar_{{ $obj->COD_OBJETO }}"></label>
                  </div>
                </td>

                {{-- ELIMINAR --}}
                <td class="text-center align-middle">
                  <input type="hidden" name="permisos[{{ $obj->COD_OBJETO }}][ELIMINAR]" value="0">
                  <div class="custom-control custom-switch d-inline-block">
                    <input type="checkbox"
                           class="custom-control-input"
                           id="eliminar_{{ $obj->COD_OBJETO }}"
                           name="permisos[{{ $obj->COD_OBJETO }}][ELIMINAR]"
                           value="1" {{ $isOn('ELIMINAR') ? 'checked' : '' }}>
                    <label class="custom-control-label" for="eliminar_{{ $obj->COD_OBJETO }}"></label>
                  </div>
                </td>
              </tr>
            @empty
              <tr><td colspan="5" class="text-center text-muted p-4">No hay objetos para mostrar.</td></tr>
            @endforelse
          </tbody>
        </table>

        <div class="p-3 d-flex justify-content-end">
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Guardar cambios
          </button>
        </div>
      </form>
    </div>
  </div>
@endsection
