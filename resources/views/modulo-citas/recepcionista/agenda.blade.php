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
                <i class="fas fa-plus"></i>
                Crear cita
            </button>
            <button class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-sync"></i>
                Sincronizar agenda
            </button>
        </div>
    </div>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-12 col-xl-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="fw-semibold mb-3">Notas del día</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <span class="badge bg-info me-2">08:00</span>
                            Llegará un grupo escolar para fluorización.
                        </li>
                        <li class="mb-3">
                            <span class="badge bg-warning me-2">11:30</span>
                            Reconfirmar cita de Carlos Pérez.
                        </li>
                        <li>
                            <span class="badge bg-success me-2">15:00</span>
                            Bloqueado consultorio 3 para mantenimiento.
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-8">
            @include('modulo-citas.shared.calendar', [
                'events'     => $events,
                'legend'     => $legend,
                'modalId'    => 'modalCitaAgendaRecep',
                'calendarId' => 'calendar-recepcion',
            ])
        </div>
    </div>

    @include('modulo-citas.shared.cita-modal', [
        'modalId' => 'modalCitaAgendaRecep',
    ])
@endsection
