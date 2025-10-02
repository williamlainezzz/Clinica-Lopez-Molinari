@extends('adminlte::page')
@section('title','Procesos de Citas')
@section('content_header')<h1>Flujo de estados de Citas</h1>@endsection
@section('content')
@php
$kpis = [
  ['Estado'=>'Pendiente','Cant'=>5,'class'=>'bg-warning'],
  ['Estado'=>'Confirmada','Cant'=>24,'class'=>'bg-info'],
  ['Estado'=>'Cancelada','Cant'=>3,'class'=>'bg-danger'],
  ['Estado'=>'Reprogramada','Cant'=>2,'class'=>'bg-secondary'],
];
@endphp

<div class="row">
  @foreach($kpis as $k)
  <div class="col-md-3">
    <div class="small-box {{ $k['class'] }}">
      <div class="inner"><h3>{{ $k['Cant'] }}</h3><p>{{ $k['Estado'] }}</p></div>
      <div class="icon"><i class="fas fa-sync-alt"></i></div>
    </div>
  </div>
  @endforeach
</div>

@include('components.table-tools')

<div class="card">
  <div class="card-header">Últimos movimientos</div>
  <div class="card-body p-0">
    <table class="table mb-0">
      <thead><tr><th>Fecha</th><th>Paciente</th><th>Doctor</th><th>De</th><th>A</th><th>Obs</th></tr></thead>
      <tbody>
        <tr><td>2025-08-10 09:10</td><td>Ana Rivera</td><td>Dr. López</td><td>Pendiente</td><td>Confirmada</td><td>—</td></tr>
        <tr><td>2025-08-10 10:05</td><td>Carlos Pérez</td><td>Dra. Molina</td><td>Pendiente</td><td>Cancelada</td><td>Ausencia</td></tr>
      </tbody>
    </table>
  </div>
</div>
@endsection
