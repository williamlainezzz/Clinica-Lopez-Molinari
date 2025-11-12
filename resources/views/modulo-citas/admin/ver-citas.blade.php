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
                Nueva cita
            </button>
            <a href="#" class="btn btn-outline-secondary shadow-sm" role="button">
                <i class="fas fa-file-export"></i>
                Exportar CSV
            </a>
        </div>
    </div>
@endsection

@section('content')
    @include('modulo-citas.shared.summary-cards', ['metrics' => $metrics])

    @include('modulo-citas.shared.filter-bar', [
        'filters' => $filters,
        'action'  => route('agenda.citas'),
    ])

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="card-title fw-semibold mb-0">Citas programadas</h3>
                    <small class="text-muted">Listado general con acciones rápidas de gestión.</small>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-print"></i>
                        Imprimir
                    </button>
                    <button class="btn btn-sm btn-outline-success">
                        <i class="fas fa-file-excel"></i>
                        Exportar Excel
                    </button>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-uppercase small text-muted">Fecha</th>
                        <th class="text-uppercase small text-muted">Hora</th>
                        <th class="text-uppercase small text-muted">Paciente</th>
                        <th class="text-uppercase small text-muted">Doctor</th>
                        <th class="text-uppercase small text-muted">Motivo</th>
                        <th class="text-uppercase small text-muted">Estado</th>
                        <th class="text-uppercase small text-muted">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $cita)
                        @php
                            $badgeClass = match ($cita['estado']) {
                                'Confirmada' => 'bg-success',
                                'Pendiente'  => 'bg-warning text-dark',
                                'Cancelada'  => 'bg-danger',
                                'Completada' => 'bg-primary',
                                default      => 'bg-secondary',
                            };
                        @endphp
                        <tr>
                            <td class="fw-semibold">{{ \Illuminate\Support\Carbon::parse($cita['fecha'])->format('d/m/Y') }}</td>
                            <td>{{ $cita['hora'] }}</td>
                            <td>
                                <div class="fw-semibold">{{ $cita['paciente'] }}</div>
                                <small class="text-muted">{{ $cita['canal'] }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $cita['doctor'] }}</div>
                                <small class="text-muted">{{ $cita['ubicacion'] }}</small>
                            </td>
                            <td>{{ $cita['motivo'] }}</td>
                            <td>
                                <span class="badge {{ $badgeClass }}">{{ $cita['estado'] }}</span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-xs btn-outline-primary" data-cita-target="modalCitaAdmin" data-cita='@json($cita)'>
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-xs btn-outline-secondary">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-xs btn-outline-warning">
                                        <i class="fas fa-sync"></i>
                                    </button>
                                    <button class="btn btn-xs btn-outline-danger">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No hay citas con los filtros seleccionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 text-muted small">
            Última actualización: {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    @include('modulo-citas.shared.cita-modal', [
        'modalId' => 'modalCitaAdmin',
    ])
@endsection
