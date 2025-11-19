@extends('adminlte::page')

@section('title', $pageTitle)

@section('content_header')
    <div class="d-flex flex-wrap justify-content-between align-items-start">
        <div class="mb-2">
            <h1 class="h3 font-weight-bold text-primary mb-1">{{ ucfirst($heading) }}</h1>
            <p class="text-muted mb-0">{{ $intro }}</p>
        </div>
        <div class="btn-group mt-2 mt-md-0">
            <button class="btn btn-primary" data-toggle="modal" data-target="#modalCrearCitaAdmin">
                <i class="fas fa-plus-circle"></i> Crear cita
            </button>
            <button class="btn btn-outline-secondary" data-toggle="modal" data-target="#modalDoctorPacientes">
                <i class="fas fa-user-friends"></i> Doctores y pacientes
            </button>
        </div>
    </div>
@endsection

@section('content')
    <div class="row mb-4">
        @forelse(($stats ?? []) as $stat)
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex align-items-center">
                        <span class="badge badge-{{ $stat['color'] ?? 'secondary' }} mr-3 p-3 rounded-circle text-white">
                            <i class="{{ $stat['icon'] ?? 'fas fa-info-circle' }}"></i>
                        </span>
                        <div>
                            <p class="text-muted text-uppercase small mb-1">{{ $stat['label'] }}</p>
                            <h3 class="mb-0 font-weight-bold">{{ $stat['value'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light border mb-0">Sin datos de actividad todavía.</div>
            </div>
        @endforelse
    </div>

    <div class="row">
        <div class="col-xl-8 mb-4">
            @forelse(($doctorPanels ?? []) as $doctor)
                @php
                    $doctorId       = $doctor['doctor_persona_id'] ?? null;
                    $pacientes      = $doctor['pacientes'] ?? [];
                    $proximo        = collect($pacientes)->sortBy(fn($p) => ($p['fecha'] ?? '') . ' ' . ($p['hora'] ?? ''))->first();
                    $proximaEtiqueta = $proximo ? (($proximo['fecha'] ?? '') . ($proximo['hora'] ? ' · ' . $proximo['hora'] : '')) : 'Sin programar';
                @endphp
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body d-flex flex-wrap justify-content-between align-items-center">
                        <div class="mb-3 mb-md-0">
                            <h3 class="h5 mb-1">{{ $doctor['nombre'] ?? 'Doctor' }}</h3>
                            <p class="text-muted mb-0">{{ $doctor['especialidad'] ?? 'Odontología' }}</p>
                            <div class="d-flex flex-wrap text-muted small mt-2">
                                <div class="mr-4">
                                    <strong>{{ count($pacientes) }}</strong> pacientes activos
                                </div>
                                <div>
                                    Próxima cita: <strong>{{ $proximaEtiqueta }}</strong>
                                </div>
                            </div>
                        </div>
                        @php
                            $assignedPatients = collect(($doctorPatientMap[$doctorId]['patients'] ?? []));
                        @endphp
                        <div class="text-right">
                            <div class="btn-group-vertical btn-group-sm">
                                <button class="btn btn-outline-primary btn-open-doctor"
                                        data-doctor-id="{{ $doctorId }}"
                                        data-doctor-name="{{ $doctor['nombre'] ?? 'Doctor' }}"
                                        data-target="#modalDoctorCitas"
                                        data-toggle="modal">
                                    <i class="fas fa-eye"></i> Ver citas
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <template id="doctor-modal-{{ $doctorId }}">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Paciente</th>
                                    <th>Motivo</th>
                                    <th>Fecha / Hora</th>
                                    <th>Estado</th>
                                    <th class="text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pacientes as $paciente)
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
                                        <td class="font-weight-bold">{{ $paciente['nombre'] ?? 'Paciente' }}</td>
                                        <td>{{ $paciente['motivo'] ?? 'N/D' }}</td>
                                        <td>{{ $paciente['fecha'] ?? '' }} @if(!empty($paciente['hora'])) · {{ $paciente['hora'] }} @endif</td>
                                        <td>
                                            <span class="badge badge-{{ $badge }}">{{ $estadoLabel }}</span>
                                        </td>
                                        <td class="text-right">
                                            @if($citaId)
                                                <div class="btn-group btn-group-sm">
                                                    <form method="POST" action="{{ route('agenda.citas.confirmar', $citaId) }}"
                                                          class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-success"
                                                                @if($estadoRaw === 'CONFIRMADA') disabled @endif>
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('agenda.citas.cancelar', $citaId) }}"
                                                          class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-danger"
                                                                @if($estadoRaw === 'CANCELADA') disabled @endif>
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                    <button type="button"
                                                            class="btn btn-outline-warning text-dark btn-open-reprogramar"
                                                            data-toggle="modal"
                                                            data-target="#modalReprogramar"
                                                            data-cita-id="{{ $citaId }}"
                                                            data-fecha="{{ $paciente['fecha'] ?? '' }}"
                                                            data-hora="{{ $paciente['hora'] ?? '' }}">
                                                        <i class="fas fa-sync"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Sin citas registradas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </template>

                <template id="doctor-pacientes-{{ $doctorId }}">
                    @if($assignedPatients->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-sm table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Apellido</th>
                                        <th>Teléfono</th>
                                        <th>Dirección</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assignedPatients as $paciente)
                                        <tr>
                                            <td>{{ $paciente['primer_nombre'] ?? $paciente['nombre'] ?? 'Paciente' }}</td>
                                            <td>{{ $paciente['primer_apellido'] ?? '—' }}</td>
                                            <td>{{ $paciente['telefono'] ?? 'Sin teléfono' }}</td>
                                            <td>{{ $paciente['direccion'] ?? 'Sin dirección' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">Sin pacientes asignados en el directorio.</p>
                    @endif
                </template>
            @empty
                <div class="alert alert-info">No se encontraron doctores con citas registradas.</div>
            @endforelse
        </div>
        <div class="col-xl-4 mb-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h3 class="h6 mb-1">Pacientes sin doctor</h3>
                            <small class="text-muted">Asigna pacientes en espera.</small>
                        </div>
                        <span class="badge badge-info badge-pill">{{ count($availablePatients ?? []) }}</span>
                    </div>
                    <button class="btn btn-outline-primary btn-block"
                            data-toggle="modal"
                            data-target="#modalPacientesSinDoctor">
                        Gestionar pacientes
                    </button>
                </div>
            </div>
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h3 class="h6 mb-2">Resumen operativo</h3>
                    <ul class="list-unstyled small mb-0 text-muted">
                        <li class="mb-2">
                            <i class="fas fa-user-md text-primary mr-2"></i>
                            Doctores activos: <strong>{{ count($doctorPanels ?? []) }}</strong>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-user-friends text-success mr-2"></i>
                            Pacientes asignados: <strong>{{ collect($doctorPanels ?? [])->sum(fn($doc) => count($doc['pacientes'] ?? [])) }}</strong>
                        </li>
                        <li>
                            <i class="fas fa-clock text-warning mr-2"></i>
                            Próximas citas en agenda global: <strong>{{ count($eventList ?? []) }}</strong>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Citas por doctor --}}
    <div class="modal fade" id="modalDoctorCitas" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Citas de <span class="js-doctor-name">Doctor</span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body js-doctor-body">
                    <p class="text-muted mb-0">Selecciona un doctor para visualizar sus citas.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Asignaciones doctor-paciente --}}
    <div class="modal fade" id="modalDoctorPacientes" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Asignaciones doctor - paciente</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <h6 class="text-uppercase small text-muted">Doctores</h6>
                            <div class="list-group js-doctor-list">
                                @forelse(($doctorPanels ?? []) as $doctor)
                                    @php
                                        $doctorId = $doctor['doctor_persona_id'] ?? null;
                                    @endphp
                                    <button type="button"
                                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center js-select-doctor"
                                            data-doctor-id="{{ $doctorId }}"
                                            data-doctor-name="{{ $doctor['nombre'] ?? 'Doctor' }}">
                                        <div>
                                            <strong>{{ $doctor['nombre'] ?? 'Doctor' }}</strong>
                                            <div class="small text-muted">{{ $doctor['especialidad'] ?? 'Odontología' }}</div>
                                        </div>
                                        <span class="badge badge-light badge-pill">{{ count($doctor['pacientes'] ?? []) }}</span>
                                    </button>
                                @empty
                                    <p class="text-muted mb-0">No hay doctores registrados.</p>
                                @endforelse
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="text-uppercase small text-muted mb-0">Pacientes asignados</h6>
                                <span class="font-weight-bold js-pacientes-doctor text-primary">Selecciona un doctor</span>
                            </div>
                            <div class="js-pacientes-body">
                                <p class="text-muted mb-0">Selecciona un doctor para visualizar a sus pacientes asignados.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Crear cita --}}
    <div class="modal fade" id="modalCrearCitaAdmin" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="POST" action="{{ route('agenda.citas.store') }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Crear cita</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="small text-uppercase text-muted">Doctor</label>
                            <select name="doctor_persona_id" class="form-control" required>
                                <option value="">Seleccione un doctor...</option>
                                @foreach(($doctorsList ?? []) as $doc)
                                    <option value="{{ $doc['persona_id'] }}">{{ $doc['nombre'] }} ({{ $doc['usuario'] }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="small text-uppercase text-muted">Paciente</label>
                            <select name="paciente_persona_id" class="form-control" required>
                                <option value="">Seleccione primero un doctor</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Fecha</label>
                            <input type="date" name="fecha" class="form-control" required>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Hora inicio</label>
                            <input type="time" name="hora_inicio" class="form-control" required>
                        </div>
                        <div class="form-group col-md-3">
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
                    <button type="submit" class="btn btn-primary">Guardar cita</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: Pacientes sin doctor --}}
    <div class="modal fade" id="modalPacientesSinDoctor" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pacientes en espera</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if(empty($availablePatients))
                        <p class="text-muted mb-0">No hay pacientes pendientes de asignación.</p>
                    @else
                        @foreach($availablePatients as $patient)
                            <div class="border rounded p-3 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $patient['nombre'] ?? 'Paciente' }}</strong>
                                        <p class="text-muted small mb-0">{{ $patient['motivo'] ?? 'Pendiente de asignar doctor' }}</p>
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('agenda.pacientes.asignar_recepcion') }}" class="mt-3">
                                    @csrf
                                    <input type="hidden" name="paciente_persona_id" value="{{ $patient['persona_id'] }}">
                                    <div class="form-row align-items-end">
                                        <div class="form-group col-md-8 mb-0">
                                            <label class="small text-muted">Selecciona un doctor</label>
                                            <select name="doctor_persona_id" class="form-control form-control-sm" required>
                                                <option value="">Doctor...</option>
                                                @foreach($doctorsList as $doc)
                                                    <option value="{{ $doc['persona_id'] }}">{{ $doc['nombre'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-0 text-right">
                                            <button type="submit" class="btn btn-sm btn-outline-success">Asignar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @endforeach
                    @endif
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
        const map = @json($doctorPatientMap ?? []);

        function hydratePatients(modal, doctorId) {
            const select = modal.find('select[name="paciente_persona_id"]');
            select.empty();
            if (!doctorId) {
                select.append('<option value="">Seleccione primero un doctor</option>');
                return;
            }
            const group = map[doctorId] ? map[doctorId].patients : [];
            if (!group.length) {
                select.append('<option value="">Sin pacientes asignados</option>');
                return;
            }
            select.append('<option value="">Seleccione un paciente...</option>');
            group.forEach(function (patient) {
                select.append('<option value="' + patient.persona_id + '">' + patient.nombre + '</option>');
            });
        }

        $('#modalCrearCitaAdmin').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const doctorId = button ? button.data('doctor') : '';
            const modal = $(this);
            modal.find('form')[0].reset();
            if (doctorId) {
                modal.find('select[name="doctor_persona_id"]').val(String(doctorId));
            } else {
                modal.find('select[name="doctor_persona_id"]').val('');
            }
            hydratePatients(modal, modal.find('select[name="doctor_persona_id"]').val());
        });

        $('#modalCrearCitaAdmin select[name="doctor_persona_id"]').on('change', function () {
            hydratePatients($('#modalCrearCitaAdmin'), this.value);
        });

        $('.btn-open-doctor').on('click', function () {
            const doctorId = $(this).data('doctor-id');
            const template = document.getElementById('doctor-modal-' + doctorId);
            const modal = $('#modalDoctorCitas');
            modal.find('.js-doctor-name').text($(this).data('doctor-name'));
            modal.find('.js-doctor-body').html(template ? template.innerHTML : '<p class="text-muted mb-0">Sin información disponible.</p>');
        });

        const pacientesModal = $('#modalDoctorPacientes');
        const pacientesBody = pacientesModal.find('.js-pacientes-body');

        function renderDoctorPatients(doctorId) {
            const template = document.getElementById('doctor-pacientes-' + doctorId);
            if (template) {
                pacientesBody.html(template.innerHTML);
            } else {
                pacientesBody.html('<p class="text-muted mb-0">Sin información disponible.</p>');
            }
        }

        $('.js-select-doctor').on('click', function () {
            const button = $(this);
            const doctorId = button.data('doctor-id');
            const doctorName = button.data('doctor-name');
            button.closest('.js-doctor-list').find('.js-select-doctor').removeClass('active');
            button.addClass('active');
            pacientesModal.find('.js-pacientes-doctor').text(doctorName || 'Doctor');
            renderDoctorPatients(doctorId);
        });

        pacientesModal.on('show.bs.modal', function () {
            pacientesModal.find('.js-select-doctor').removeClass('active');
            pacientesModal.find('.js-pacientes-doctor').text('Selecciona un doctor');
            pacientesBody.html('<p class="text-muted mb-0">Selecciona un doctor para visualizar a sus pacientes asignados.</p>');
        });

        pacientesModal.on('shown.bs.modal', function () {
            const firstDoctor = pacientesModal.find('.js-select-doctor').first();
            if (firstDoctor.length) {
                firstDoctor.trigger('click');
            }
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
    })();
</script>
@endsection
