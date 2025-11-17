@extends('adminlte::page')

@section('title', $pageTitle)

@section('content_header')
    <div class="d-flex flex-wrap justify-content-between align-items-start">
        <div class="mb-2">
            <h1 class="h3 font-weight-bold text-primary mb-1">{{ $heading }}</h1>
            <p class="text-muted mb-0">{{ $intro }}</p>
        </div>
        <div class="btn-group mt-2 mt-md-0">
            <button class="btn btn-outline-primary" data-toggle="modal" data-target="#modalMisPacientes">
                <i class="fas fa-users"></i> Ver mis pacientes
            </button>
            <button class="btn btn-primary" data-toggle="modal" data-target="#modalCrearCitaDoctor">
                <i class="fas fa-plus"></i> Crear cita
            </button>
            <button class="btn btn-outline-secondary" data-toggle="modal" data-target="#modalCodigoRegistro">
                <i class="fas fa-qrcode"></i> Registrar paciente
            </button>
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

    @php
        $citasPacientes = is_array($activeDoctor ?? null) ? ($activeDoctor['pacientes'] ?? []) : [];
        $doctorPersonaId = $activeDoctor['doctor_persona_id'] ?? (auth()->user()->FK_COD_PERSONA ?? null);

        $assignedPatients = collect($doctorPatientMap[$doctorPersonaId]['patients'] ?? []);

        if ($assignedPatients->isEmpty()) {
            $assignedPatients = collect($citasPacientes)
                ->map(function ($paciente) {
                    return [
                        'persona_id' => $paciente['persona_id'] ?? null,
                        'nombre'     => $paciente['nombre'] ?? 'Paciente',
                        'codigo'     => $paciente['codigo'] ?? ($paciente['persona_id'] ?? null ? 'PAC-' . str_pad($paciente['persona_id'], 4, '0', STR_PAD_LEFT) : 'PAC-0000'),
                    ];
                })
                ->filter(fn ($paciente) => !empty($paciente['persona_id']))
                ->unique('persona_id')
                ->values();
        }

        $assignedPatients = $assignedPatients->values();
        $misPacientesAsignados = $assignedPatients;
    @endphp

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="h5 mb-0">Mis pacientes</h3>
                        <small class="text-muted">Gestiona citas, confirma o adopta pacientes.</small>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Paciente</th>
                                <th>Motivo</th>
                                <th>Próxima cita</th>
                                <th>Estado</th>
                                <th class="text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($citasPacientes as $paciente)
                                @php
                                    $estadoRaw   = strtoupper($paciente['estado'] ?? '');
                                    $badge       = match ($estadoRaw) {
                                        'CONFIRMADA' => 'success',
                                        'CANCELADA'  => 'danger',
                                        'PENDIENTE'  => 'warning',
                                        default      => 'secondary',
                                    };
                                    $estadoLabel = match ($estadoRaw) {
                                        'CONFIRMADA' => 'Confirmada',
                                        'CANCELADA'  => 'Cancelada',
                                        'PENDIENTE'  => 'Pendiente',
                                        default      => ($paciente['estado'] ?? 'Sin estado'),
                                    };
                                    $citaId = $paciente['cita_id'] ?? ($paciente['id'] ?? null);
                                @endphp
                                <tr>
                                    <td class="font-weight-bold">
                                        {{ $paciente['nombre'] ?? 'Paciente' }}
                                    </td>
                                    <td>{{ $paciente['motivo'] ?? 'N/D' }}</td>
                                    <td>{{ $paciente['fecha'] ?? '' }} @if(!empty($paciente['hora'])) · {{ $paciente['hora'] }} @endif</td>
                                    <td>
                                        <span class="badge badge-{{ $badge }}">{{ $estadoLabel }}</span>
                                    </td>
                                    <td class="text-right">
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-info btn-detalle-paciente"
                                                    data-toggle="modal"
                                                    data-target="#modalDetallePaciente"
                                                    data-paciente='@json($paciente)'>
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-primary btn-crear-cita"
                                                    data-toggle="modal"
                                                    data-target="#modalCrearCitaDoctor"
                                                    data-paciente="{{ $paciente['persona_id'] ?? '' }}">
                                                <i class="fas fa-calendar-plus"></i>
                                            </button>
                                            @if($citaId)
                                                <form method="POST" action="{{ route('agenda.citas.confirmar', $citaId) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-success"
                                                            @if($estadoRaw === 'CONFIRMADA') disabled @endif>
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('agenda.citas.cancelar', $citaId) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-danger"
                                                            @if($estadoRaw === 'CANCELADA') disabled @endif>
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                                <button type="button" class="btn btn-outline-warning text-dark btn-open-reprogramar"
                                                        data-toggle="modal"
                                                        data-target="#modalReprogramar"
                                                        data-cita-id="{{ $citaId }}"
                                                        data-fecha="{{ $paciente['fecha'] ?? '' }}"
                                                        data-hora="{{ $paciente['hora'] ?? '' }}">
                                                    <i class="fas fa-sync"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Aún no tienes pacientes con citas registradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="h6 mb-1">Pacientes sin doctor</h3>
                            <small class="text-muted">Adopta pacientes registrados recientemente.</small>
                        </div>
                        <span class="badge badge-info">{{ count($availablePatients ?? []) }}</span>
                    </div>
                    <button class="btn btn-outline-primary btn-block mt-3" data-toggle="modal" data-target="#modalAdoptarPacientes">
                        Ver lista
                    </button>
                </div>
            </div>
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h3 class="h6 mb-2">Mi enlace de registro</h3>
                    @if(!empty($shareLink))
                        <p class="text-muted small">Comparte este enlace para que nuevos pacientes se registren contigo.</p>
                        <div class="bg-light rounded p-2 mb-2">
                            <small class="text-monospace">{{ $shareLink }}</small>
                        </div>
                        <div class="text-muted small">Código: <strong>{{ $shareCode }}</strong></div>
                    @else
                        <p class="text-muted mb-0">No se pudo generar un enlace de registro.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Detalle paciente --}}
    <div class="modal fade" id="modalDetallePaciente" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ficha del paciente</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Nombre</dt>
                        <dd class="col-sm-8" data-field="nombre">-</dd>
                        <dt class="col-sm-4">Próxima cita</dt>
                        <dd class="col-sm-8" data-field="fecha">-</dd>
                        <dt class="col-sm-4">Motivo</dt>
                        <dd class="col-sm-8" data-field="motivo">-</dd>
                        <dt class="col-sm-4">Estado</dt>
                        <dd class="col-sm-8" data-field="estado">-</dd>
                        <dt class="col-sm-4">Notas</dt>
                        <dd class="col-sm-8" data-field="nota">-</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Mis pacientes --}}
    <div class="modal fade" id="modalMisPacientes" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pacientes asignados</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if($assignedPatients->isEmpty())
                        <p class="text-muted mb-0">Aún no tienes pacientes asignados. Puedes adoptar uno desde la sección "Pacientes sin doctor".</p>
                    @else
                        <div class="row">
                            @foreach($assignedPatients as $paciente)
                                <div class="col-md-6 mb-3">
                                    <div class="border rounded p-3 h-100 d-flex flex-column">
                                        <div>
                                            <strong>{{ $paciente['nombre'] }}</strong>
                                            <p class="text-muted small mb-1">Código {{ $paciente['codigo'] }}</p>
                                        </div>
                                        <div class="mt-auto">
                                            <button class="btn btn-sm btn-outline-primary btn-crear-cita" data-toggle="modal"
                                                    data-target="#modalCrearCitaDoctor"
                                                    data-paciente="{{ $paciente['persona_id'] }}">
                                                <i class="fas fa-calendar-plus"></i> Programar cita
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Crear cita doctor --}}
    <div class="modal fade" id="modalCrearCitaDoctor" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="POST" action="{{ route('agenda.citas.store') }}" class="modal-content">
                @csrf
                <input type="hidden" name="doctor_persona_id" value="{{ $doctorPersonaId }}">
                <div class="modal-header">
                    <h5 class="modal-title">Nueva cita</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Paciente</label>
                            <select name="paciente_persona_id" class="form-control" required>
                                <option value="">Selecciona un paciente...</option>
                                @foreach($misPacientesAsignados as $paciente)
                                    <option value="{{ $paciente['persona_id'] }}">
                                        {{ $paciente['nombre'] }} ({{ $paciente['codigo'] }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Fecha</label>
                            <input type="date" name="fecha" class="form-control" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Hora inicio</label>
                            <input type="time" name="hora_inicio" class="form-control" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Hora fin</label>
                            <input type="time" name="hora_fin" class="form-control">
                        </div>
                        <div class="form-group col-12">
                            <label>Motivo</label>
                            <input type="text" name="motivo" class="form-control" maxlength="255" required>
                        </div>
                        <div class="form-group col-12 mb-0">
                            <label>Observaciones</label>
                            <textarea name="observaciones" class="form-control" rows="2" maxlength="500"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: Pacientes sin doctor --}}
    <div class="modal fade" id="modalAdoptarPacientes" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pacientes disponibles</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if(empty($availablePatients))
                        <p class="text-muted mb-0">No hay pacientes en espera.</p>
                    @else
                        @foreach($availablePatients as $patient)
                            <div class="border rounded p-3 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $patient['nombre'] ?? 'Paciente' }}</strong>
                                        <p class="text-muted small mb-0">{{ $patient['motivo'] ?? 'Sin motivo asignado' }}</p>
                                    </div>
                                    <form method="POST" action="{{ route('agenda.pacientes.asignar', $patient['persona_id'] ?? 0) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success">
                                            Agregar a mis pacientes
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Código de registro --}}
    <div class="modal fade" id="modalCodigoRegistro" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Comparte tu enlace</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    @if(!empty($shareLink))
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($shareLink) }}"
                             alt="QR registro" class="mb-3">
                        <p class="text-muted small">Escanea o comparte este enlace:</p>
                        <div class="bg-light rounded p-2 text-monospace mb-2">{{ $shareLink }}</div>
                        <p class="text-muted mb-0">Código de referencia: <strong>{{ $shareCode }}</strong></p>
                        <a href="{{ $shareLink }}" target="_blank" rel="noopener"
                           class="btn btn-primary btn-block mt-3">
                            <i class="fas fa-external-link-alt"></i> Abrir formulario de registro
                        </a>
                        <p class="text-muted small mt-3 mb-0">
                            El formulario solicita nombres, género, contacto, dirección, correo y preguntas de seguridad,
                            igual que el registro general.
                        </p>
                    @else
                        <p class="text-muted mb-0">No se pudo generar el enlace de registro.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Reprogramar --}}
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
                    <p class="text-muted">Selecciona la nueva fecha y hora. La cita quedará como <strong>PENDIENTE</strong>.</p>
                    <div class="form-group">
                        <label>Fecha</label>
                        <input type="date" name="fecha" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Hora inicio</label>
                        <input type="time" name="hora_inicio" class="form-control" required>
                    </div>
                    <div class="form-group mb-0">
                        <label>Hora fin (opcional)</label>
                        <input type="time" name="hora_fin" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
