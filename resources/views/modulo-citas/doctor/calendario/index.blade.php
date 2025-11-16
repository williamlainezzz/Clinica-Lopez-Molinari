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
            <button class="btn btn-outline-primary">
                <i class="fas fa-calendar-plus"></i> Agendar bloque
            </button>
            <button class="btn btn-outline-secondary">
                <i class="fas fa-sync"></i> Actualizar
            </button>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        {{-- CALENDARIO PRINCIPAL --}}
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="h5 mb-0">Calendario noviembre 2025</h3>
                        <small class="text-muted">Click sobre una cita para ver detalle.</small>
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
                                @foreach($calendarMatrix as $week)
                                    <tr>
                                        @foreach($week as $day)
                                            @php
                                                $dateKey      = $day['date'];
                                                $eventsOfDay  = $calendarEvents[$dateKey] ?? [];
                                                $classes      = 'agenda-calendar__day';

                                                if (!empty($day['isMuted'])) {
                                                    $classes .= ' is-muted';
                                                }
                                                if (!empty($day['isToday'])) {
                                                    $classes .= ' is-today';
                                                }
                                            @endphp

                                            <td class="{{ $classes }}">
                                                <div class="agenda-calendar__day-number">
                                                    {{ $day['label'] }}
                                                </div>

                                                @foreach($eventsOfDay as $event)
                                                    @php
                                                        $estado = $event['estado'] ?? 'Pendiente';

                                                        $pillClass = match($estado) {
                                                            'Confirmada', 'CONFIRMADA' => 'bg-success',
                                                            'Cancelada',  'CANCELADA'  => 'bg-danger',
                                                            default                    => 'bg-warning text-dark',
                                                        };

                                                        $paciente = $event['paciente'] ?? '-';
                                                        $hora     = $event['hora']     ?? '';
                                                    @endphp

                                                    <span class="agenda-calendar__pill {{ $pillClass }} js-event-pill"
                                                          data-toggle="modal"
                                                          data-target="#modalEventoAgenda"
                                                          data-event='@json($event)'>
                                                        {{ $hora }} · {{ \Illuminate\Support\Str::limit($paciente, 14) }}
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

        {{-- PANEL LATERAL: PRÓXIMAS CITAS --}}
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h3 class="h6 mb-0">Próximas citas</h3>
                </div>

                @if(empty($eventList) || count($eventList) === 0)
                    <div class="card-body">
                        <p class="text-muted mb-0">
                            No tienes citas programadas en los próximos días.
                        </p>
                    </div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach($eventList as $i => $event)
                            @break($i >= 6) {{-- solo mostramos las primeras 6 --}}

                            @php
                                $paciente = $event['paciente'] ?? '-';
                                $hora     = $event['hora']     ?? '';
                                $fecha    = $event['fecha']    ?? '';
                                $motivo   = $event['motivo']   ?? '';
                            @endphp

                            <div class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <strong>{{ $paciente }}</strong>
                                    <span class="text-muted">{{ $hora }}</span>
                                </div>
                                <small class="text-muted d-block">
                                    {{ $fecha }}
                                </small>
                                @if($motivo)
                                    <span class="badge badge-pill badge-light text-capitalize">
                                        {{ \Illuminate\Support\Str::limit($motivo, 24) }}
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted">Acciones rápidas</h6>
                    <button class="btn btn-primary btn-block mb-2">
                        <i class="fas fa-calendar-plus"></i> Crear cita manual
                    </button>
                    <button class="btn btn-outline-secondary btn-block">
                        <i class="fas fa-file-alt"></i> Descargar mi agenda
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL: DETALLE DE CITA --}}
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
                        <dd class="col-sm-8" data-field="paciente">-</dd>

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
