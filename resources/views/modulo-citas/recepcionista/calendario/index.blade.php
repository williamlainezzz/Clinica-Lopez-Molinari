@extends('adminlte::page')
@include('modulo-citas.shared._calendar_styles')
@include('modulo-citas.shared._calendar_scripts')

@section('title', $pageTitle ?? 'Agenda · Recepción')

@section('content_header')
    <div class="d-flex flex-wrap justify-content-between align-items-start">
        <div class="mb-2">
            <h1 class="h3 font-weight-bold text-primary mb-1">{{ ucfirst($heading ?? 'Agenda') }}</h1>
            <p class="text-muted mb-0">{{ $intro ?? 'Coordina todas las citas de la clínica.' }}</p>
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
            <button class="btn btn-primary ml-2" data-toggle="modal" data-target="#modalCrearCitaRecepcion">
                <i class="fas fa-plus-circle"></i> Nueva cita
            </button>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h3 class="h5 mb-0">{{ $calendarContext['label'] ?? 'Agenda' }}</h3>
                        <small class="text-muted">Haz clic en cualquier cita para ver o editar detalles.</small>
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
                                                          data-target="#modalEventoRecepcion"
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
                        <p class="text-muted text-center mb-0 mt-3">Sin datos para mostrar.</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h3 class="h6 mb-0">Próximas citas</h3>
                    <span class="badge badge-light">{{ ($eventList instanceof \Illuminate\Support\Collection ? $eventList->count() : count($eventList ?? [])) }}</span>
                </div>
                <div class="list-group list-group-flush" style="max-height: 420px; overflow-y: auto;">
                    @forelse(collect($eventList ?? [])->take(8) as $event)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $event['paciente'] ?? 'Paciente' }}</strong>
                                <span class="text-muted">{{ substr($event['hora'] ?? '00:00', 0, 5) }}</span>
                            </div>
                            <small class="text-muted">{{ $event['fecha'] ?? '' }} · {{ $event['doctor'] ?? '' }}</small>
                            <div>
                                <span class="badge badge-pill badge-light text-capitalize">{{ strtolower($event['motivo'] ?? 'Motivo') }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item text-center text-muted">No hay citas próximas.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Modal detalle --}}
    <div class="modal fade" id="modalEventoRecepcion" tabindex="-1" aria-hidden="true">
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
                        <dt class="col-sm-4">Doctor</dt>
                        <dd class="col-sm-8" data-field="doctor">-</dd>
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
                    </dl>
                </div>
                <div class="modal-footer">
                    <form method="POST" id="formEliminarCitaRecepcion" class="mr-auto">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </form>
                    <button class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button class="btn btn-primary" id="btnEditarEventoRecepcion" data-toggle="modal" data-target="#modalEditarCitaRecepcion">
                        <i class="fas fa-pen"></i> Editar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal crear --}}
    <div class="modal fade" id="modalCrearCitaRecepcion" tabindex="-1" aria-hidden="true">
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
                            <label>Doctor</label>
                            <select name="doctor_persona_id" class="form-control" required>
                                <option value="">Seleccione un doctor...</option>
                                @foreach(($doctorsList ?? []) as $doc)
                                    <option value="{{ $doc['persona_id'] }}">{{ $doc['nombre'] }} ({{ $doc['usuario'] }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Paciente</label>
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
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal editar --}}
    <div class="modal fade" id="modalEditarCitaRecepcion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="POST" class="modal-content" id="formEditarCitaRecepcion">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Editar cita</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Doctor</label>
                            <select name="doctor_persona_id" class="form-control" required>
                                <option value="">Seleccione un doctor...</option>
                                @foreach(($doctorsList ?? []) as $doc)
                                    <option value="{{ $doc['persona_id'] }}">{{ $doc['nombre'] }} ({{ $doc['usuario'] }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Paciente</label>
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
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
<script>
    (function () {
        const map = @json($doctorPatientMap ?? []);
        const baseUrl = "{{ url('/agenda/citas') }}";
        let eventoActual = null;

        function hydrateSelect(modal, doctorId, selectedPatient) {
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
                const option = $('<option></option>').val(patient.persona_id).text(patient.nombre + ' (' + patient.codigo + ')');
                if (String(patient.persona_id) === String(selectedPatient || '')) {
                    option.attr('selected', true);
                }
                select.append(option);
            });
        }

        $('#modalCrearCitaRecepcion').on('show.bs.modal', function () {
            const modal = $(this);
            modal.find('form')[0].reset();
            hydrateSelect(modal, modal.find('select[name="doctor_persona_id"]').val());
        });

        $('#modalCrearCitaRecepcion select[name="doctor_persona_id"]').on('change', function () {
            hydrateSelect($('#modalCrearCitaRecepcion'), this.value);
        });

        $('#modalEditarCitaRecepcion select[name="doctor_persona_id"]').on('change', function () {
            hydrateSelect($('#modalEditarCitaRecepcion'), this.value, $('#modalEditarCitaRecepcion').data('selectedPatient'));
        });

        $('.agenda-calendar').on('click', '.js-event-pill', function () {
            const eventData = $(this).data('event') || {};
            eventoActual = eventData;
            const modal = $('#modalEventoRecepcion');
            modal.find('[data-field="doctor"]').text(eventData.doctor || '');
            modal.find('[data-field="paciente"]').text(eventData.paciente || '');
            modal.find('[data-field="motivo"]').text(eventData.motivo || '');
            modal.find('[data-field="fecha"]').text(eventData.fecha || '');
            modal.find('[data-field="hora"]').text(eventData.hora || '');
            modal.find('[data-field="estado"]').text(eventData.estado || '');
            modal.find('#formEliminarCitaRecepcion').attr('action', baseUrl + '/' + (eventData.id || 0));
        });

        $('#btnEditarEventoRecepcion').on('click', function () {
            if (!eventoActual) {
                return;
            }
            const modal = $('#modalEditarCitaRecepcion');
            const form = modal.find('#formEditarCitaRecepcion');
            form.attr('action', baseUrl + '/' + (eventoActual.id || 0));
            modal.data('selectedPatient', eventoActual.paciente_persona_id || '');
            form[0].reset();
            form.find('input[name="fecha"]').val(eventoActual.fecha || '');
            form.find('input[name="hora_inicio"]').val((eventoActual.hora || '').substring(0,5));
            const horaFin = eventoActual.hora_fin || eventoActual.hor_fin || '';
            form.find('input[name="hora_fin"]').val(horaFin ? horaFin.substring(0,5) : '');
            form.find('input[name="motivo"]').val(eventoActual.motivo || '');
            form.find('textarea[name="observaciones"]').val(eventoActual.nota || '');
            form.find('select[name="doctor_persona_id"]').val(eventoActual.doctor_persona_id || '').trigger('change');
            hydrateSelect(modal, form.find('select[name="doctor_persona_id"]').val(), eventoActual.paciente_persona_id || '');
            $('#modalEventoRecepcion').modal('hide');
            modal.modal('show');
        });

        $('#modalEditarCitaRecepcion').on('hidden.bs.modal', function () {
            $(this).removeData('selectedPatient');
        });
    })();
</script>
@endsection
