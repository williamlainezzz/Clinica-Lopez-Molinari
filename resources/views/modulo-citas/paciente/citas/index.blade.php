@extends('adminlte::page')

@section('title', $pageTitle)

@section('content_header')
    <div class="d-flex flex-wrap justify-content-between align-items-start">
        <div class="mb-2">
            <h1 class="h3 font-weight-bold text-primary mb-1">{{ $heading }}</h1>
            <p class="text-muted mb-0">{{ $intro }}</p>
        </div>
        <a href="{{ route('agenda.calendario') }}" class="btn btn-outline-primary mt-2 mt-md-0">
            <i class="fas fa-calendar"></i> Ver agenda completa
        </a>
    </div>
@endsection

@section('content')
    <div class="row mb-4">
        @foreach($stats as $stat)
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted text-uppercase small mb-1">{{ $stat['label'] }}</p>
                        <h3 class="mb-0 font-weight-bold">{{ $stat['value'] }}</h3>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h3 class="h6 text-uppercase text-muted">Doctor asignado</h3>
                    <p class="font-weight-bold mb-1">{{ $patientRecord['profile']['doctor'] }}</p>
                    <p class="text-muted mb-2">{{ $patientRecord['profile']['especialidad'] }}</p>
                    <div class="bg-light p-3 rounded">
                        <small class="text-uppercase text-muted">Próxima cita</small>
                        <p class="mb-1 font-weight-bold">{{ $patientRecord['profile']['proxima']['fecha'] }} · {{ $patientRecord['profile']['proxima']['hora'] }}</p>
                        <p class="mb-0 text-muted">{{ $patientRecord['profile']['proxima']['motivo'] }} · {{ $patientRecord['profile']['proxima']['estado'] }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h3 class="h6 text-uppercase text-muted">Preparativos</h3>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i> Confirmaste asistencia</li>
                        <li class="mb-2"><i class="fas fa-file-upload text-primary mr-2"></i> Subiste tu radiografía</li>
                        <li><i class="fas fa-comment-medical text-info mr-2"></i> Recibirás recordatorio 24h antes</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h3 class="h6 mb-0">Mis próximas citas</h3>
            <span class="text-muted small">Vista de demostración</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Motivo</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($eventList as $event)
                        <tr>
                            <td>{{ $event['fecha'] }}</td>
                            <td>{{ $event['hora'] }}</td>
                            <td>{{ $event['motivo'] }}</td>
                            <td><span class="badge badge-info">{{ $event['estado'] }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">Sin citas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
