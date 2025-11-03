@extends('adminlte::page')

@section('title', $pageTitle ?? 'Agenda')

@section('content_header')
    <h1>{{ $heading ?? 'Agenda' }}</h1>
@endsection

@section('content')

    {{-- Banner por rol/section --}}
    @includeIf($bannerPartial)

    <div class="card">
        <div class="card-header">
            {{-- Toolbar por rol/section --}}
            <div class="mb-3">
                @includeIf($toolbarPartial)
            </div>

            {{-- Filtros (GET) --}}
            <form method="GET" action="{{ route($routeName) }}">
                <div class="row">
                    <div class="col-md-3">
                        <label class="mb-1">Desde</label>
                        <input type="date" class="form-control"
                               name="desde" value="{{ $filters['desde'] }}">
                    </div>
                    <div class="col-md-3">
                        <label class="mb-1">Hasta</label>
                        <input type="date" class="form-control"
                               name="hasta" value="{{ $filters['hasta'] }}">
                    </div>
                    <div class="col-md-3">
                        <label class="mb-1">Estado</label>
                        <select class="form-control" name="estado">
                            <option value="">Todos</option>
                            @foreach(($catalogoEstados ?? []) as $est)
                                <option value="{{ $est }}" {{ (isset($filters['estado']) && $filters['estado']===$est) ? 'selected' : '' }}>
                                    {{ $est }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="mb-1">Doctor</label>
                        <select class="form-control" name="doctor">
                            <option value="">Todos</option>
                            @foreach(($catalogoDoctores ?? []) as $doc)
                                <option value="{{ $doc }}" {{ (isset($filters['doctor']) && $filters['doctor']===$doc) ? 'selected' : '' }}>
                                    {{ $doc }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-3">
                    <button class="btn btn-primary">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="{{ route($routeName) }}" class="btn btn-secondary">
                        Limpiar
                    </a>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Paciente</th>
                            @if($showDoctorColumn)
                                <th>Doctor</th>
                            @endif
                            <th>Estado</th>
                            <th>Motivo</th>
                            @if($showActions)
                                <th style="width: 160px;">Acciones</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $row)
                            @php
                                $id = $loop->index + 1; // ID temporal (stub)
                                $estado = $row['estado'];
                                $badge = match ($estado) {
                                    'Confirmada' => 'success',
                                    'Pendiente'  => 'warning',
                                    'Cancelada'  => 'danger',
                                    default      => 'secondary',
                                };
                            @endphp
                            <tr>
                                <td>{{ $row['fecha'] }}</td>
                                <td>{{ $row['hora'] }}</td>
                                <td>{{ $row['paciente'] }}</td>

                                @if($showDoctorColumn)
                                    <td>{{ $row['doctor'] }}</td>
                                @endif

                                <td><span class="badge badge-{{ $badge }}">{{ $estado }}</span></td>
                                <td>{{ $row['motivo'] }}</td>

                                @if($showActions)
                                    <td class="text-nowrap">
                                        @switch($rol)
                                            @case('ADMIN')
                                            @case('RECEPCIONISTA')
                                                <a  href="{{ route('citas.show', $id) }}"
                                                    class="btn btn-xs btn-info"
                                                    title="Ver"
                                                    data-toggle="modal" data-target="#modalVer"
                                                    data-id="{{ $id }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <a  href="{{ route('citas.reprogramar.form', $id) }}"
                                                    class="btn btn-xs btn-warning"
                                                    title="Reprogramar"
                                                    data-toggle="modal" data-target="#modalReprogramar"
                                                    data-id="{{ $id }}">
                                                    <i class="fas fa-sync"></i>
                                                </a>

                                                <button type="button"
                                                    class="btn btn-xs btn-danger btn-cancelar"
                                                    title="Cancelar"
                                                    data-toggle="modal" data-target="#modalCancelar"
                                                    data-action="{{ route('citas.cancelar', $id) }}"
                                                    data-id="{{ $id }}">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                @break

                                            @case('DOCTOR')
                                                <a  href="{{ route('citas.show', $id) }}"
                                                    class="btn btn-xs btn-info"
                                                    title="Ver"
                                                    data-toggle="modal" data-target="#modalVer"
                                                    data-id="{{ $id }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <a  href="{{ route('citas.reprogramar.form', $id) }}"
                                                    class="btn btn-xs btn-warning"
                                                    title="Reprogramar"
                                                    data-toggle="modal" data-target="#modalReprogramar"
                                                    data-id="{{ $id }}">
                                                    <i class="fas fa-sync"></i>
                                                </a>

                                                <button type="button"
                                                    class="btn btn-xs btn-danger btn-cancelar"
                                                    title="Cancelar"
                                                    data-toggle="modal" data-target="#modalCancelar"
                                                    data-action="{{ route('citas.cancelar', $id) }}"
                                                    data-id="{{ $id }}">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                @break
                                        @endswitch
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 6 + ($showDoctorColumn?1:0) + ($showActions?1:0) }}" class="text-center text-muted">
                                    Sin resultados con los filtros actuales.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer">
            <small class="text-muted">
                (Demo) Estos datos son de prueba. Conectaremos a la BD en el siguiente bloque.
            </small>
        </div>
    </div>

    {{-- Modal: Ver --}}
    <div class="modal fade" id="modalVer" tabindex="-1" role="dialog" aria-labelledby="modalVerLabel" aria-hidden="true">
      <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
          <div class="modal-header bg-info">
            <h5 class="modal-title" id="modalVerLabel">Detalle de Cita (stub)</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            Cita <span id="ver-cita-id" class="font-weight-bold">#</span> — Aquí mostraremos los datos reales cuando conectemos a la BD.
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>

    {{-- Modal: Reprogramar --}}
    <div class="modal fade" id="modalReprogramar" tabindex="-1" role="dialog" aria-labelledby="modalReprogramarLabel" aria-hidden="true">
      <div class="modal-dialog modal-md" role="document">
        <form id="formReprogramar" method="POST">
          @csrf
          @method('PUT')
          <div class="modal-content">
            <div class="modal-header bg-warning">
              <h5 class="modal-title" id="modalReprogramarLabel">Reprogramar Cita (stub)</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              Cita <span id="repro-cita-id" class="font-weight-bold">#</span>
              <div class="form-group mt-3">
                <label>Nueva fecha</label>
                <input type="date" name="nueva_fecha" class="form-control">
              </div>
              <div class="form-group">
                <label>Nueva hora</label>
                <input type="time" name="nueva_hora" class="form-control">
              </div>
              <small class="text-muted">(Demo) Este formulario aún no guarda en BD.</small>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-warning">Guardar</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
          </div>
        </form>
      </div>
    </div>

    {{-- Modal: Cancelar --}}
    <div class="modal fade" id="modalCancelar" tabindex="-1" role="dialog" aria-labelledby="modalCancelarLabel" aria-hidden="true">
      <div class="modal-dialog modal-sm" role="document">
        <form id="formCancelar" method="POST">
          @csrf
          @method('DELETE')
          <div class="modal-content">
            <div class="modal-header bg-danger">
              <h5 class="modal-title" id="modalCancelarLabel">Cancelar Cita (stub)</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              ¿Seguro que deseas cancelar la cita <span id="cancel-cita-id" class="font-weight-bold">#</span>?
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-danger">Sí, cancelar</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
            </div>
          </div>
        </form>
      </div>
    </div>

@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // VER
    $('#modalVer').on('show.bs.modal', function (event) {
        const btn = $(event.relatedTarget);
        const id  = btn.data('id') || '#';
        $('#ver-cita-id').text('#' + id);
    });

    // REPROGRAMAR
    $('#modalReprogramar').on('show.bs.modal', function (event) {
        const btn = $(event.relatedTarget);
        const id  = btn.data('id') || '#';
        $('#repro-cita-id').text('#' + id);
        // Acción del form (PUT)
        const action = btn.attr('href') || '{{ url('/agenda/citas') }}/' + id + '/reprogramar';
        $('#formReprogramar').attr('action', action);
    });

    // CANCELAR
    $('#modalCancelar').on('show.bs.modal', function (event) {
        const btn = $(event.relatedTarget);
        const id  = btn.data('id') || '#';
        $('#cancel-cita-id').text('#' + id);
        const action = btn.data('action') || '{{ url('/agenda/citas') }}/' + id;
        $('#formCancelar').attr('action', action);
    });
});
</script>
@endpush
