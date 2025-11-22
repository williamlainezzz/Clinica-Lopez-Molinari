@extends('adminlte::page')

@section('title','Notificaciones de citas')

@section('content_header')
<div class="d-flex justify-content-between align-items-center flex-wrap">
  <div>
    <h1 class="m-0">Notificaciones de citas</h1>
    <p class="text-muted mb-0">Historial de correos enviados a pacientes y doctores.</p>
  </div>
  <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Notificaciones</li>
  </ol>
</div>
@endsection

@section('content')
<div class="card shadow-sm">
  <div class="card-body pb-0">
    <form method="GET" class="form-row align-items-end">
      <div class="form-group col-md-2 mb-3">
        <label for="fecha_desde" class="small text-muted mb-1">Fecha desde</label>
        <input type="date" name="fecha_desde" id="fecha_desde" value="{{ $filtros['fecha_desde'] ?? '' }}" class="form-control">
      </div>
      <div class="form-group col-md-2 mb-3">
        <label for="fecha_hasta" class="small text-muted mb-1">Fecha hasta</label>
        <input type="date" name="fecha_hasta" id="fecha_hasta" value="{{ $filtros['fecha_hasta'] ?? '' }}" class="form-control">
      </div>
      <div class="form-group col-md-3 mb-3">
        <label for="estado_cita" class="small text-muted mb-1">Estado de la cita</label>
        <select name="estado_cita" id="estado_cita" class="form-control">
          <option value="">Todos</option>
          @foreach($estados as $id => $nombre)
            <option value="{{ $id }}" {{ ($filtros['estado_cita'] ?? '') == $id ? 'selected' : '' }}>{{ $nombre }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group col-md-3 mb-3">
        <label for="tipo" class="small text-muted mb-1">Tipo de notificación</label>
        <select name="tipo" id="tipo" class="form-control">
          <option value="">Todos</option>
          @foreach($tipos as $key => $label)
            <option value="{{ $key }}" {{ ($filtros['tipo'] ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group col-md-2 mb-3">
        <label for="q" class="small text-muted mb-1">Búsqueda</label>
        <div class="input-group">
          <input type="text" name="q" id="q" value="{{ $filtros['q'] ?? '' }}" class="form-control" placeholder="Paciente, doctor, mensaje">
          <div class="input-group-append">
            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <span class="h6 mb-0">Historial</span>
    @if(isset($paginacion) && $paginacion)
      <span class="text-muted small">Total: {{ $paginacion->total() }}</span>
    @endif
  </div>
  <div class="card-body p-0 table-responsive">
    <table class="table table-hover mb-0">
      <thead class="thead-light">
        <tr>
          <th>Fecha envío</th>
          <th>Paciente</th>
          <th>Doctor</th>
          <th>Estado cita</th>
          <th>Tipo</th>
          <th>Mensaje</th>
        </tr>
      </thead>
      <tbody>
        @forelse($notificaciones as $n)
          <tr class="{{ $n->LEIDA ? '' : 'table-warning' }}">
            <td>
              <div class="d-flex align-items-center">
                @if(!$n->LEIDA)
                  <i class="fas fa-circle text-warning mr-2" title="Nueva"></i>
                @endif
                <span>{{ optional($n->FEC_ENVIO ? \Carbon\Carbon::parse($n->FEC_ENVIO) : null)?->format('d/m/Y H:i') }}</span>
              </div>
            </td>
            <td>{{ $n->paciente_nombre ?? 'N/D' }}</td>
            <td>{{ $n->doctor_nombre ?? 'N/D' }}</td>
            <td><span class="badge badge-info">{{ $n->estado_cita_nombre ?? 'N/D' }}</span></td>
            <td><span class="badge badge-secondary">{{ $tipos[$n->TIPO_NOTIFICACION] ?? $n->TIPO_NOTIFICACION }}</span></td>
            <td>{{ $n->MSG_NOTIFICACION }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted py-4">No hay notificaciones para mostrar.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if(isset($paginacion) && $paginacion)
    <div class="card-footer clearfix">
      {{ $paginacion->links() }}
    </div>
  @endif
</div>
@endsection
