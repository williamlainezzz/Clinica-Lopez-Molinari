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
                <i class="fas fa-plus-circle"></i> Agendar
            </button>
            <button class="btn btn-outline-secondary">
                <i class="fas fa-print"></i> Imprimir listado
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

    @php
        // Aseguramos que siempre tengamos arreglos para evitar errores
        $doctorPanelsSafe    = is_iterable($doctorPanels ?? []) ? $doctorPanels : [];
        $availablePatientsSafe = is_iterable($availablePatients ?? []) ? $availablePatients : [];
    @endphp

    <div class="row">
        {{-- PANEL PRINCIPAL: doctores y sus citas --}}
        <div class="col-xl-8">
            @forelse($doctorPanelsSafe as $doctor)
                @php
                    $doctorNombre       = $doctor['nombre']       ?? 'Doctor sin nombre';
                    $doctorEspecialidad = $doctor['especialidad'] ?? 'Odontología';
                    $doctorContacto     = $doctor['contacto']     ?? '';
                    $citasDoctor        = $doctor['pacientes']    ?? [];
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
                        <div class="text-muted small mt-2 mt-md-0">
                            Panel de citas del doctor
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
                                        <th style="width: 230px;">Acciones</th>
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

                                            $estadoLabel = match ($estadoUpper) {
                                                'CONFIRMADA' => 'Confirmada',
                                                'PENDIENTE'  => 'Pendiente',
                                                'CANCELADA'  => 'Cancelada',
                                                default      => ($estadoRaw !== '' ? $estadoRaw : 'Sin estado'),
                                            };

                                            // Soporta tanto ['cita_id'] como ['id']
                                            $citaId = $paciente['cita_id'] ?? ($paciente['id'] ?? null);
                                            $fecha  = $paciente['fecha']    ?? '';
                                            $hora   = $paciente['hora']     ?? '';
                                            $nota   = $paciente['nota']     ?? '';
                                        @endphp
                                        <tr>
                                            <td class="font-weight-bold">{{ $paciente['nombre'] ?? 'Paciente sin nombre' }}</td>
                                            <td>{{ $paciente['motivo'] ?? '' }}</td>
                                            <td>{{ $fecha }} @if($hora) · {{ $hora }} @endif</td>
                                            <td>
                                                <span class="badge badge-{{ $badge }}">{{ $estadoLabel }}</span>
                                            </td>
                                            <td class="text-muted">{{ $nota }}</td>
                                            <td>
                                                @if($citaId)
                                                    <div class="btn-group btn-group-sm">
                                                        {{-- Confirmar --}}
                                                        <form method="POST"
                                                              action="{{ route('agenda.citas.confirmar', $citaId) }}"
                                                              class="d-inline">
                                                            @csrf
                                                            <button type="submit"
                                                                    class="btn btn-outline-success"
                                                                    @if($estadoUpper === 'CONFIRMADA') disabled @endif>
                                                                Confirmar
                                                            </button>
                                                        </form>

                                                        {{-- Cancelar --}}
                                                        <form method="POST"
                                                              action="{{ route('agenda.citas.cancelar', $citaId) }}"
                                                              class="d-inline">
                                                            @csrf
                                                            <button type="submit"
                                                                    class="btn btn-outline-danger"
                                                                    @if($estadoUpper === 'CANCELADA') disabled @endif>
                                                                Cancelar
                                                            </button>
                                                        </form>

                                                        {{-- Reprogramar (abre modal) --}}
                                                        <button type="button"
                                                                class="btn btn-outline-warning text-dark btn-open-reprogramar"
                                                                data-toggle="modal"
                                                                data-target="#modalReprogramar"
                                                                data-cita-id="{{ $citaId }}"
                                                                data-fecha="{{ $fecha }}"
                                                                data-hora="{{ $hora }}">
                                                            Reprogramar
                                                        </button>
                                                    </div>
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
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center text-muted">
                        No se encontraron doctores con citas en el sistema.
                    </div>
                </div>
            @endforelse
        </div>

        {{-- PANEL LATERAL: solicitudes en espera + recordatorios --}}
        <div class="col-xl-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h3 class="h6 mb-0">Solicitudes en espera</h3>
                </div>
                <div class="card-body">
                    @forelse($availablePatientsSafe as $patient)
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="mb-1">{{ $patient['nombre'] ?? 'Paciente' }}</h5>
                                <p class="text-muted mb-0 small">
                                    {{ $patient['motivo'] ?? 'Motivo no especificado' }}
                                </p>
                                @if(!empty($patient['preferencia']))
                                    <small class="text-muted">
                                        Preferencia: {{ $patient['preferencia'] }}
                                    </small>
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
                        <p class="text-muted mb-0">
                            No hay pacientes en espera de asignación.
                        </p>
                    @endforelse
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted">Recordatorios</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <i class="fas fa-phone text-success mr-2"></i>
                            Llamar a pacientes pendientes
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-envelope text-primary mr-2"></i>
                            Enviar correos de confirmación
                        </li>
                        <li>
                            <i class="fas fa-file-export text-info mr-2"></i>
                            Compartir agenda con administración
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Nueva cita (por ahora solo diseño, lógica luego) --}}
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
                            <label>Paciente</label>
                            <input type="text" class="form-control" placeholder="Nombre completo">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Doctor</label>
                            <select class="form-control">
                                @foreach($doctorPanelsSafe as $doctor)
                                    <option>{{ $doctor['nombre'] ?? 'Doctor' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Fecha</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Hora</label>
                            <input type="time" class="form-control">
                        </div>
                        <div class="form-group col-12 mb-0">
                            <label>Motivo</label>
                            <textarea class="form-control" rows="2" placeholder="Motivo de la cita"></textarea>
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
                        Ajusta la nueva fecha y hora para la cita seleccionada.
                        El estado se dejará como <strong>PENDIENTE</strong>.
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
