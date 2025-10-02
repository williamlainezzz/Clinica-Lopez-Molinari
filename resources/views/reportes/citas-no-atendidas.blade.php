@extends('adminlte::page')
@section('title','Citas no atendidas / ausencia')
@section('content_header')
  <h1>Citas no atendidas o con ausencia</h1>
@endsection
@section('content')
<div class="card">
  <div class="card-header">
    <form class="form-inline">
      <div class="form-group mr-2"><label class="mr-2">Desde</label><input type="date" class="form-control"></div>
      <div class="form-group mr-2"><label class="mr-2">Hasta</label><input type="date" class="form-control"></div>
      <button class="btn btn-primary">Generar</button>
    </form>
  </div>
  <div class="card-body table-responsive p-0">
    <table class="table table-sm mb-0">
      <thead><tr><th>#</th><th>Fecha</th><th>Paciente</th><th>Doctor</th><th>Motivo</th><th>Observación</th></tr></thead>
      <tbody><tr><td>1</td><td>2025-08-05</td><td>Carlos Pérez</td><td>Dra. Molina</td><td>Control</td><td>Ausencia</td></tr></tbody>
    </table>
  </div>
</div>
@endsection
