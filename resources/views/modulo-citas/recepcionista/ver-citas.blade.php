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
                Agendar cita
            </button>
            <button class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-phone"></i>
                Registrar confirmación
            </button>
        </div>
    </div>
@endsection

@section('content')
    @include('modulo-citas.shared.filter-bar', [
        'filters' => $filters,
        'action'  => route('agenda.citas'),
    ])

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
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
                            <th class="text-uppercase small text-muted">Contacto</th>
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
                                <td>{{ \Illuminate\Support\Carbon::parse($cita['fecha'])->format('d/m/Y') }}</td>
                                <td>{{ $cita['hora'] }}</td>
                                <td>
                                    <strong>{{ $cita['paciente'] }}</strong>
                                    <div class="text-muted small">{{ $cita['canal'] }}</div>
                                </td>
                                <td>{{ $cita['doctor'] }}</td>
                                <td>{{ $cita['motivo'] }}</td>
                                <td><span class="badge {{ $badgeClass }}">{{ $cita['estado'] }}</span></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-xs btn-outline-secondary" title="Llamar">
                                            <i class="fas fa-phone"></i>
                                        </button>
                                        <button class="btn btn-xs btn-outline-secondary" title="Enviar WhatsApp">
                                            <i class="fab fa-whatsapp"></i>
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-xs btn-outline-primary" data-cita-target="modalCitaRecep" data-cita='@json($cita)'>
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-xs btn-outline-success">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-xs btn-outline-danger">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    Sin citas registradas en este rango.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('modulo-citas.shared.cita-modal', [
        'modalId' => 'modalCitaRecep',
    ])
@endsection
