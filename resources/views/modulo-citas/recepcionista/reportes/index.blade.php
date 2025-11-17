@extends('adminlte::page')

@section('title', $pageTitle)

@section('content_header')
    <div class="d-flex flex-wrap justify-content-between align-items-start">
        <div class="mb-2">
            <h1 class="h3 font-weight-bold text-primary mb-1">{{ ucfirst($heading) }}</h1>
            <p class="text-muted mb-0">{{ $intro }}</p>
        </div>
        <a href="{{ route('agenda.export.recepcionista_bitacora') }}" class="btn btn-outline-primary mt-2 mt-md-0" target="_blank">
            <i class="fas fa-download"></i> Exportar bitácora
        </a>
    </div>
@endsection

@section('content')
    @php
        $eventCollection = collect($eventList ?? []);
        $porEstado       = $eventCollection->groupBy('estado')->map->count();
        $timelineSafe    = collect($timeline ?? []);
    @endphp

    {{-- Resumen por estado --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <h3 class="h6 mb-0">Estados registrados</h3>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Estado</th>
                        <th class="text-right">Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($porEstado as $estado => $cantidad)
                        <tr>
                            <td>{{ $estado }}</td>
                            <td class="text-right font-weight-bold">{{ $cantidad }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center text-muted">
                                No hay estados registrados en este período.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
