@extends('adminlte::page')

@section('title', 'Editar usuario')

@section('content_header')
  <h1>Editar usuario</h1>
@stop

@section('content')
  @if(session('success')) <x-adminlte-alert theme="success" title="OK" dismissable>{{ session('success') }}</x-adminlte-alert> @endif
  @if ($errors->any())
    <x-adminlte-alert theme="danger" title="Errores" dismissable>
      <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </x-adminlte-alert>
  @endif

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('seguridad.usuarios.update', $u->COD_USUARIO) }}">
        @csrf @method('PUT')

        <div class="form-group">
          <label>Persona</label>
          <select name="FK_COD_PERSONA" class="form-control" required>
            @foreach($personas as $p)
              <option value="{{ $p->COD_PERSONA }}" @selected(old('FK_COD_PERSONA', $u->FK_COD_PERSONA)==$p->COD_PERSONA)>{{ $p->nombre }}</option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <label>Usuario</label>
          <input name="USR_USUARIO" class="form-control" value="{{ old('USR_USUARIO', $u->USR_USUARIO) }}" required>
        </div>

        <div class="form-group">
          <label>Rol</label>
          <select name="FK_COD_ROL" class="form-control" required>
            @foreach($roles as $r)
              <option value="{{ $r->COD_ROL }}" @selected(old('FK_COD_ROL', $u->FK_COD_ROL)==$r->COD_ROL)>{{ $r->NOM_ROL }}</option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <label>Estado</label>
          <select name="ESTADO_USUARIO" class="form-control" required>
            @foreach($estados as $e)
              <option value="{{ $e->id }}" @selected(old('ESTADO_USUARIO', $u->ESTADO_USUARIO)==$e->id)>{{ $e->txt }}</option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <label>Nueva contraseña (opcional)</label>
          <input type="password" name="password" class="form-control" placeholder="Dejar vacío para no cambiar">
          <small class="text-muted">Si la ingresas, debe cumplir las reglas de seguridad.</small>
        </div>

        <a href="{{ route('seguridad.usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
        <button class="btn btn-primary">Actualizar</button>
      </form>
    </div>
  </div>
@stop
