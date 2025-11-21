@extends('adminlte::page')
@section('title','Pacientes activos e inactivos')
@section('content_header')
  <h1>Pacientes activos e inactivos</h1>
@endsection
@section('content')
<div class="card">
  <div class="card-header">
    <form class="form-inline" action="{{ route('reportes.pacientes_estado') }}" method="POST">
      @csrf
      <div class="form-group mr-2"><label class="mr-2">Estado</label>
        <select name="estado" class="form-control">
          <option value="activos" @selected(($filters['estado'] ?? 'activos')==='activos')>Activos</option>
          <option value="inactivos" @selected(($filters['estado'] ?? 'activos')==='inactivos')>Inactivos</option>
          <option value="todos" @selected(($filters['estado'] ?? 'activos')==='todos')>Todos</option>
        </select>
      </div>
      <button class="btn btn-primary">Generar</button>
    </form>
  </div>
  <div class="card-body table-responsive p-0">
    <table class="table table-sm mb-0">
      <thead><tr><th>#</th><th>Nombre</th><th>Teléfono</th><th>Última cita</th><th>Estado</th></tr></thead>
      <tbody>
        @forelse($pacientes as $paciente)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $paciente->PRIMER_NOMBRE }} {{ $paciente->PRIMER_APELLIDO }}</td>
            <td>{{ $paciente->telefonos ?: 'N/D' }}</td>
            <td>{{ $paciente->ultima_cita ?: '—' }}</td>
            <td>{{ $paciente->ESTADO_USUARIO === 1 ? 'Activo' : 'Inactivo' }}</td>
          </tr>
        @empty
          <tr><td colspan="5" class="text-center">No hay registros para los filtros seleccionados.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
