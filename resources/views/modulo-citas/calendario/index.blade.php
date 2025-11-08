@extends('adminlte::page')

@section('title', $pageTitle ?? 'Calendario de citas')

@section('content_header')
    <h1>{{ $heading ?? 'Calendario' }}</h1>
@endsection

@section('content')
    @includeIf($bannerPartial)

    <div class="row">
        <div class="col-lg-9 mb-4">
            <div class="card card-primary card-outline shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap: .75rem;">
                    <div>
                        <h3 class="card-title mb-0"><i class="fas fa-calendar-alt mr-2"></i> Calendario de citas</h3>
                        <small class="text-muted">Vista interactiva de la agenda diaria.</small>
                    </div>
                    <span class="badge badge-{{ $isReadOnly ? 'secondary' : 'success' }}">
                        {{ $isReadOnly ? 'Solo lectura' : 'Gestión habilitada' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        @includeIf($toolbarPartial)
                    </div>
                    <div id="agenda-calendar" data-events='@json($calendarEvents ?? [])'></div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header">
                    <h3 class="card-title mb-0"><i class="fas fa-bolt mr-2"></i> Acciones rápidas</h3>
                </div>
                <div class="card-body">
                    @forelse($quickActions ?? [] as $action)
                        <button type="button"
                                class="{{ $action['class'] ?? 'btn btn-primary btn-block' }} mb-2 {{ !empty($action['disabled']) ? 'disabled' : '' }}">
                            <i class="{{ $action['icon'] ?? 'fas fa-circle' }} mr-1"></i>
                            {{ $action['label'] ?? 'Acción' }}
                        </button>
                        @if(!empty($action['description']))
                            <p class="text-muted small mb-3">{{ $action['description'] }}</p>
                        @endif
                    @empty
                        <p class="text-muted mb-0">No hay acciones disponibles para tu rol.</p>
                    @endforelse
                </div>
                @if(!empty($legend))
                    <div class="card-footer bg-light">
                        <div class="small text-muted mb-2">Estados de cita</div>
                        <ul class="list-unstyled mb-0">
                            @foreach($legend as $item)
                                <li class="mb-1">
                                    <span class="badge badge-{{ $item['variant'] ?? 'secondary' }} mr-2">&nbsp;</span>
                                    {{ $item['label'] ?? '' }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-header">
                    <h3 class="card-title mb-0"><i class="fas fa-check-circle mr-2"></i> Lo que puedes hacer</h3>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        @foreach(($capabilities ?? []) as $capability)
                            <li class="mb-2"><i class="fas fa-arrow-circle-right text-primary mr-2"></i>{{ $capability }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title mb-0"><i class="fas fa-clock mr-2"></i> Próximas citas</h3>
                </div>
                <div class="list-group list-group-flush">
                    @forelse(($upcomingEvents ?? []) as $event)
                        @php
                            $estado = $event['estado'] ?? 'Pendiente';
                            $variant = match ($estado) {
                                'Confirmada' => 'success',
                                'Cancelada'  => 'danger',
                                default      => 'warning',
                            };
                        @endphp
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>{{ $event['fecha'] }}</strong>
                                    <div class="text-muted small">{{ $event['hora'] }} · {{ $event['doctor'] }}</div>
                                </div>
                                <span class="badge badge-{{ $variant }} align-self-start">{{ $estado }}</span>
                            </div>
                            <div class="mt-2">
                                <i class="fas fa-user mr-1 text-muted"></i>{{ $event['paciente'] }}
                            </div>
                            <p class="text-muted small mb-0 mt-1"><i class="fas fa-notes-medical mr-1"></i>{{ $event['motivo'] }}</p>
                        </div>
                    @empty
                        <div class="list-group-item text-muted">Sin citas programadas.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
    <style>
        #agenda-calendar {
            min-height: 620px;
        }
    </style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('agenda-calendar');
            if (!calendarEl) {
                return;
            }

            const events = calendarEl.dataset.events ? JSON.parse(calendarEl.dataset.events) : [];

            const calendar = new FullCalendar.Calendar(calendarEl, {
                height: 'auto',
                locale: 'es',
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: events,
                editable: {{ $isReadOnly ? 'false' : 'true' }},
                selectable: {{ $isReadOnly ? 'false' : 'true' }},
                eventClick: function (info) {
                    const props = info.event.extendedProps || {};
                    const detalle = [
                        'Paciente: ' + (props.paciente || ''),
                        'Doctor: ' + (props.doctor || ''),
                        'Estado: ' + (props.estado || ''),
                        'Motivo: ' + (props.motivo || '')
                    ].join('\n');
                    alert(detalle);
                }
            });

            calendar.render();
        });
    </script>
@endpush
