@extends('adminlte::page')
@section('title','Pacientes activos e inactivos')
@section('content_header')
  <h1>Pacientes activos e inactivos</h1>
@endsection
@section('content')
<div class="card">
  <div class="card-header">
    <form class="form-inline">
      <div class="form-group mr-2"><label class="mr-2">Estado</label>
        <select class="form-control"><option>Activos</option><option>Inactivos</option></select>
      </div>
      <button class="btn btn-primary">Generar</button>
    </form>
  </div>
  <div class="card-body table-responsive p-0">
    <table class="table table-sm mb-0">
      <thead><tr><th>#</th><th>Nombre</th><th>Teléfono</th><th>Última cita</th><th>Estado</th></tr></thead>
      <tbody><tr><td>1</td><td>Ana Rivera</td><td>9990-1234</td><td>2025-08-01</td><td>Activo</td></tr></tbody>
    </table>
  </div>
</div>
@endsection
