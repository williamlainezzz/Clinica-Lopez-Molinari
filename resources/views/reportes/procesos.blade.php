@extends('adminlte::page')

@section('title','Procesos de Citas')

@section('content_header')
  <h1>Flujo de estados de Citas</h1>
@endsection

@section('content')
<div class="row">
  @forelse($kpis as $k)
    <div class="col-md-3">
      <div class="small-box {{ $k['class'] }}">
        <div class="inner">
          <h3>{{ $k['Cant'] }}</h3>
          <p>{{ $k['Estado'] }}</p>
        </div>
        <div class="icon">
          <i class="fas fa-sync-alt"></i>
        </div>
      </div>
    </div>
  @empty
    <div class="col-12">
      <p class="text-muted">No hay datos disponibles.</p>
    </div>
  @endforelse
</div>

@include('components.table-tools')

<div class="card">
  <div class="card-header">Últimos movimientos</div>
  <div class="card-body p-0">
    <table class="table mb-0">
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Paciente</th>
          <th>Doctor</th>
          <th>De</th>
          <th>A</th>
          <th>Obs</th>
        </tr>
      </thead>
      <tbody>
        @forelse($movimientos as $m)
          <tr>
            <td>{{ $m->created_at }}</td>
            <td>{{ $m->usuario ?? 'N/D' }}</td>
            <td>{{ $m->OBJETO }}</td>
            <td>{{ $m->ACCION }}</td>
            <td>{{ $m->ACCION }}</td>
            <td>{{ $m->DESCRIPCION }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center">
              No hay registros disponibles.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
