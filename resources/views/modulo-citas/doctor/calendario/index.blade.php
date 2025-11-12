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
        <div class="btn-group mt-2 mt-md-0">
            <button class="btn btn-outline-primary"><i class="fas fa-calendar-plus"></i> Nueva cita</button>
            <button class="btn btn-outline-secondary"><i class="fas fa-clock"></i> Configurar horarios</button>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h3 class="h5 mb-0">Agenda semanal</h3>
                    <span class="text-muted small">Vista demo</span>
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
                                @foreach($calendarMatrix as $week)
                                    <tr>
                                        @foreach($week as $day)
                                            @php
                                                $dateKey = $day['date'];
                                                $eventsOfDay = $calendarEvents[$dateKey] ?? [];
                                                $classes = 'agenda-calendar__day';
                                                if (!empty($day['isMuted'])) {
                                                    $classes .= ' is-muted';
                                                }
                                                if (!empty($day['isToday'])) {
                                                    $classes .= ' is-today';
                                                }
                                            @endphp
                                            <td class="{{ $classes }}">
                                                <div class="agenda-calendar__day-number">{{ $day['label'] }}</div>
                                                @foreach($eventsOfDay as $event)
                                                    @php
                                                        $pillClass = match($event['estado']) {
                                                            'Confirmada' => 'bg-success',
                                                            'Pendiente'  => 'bg-warning text-dark',
                                                            'Cancelada'  => 'bg-danger',
                                                            default      => 'bg-secondary',
                                                        };
                                                    @endphp
                                                    <span class="agenda-calendar__pill {{ $pillClass }} js-event-pill"
                                                          data-toggle="modal"
                                                          data-target="#modalEventoAgenda"
                                                          data-event='@json($event)'>
                                                        {{ $event['hora'] }} · {{ \Illuminate\Support\Str::limit($event['motivo'], 12) }}
                                                    </span>
                                                @endforeach
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h3 class="h6 mb-0">Próximas 24 horas</h3>
                </div>
                <div class="list-group list-group-flush">
                    @foreach(array_slice($eventList, 0, 4) as $event)
                        <div class="list-group-item">
                            <strong>{{ $event['hora'] }}</strong> · {{ $event['paciente'] }}
                            <div class="text-muted small">{{ $event['motivo'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h3 class="h6 mb-0">Recordatorios</h3>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="fas fa-envelope text-primary mr-2"></i> Enviar indicaciones a Carlos</li>
                        <li class="mb-2"><i class="fas fa-vial text-success mr-2"></i> Revisar laboratorio de Ana</li>
                        <li><i class="fas fa-user-clock text-warning mr-2"></i> Confirmar disponibilidad de María</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEventoAgenda" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" data-field="titulo">Detalle de cita</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Paciente</dt>
                        <dd class="col-sm-8" data-field="titulo">-</dd>
                        <dt class="col-sm-4">Estado</dt>
                        <dd class="col-sm-8" data-field="estado">-</dd>
                        <dt class="col-sm-4">Motivo</dt>
                        <dd class="col-sm-8" data-field="motivo">-</dd>
                        <dt class="col-sm-4">Fecha</dt>
                        <dd class="col-sm-8" data-field="fecha">-</dd>
                        <dt class="col-sm-4">Hora</dt>
                        <dd class="col-sm-8" data-field="hora">-</dd>
                        <dt class="col-sm-4">Ubicación</dt>
                        <dd class="col-sm-8" data-field="ubicacion">-</dd>
                    </dl>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary">Reprogramar</button>
                    <button class="btn btn-primary">Registrar atención</button>
                </div>
            </div>
        </div>
    </div>
@endsection
