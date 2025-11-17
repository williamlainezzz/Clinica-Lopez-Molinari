@extends('adminlte::page')

@section('title', $pageTitle)

@section('content_header')
    <div class="d-flex flex-wrap justify-content-between align-items-start">
        <div class="mb-2">
            <h1 class="h3 font-weight-bold text-primary mb-1">{{ $heading }}</h1>
            <p class="text-muted mb-0">{{ $intro }}</p>
        </div>
        <a href="{{ route('agenda.reportes') }}" class="btn btn-outline-primary mt-2 mt-md-0">
            <i class="fas fa-history"></i> Ver historial
        </a>
    </div>
@endsection

@section('content')
    <div class="row mb-4">
        @forelse($stats as $stat)
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <span class="badge badge-{{ $stat['color'] ?? 'secondary' }} mr-3 p-3 rounded-circle text-white">
                            <i class="{{ $stat['icon'] ?? 'fas fa-info-circle' }}"></i>
                        </span>
                        <div>
                            <p class="text-muted text-uppercase small mb-1">{{ $stat['label'] }}</p>
                            <h3 class="mb-0 font-weight-bold">{{ $stat['value'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light border text-muted mb-0">
                    Aún no hay estadísticas disponibles. Programa tu primera cita para ver actividad aquí.
                </div>
            </div>
        @endforelse
    </div>

    @php
        $profile = $patientRecord['profile'] ?? [];
        $doctorName = $profile['doctor'] ?? 'Sin doctor asignado';
        $especialidad = $profile['especialidad'] ?? 'Odontología';
        $proxima = $profile['proxima'] ?? [];
        $nextDate = $proxima['fecha'] ?? null;
        $nextTime = $proxima['hora'] ?? null;
        $nextMotivo = $proxima['motivo'] ?? null;
        $nextEstado = $proxima['estado'] ?? null;
    @endphp

    <div class="row mb-4">
        <div class="col-12 col-lg-8 col-xl-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h3 class="h6 text-uppercase text-muted">Doctor asignado</h3>
                    <p class="font-weight-bold mb-1">{{ $doctorName }}</p>
                    <p class="text-muted mb-3">{{ $especialidad }}</p>
                    <div class="bg-light p-3 rounded">
                        <small class="text-uppercase text-muted">Próxima cita</small>
                        @if($nextDate || $nextTime)
                            <p class="mb-1 font-weight-bold">
                                {{ $nextDate ?? 'Fecha por definir' }}
                                @if($nextTime)
                                    · {{ $nextTime }}
                                @endif
                            </p>
                            <p class="mb-0 text-muted">
                                {{ $nextMotivo ?? 'Motivo por definir' }}
                                @if($nextEstado)
                                    · {{ $nextEstado }}
                                @endif
                            </p>
                        @else
                            <p class="mb-0 text-muted">Aún no tienes una cita programada.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $upcoming = collect($eventList ?? [])->sortBy(function ($event) {
            $fecha = $event['fecha'] ?? '';
            $hora  = $event['hora'] ?? '';

            return trim($fecha . ' ' . $hora);
        })->values();
    @endphp

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h3 class="h6 mb-0">Mis próximas citas</h3>
            <span class="text-muted small">Actualizado automáticamente</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Doctor</th>
                        <th>Motivo</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($upcoming as $event)
                        @php
                            $estado = strtoupper($event['estado'] ?? '');
                            $badge = match ($estado) {
                                'CONFIRMADA' => 'success',
                                'CANCELADA'  => 'danger',
                                'PENDIENTE'  => 'warning',
                                default      => 'secondary',
                            };
                            $estadoLabel = $event['estado'] ?? 'Sin estado';
                        @endphp
                        <tr>
                            <td>{{ $event['fecha'] }}</td>
                            <td>{{ $event['hora'] }}</td>
                            <td>{{ $event['doctor'] ?? 'Por asignar' }}</td>
                            <td>{{ $event['motivo'] ?? 'Motivo no registrado' }}</td>
                            <td>
                                <span class="badge badge-{{ $badge }}">{{ $estadoLabel }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Sin citas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
