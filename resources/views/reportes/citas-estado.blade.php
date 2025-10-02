@extends('adminlte::page')
@section('title','Citas por estado')
@section('content_header')
  <h1>Citas por estado</h1>
@endsection
@section('content')
<div class="card">
  <div class="card-header">
    <form class="form-inline">
      <div class="form-group mr-2"><label class="mr-2">Estado</label>
        <select class="form-control">
          <option>Confirmada</option><option>Pendiente</option><option>Cancelada</option><option>Reprogramada</option>
        </select>
      </div>
      <button class="btn btn-primary">Generar</button>
    </form>
  </div>
  <div class="card-body table-responsive p-0">
    <table class="table table-sm mb-0">
      <thead><tr><th>#</th><th>Fecha</th><th>Paciente</th><th>Doctor</th><th>Estado</th></tr></thead>
      <tbody><tr><td>1</td><td>2025-08-12</td><td>Carlos Pérez</td><td>Dra. Molina</td><td>Pendiente</td></tr></tbody>
    </table>
  </div>
</div>
@endsection
