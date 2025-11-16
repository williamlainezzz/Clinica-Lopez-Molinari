@extends('adminlte::page')

@section('title', $pageTitle)

@section('content_header')
    <div class="d-flex flex-wrap justify-content-between align-items-start">
        <div class="mb-2">
            <h1 class="h3 font-weight-bold text-primary mb-1">{{ $heading }}</h1>
            <p class="text-muted mb-0">{{ $intro }}</p>
        </div>
        <div class="btn-group mt-2 mt-md-0">
            <button class="btn btn-primary"><i class="fas fa-user-plus"></i> Registrar paciente</button>
            <button class="btn btn-outline-secondary"><i class="fas fa-qrcode"></i> Generar QR</button>
        </div>
    </div>
@endsection

@section('content')
    <div class="row mb-4">
        @foreach($stats as $stat)
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm h-100">
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
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="h5 mb-0">Pacientes asignados</h3>
                        <small class="text-muted">Gestiona citas y notas clínicas</small>
                    </div>
                    <button class="btn btn-sm btn-outline-primary">Agregar cita rápida</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Paciente</th>
                                <th>Motivo</th>
                                <th>Próxima cita</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(($activeDoctor['pacientes'] ?? []) as $paciente)
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

                                    // Soporta tanto ['cita_id'] como ['id'] según venga del controlador
                                    $citaId = $paciente['cita_id'] ?? ($paciente['id'] ?? null);
                                    $fecha  = $paciente['fecha']    ?? '';
                                    $hora   = $paciente['hora']     ?? '';
                                @endphp
                                <tr>
                                    <td class="font-weight-bold">{{ $paciente['nombre'] ?? 'Paciente sin nombre' }}</td>
                                    <td>{{ $paciente['motivo'] ?? '' }}</td>
                                    <td>{{ $fecha }} @if($hora) · {{ $hora }} @endif</td>
                                    <td><span class="badge badge-{{ $badge }}">{{ $estadoLabel }}</span></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-info">Ver ficha</button>

                                            @if($citaId)
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
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h3 class="h6 mb-0">Notas rápidas</h3>
                </div>
                <div class="card-body">
                    <form>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Paciente</label>
                                <select class="form-control">
                                    @foreach($activeDoctor['pacientes'] as $paciente)
                                        <option>{{ $paciente['nombre'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Tipo de nota</label>
                                <select class="form-control">
                                    <option>Seguimiento</option>
                                    <option>Recordatorio</option>
                                    <option>Indicaciones</option>
                                    <option>Laboratorio</option>
                                </select>
                            </div>
                            <div class="form-group col-12">
                                <label>Nota</label>
                                <textarea class="form-control" rows="2" placeholder="Observaciones clínicas"></textarea>
                            </div>
                        </div>
                        <button class="btn btn-primary">Guardar</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body d-flex align-items-center">
                    <div class="mr-3">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fas fa-user-md"></i>
                        </div>
                    </div>
                    <div>
                        <h5 class="mb-1">{{ $activeDoctor['nombre'] }}</h5>
                        <p class="mb-0 text-muted">{{ $activeDoctor['especialidad'] }}</p>
                        <small class="text-muted">{{ $activeDoctor['contacto'] }}</small>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h3 class="h6 mb-0">Ficha del paciente</h3>
                </div>
                <div class="card-body">
                    <h5 class="mb-1">{{ $patientRecord['profile']['nombre'] }}</h5>
                    <p class="text-muted mb-1">
                        <i class="fas fa-user-md mr-1"></i> {{ $patientRecord['profile']['doctor'] }}
                    </p>
                    <p class="text-muted mb-2">
                        <i class="fas fa-calendar-alt mr-1"></i>
                        Próxima cita:
                        {{ $patientRecord['profile']['proxima']['fecha'] }}
                        ·
                        {{ $patientRecord['profile']['proxima']['hora'] }}
                    </p>
                    <p class="mb-0">
                        <span class="badge badge-info">{{ $patientRecord['profile']['proxima']['estado'] }}</span>
                    </p>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h3 class="h6 mb-0">Compartir formulario</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">Comparte el enlace para que un paciente se registre y quede asignado automáticamente.</p>
                    <div class="bg-light p-3 rounded mb-2">
                        <small class="text-uppercase text-muted">Link</small>
                        <p class="mb-0">{{ $shareLink }}</p>
                    </div>
                    <div class="bg-light p-3 rounded mb-3">
                        <small class="text-uppercase text-muted">Código QR</small>
                        <p class="mb-0">{{ $shareCode }}</p>
                    </div>
                    <button class="btn btn-outline-primary btn-block">Copiar enlace</button>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h3 class="h6 mb-0">Pacientes en espera</h3>
                </div>
                <div class="card-body">
                    @foreach($availablePatients as $patient)
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="mb-1">{{ $patient['nombre'] }}</h5>
                                <p class="mb-1 text-muted">{{ $patient['motivo'] }}</p>
                                <small class="text-muted">Preferencia: {{ $patient['preferencia'] }}</small>
                                <div class="mt-2">
                                    <button class="btn btn-sm btn-outline-success">Asignar</button>
                                </div>
                            </div>
                            @if(!$loop->last)
                                <hr>
                            @endif
                        </div>
                    @endforeach
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
