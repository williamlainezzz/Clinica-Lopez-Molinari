@extends('adminlte::page')
@section('title','Agenda por doctor')
@section('content_header')
  <h1>Agenda por doctor</h1>
@endsection
@section('content')
<div class="card">
  <div class="card-header">
    <form class="form-inline">
      <div class="form-group mr-2"><label class="mr-2">Doctor</label>
        <select class="form-control"><option>Dr. López</option><option>Dra. Molina</option></select>
      </div>
      <div class="form-group mr-2"><label class="mr-2">Fecha</label><input type="date" class="form-control"></div>
      <button class="btn btn-primary">Generar</button>
    </form>
  </div>
  <div class="card-body table-responsive p-0">
    <table class="table table-sm mb-0">
      <thead><tr><th>Hora</th><th>Paciente</th><th>Motivo</th><th>Estado</th></tr></thead>
      <tbody><tr><td>08:30</td><td>Ana Rivera</td><td>Limpieza</td><td>Confirmada</td></tr></tbody>
    </table>
  </div>
</div>
@endsection
