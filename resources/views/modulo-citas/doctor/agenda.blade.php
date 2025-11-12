@extends('adminlte::page')

@section('title', $pageTitle)

@section('content_header')
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
        <div>
            <h1 class="mb-1">{{ $heading }}</h1>
            <p class="text-muted mb-0">{{ $subheading }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-primary shadow-sm">
                <i class="fas fa-calendar-plus"></i>
                Nueva cita
            </button>
            <button class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-file-medical"></i>
                Registrar evolución
            </button>
        </div>
    </div>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-12 col-xl-8">
            @include('modulo-citas.shared.calendar', [
                'events'     => $events,
                'legend'     => $legend,
                'modalId'    => 'modalCitaAgendaDoctor',
                'calendarId' => 'calendar-doctor',
            ])
        </div>
        <div class="col-12 col-xl-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title fw-semibold mb-0">Próximas citas</h5>
                    <small class="text-muted">Resumen de tus atenciones para esta semana.</small>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach($upcoming as $cita)
                            <li class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="fw-semibold mb-1">{{ $cita['paciente'] }}</h6>
                                        <p class="mb-1 text-muted">{{ $cita['motivo'] }}</p>
                                        <small class="text-muted">{{ \Illuminate\Support\Carbon::parse($cita['fecha'])->format('d/m/Y') }} · {{ $cita['hora'] }}</small>
                                    </div>
                                    <span class="badge bg-primary">{{ $cita['estado'] }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @include('modulo-citas.shared.cita-modal', [
        'modalId'    => 'modalCitaAgendaDoctor',
        'showConfirm'=> true,
        'showCancel' => true,
    ])
@endsection
