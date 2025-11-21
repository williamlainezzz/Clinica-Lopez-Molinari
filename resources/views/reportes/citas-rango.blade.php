@extends('adminlte::page')
@section('title','Citas por rango de fechas')
@section('content_header')
  <h1>Citas por rango de fechas</h1>
@endsection
@section('content')
<div class="card">
  <div class="card-header">
    <form class="form-inline" action="{{ route('reportes.citas_rango') }}" method="POST">
      @csrf
      <div class="form-group mr-2">
        <label class="mr-2">Desde</label>
        <input type="date" name="fecha_inicio" value="{{ $filters['fecha_inicio'] ?? '' }}" class="form-control">
      </div>
      <div class="form-group mr-2">
        <label class="mr-2">Hasta</label>
        <input type="date" name="fecha_fin" value="{{ $filters['fecha_fin'] ?? '' }}" class="form-control">
      </div>
      <div class="form-group mr-2">
        <label class="mr-2">Doctor</label>
        <select name="doctor" class="form-control">
          <option value="">Todos</option>
          @foreach($doctores as $doctor)
            <option value="{{ $doctor->COD_PERSONA }}" @selected(($filters['doctor'] ?? '')==$doctor->COD_PERSONA)>
              {{ $doctor->PRIMER_NOMBRE }} {{ $doctor->PRIMER_APELLIDO }}
            </option>
          @endforeach
        </select>
      </div>
      <button class="btn btn-primary">Generar</button>
    </form>
  </div>
  <div class="card-body table-responsive p-0">
    <table class="table table-sm mb-0">
      <thead><tr><th>#</th><th>Fecha</th><th>Hora</th><th>Paciente</th><th>Doctor</th><th>Estado</th></tr></thead>
      <tbody>
        @forelse($citas as $cita)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $cita->FEC_CITA }}</td>
            <td>{{ substr($cita->HOR_CITA,0,5) }}@if($cita->HOR_FIN) - {{ substr($cita->HOR_FIN,0,5) }}@endif</td>
            <td>{{ $cita->paciente_nombre }}</td>
            <td>{{ $cita->doctor_nombre }}</td>
            <td>{{ $cita->estado_nombre }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center">No hay registros para los filtros seleccionados.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
