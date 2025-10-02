@extends('adminlte::page')
@section('title','Usuarios por rol')
@section('content_header')
  <h1>Usuarios por rol</h1>
@endsection
@section('content')
<div class="card">
  <div class="card-header">
    <form class="form-inline">
      <div class="form-group mr-2"><label class="mr-2">Rol</label>
        <select class="form-control"><option>Administrador</option><option>Doctor</option><option>Paciente</option><option>Recepcionista</option></select>
      </div>
      <button class="btn btn-primary">Generar</button>
    </form>
  </div>
  <div class="card-body table-responsive p-0">
    <table class="table table-sm mb-0">
      <thead><tr><th>#</th><th>Usuario</th><th>Nombre</th><th>Rol</th><th>Estado</th></tr></thead>
      <tbody><tr><td>1</td><td>gsolis</td><td>Guillermo Solís</td><td>Administrador</td><td>Activo</td></tr></tbody>
    </table>
  </div>
</div>
@endsection
