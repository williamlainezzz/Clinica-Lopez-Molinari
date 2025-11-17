@extends('adminlte::page')
@include('modulo-citas.shared._calendar_styles')
@include('modulo-citas.shared._calendar_scripts')

@section('title', $pageTitle)

@section('content_header')
    <div class="d-flex flex-wrap justify-content-between align-items-start">
        <div class="mb-2">
            <h1 class="h3 font-weight-bold text-primary mb-1">{{ $heading }}</h1>
            <p class="text-muted mb-0">{{ $intro }}</p>
        </div>
        <div class="btn-group mt-2 mt-md-0 align-items-center">
            <a href="{{ route('agenda.calendario', ['month' => $calendarContext['prev'] ?? null]) }}"
               class="btn btn-outline-secondary">
                <i class="fas fa-chevron-left"></i>
            </a>
            <span class="btn btn-light text-uppercase font-weight-bold disabled">
                {{ $calendarContext['label'] ?? 'Agenda' }}
            </span>
            <a href="{{ route('agenda.calendario', ['month' => $calendarContext['next'] ?? null]) }}"
               class="btn btn-outline-secondary">
                <i class="fas fa-chevron-right"></i>
            </a>
            <button class="btn btn-primary ml-2" data-toggle="modal" data-target="#modalCrearCitaDoctor">
                <i class="fas fa-plus-circle"></i> Crear cita
            </button>
        </div>
    </div>
@endsection

