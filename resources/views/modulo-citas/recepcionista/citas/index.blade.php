@extends('adminlte::page')

@section('title', $pageTitle)

@section('content_header')
    <div class="d-flex flex-wrap justify-content-between align-items-start">
        <div class="mb-2">
            <h1 class="h3 font-weight-bold text-primary mb-1">{{ $heading }}</h1>
            <p class="text-muted mb-0">{{ $intro }}</p>
        </div>
        <div class="btn-group mt-2 mt-md-0">
            <a href="{{ route('agenda.calendario') }}" class="btn btn-outline-primary">
                <i class="fas fa-calendar-alt"></i> Ver calendario
            </a>
            <button class="btn btn-outline-secondary" data-toggle="modal" data-target="#modalPacientesSinDoctor">
                <i class="fas fa-user-clock"></i> Pacientes sin doctor
            </button>
        </div>
    </div>
@endsection

@section('content')
    <div class="row mb-4">
        @foreach($stats as $stat)
            <div class="col-md-3 mb-3">
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

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h3 class="h5 mb-0">Doctores registrados</h3>
            <small class="text-muted">Consulta y gestiona los pacientes asignados.</small>
        </div>
        <div class="list-group list-group-flush">
            @forelse(($doctorPanels ?? []) as $doctor)
                @php
                    $doctorId    = $doctor['doctor_persona_id'] ?? null;
                    $totalPac    = count($doctor['pacientes'] ?? []);
                    $codigo      = $doctor['codigo'] ?? '';
                @endphp
                <div class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <strong>{{ $doctor['nombre'] ?? 'Doctor' }}</strong>
                        <p class="text-muted mb-0 small">{{ $doctor['especialidad'] ?? 'Odontología' }} · {{ $codigo }}</p>
                    </div>
                    <div class="text-right">
                        <span class="badge badge-light mr-3">{{ $totalPac }} pacientes</span>
                        <button class="btn btn-sm btn-outline-primary btn-ver-pacientes"
                                data-toggle="modal"
                                data-target="#modalPacientesDoctor"
                                data-doctor-id="{{ $doctorId }}">
                            <i class="fas fa-users"></i> Ver pacientes
                        </button>
                    </div>
                </div>
                <template id="recep-doctor-{{ $doctorId }}">
                    <div class="row">
                        @forelse($doctor['pacientes'] ?? [] as $paciente)
                            <div class="col-md-6 mb-3">
                                <div class="border rounded p-3 h-100">
                                    <p class="font-weight-bold mb-1">{{ $paciente['nombre'] ?? 'Paciente' }}</p>
                                    <p class="text-muted mb-2">Código: {{ $paciente['codigo'] ?? 'N/D' }}</p>
                                    <p class="text-muted small mb-0">Asignado al Dr. {{ $doctor['nombre'] ?? '' }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-muted mb-0">Sin pacientes asignados.</p>
                            </div>
                        @endforelse
                    </div>
                </template>
            @empty
                <div class="list-group-item text-muted text-center">No hay doctores con citas registradas.</div>
            @endforelse
        </div>
    </div>

    {{-- Modal pacientes doctor --}}
    <div class="modal fade" id="modalPacientesDoctor" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pacientes asignados</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body js-modal-body">
                    <p class="text-muted mb-0">Selecciona un doctor para ver sus pacientes.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal pacientes sin doctor --}}
    <div class="modal fade" id="modalPacientesSinDoctor" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pacientes sin doctor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if(empty($availablePatients))
                        <p class="text-muted mb-0">No hay pacientes en espera de asignación.</p>
                    @else
                        @foreach($availablePatients as $patient)
                            <div class="border rounded p-3 mb-3">
                                <strong>{{ $patient['nombre'] ?? 'Paciente' }}</strong>
                                <p class="text-muted small mb-2">{{ $patient['motivo'] ?? 'Sin motivo' }}</p>
                                <form method="POST" action="{{ route('agenda.pacientes.asignar_recepcion') }}" class="form-inline flex-wrap">
                                    @csrf
                                    <input type="hidden" name="paciente_persona_id" value="{{ $patient['persona_id'] }}">
                                    <div class="form-group mr-2 mb-2">
                                        <select name="doctor_persona_id" class="form-control form-control-sm" required>
                                            <option value="">Doctor...</option>
                                            @foreach($doctorsList as $doc)
                                                <option value="{{ $doc['persona_id'] }}">{{ $doc['nombre'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-outline-success mb-2">
                                        Asignar
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Modal reprogramar --}}
    <div class="modal fade" id="modalReprogramarRecepcion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" id="formReprogramarRecepcion" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reprogramar cita</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Selecciona la nueva fecha y hora.</p>
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
        const baseUrl = "{{ url('/agenda/citas') }}";

        $('.btn-ver-pacientes').on('click', function () {
            const doctorId = $(this).data('doctor-id');
            const template = document.getElementById('recep-doctor-' + doctorId);
            const modal = $('#modalPacientesDoctor');
            modal.find('.js-modal-body').html(template ? template.innerHTML : '<p class="text-muted mb-0">Sin información disponible.</p>');
        });

        $('#modalReprogramarRecepcion').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const citaId = button.data('cita-id');
            const fecha = button.data('fecha') || '';
            const hora = button.data('hora') || '';
            const form = $('#formReprogramarRecepcion');
            form.attr('action', baseUrl + '/' + citaId + '/reprogramar');
            form.find('input[name="fecha"]').val(fecha);
            form.find('input[name="hora_inicio"]').val(hora);
            form.find('input[name="hora_fin"]').val('');
        });
    })();
</script>
@endsection
