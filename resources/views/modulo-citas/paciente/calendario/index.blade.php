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
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h3 class="h6 mb-0">Calendario personal</h3>
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
                                                    <span class="agenda-calendar__pill bg-soft js-event-pill"
                                                          data-toggle="modal"
                                                          data-target="#modalEventoAgenda"
                                                          data-event='@json($event)'>
                                                        {{ $event['hora'] }} · {{ \Illuminate\Support\Str::limit($event['motivo'], 15) }}
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
                    <h3 class="h6 mb-0">Detalle próximo evento</h3>
                </div>
                <div class="card-body">
                    @if(!empty($eventList))
                        <p class="font-weight-bold mb-1">{{ $eventList[0]['motivo'] }}</p>
                        <p class="text-muted mb-1">{{ $eventList[0]['fecha'] }} · {{ $eventList[0]['hora'] }}</p>
                        <p class="text-muted mb-0">Doctor: {{ $eventList[0]['doctor'] }}</p>
                    @else
                        <p class="text-muted mb-0">No hay citas registradas.</p>
                    @endif
                </div>
            </div>
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h3 class="h6 mb-0">Recomendaciones</h3>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="fas fa-bell text-warning mr-2"></i> Llega 15 minutos antes</li>
                        <li class="mb-2"><i class="fas fa-tooth text-primary mr-2"></i> Lleva tus radiografías</li>
                        <li><i class="fas fa-headset text-info mr-2"></i> Comunícate con recepción si necesitas ayuda</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEventoAgenda" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" data-field="titulo">Mi cita</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Doctor</dt>
                        <dd class="col-sm-8" data-field="doctor">-</dd>
                        <dt class="col-sm-4">Estado</dt>
                        <dd class="col-sm-8" data-field="estado">-</dd>
                        <dt class="col-sm-4">Motivo</dt>
                        <dd class="col-sm-8" data-field="motivo">-</dd>
                        <dt class="col-sm-4">Fecha</dt>
                        <dd class="col-sm-8" data-field="fecha">-</dd>
                        <dt class="col-sm-4">Hora</dt>
                        <dd class="col-sm-8" data-field="hora">-</dd>
                    </dl>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endsection
