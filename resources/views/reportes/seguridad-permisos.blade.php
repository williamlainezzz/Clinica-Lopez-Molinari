@extends('adminlte::page')
@section('title','Permisos por rol')
@section('content_header')<h1>Permisos por rol</h1>@endsection
@section('content')
@php
$roles = [
 'Administrador'=>['Citas: CRUD','Usuarios: CRUD','Permisos: CRUD'],
 'Doctor'=>['Citas: ver/editar propias','Disponibilidad: CRUD'],
 'Recepcionista'=>['Citas: CRUD','Pacientes: ver/crear'],
 'Paciente'=>['Citas: ver propias','Perfil: editar'],
];
@endphp
<div class="card">
  <div class="card-body">
    @foreach($roles as $rol=>$perms)
      <h5 class="mt-3">{{ $rol }}</h5>
      <ul class="mb-2">
        @foreach($perms as $p)<li>• {{ $p }}</li>@endforeach
      </ul>
    @endforeach
  </div>
</div>
@endsection
