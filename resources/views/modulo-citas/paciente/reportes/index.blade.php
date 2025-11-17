@extends('adminlte::page')

@section('title', $pageTitle)

@section('content_header')
    <div class="d-flex flex-wrap justify-content-between align-items-start">
        <div class="mb-2">
            <h1 class="h3 font-weight-bold text-primary mb-1">{{ $heading }}</h1>
            <p class="text-muted mb-0">{{ $intro }}</p>
        </div>
        <a href="{{ route('agenda.export.paciente_historial') }}" class="btn btn-outline-primary mt-2 mt-md-0" target="_blank">
            <i class="fas fa-file-download"></i> Descargar historial
        </a>
    </div>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Doctor</th>
                        <th>Motivo</th>
                        <th>Estado</th>
                        <th>Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patientRecord['historial'] ?? [] as $item)
                        @php
                            $estado = strtoupper($item['estado'] ?? '');
                            $badge = match ($estado) {
                                'CONFIRMADA' => 'success',
                                'COMPLETADA' => 'primary',
                                'CANCELADA'  => 'danger',
                                default      => 'secondary',
                            };
                        @endphp
                        <tr>
                            <td>{{ $item['fecha'] }}</td>
                            <td>{{ $item['doctor'] }}</td>
                            <td>{{ $item['motivo'] }}</td>
                            <td><span class="badge badge-{{ $badge }}">{{ $item['estado'] }}</span></td>
                            <td>{{ $item['detalle'] ?? 'Sin detalle' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Aún no tienes citas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
