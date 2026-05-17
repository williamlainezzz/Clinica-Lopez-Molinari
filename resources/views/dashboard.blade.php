@extends('adminlte::page')

@section('title', 'Panel')

@section('css')
<style>
  .welcome-hero-card {
    border: 1px solid var(--theme-border, #dde6f3) !important;
    border-radius: 1.25rem !important;
    background:
      radial-gradient(circle at top right, rgba(96, 165, 250, 0.16), transparent 30%),
      linear-gradient(135deg, rgba(255, 255, 255, 0.96) 0%, rgba(241, 247, 255, 0.94) 100%) !important;
    box-shadow: var(--theme-shadow, 0 14px 34px rgba(15, 23, 42, 0.08)) !important;
    margin-bottom: 1.15rem;
    overflow: hidden;
  }

  .welcome-hero-title {
    margin: 0;
    font-weight: 800;
    font-size: 1.75rem;
    color: var(--theme-text, #1f2d3d);
    line-height: 1.25;
    letter-spacing: 0;
  }

  .welcome-hero-name {
    color: var(--theme-accent-start, #114b9b);
  }

  .welcome-hero-copy {
    margin: .65rem 0 0;
    color: var(--theme-text-soft, #49586f);
    max-width: 840px;
    font-size: .98rem;
  }

  .welcome-role-pill {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    padding: .42rem .82rem;
    font-size: .78rem;
    font-weight: 800;
    color: var(--theme-link, #0b4b84);
    background: var(--theme-accent-soft, #e8f2ff);
    border: 1px solid var(--theme-toggle-border, #c8dcfa);
    letter-spacing: 0;
    margin-top: .85rem;
  }

  .welcome-role-pill i {
    margin-right: .38rem;
    font-size: .76rem;
    opacity: .82;
  }

  html[data-theme='dark'] .welcome-hero-card {
    border-color: rgba(96, 165, 250, 0.22);
    background:
      radial-gradient(circle at top right, rgba(96, 165, 250, 0.18), transparent 30%),
      linear-gradient(135deg, rgba(10, 22, 46, 0.96) 0%, rgba(11, 27, 56, 0.92) 100%) !important;
  }

  html[data-theme='dark'] .welcome-hero-title {
    color: #edf4ff;
  }

  html[data-theme='dark'] .welcome-hero-name {
    color: #75b4ff;
  }

  html[data-theme='dark'] .welcome-hero-copy {
    color: #c6d5ec;
  }

  html[data-theme='dark'] .welcome-role-pill {
    color: #d7e9ff;
    background: rgba(59, 130, 246, 0.16);
    border-color: rgba(96, 165, 250, 0.4);
  }

  @media (max-width: 767.98px) {
    .welcome-hero-title {
      font-size: 1.28rem;
    }

    .welcome-hero-copy {
      font-size: .92rem;
    }
  }
</style>
@endsection

@section('content_header')
  <h1></h1>
@endsection

@section('content')
@php
  $usuario = auth()->user();
  $persona = optional($usuario)->persona;
  $rol = optional($usuario)->rol;

  $nombreBase = trim((string) ($persona->PRIMER_NOMBRE ?? ''));
  $apellidoBase = trim((string) ($persona->PRIMER_APELLIDO ?? ''));
  $nombreCompleto = trim($nombreBase . ' ' . $apellidoBase);

  if ($nombreCompleto === '') {
      $nombreCompleto = trim((string) ($persona->nombre_completo ?? ''));
  }

  if ($nombreCompleto === '') {
      $nombreCompleto = trim((string) ($usuario->USR_USUARIO ?? 'Usuario'));
  }

  $generoRaw = trim((string) ($persona->TIPO_GENERO ?? ''));
  $generoNormalizado = \Illuminate\Support\Str::lower(\Illuminate\Support\Str::ascii($generoRaw));
  $esFemenino = in_array($generoNormalizado, ['f', 'femenino', 'mujer', 'female'], true);

  $rolRaw = trim((string) ($rol->NOM_ROL ?? ''));
  $rolNormalizado = \Illuminate\Support\Str::lower(\Illuminate\Support\Str::ascii($rolRaw));
  $esDoctor = \Illuminate\Support\Str::contains($rolNormalizado, 'doctor');

  $saludoBase = $esFemenino ? 'Bienvenida' : 'Bienvenido';
  $encabezadoBienvenida = $saludoBase . ', ' . $nombreCompleto;

  if ($esDoctor) {
      $prefijoDoctor = $esFemenino ? 'Dra.' : 'Dr.';
      $encabezadoBienvenida = $saludoBase . ', ' . $prefijoDoctor . ' ' . $nombreCompleto;
  }

  $etiquetaRol = 'Usuario del sistema';
  if ($esDoctor) {
      $etiquetaRol = $esFemenino ? 'Doctora' : 'Doctor';
  } elseif (\Illuminate\Support\Str::contains($rolNormalizado, 'admin')) {
      $etiquetaRol = 'Administrador del sistema';
  } elseif (\Illuminate\Support\Str::contains($rolNormalizado, 'recep')) {
      $etiquetaRol = 'Recepcionista';
  } elseif (\Illuminate\Support\Str::contains($rolNormalizado, 'pacient')) {
      $etiquetaRol = 'Paciente';
  } elseif ($rolRaw !== '') {
      $etiquetaRol = \Illuminate\Support\Str::title(\Illuminate\Support\Str::lower($rolRaw));
  }
@endphp

<div class="card welcome-hero-card">
  <div class="card-body py-4 px-4 px-md-5">
    <h2 class="welcome-hero-title">
      {!! str_replace($nombreCompleto, '<span class="welcome-hero-name">'.e($nombreCompleto).'</span>', e($encabezadoBienvenida)) !!}
    </h2>

    <p class="welcome-hero-copy">
      Le damos la bienvenida al sistema del Complejo Dental Lopez Molinari. Desde este panel podra gestionar sus actividades de forma segura, rapida y organizada.
    </p>

    <span class="welcome-role-pill">
      <i class="fas fa-tooth" aria-hidden="true"></i>
      {{ $etiquetaRol }}
    </span>
  </div>
</div>

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
      <a href="{{ route('pacientes.index') }}" class="small-box-footer">Ver pacientes <i class="fas fa-arrow-circle-right"></i></a>
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
  <div class="card-header"><h3 class="card-title">Resumen rapido</h3></div>
  <div class="card-body">
    <ul class="list-inline m-0">
      <li class="list-inline-item mr-4"><i class="far fa-circle text-info"></i> Confirmadas</li>
      <li class="list-inline-item mr-4"><i class="far fa-circle text-warning"></i> Pendientes</li>
      <li class="list-inline-item mr-4"><i class="far fa-circle text-danger"></i> Canceladas</li>
    </ul>
  </div>
</div>
@endsection