<script>
    (function () {
        $(document).on('click', '.btn-crear-cita', function () {
            $('#modalMisPacientes').modal('hide');
        });

        $('#modalReprogramar').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const citaId = button.data('cita-id');
            const fecha = button.data('fecha') || '';
            const hora = button.data('hora') || '';
            const modal = $(this);
            const form = modal.find('#formReprogramar');
            const baseUrl = "{{ url('/agenda/citas') }}";
            form.attr('action', baseUrl + '/' + citaId + '/reprogramar');
            form.find('input[name="fecha"]').val(fecha);
            form.find('input[name="hora_inicio"]').val(hora);
            form.find('input[name="hora_fin"]').val('');
        });

        $('.btn-detalle-paciente').on('click', function () {
            const data = $(this).data('paciente') || {};
            const modal = $('#modalDetallePaciente');
            modal.find('[data-field="nombre"]').text(data.nombre || '');
            modal.find('[data-field="motivo"]').text(data.motivo || '');
            modal.find('[data-field="fecha"]').text((data.fecha || '') + (data.hora ? ' · ' + data.hora : ''));
            modal.find('[data-field="estado"]').text(data.estado || '');
            modal.find('[data-field="nota"]').text(data.nota || 'Sin notas');
        });

        $('#modalCrearCitaDoctor').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const pacienteId = button ? button.data('paciente') : '';
            const select = $(this).find('select[name="paciente_persona_id"]');
            select.val(pacienteId || '');
        });
    })();
</script>
@endsection
