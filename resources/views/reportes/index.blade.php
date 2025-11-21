@extends('adminlte::page')
@section('title','Reportes')
@section('content_header')
  <h1>Reportes</h1>
@endsection
@section('content')
<div class="row">
  <div class="col-md-4">
    <div class="small-box bg-info">
      <div class="inner">
        <h3>{{ $resumen['citas'] ?? 0 }}</h3>
        <p>Citas registradas</p>
      </div>
      <div class="icon"><i class="fas fa-calendar-check"></i></div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="small-box bg-success">
      <div class="inner">
        <h3>{{ $resumen['pacientes'] ?? 0 }}</h3>
        <p>Pacientes</p>
      </div>
      <div class="icon"><i class="fas fa-user-injured"></i></div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="small-box bg-primary">
      <div class="inner">
        <h3>{{ $resumen['usuarios'] ?? 0 }}</h3>
        <p>Usuarios</p>
      </div>
      <div class="icon"><i class="fas fa-users"></i></div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header">Tipos de reporte</div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-6 mb-2"><a class="btn btn-outline-primary btn-block" href="{{ route('reportes.citas_rango') }}">Citas por rango de fechas</a></div>
      <div class="col-md-6 mb-2"><a class="btn btn-outline-primary btn-block" href="{{ route('reportes.citas_estado') }}">Citas por estado</a></div>
      <div class="col-md-6 mb-2"><a class="btn btn-outline-primary btn-block" href="{{ route('reportes.agenda_doctor') }}">Agenda por doctor</a></div>
      <div class="col-md-6 mb-2"><a class="btn btn-outline-primary btn-block" href="{{ route('reportes.pacientes_estado') }}">Pacientes activos/inactivos</a></div>
      <div class="col-md-6 mb-2"><a class="btn btn-outline-primary btn-block" href="{{ route('reportes.usuarios_rol') }}">Usuarios por rol</a></div>
      <div class="col-md-6 mb-2"><a class="btn btn-outline-primary btn-block" href="{{ route('reportes.citas_no_atendidas') }}">Citas no atendidas/ausencia</a></div>
      <div class="col-md-6 mb-2"><a class="btn btn-outline-primary btn-block" href="{{ route('reportes.procesos') }}">Procesos</a></div>
      <div class="col-md-6 mb-2"><a class="btn btn-outline-primary btn-block" href="{{ route('reportes.seguridad_permisos') }}">Seguridad / permisos</a></div>
    </div>
  </div>
</div>
@endsection
