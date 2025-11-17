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
            <a href="{{ route('export.citas.csv') }}" class="btn btn-outline-secondary">
                <i class="fas fa-file-download"></i> Reportes
            </a>
        </div>
    </div>
@endsection

@section('content')
    {{-- Tarjetas de resumen --}}
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

    @php
        // Aplanamos doctorPanels → filas de tabla
        $rows = collect($doctorPanels ?? [])->flatMap(function ($doctor) {
            return collect($doctor['pacientes'] ?? [])->map(function ($pac) use ($doctor) {
                return [
                    'doctor'   => $doctor['nombre'] ?? 'Sin nombre',
                    'paciente' => $pac['nombre'] ?? 'Paciente',
                    'motivo'   => $pac['motivo'] ?? '',
                    'fecha'    => $pac['fecha'] ?? '',
                    'hora'     => $pac['hora'] ?? '',
                    'estado'   => $pac['estado'] ?? '',
                    'cita_id'  => $pac['cita_id'] ?? ($pac['id'] ?? null),
                ];
            });
        })->values();
    @endphp

    <div class="row">
        {{-- PANEL PRINCIPAL: Citas programadas --}}
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h3 class="h5 mb-0">Citas programadas</h3>
                    <small class="text-muted">Supervisa y gestiona citas por doctor y paciente.</small>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Doctor</th>
                                <th>Paciente</th>
                                <th>Motivo</th>
                                <th>Fecha / Hora</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rows as $row)
                                @php
                                    $estadoRaw   = $row['estado'] ?? '';
                                    $estadoUpper = strtoupper(trim($estadoRaw));

                                    $badge = match ($estadoUpper) {
                                        'CONFIRMADA' => 'success',
                                        'CANCELADA'  => 'danger',
                                        'PENDIENTE'  => 'warning',
                                        default      => 'secondary',
                                    };

                                    $estadoLabel = match ($estadoUpper) {
                                        'CONFIRMADA' => 'Confirmada',
                                        'CANCELADA'  => 'Cancelada',
                                        'PENDIENTE'  => 'Pendiente',
                                        default      => ($estadoRaw !== '' ? $estadoRaw : 'Sin estado'),
                                    };

                                    $citaId = $row['cita_id'] ?? null;
                                @endphp
                                <tr>
                                    <td class="font-weight-bold">{{ $row['doctor'] }}</td>
                                    <td>{{ $row['paciente'] }}</td>
                                    <td>{{ $row['motivo'] }}</td>
                                    <td>{{ $row['fecha'] }} @if($row['hora']) · {{ $row['hora'] }} @endif</td>
                                    <td>
                                        <span class="badge badge-{{ $badge }}">
                                            {{ $estadoLabel }}
                                        </span>
                                    </td>
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

                                                {{-- Reprogramar --}}
                                                <button type="button"
                                                        class="btn btn-outline-warning text-dark btn-open-reprogramar"
                                                        data-toggle="modal"
                                                        data-target="#modalReprogramar"
                                                        data-cita-id="{{ $citaId }}"
                                                        data-fecha="{{ $row['fecha'] }}"
                                                        data-hora="{{ $row['hora'] }}">
                                                    Reprogramar
                                                </button>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        No hay citas registradas todavía.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- PANEL LATERAL: Pacientes sin doctor + Tips --}}
        <div class="col-lg-4 mb-4">
            {{-- Pacientes sin doctor --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="h6 mb-0">Pacientes sin doctor</h3>
                        <small class="text-muted">Asigna un doctor a cada paciente.</small>
                    </div>
                </div>
                <div class="card-body">
                    @if(empty($availablePatients))
                        <p class="text-muted mb-0">
                            No hay pacientes en espera de asignación.
                        </p>
                    @else
                        @if(empty($doctorsList))
                            <p class="text-muted mb-0">
                                Hay pacientes sin doctor, pero aún no hay doctores registrados.
                            </p>
                        @else
                            @foreach($availablePatients as $patient)
                                <div class="mb-3">
                                    <h5 class="mb-1">{{ $patient['nombre'] ?? 'Paciente' }}</h5>
                                    <p class="mb-1 text-muted">
                                        {{ $patient['motivo'] ?? 'Pendiente de asignar doctor' }}
                                    </p>

                                    <form method="POST"
                                          action="{{ route('agenda.pacientes.asignar_recepcion') }}">
                                        @csrf

                                        {{-- Paciente a asignar --}}
                                        <input type="hidden" name="paciente_persona_id"
                                               value="{{ $patient['persona_id'] }}">

                                        {{-- Doctor destino --}}
                                        <div class="form-group mb-2">
                                            <label class="small mb-1">Doctor</label>
                                            <select name="doctor_persona_id"
                                                    class="form-control form-control-sm"
                                                    required>
                                                <option value="">Seleccione un doctor...</option>
                                                @foreach($doctorsList as $doc)
                                                    <option value="{{ $doc['persona_id'] }}">
                                                        {{ $doc['nombre'] }}
                                                        ({{ $doc['usuario'] }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <button type="submit"
                                                class="btn btn-sm btn-outline-success">
                                            Asignar
                                        </button>
                                    </form>
                                </div>

                                @if(!$loop->last)
                                    <hr>
                                @endif
                            @endforeach
                        @endif
                    @endif
                </div>
            </div>

            {{-- Tips de uso --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h3 class="h6 mb-0">Tips de uso</h3>
                </div>
                <div class="card-body">
                    <ul class="small mb-0">
                        <li>Usa <strong>Confirmar</strong> cuando el paciente confirme por llamada o WhatsApp.</li>
                        <li>Usa <strong>Cancelar</strong> solo si la cita definitivamente no se realizará.</li>
                        <li>Con <strong>Reprogramar</strong> ajustas fecha y hora; la cita queda como <em>PENDIENTE</em>.</li>
                        <li>Desde este panel puedes asignar pacientes nuevos a cualquier doctor disponible.</li>
                    </ul>
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
    // Reutilizamos el mismo JS que en doctor para abrir el modal de reprogramar
    $('#modalReprogramar').on('show.bs.modal', function (event) {
        var button   = $(event.relatedTarget);
        var citaId   = button.data('cita-id');
        var fecha    = button.data('fecha') || '';
        var hora     = button.data('hora')  || '';

        var modal    = $(this);
        var form     = modal.find('#formReprogramar');

        var baseUrl  = "{{ url('/agenda/citas') }}";
        form.attr('action', baseUrl + '/' + citaId + '/reprogramar');

        modal.find('#reprog_fecha').val(fecha);
        modal.find('#reprog_hora_inicio').val(hora);
        modal.find('#reprog_hora_fin').val('');
    });
</script>
@endsection
