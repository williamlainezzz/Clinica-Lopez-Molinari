@extends('adminlte::page')

@section('title', $pageTitle)

@section('content_header')
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
        <div>
            <h1 class="mb-1">{{ $heading }}</h1>
            <p class="text-muted mb-0">{{ $subheading }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-outline-primary shadow-sm">
                <i class="fas fa-download"></i>
                Descargar comprobante
            </button>
        </div>
    </div>
@endsection

@section('content')
    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="fw-semibold mb-3">Tu doctor asignado</h5>
                    <p class="fw-semibold mb-1">{{ $doctorProfile['nombre'] }}</p>
                    <p class="text-muted mb-3">{{ $doctorProfile['especialidad'] }}</p>
                    <ul class="list-unstyled mb-0 text-muted small">
                        <li class="mb-2"><i class="fas fa-envelope me-2 text-primary"></i>{{ $doctorProfile['correo'] }}</li>
                        <li class="mb-2"><i class="fas fa-phone me-2 text-primary"></i>{{ $doctorProfile['telefono'] }}</li>
                        <li class="mb-2"><i class="fas fa-map-marker-alt me-2 text-primary"></i>{{ $doctorProfile['ubicacion'] }}</li>
                        <li><i class="fas fa-clock me-2 text-primary"></i>{{ $doctorProfile['horario'] }}</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="fw-semibold mb-3">Próxima cita</h5>
                    @if($nextAppointment)
                        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                            <div>
                                <h3 class="fw-bold mb-1">{{ \Illuminate\Support\Carbon::parse($nextAppointment['fecha'])->locale('es')->isoFormat('D [de] MMMM') }}</h3>
                                <p class="mb-0 text-muted">{{ $nextAppointment['hora'] }} · {{ $nextAppointment['motivo'] }}</p>
                            </div>
                            <div class="text-muted small">
                                <p class="mb-1"><i class="fas fa-map-marker-alt text-primary me-2"></i>{{ $nextAppointment['ubicacion'] }}</p>
                                <p class="mb-0"><i class="fas fa-info-circle text-primary me-2"></i>{{ $nextAppointment['nota'] }}</p>
                            </div>
                            <div class="d-flex flex-column gap-2">
                                <button class="btn btn-outline-success">
                                    <i class="fas fa-check"></i>
                                    Confirmar asistencia
                                </button>
                                <button class="btn btn-outline-danger">
                                    <i class="fas fa-sync"></i>
                                    Solicitar cambio
                                </button>
                            </div>
                        </div>
                    @else
                        <p class="text-muted mb-0">No tienes citas programadas. Agenda una nueva cita desde la recepción.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="mb-3">
        <h5 class="fw-semibold mb-1">Agenda personal</h5>
        <small class="text-muted">Toca una cita en el calendario para ver los detalles.</small>
    </div>

    @include('modulo-citas.shared.calendar', [
        'events'     => $events,
        'legend'     => $legend,
        'modalId'    => 'modalCitaPaciente',
        'calendarId' => 'calendar-paciente',
    ])

    @include('modulo-citas.shared.cita-modal', [
        'modalId'   => 'modalCitaPaciente',
        'readonly'  => true,
        'showCancel'=> false,
        'showConfirm' => false,
    ])
@endsection
