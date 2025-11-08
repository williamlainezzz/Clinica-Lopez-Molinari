@extends('adminlte::page')

@section('title', 'Panel')

@section('content_header')
  <h1></h1>
@endsection

@section('content')
<div class="row">
  <div class="col-lg-3 col-6">
    <div class="small-box bg-info">
      <div class="inner">
        <h3>24</h3><p>Citas de hoy</p>
      </div>
      <div class="icon"><i class="fas fa-calendar-check"></i></div>
      {{-- antes: route('citas.index') --}}
      <a href="{{ route('agenda.citas') }}" class="small-box-footer">Ver citas <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>

  <div class="col-lg-3 col-6">
    <div class="small-box bg-success">
      <div class="inner">
        <h3>8</h3><p>Doctores disponibles</p>
      </div>
      <div class="icon"><i class="fas fa-user-md"></i></div>
      {{-- antes: route('disponibilidad.index') --}}
      <a href="{{ route('agenda.calendario') }}" class="small-box-footer">Ver disponibilidad <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>

  <div class="col-lg-3 col-6">
    <div class="small-box bg-warning">
      <div class="inner">
        <h3>152</h3><p>Pacientes activos</p>
      </div>
      <div class="icon"><i class="fas fa-user-injured"></i></div>
      {{-- este ya es correcto --}}
      <a href="{{ route('personas.index', ['section' => 'pacientes']) }}" class="small-box-footer">Ver pacientes <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>

  <div class="col-lg-3 col-6">
    <div class="small-box bg-danger">
      <div class="inner">
        <h3>5</h3><p>Citas pendientes</p>
      </div>
      <div class="icon"><i class="fas fa-exclamation-circle"></i></div>
      {{-- antes: route('estado-cita.index') --}}
      <a href="{{ route('agenda.reportes') }}" class="small-box-footer">Ver estados <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header"><h3 class="card-title">Resumen rápido</h3></div>
  <div class="card-body">
    <ul class="list-inline m-0">
      <li class="list-inline-item mr-4"><i class="far fa-circle text-info"></i> Confirmadas</li>
      <li class="list-inline-item mr-4"><i class="far fa-circle text-warning"></i> Pendientes</li>
      <li class="list-inline-item mr-4"><i class="far fa-circle text-danger"></i> Canceladas</li>
    </ul>
  </div>
</div>
@endsection