@section('content')
    @php
        $doctorPersonaId = auth()->user()->FK_COD_PERSONA ?? null;
        $misPacientesAsignados = collect($doctorPatientMap[$doctorPersonaId]['patients'] ?? [])
            ->map(fn($p) => [
                'persona_id' => $p['persona_id'],
                'nombre'     => $p['nombre'],
                'codigo'     => $p['codigo'],
            ])->values();
    @endphp
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h3 class="h5 mb-0">{{ $calendarContext['label'] ?? 'Agenda' }}</h3>
                        <small class="text-muted">Selecciona una cita para ver detalles o reprogramar.</small>
                    </div>
                    <div class="agenda-calendar__legend">
                        <span><i class="fas fa-circle text-success"></i> Confirmada</span>
                        <span><i class="fas fa-circle text-warning"></i> Pendiente</span>
                        <span><i class="fas fa-circle text-danger"></i> Cancelada</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table agenda-calendar mb-0">
                            <thead>
                                <tr>
                                    <th>Lun</th>
                                    <th>Mar</th>
                                    <th>Mié</th>
                                    <th>Jue</th>
                                    <th>Vie</th>
                                    <th>Sáb</th>
                                    <th>Dom</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(($calendarMatrix ?? []) as $week)
                                    <tr>
                                        @foreach($week as $day)
                                            @php
                                                $dateKey     = $day['date'] ?? null;
                                                $eventsOfDay = $dateKey ? ($calendarEvents[$dateKey] ?? []) : [];
                                                $classes     = 'agenda-calendar__day';
                                                if (!empty($day['isMuted'])) {
                                                    $classes .= ' is-muted';
                                                }
                                                if (!empty($day['isToday'])) {
                                                    $classes .= ' is-today';
                                                }
                                            @endphp
                                            <td class="{{ $classes }}">
                                                <div class="agenda-calendar__day-number">{{ $day['label'] ?? '' }}</div>
                                                @foreach($eventsOfDay as $event)
                                                    @php
                                                        $estado = $event['estado'] ?? 'Pendiente';
                                                        $pillClass = match($estado) {
                                                            'Confirmada' => 'bg-success',
                                                            'Cancelada'  => 'bg-danger',
                                                            default      => 'bg-warning text-dark',
                                                        };
                                                    @endphp
                                                    <span class="agenda-calendar__pill {{ $pillClass }} js-event-pill"
                                                          data-toggle="modal"
                                                          data-target="#modalDetalleCitaDoctor"
                                                          data-event='@json($event)'>
                                                        {{ substr($event['hora'] ?? '00:00', 0, 5) }} ·
                                                        {{ \Illuminate\Support\Str::limit($event['paciente'] ?? 'Paciente', 14) }}
                                                    </span>
                                                @endforeach
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if(empty($calendarMatrix ?? []))
                        <p class="text-muted text-center mb-0 mt-3">Aún no hay eventos para mostrar.</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h3 class="h6 mb-0">Próximas citas</h3>
                </div>
                <div class="list-group list-group-flush" style="max-height: 420px; overflow-y: auto;">
                    @forelse(collect($eventList ?? [])->take(8) as $event)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $event['paciente'] ?? 'Paciente' }}</strong>
                                <span class="text-muted">{{ substr($event['hora'] ?? '00:00', 0, 5) }}</span>
                            </div>
                            <small class="text-muted d-block">{{ $event['fecha'] ?? '' }}</small>
                            <span class="badge badge-pill badge-light text-capitalize">{{ strtolower($event['motivo'] ?? 'Motivo') }}</span>
                        </div>
                    @empty
                        <div class="list-group-item text-center text-muted">Sin citas próximas.</div>
                    @endforelse
                </div>
            </div>
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted">Mi agenda</h6>
                    <a href="{{ route('agenda.export.doctor') }}" class="btn btn-outline-secondary btn-block" target="_blank">
                        <i class="fas fa-file-download"></i> Descargar mi agenda
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal detalle --}}
    <div class="modal fade" id="modalDetalleCitaDoctor" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalle de cita</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Paciente</dt>
                        <dd class="col-sm-8" data-field="paciente">-</dd>
                        <dt class="col-sm-4">Motivo</dt>
                        <dd class="col-sm-8" data-field="motivo">-</dd>
                        <dt class="col-sm-4">Fecha</dt>
                        <dd class="col-sm-8" data-field="fecha">-</dd>
                        <dt class="col-sm-4">Hora</dt>
                        <dd class="col-sm-8" data-field="hora">-</dd>
                        <dt class="col-sm-4">Estado</dt>
                        <dd class="col-sm-8" data-field="estado">-</dd>
                        <dt class="col-sm-4">Notas</dt>
                        <dd class="col-sm-8" data-field="nota">-</dd>
                    </dl>
                </div>
                <div class="modal-footer">
                    <form method="POST" id="formEliminarCitaDoctor" class="mr-auto">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </form>
                    <button class="btn btn-outline-secondary" id="btnReprogramarDesdeDetalle">
                        <i class="fas fa-sync"></i> Reprogramar
                    </button>
                    <button class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button class="btn btn-primary" id="btnEditarCitaDoctor">
                        <i class="fas fa-pen"></i> Editar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal crear cita --}}
    <div class="modal fade" id="modalCrearCitaDoctor" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="POST" action="{{ route('agenda.citas.store') }}" class="modal-content">
                @csrf
                <input type="hidden" name="doctor_persona_id" value="{{ $doctorPersonaId }}">
                <div class="modal-header">
                    <h5 class="modal-title">Programar nueva cita</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Buscar paciente</label>
                        <input type="search" id="filtroPacientesDoctor" class="form-control" placeholder="Escribe un nombre...">
                    </div>
                    <div class="form-group">
                        <label>Paciente asignado</label>
                        <select name="paciente_persona_id" class="form-control" required>
                            <option value="">Seleccione un paciente...</option>
                            @foreach($misPacientesAsignados as $paciente)
                                <option value="{{ $paciente['persona_id'] }}">
                                    {{ $paciente['nombre'] }} ({{ $paciente['codigo'] }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row">
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
                    </div>
                    <div class="form-group">
                        <label>Motivo</label>
                        <input type="text" name="motivo" class="form-control" maxlength="255" required>
                    </div>
                    <div class="form-group mb-0">
                        <label>Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="2" maxlength="500"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal editar cita --}}
    <div class="modal fade" id="modalEditarCitaDoctor" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="POST" id="formEditarCitaDoctor" class="modal-content">
                @csrf
                @method('PUT')
                <input type="hidden" name="doctor_persona_id" value="{{ $doctorPersonaId }}">
                <div class="modal-header">
                    <h5 class="modal-title">Editar cita</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Paciente</label>
                        <select name="paciente_persona_id" class="form-control" required>
                            <option value="">Seleccione un paciente...</option>
                            @foreach($misPacientesAsignados as $paciente)
                                <option value="{{ $paciente['persona_id'] }}">
                                    {{ $paciente['nombre'] }} ({{ $paciente['codigo'] }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row">
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
                    </div>
                    <div class="form-group">
                        <label>Motivo</label>
                        <input type="text" name="motivo" class="form-control" maxlength="255" required>
                    </div>
                    <div class="form-group mb-0">
                        <label>Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="2" maxlength="500"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal reprogramar --}}
    <div class="modal fade" id="modalReprogramarDoctor" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" id="formReprogramarDoctor" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reprogramar cita</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Define la nueva fecha y hora. La cita quedará como pendiente.</p>
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
        const pacientes = @json($misPacientesAsignados);
        const baseUrl = "{{ url('/agenda/citas') }}";
        let eventoActual = null;

        function renderPacientes(selectElement, term = '', selectedId = '') {
            const select = $(selectElement);
            select.empty();
            select.append('<option value="">Seleccione un paciente...</option>');
            pacientes
                .filter(p => p.nombre.toLowerCase().includes(term.toLowerCase()))
                .forEach(p => {
                    select.append('<option value="' + p.persona_id + '">' + p.nombre + ' (' + p.codigo + ')</option>');
                });
            if (selectedId) {
                select.val(String(selectedId));
            }
        }

        const createSelect = $('#modalCrearCitaDoctor select[name="paciente_persona_id"]');

        $('#filtroPacientesDoctor').on('input', function () {
            renderPacientes(createSelect, $(this).val() || '');
        });

        $('#modalCrearCitaDoctor').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const pacienteId = button ? button.data('paciente') : '';
            renderPacientes(createSelect, '', pacienteId || '');
        });

        $(document).on('click', '.btn-crear-cita', function () {
            $('#modalMisPacientes').modal('hide');
        });

        $('.agenda-calendar').on('click', '.js-event-pill', function () {
            const data = $(this).data('event') || {};
            eventoActual = data;
            const modal = $('#modalDetalleCitaDoctor');
            modal.find('[data-field="paciente"]').text(data.paciente || '');
            modal.find('[data-field="motivo"]').text(data.motivo || '');
            modal.find('[data-field="fecha"]').text(data.fecha || '');
            modal.find('[data-field="hora"]').text(data.hora || '');
            modal.find('[data-field="estado"]').text(data.estado || '');
            modal.find('[data-field="nota"]').text(data.nota || 'Sin notas');
            $('#formEliminarCitaDoctor').attr('action', baseUrl + '/' + (data.id || 0));
        });

        $('#btnReprogramarDesdeDetalle').on('click', function () {
            if (!eventoActual) {
                return;
            }
            const modal = $('#modalReprogramarDoctor');
            const form = modal.find('#formReprogramarDoctor');
            form.attr('action', baseUrl + '/' + (eventoActual.id || 0) + '/reprogramar');
            form.find('input[name="fecha"]').val(eventoActual.fecha || '');
            form.find('input[name="hora_inicio"]').val((eventoActual.hora || '').substring(0, 5));
            const horaFin = eventoActual.hora_fin || eventoActual.hor_fin || '';
            form.find('input[name="hora_fin"]').val(horaFin ? horaFin.substring(0, 5) : '');
            $('#modalDetalleCitaDoctor').modal('hide');
            modal.modal('show');
        });

        $('#modalReprogramarDoctor').on('hidden.bs.modal', function () {
            $('#modalDetalleCitaDoctor').modal('show');
        });

        $('#btnEditarCitaDoctor').on('click', function () {
            if (!eventoActual) {
                return;
            }
            const modal = $('#modalEditarCitaDoctor');
            const form = modal.find('#formEditarCitaDoctor');
            form.attr('action', baseUrl + '/' + (eventoActual.id || 0));
            renderPacientes(modal.find('select[name="paciente_persona_id"]'), '', eventoActual.paciente_persona_id || '');
            form.find('input[name="fecha"]').val(eventoActual.fecha || '');
            form.find('input[name="hora_inicio"]').val((eventoActual.hora || '').substring(0, 5));
            const horaFin = eventoActual.hora_fin || eventoActual.hor_fin || '';
            form.find('input[name="hora_fin"]').val(horaFin ? horaFin.substring(0, 5) : '');
            form.find('input[name="motivo"]').val(eventoActual.motivo || '');
            form.find('textarea[name="observaciones"]').val(eventoActual.nota || '');
            $('#modalDetalleCitaDoctor').modal('hide');
            modal.modal('show');
        });

        $('#modalEditarCitaDoctor').on('hidden.bs.modal', function () {
            $('#modalDetalleCitaDoctor').modal('show');
        });
    })();
</script>
@endsection
