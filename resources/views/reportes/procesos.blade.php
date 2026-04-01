@extends('adminlte::page')

@section('title', 'Flujo de citas')

@section('content_header')
  <h1>Flujo de citas</h1>
@endsection

@section('content')
<div class="row">
  @forelse($kpis as $kpi)
    <div class="col-lg-3 col-md-4 col-sm-6">
      <div class="small-box {{ $kpi['class'] }}">
        <div class="inner">
          <h3>{{ $kpi['Cant'] }}</h3>
          <p>{{ $kpi['Estado'] }}</p>
        </div>
        <div class="icon">
          <i class="fas fa-chart-line"></i>
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
  <div class="card-header">Últimas citas registradas</div>
  <div class="card-body p-0">
    <table class="table mb-0">
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Hora</th>
          <th>Paciente</th>
          <th>Doctor</th>
          <th>Estado</th>
          <th>Motivo</th>
        </tr>
      </thead>
      <tbody>
        @forelse($ultimasCitas as $cita)
          <tr>
            <td>{{ $cita->FEC_CITA }}</td>
            <td>{{ $cita->HOR_CITA ? substr($cita->HOR_CITA, 0, 5) : 'N/D' }}</td>
            <td>{{ $cita->paciente_nombre ?? 'N/D' }}</td>
            <td>{{ $cita->doctor_nombre ?? 'N/D' }}</td>
            <td>{{ $cita->estado_nombre ?? 'Sin estado' }}</td>
            <td>{{ $cita->MOT_CITA ?? 'Sin motivo' }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center">
              No hay citas registradas para mostrar.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
