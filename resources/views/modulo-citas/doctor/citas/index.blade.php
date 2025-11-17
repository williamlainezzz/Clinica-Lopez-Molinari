@extends('adminlte::page')

@section('title', $pageTitle)

@section('content_header')
    <div class="d-flex flex-wrap justify-content-between align-items-start">
        <div class="mb-2">
            <h1 class="h3 font-weight-bold text-primary mb-1">{{ $heading }}</h1>
            <p class="text-muted mb-0">{{ $intro }}</p>
        </div>
        <div class="btn-group mt-2 mt-md-0">
            {{-- Botón para enviar al paciente al formulario de registro (link dinámico) --}}
            @if(!empty($shareLink))
                <a href="{{ $shareLink }}" target="_blank" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Registrar paciente
                </a>
            @else
                <button class="btn btn-primary" type="button" disabled>
                    <i class="fas fa-user-plus"></i> Registrar paciente
                </button>
            @endif

            {{-- Botón placeholder para QR (luego conectamos con el generador real) --}}
            <button class="btn btn-outline-secondary" type="button">
                <i class="fas fa-qrcode"></i> Generar QR
            </button>
        </div>
    </div>
@endsection

@section('content')
    {{-- Tarjetas de resumen --}}
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

    @php
        // Por seguridad, si no hay doctor activo o no tiene pacientes, usamos arreglo vacío
        $citasPacientes = is_array($activeDoctor ?? null)
            ? ($activeDoctor['pacientes'] ?? [])
            : [];
    @endphp

    <div class="row">
        {{-- PANEL PRINCIPAL: Pacientes asignados --}}
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
                            @forelse($citasPacientes as $paciente)
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
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        No tienes pacientes con citas registradas todavía.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- NOTAS RÁPIDAS (solo si hay pacientes) --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h3 class="h6 mb-0">Notas rápidas</h3>
                </div>
                <div class="card-body">
                    @if(count($citasPacientes) === 0)
                        <p class="text-muted mb-0">
                            Aún no hay pacientes con citas; primero registra o asigna pacientes.
                        </p>
                    @else
                        <form>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Paciente</label>
                                    <select class="form-control">
                                        @foreach($citasPacientes as $paciente)
                                            <option>{{ $paciente['nombre'] ?? 'Paciente' }}</option>
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
                    @endif
                </div>
            </div>
        </div>

        {{-- PANEL LATERAL: Doctor, ficha del paciente y pacientes en espera --}}
        <div class="col-lg-4 mb-4">
            @php
                $doctorNombre       = data_get($activeDoctor ?? [], 'nombre', 'Doctor sin asignar');
                $doctorEspecialidad = data_get($activeDoctor ?? [], 'especialidad', 'Odontología');
                $doctorContacto     = data_get($activeDoctor ?? [], 'contacto', '');
                $profile            = data_get($patientRecord ?? [], 'profile', []);
                $proxima            = data_get($profile, 'proxima', []);
            @endphp

            {{-- Tarjeta doctor --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body d-flex align-items-center">
                    <div class="mr-3">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fas fa-user-md"></i>
                        </div>
                    </div>
                    <div>
                        <h5 class="mb-1">{{ $doctorNombre }}</h5>
                        <p class="mb-0 text-muted">{{ $doctorEspecialidad }}</p>
                        @if($doctorContacto)
                            <small class="text-muted">{{ $doctorContacto }}</small>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Ficha del paciente destacado --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h3 class="h6 mb-0">Ficha del paciente</h3>
                </div>
                <div class="card-body">
                    <h5 class="mb-1">{{ $profile['nombre'] ?? 'Sin pacientes seleccionados' }}</h5>

                    @if(!empty($profile))
                        <p class="text-muted mb-1">
                            <i class="fas fa-user-md mr-1"></i>
                            {{ $profile['doctor'] ?? $doctorNombre }}
                        </p>
                        <p class="text-muted mb-2">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            Próxima cita:
                            {{ $proxima['fecha'] ?? '—' }}
                            ·
                            {{ $proxima['hora'] ?? '—' }}
                        </p>
                        <p class="mb-0">
                            <span class="badge badge-info">
                                {{ $proxima['estado'] ?? 'Sin estado' }}
                            </span>
                        </p>
                    @else
                        <p class="text-muted mb-0">
                            Aún no se ha destacado ningún paciente.
                        </p>
                    @endif
                </div>
            </div>

            {{-- Compartir formulario --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h3 class="h6 mb-0">Compartir formulario</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        Comparte el enlace para que un paciente se registre y quede asignado automáticamente.
                    </p>
                    <div class="bg-light p-3 rounded mb-2">
                        <small class="text-uppercase text-muted">Link</small>
                        <p class="mb-0">{{ $shareLink }}</p>
                    </div>
                    <div class="bg-light p-3 rounded mb-3">
                        <small class="text-uppercase text-muted">Código QR</small>
                        <p class="mb-0">{{ $shareCode }}</p>
                    </div>
                    <button class="btn btn-outline-primary btn-block" type="button">
                        Copiar enlace
                    </button>
                </div>
            </div>

            {{-- Pacientes en espera (SIN doctor) --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h3 class="h6 mb-0">Pacientes en espera</h3>
                </div>
                <div class="card-body">
                    @forelse($availablePatients as $patient)
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="mb-1">{{ $patient['nombre'] ?? 'Paciente' }}</h5>
                                <p class="mb-1 text-muted">
                                    {{ $patient['motivo'] ?? 'Motivo no especificado' }}
                                </p>
                                @if(!empty($patient['preferencia']))
                                    <small class="text-muted">
                                        Preferencia: {{ $patient['preferencia'] }}
                                    </small>
                                @endif
                                <div class="mt-2">
                                    @if(!empty($patient['persona_id']))
                                        <form method="POST"
                                              action="{{ route('agenda.pacientes.asignar', $patient['persona_id']) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success">
                                                Asignar
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted small">Sin ID de paciente</span>
                                    @endif
                                </div>
                            </div>
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
