@extends('adminlte::page')
@section('title','Auditoría')
@section('content_header')<h1>Auditoría de cambios</h1>@endsection
@section('content')
@include('components.table-tools')
@php
$log = [
 ['fecha'=>'2025-08-12 09:00','usuario'=>'gsolis','objeto'=>'Cita','accion'=>'INSERT','detalle'=>'Cita 123'],
 ['fecha'=>'2025-08-12 09:30','usuario'=>'dlopez','objeto'=>'Disponibilidad','accion'=>'UPDATE','detalle'=>'08:00-12:00'],
];
@endphp
<table class="table">
  <thead><tr><th>Fecha</th><th>Usuario</th><th>Objeto</th><th>Acción</th><th>Detalle</th></tr></thead>
  <tbody>
    @foreach($log as $r)
    <tr>
      <td>{{ $r['fecha'] }}</td><td>{{ $r['usuario'] }}</td>
      <td>{{ $r['objeto'] }}</td><td>{{ $r['accion'] }}</td><td>{{ $r['detalle'] }}</td>
    </tr>
    @endforeach
  </tbody>
</table>
@endsection
