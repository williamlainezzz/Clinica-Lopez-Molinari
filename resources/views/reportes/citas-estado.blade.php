@extends('adminlte::page')
@section('title','Citas por estado')
@section('content_header')
  <h1>Citas por estado</h1>
@endsection
@section('content')
<div class="card">
  <div class="card-header">
    <form class="form-inline" action="{{ route('reportes.citas_estado') }}" method="POST">
      @csrf
      <div class="form-group mr-2">
        <label class="mr-2">Estado</label>
        <select name="estado" class="form-control">
          <option value="">Todos</option>
          @foreach($estados as $estado)
            <option value="{{ $estado->COD_ESTADO }}" @selected(($filters['estado'] ?? '')==$estado->COD_ESTADO)>{{ $estado->NOM_ESTADO }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group mr-2">
        <label class="mr-2">Desde</label>
        <input type="date" name="fecha_inicio" value="{{ $filters['fecha_inicio'] ?? '' }}" class="form-control">
      </div>
      <div class="form-group mr-2">
        <label class="mr-2">Hasta</label>
        <input type="date" name="fecha_fin" value="{{ $filters['fecha_fin'] ?? '' }}" class="form-control">
      </div>
      <button class="btn btn-primary">Generar</button>
    </form>
  </div>
  <div class="card-body table-responsive p-0">
    <table class="table table-sm mb-0">
      <thead><tr><th>#</th><th>Fecha</th><th>Paciente</th><th>Doctor</th><th>Estado</th></tr></thead>
      <tbody>
        @forelse($citas as $cita)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $cita->FEC_CITA }} {{ substr($cita->HOR_CITA,0,5) }}</td>
            <td>{{ $cita->paciente_nombre }}</td>
            <td>{{ $cita->doctor_nombre }}</td>
            <td>{{ $cita->estado_nombre }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center">No hay registros para los filtros seleccionados.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
