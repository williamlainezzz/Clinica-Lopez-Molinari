@extends('adminlte::page')
@section('title','Citas por rango de fechas')
@section('content_header')
  <h1>Citas por rango de fechas</h1>
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
      <thead><tr><th>#</th><th>Fecha</th><th>Hora</th><th>Paciente</th><th>Doctor</th><th>Estado</th></tr></thead>
      <tbody>
        <tr><td>1</td><td>2025-08-12</td><td>08:30</td><td>Ana Rivera</td><td>Dr. López</td><td>Confirmada</td></tr>
      </tbody>
    </table>
  </div>
</div>
@endsection
