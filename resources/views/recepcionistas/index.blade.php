@extends('adminlte::page')
@section('title','Recepcionistas')
@section('content_header')
  <div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0">Recepcionistas</h1>
    <button class="btn btn-primary"><i class="fas fa-user-plus"></i> Nueva recepcionista</button>
  </div>
@endsection
@section('content')
@php
$rows = [
  ['nom'=>'Laura Gómez','tel'=>'9901-7788','estado'=>'Activo'],
  ['nom'=>'Pedro Díaz','tel'=>'9702-5566','estado'=>'Activo'],
];
@endphp
<div class="card">
  <div class="card-body table-responsive p-0">
    <table class="table table-hover mb-0">
      <thead><tr><th>Nombre</th><th>Teléfono</th><th>Estado</th><th class="text-right">Acciones</th></tr></thead>
      <tbody>
        @foreach($rows as $r)
        <tr>
          <td>{{ $r['nom'] }}</td><td>{{ $r['tel'] }}</td>
          <td><span class="badge badge-success">{{ $r['estado'] }}</span></td>
          <td class="text-right">
            <button class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></button>
            <button class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></button>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
