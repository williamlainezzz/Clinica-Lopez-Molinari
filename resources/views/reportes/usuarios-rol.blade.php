@extends('adminlte::page')
@section('title','Usuarios por rol')
@section('content_header')
  <h1>Usuarios por rol</h1>
@endsection
@section('content')
<div class="card">
  <div class="card-header">
    <form class="form-inline" action="{{ route('reportes.usuarios_rol') }}" method="POST">
      @csrf
      <div class="form-group mr-2"><label class="mr-2">Rol</label>
        <select name="rol" class="form-control">
          <option value="">Todos</option>
          @foreach($roles as $rol)
            <option value="{{ $rol->COD_ROL }}" @selected(($filters['rol'] ?? '')==$rol->COD_ROL)>{{ $rol->NOM_ROL }}</option>
          @endforeach
        </select>
      </div>
      <button class="btn btn-primary">Generar</button>
    </form>
  </div>
  <div class="card-body table-responsive p-0">
    <table class="table table-sm mb-0">
      <thead><tr><th>#</th><th>Usuario</th><th>Nombre</th><th>Rol</th><th>Estado</th></tr></thead>
      <tbody>
        @forelse($usuarios as $usuario)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $usuario->USR_USUARIO }}</td>
            <td>{{ $usuario->nombre }}</td>
            <td>{{ $usuario->NOM_ROL }}</td>
            <td>{{ $usuario->ESTADO_USUARIO === 1 ? 'Activo' : 'Inactivo' }}</td>
          </tr>
        @empty
          <tr><td colspan="5" class="text-center">No hay registros para los filtros seleccionados.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
