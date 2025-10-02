@extends('adminlte::page')
@section('title','Pacientes')
@section('content_header')
  <div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0">Pacientes</h1>
    <button class="btn btn-primary"><i class="fas fa-user-injured"></i> Nuevo paciente</button>
  </div>
@endsection
@section('content')
@php
$rows = [
  ['nom'=>'Ana Rivera','gen'=>'Femenino','tel'=>'9990-1234','dir'=>'Col. Centro','estado'=>'Activo'],
  ['nom'=>'Carlos Pérez','gen'=>'Masculino','tel'=>'8888-5678','dir'=>'Col. La Paz','estado'=>'Activo'],
];
@endphp
<div class="card">
  <div class="card-body table-responsive p-0">
    <table class="table table-hover mb-0">
      <thead><tr><th>Nombre</th><th>Género</th><th>Teléfono</th><th>Dirección</th><th>Estado</th><th class="text-right">Acciones</th></tr></thead>
      <tbody>
        @foreach($rows as $r)
        <tr>
          <td>{{ $r['nom'] }}</td><td>{{ $r['gen'] }}</td><td>{{ $r['tel'] }}</td><td>{{ $r['dir'] }}</td>
          <td><span class="badge badge-success">{{ $r['estado'] }}</span></td>
          <td class="text-right">
            <button class="btn btn-sm btn-outline-info"><i class="fas fa-folder-open"></i></button>
            <button class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></button>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
