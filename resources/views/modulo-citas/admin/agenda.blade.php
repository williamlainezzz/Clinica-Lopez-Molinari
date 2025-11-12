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
                <i class="fas fa-layer-group"></i>
                Gestionar agendas
            </button>
        </div>
    </div>
@endsection

@section('content')
    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="fw-semibold mb-3">Acciones rápidas</h5>
                    <div class="d-grid gap-2">
                        <button class="btn btn-light border text-start">
                            <i class="fas fa-user-plus text-primary"></i>
                            Programar cita para paciente nuevo
                        </button>
                        <button class="btn btn-light border text-start">
                            <i class="fas fa-user-md text-success"></i>
                            Reservar bloque para un doctor
                        </button>
                        <button class="btn btn-light border text-start">
                            <i class="fas fa-bell text-warning"></i>
                            Enviar recordatorios de hoy
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-8">
            @include('modulo-citas.shared.calendar', [
                'events'   => $events,
                'legend'   => $legend,
                'modalId'  => 'modalCitaAgendaAdmin',
                'calendarId' => 'calendar-admin',
            ])
        </div>
    </div>

    @include('modulo-citas.shared.cita-modal', [
        'modalId' => 'modalCitaAgendaAdmin',
    ])
@endsection
