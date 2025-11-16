@extends('adminlte::page')

@section('title', $pageTitle)

@section('content_header')
    <div class="d-flex flex-wrap justify-content-between align-items-start">
        <div class="mb-2">
            <h1 class="h3 font-weight-bold text-primary mb-1">{{ ucfirst($heading) }}</h1>
            <p class="text-muted mb-0">{{ $intro }}</p>
        </div>
        <div class="btn-group mt-2 mt-md-0">
            <button class="btn btn-primary" data-toggle="modal" data-target="#modalNuevaCita">
                <i class="fas fa-plus-circle"></i> Nueva cita
            </button>
            <button class="btn btn-outline-secondary">
                <i class="fas fa-file-export"></i> Exportar agenda
            </button>
        </div>
    </div>
@endsection

@section('content')
    {{-- Tarjetas de resumen --}}
    <div class="row">
        @foreach($stats as $stat)
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex align-items-center">
                        <span class="badge badge-{{ $stat['color'] }} mr-3 p-3 rounded-circle text-white">
                            <i class="{{ $stat['icon'] }}"></i>
                        </span>
                        <div>
                            <p class="text-muted text-uppercase small mb-1">{{ $stat['label'] }}</p>
                            <h3 class="mb-0 font-weight-bold">{{ $stat['value'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row">
        {{-- Panel principal: doctores y sus citas --}}
        <div class="col-xl-8">
            @forelse($doctorPanels as $doctor)
                @php
                    $doctorNombre      = $doctor['nombre']        ?? 'Doctor sin nombre';
                    $doctorEspecialidad= $doctor['especialidad']  ?? 'Odontología';
                    $doctorContacto    = $doctor['contacto']      ?? '';
                    $citasDoctor       = $doctor['pacientes']     ?? [];
                @endphp

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h3 class="h5 mb-1">{{ $doctorNombre }}</h3>
                            <span class="text-muted">
                                {{ $doctorEspecialidad }}
                                @if($doctorContacto)
                                    · {{ $doctorContacto }}
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Paciente</th>
                                        <th>Motivo</th>
                                        <th>Fecha</th>
                                        <th>Estado</th>
                                        <th>Notas</th>
                                        <th style="width: 210px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($citasDoctor as $paciente)
                                        @php
                                            $estadoRaw   = $paciente['estado'] ?? '';
                                            $estadoUpper = strtoupper(trim($estadoRaw));

                                            $badge = match ($estadoUpper) {
                                                'CONFIRMADA' => 'success',
                                                'PENDIENTE'  => 'warning',
                                                'CANCELADA'  => 'danger',
                                                default      => 'secondary',
                                            };

                                            $estadoLabel = $estadoRaw !== '' ? $estadoRaw : 'SIN ESTADO';
                                            $citaId      = $paciente['id']    ?? null;
                                            $fecha       = $paciente['fecha'] ?? '';
                                            $hora        = $paciente['hora']  ?? '';
                                            $nota        = $paciente['nota']  ?? '';
                                        @endphp

                                        <tr>
                                            <td class="font-weight-bold">
                                                {{ $paciente['nombre'] ?? 'Paciente sin nombre' }}
                                            </td>
                                            <td>{{ $paciente['motivo'] ?? '' }}</td>
                                            <td>{{ $fecha }} @if($hora) · {{ $hora }} @endif</td>

                                            <td>
                                                <span class="badge badge-{{ $badge }}">
                                                    {{ $estadoLabel }}
                                                </span>
                                            </td>

                                            <td class="text-muted">
                                                {{ $nota }}
                                            </td>

                                            <td>
                                                @if($citaId)
                                                    {{-- Confirmar --}}
                                                    <form method="POST"
                                                          action="{{ route('agenda.citas.confirmar', $citaId) }}"
                                                          class="d-inline">
                                                        @csrf
                                                        <button type="submit"
                                                                class="btn btn-sm btn-success mb-1"
                                                                @if($estadoUpper === 'CONFIRMADA') disabled @endif>
                                                            <i class="fas fa-check"></i> Confirmar
                                                        </button>
                                                    </form>

                                                    {{-- Cancelar --}}
                                                    <form method="POST"
                                                          action="{{ route('agenda.citas.cancelar', $citaId) }}"
                                                          class="d-inline">
                                                        @csrf
                                                        <button type="submit"
                                                                class="btn btn-sm btn-danger mb-1"
                                                                @if($estadoUpper === 'CANCELADA') disabled @endif>
                                                            <i class="fas fa-times"></i> Cancelar
                                                        </button>
                                                    </form>

                                                    {{-- Reprogramar (abre modal) --}}
                                                    <button type="button"
                                                            class="btn btn-sm btn-warning mb-1 text-white btn-open-reprogramar"
                                                            data-toggle="modal"
                                                            data-target="#modalReprogramar"
                                                            data-cita-id="{{ $citaId }}"
                                                            data-fecha="{{ $fecha }}"
                                                            data-hora="{{ $hora }}">
                                                        <i class="fas fa-sync"></i> Reprogramar
                                                    </button>
                                                @else
                                                    <span class="text-muted small">Sin ID</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">
                                                No hay citas registradas para este doctor.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-info">
                    No se encontraron citas en el sistema.
                </div>
            @endforelse
        </div>

        {{-- Panel lateral: pacientes sin doctor y workflow rápido --}}
        <div class="col-xl-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h3 class="h6 mb-0">Pacientes sin doctor asignado</h3>
                </div>
                <div class="card-body">
                    @forelse($availablePatients as $patient)
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="mb-1">{{ $patient['nombre'] ?? 'Paciente' }}</h5>
                                <p class="text-muted mb-0 small">
                                    {{ $patient['motivo'] ?? 'Motivo no especificado' }}
                                    @if(!empty($patient['preferencia']))
                                        · Preferencia: {{ $patient['preferencia'] }}
                                    @endif
                                </p>
                                @if(!empty($patient['ultima']))
                                    <small class="text-muted">Actualizado {{ $patient['ultima'] }}</small>
                                @endif
                            </div>
                            <button class="btn btn-sm btn-outline-primary">
                                Asignar
                            </button>
                        </div>
                        @if(!$loop->last)
                            <hr>
                        @endif
                    @empty
                        <p class="text-muted mb-0">No hay pacientes en espera de asignación.</p>
                    @endforelse
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted">Workflow rápido</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <i class="fas fa-check-circle text-success mr-2"></i>
                            Confirmar citas de mañana
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-envelope text-primary mr-2"></i>
                            Enviar recordatorios SMS
                        </li>
                        <li>
                            <i class="fas fa-file-medical text-info mr-2"></i>
                            Revisar solicitudes de nuevos pacientes
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Nueva cita (de momento solo diseño; la lógica la podemos hacer luego) --}}
    <div class="modal fade" id="modalNuevaCita" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crear cita</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="form-label">Paciente</label>
                            <input type="text" class="form-control" placeholder="Nombre completo">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">Doctor</label>
                            <select class="form-control">
                                @foreach($doctorPanels as $doctor)
                                    <option>{{ $doctor['nombre'] ?? 'Doctor' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">Fecha</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">Hora</label>
                            <input type="time" class="form-control">
                        </div>
                        <div class="form-group col-12 mb-0">
                            <label class="form-label">Motivo</label>
                            <textarea class="form-control" rows="2" placeholder="Detalle del procedimiento"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Reprogramar cita --}}
    <div class="modal fade" id="modalReprogramar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" id="formReprogramar" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reprogramar cita</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">
                        Ajusta la nueva fecha y hora para la cita seleccionada. El estado se dejará como
                        <strong>PENDIENTE</strong>.
                    </p>

                    <div class="form-group">
                        <label for="reprog_fecha">Fecha</label>
                        <input type="date" name="fecha" id="reprog_fecha" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="reprog_hora_inicio">Hora inicio</label>
                        <input type="time" name="hora_inicio" id="reprog_hora_inicio" class="form-control" required>
                    </div>

                    <div class="form-group mb-0">
                        <label for="reprog_hora_fin">Hora fin (opcional)</label>
                        <input type="time" name="hora_fin" id="reprog_hora_fin" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cerrar</button>
                    <button class="btn btn-primary" type="submit">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
<script>
    // Cuando se abre el modal de reprogramación, cargamos datos de la cita
    $('#modalReprogramar').on('show.bs.modal', function (event) {
        var button   = $(event.relatedTarget); // Botón que abrió el modal
        var citaId   = button.data('cita-id');
        var fecha    = button.data('fecha') || '';
        var hora     = button.data('hora')  || '';

        var modal    = $(this);
        var form     = modal.find('#formReprogramar');

        // Ruta base /agenda/citas
        var baseUrl  = "{{ url('/agenda/citas') }}";
        form.attr('action', baseUrl + '/' + citaId + '/reprogramar');

        // Rellenar campos
        modal.find('#reprog_fecha').val(fecha);
        modal.find('#reprog_hora_inicio').val(hora);
        modal.find('#reprog_hora_fin').val('');
    });
</script>
@endsection
