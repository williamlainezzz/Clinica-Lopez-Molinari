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
        </div>
    </div>
@endsection

@section('content')
    @php
        $eventCollection = collect($calendarEvents ?? []);
        $eventListCollection = collect($eventList ?? []);
        $nextEvent = $eventListCollection
            ->sortBy(fn($event) => trim(($event['fecha'] ?? '') . ' ' . ($event['hora'] ?? '')))
            ->first();
    @endphp
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h3 class="h6 mb-0">Calendario personal</h3>
                    <small class="text-muted">Toca una cita para ver los detalles.</small>
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
                                                $dateKey = $day['date'] ?? null;
                                                $eventsOfDay = $dateKey ? ($eventCollection[$dateKey] ?? []) : [];
                                                $classes = 'agenda-calendar__day';
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
                                                        $fechaEvento = ($event['fecha'] ?? '') . ' ' . ($event['hora'] ?? '00:00');
                                                        $isPast = $fechaEvento ? \Carbon\Carbon::parse($fechaEvento)->lt(now()) : false;
                                                        $pillClass = $isPast ? 'bg-danger text-white' : 'bg-primary';
                                                    @endphp
                                                    <span class="agenda-calendar__pill {{ $pillClass }} js-event-pill"
                                                          data-toggle="modal"
                                                          data-target="#modalEventoPaciente"
                                                          data-event='@json($event)'>
                                                        {{ substr($event['hora'] ?? '00:00', 0, 5) }} ·
                                                        {{ \Illuminate\Support\Str::limit($event['motivo'] ?? 'Cita', 14) }}
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
                    @if($nextEvent)
                        <p class="font-weight-bold mb-1">{{ $nextEvent['motivo'] ?? 'Cita' }}</p>
                        <p class="text-muted mb-1">{{ $nextEvent['fecha'] }} · {{ $nextEvent['hora'] }}</p>
                        <p class="text-muted mb-0">Doctor: {{ $nextEvent['doctor'] ?? 'Por asignar' }}</p>
                        <p class="text-muted mb-0">Estado: {{ $nextEvent['estado'] ?? 'Pendiente' }}</p>
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
                    <ul class="list-unstyled mb-0 text-muted">
                        <li class="mb-2"><i class="fas fa-bell text-warning mr-2"></i> Llega 15 minutos antes.</li>
                        <li class="mb-2"><i class="fas fa-tooth text-primary mr-2"></i> Lleva tus radiografías.</li>
                        <li><i class="fas fa-headset text-info mr-2"></i> Contacta a recepción si necesitas ayuda.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEventoPaciente" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalle de la cita</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Doctor</dt>
                        <dd class="col-sm-8" data-field="doctor">-</dd>
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
                    <button class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    (function () {
        $('.agenda-calendar').on('click', '.js-event-pill', function () {
            const data = $(this).data('event') || {};
            const modal = $('#modalEventoPaciente');
            modal.find('[data-field="doctor"]').text(data.doctor || '');
            modal.find('[data-field="motivo"]').text(data.motivo || '');
            modal.find('[data-field="fecha"]').text(data.fecha || '');
            modal.find('[data-field="hora"]').text(data.hora || '');
            modal.find('[data-field="estado"]').text(data.estado || 'Pendiente');
        });
    })();
</script>
@endsection
