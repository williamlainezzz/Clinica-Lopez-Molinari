@extends('adminlte::page')
@include('modulo-citas.shared._calendar_styles')
@include('modulo-citas.shared._calendar_scripts')

@section('title', $pageTitle ?? 'Agenda · Administración')

@section('content_header')
    <div class="d-flex flex-wrap justify-content-between align-items-start">
        <div class="mb-2">
            <h1 class="h3 font-weight-bold text-primary mb-1">{{ ucfirst($heading ?? 'Agenda global') }}</h1>
            <p class="text-muted mb-0">{{ $intro ?? 'Gestiona la agenda completa de la clínica.' }}</p>
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
            <button class="btn btn-primary ml-2" data-toggle="modal" data-target="#modalCrearCitaCalendario">
                <i class="fas fa-plus-circle"></i> Crear cita
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
                        <small class="text-muted">Selecciona un evento para ver detalles.</small>
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
                                                          data-target="#modalEventoAdmin"
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
                        <p class="text-muted text-center mb-0 mt-3">No hay datos para mostrar en el calendario.</p>
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
                    @forelse(($eventList instanceof \Illuminate\Support\Collection ? $eventList : collect($eventList ?? []))->take(8) as $event)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $event['paciente'] ?? 'Paciente' }}</strong>
                                <span class="text-muted">{{ substr($event['hora'] ?? '00:00', 0, 5) }}</span>
                            </div>
                            <small class="text-muted d-block">{{ $event['fecha'] ?? '' }} · {{ $event['doctor'] ?? '' }}</small>
                            <span class="badge badge-pill badge-light text-capitalize">{{ strtolower($event['motivo'] ?? 'Motivo') }}</span>
                        </div>
                    @empty
                        <div class="list-group-item text-muted text-center">Sin citas programadas.</div>
                    @endforelse
                </div>
            </div>
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h3 class="h6 mb-3">Acciones</h3>
                    <button class="btn btn-outline-primary btn-block mb-2" data-toggle="modal" data-target="#modalCrearCitaCalendario">
                        <i class="fas fa-calendar-plus"></i> Nueva cita
                    </button>
                    <a href="{{ route('export.citas.csv') }}" class="btn btn-outline-secondary btn-block">
                        <i class="fas fa-download"></i> Descargar agenda
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Detalle de evento --}}
    <div class="modal fade" id="modalEventoAdmin" tabindex="-1" aria-hidden="true">
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
                    <form method="POST" id="formEliminarCita" class="mr-auto">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </form>
                    <button class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button class="btn btn-primary" id="btnEditarDesdeDetalle">
                        <i class="fas fa-pen"></i> Editar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Crear cita (calendario) --}}
    <div class="modal fade" id="modalCrearCitaCalendario" tabindex="-1" aria-hidden="true">
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

    {{-- Modal: Editar cita --}}
    <div class="modal fade" id="modalEditarCitaCalendario" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="POST" class="modal-content" id="formEditarCitaCalendario">
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
        let currentEvent = null;

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

        $('#modalCrearCitaCalendario').on('show.bs.modal', function () {
            const modal = $(this);
            modal.find('form')[0].reset();
            hydrateSelect(modal, modal.find('select[name="doctor_persona_id"]').val());
        });

        $('#modalCrearCitaCalendario select[name="doctor_persona_id"]').on('change', function () {
            hydrateSelect($('#modalCrearCitaCalendario'), this.value);
        });

        $('#modalEditarCitaCalendario select[name="doctor_persona_id"]').on('change', function () {
            hydrateSelect($('#modalEditarCitaCalendario'), this.value, $('#modalEditarCitaCalendario').data('selectedPatient'));
        });

        $('.agenda-calendar').on('click', '.js-event-pill', function () {
            const eventData = $(this).data('event') || {};
            currentEvent = eventData;
            const modal = $('#modalEventoAdmin');
            modal.find('[data-field="doctor"]').text(eventData.doctor || '');
            modal.find('[data-field="paciente"]').text(eventData.paciente || '');
            modal.find('[data-field="motivo"]').text(eventData.motivo || '');
            modal.find('[data-field="fecha"]').text(eventData.fecha || '');
            modal.find('[data-field="hora"]').text(eventData.hora || '');
            modal.find('[data-field="estado"]').text(eventData.estado || '');
            modal.find('#formEliminarCita').attr('action', baseUrl + '/' + (eventData.id || 0));
        });

        $('#btnEditarDesdeDetalle').on('click', function () {
            if (!currentEvent) {
                return;
            }
            const modal = $('#modalEditarCitaCalendario');
            const form = modal.find('#formEditarCitaCalendario');
            form.attr('action', baseUrl + '/' + (currentEvent.id || 0));
            modal.data('selectedPatient', currentEvent.paciente_persona_id || '');
            form[0].reset();
            form.find('input[name="fecha"]').val(currentEvent.fecha || '');
            form.find('input[name="hora_inicio"]').val((currentEvent.hora || '').substring(0,5));
            const horaFin = currentEvent.hora_fin || currentEvent.hor_fin || '';
            form.find('input[name="hora_fin"]').val(horaFin ? horaFin.substring(0,5) : '');
            form.find('input[name="motivo"]').val(currentEvent.motivo || '');
            form.find('textarea[name="observaciones"]').val(currentEvent.nota || '');
            form.find('select[name="doctor_persona_id"]').val(currentEvent.doctor_persona_id || '').trigger('change');
            hydrateSelect(modal, form.find('select[name="doctor_persona_id"]').val(), currentEvent.paciente_persona_id || '');
            $('#modalEventoAdmin').modal('hide');
            modal.modal('show');
        });

        $('#modalEditarCitaCalendario').on('shown.bs.modal', function () {
            if (currentEvent) {
                hydrateSelect($('#modalEditarCitaCalendario'), currentEvent.doctor_persona_id || '', currentEvent.paciente_persona_id || '');
            }
        });

        $('#modalEditarCitaCalendario').on('hidden.bs.modal', function () {
            $(this).removeData('selectedPatient');
        });
    })();
</script>
@endsection
