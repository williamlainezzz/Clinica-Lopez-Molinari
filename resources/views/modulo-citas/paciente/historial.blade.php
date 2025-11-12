@extends('adminlte::page')

@section('title', $pageTitle)

@section('content_header')
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
        <div>
            <h1 class="mb-1">{{ $heading }}</h1>
            <p class="text-muted mb-0">{{ $subheading }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-print"></i>
                Imprimir historial
            </button>
        </div>
    </div>
@endsection

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-uppercase small text-muted">Fecha</th>
                            <th class="text-uppercase small text-muted">Hora</th>
                            <th class="text-uppercase small text-muted">Doctor</th>
                            <th class="text-uppercase small text-muted">Motivo</th>
                            <th class="text-uppercase small text-muted">Estado</th>
                            <th class="text-uppercase small text-muted">Resultado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($history as $item)
                            @php
                                $badge = match ($item['estado']) {
                                    'Completada' => 'bg-success',
                                    'Cancelada'  => 'bg-danger',
                                    default      => 'bg-secondary',
                                };
                            @endphp
                            <tr>
                                <td>{{ $item['fecha'] }}</td>
                                <td>{{ $item['hora'] }}</td>
                                <td>{{ $item['doctor'] }}</td>
                                <td>{{ $item['motivo'] }}</td>
                                <td><span class="badge {{ $badge }}">{{ $item['estado'] }}</span></td>
                                <td>{{ $item['resultado'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Aún no tienes historial de citas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
