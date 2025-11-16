@extends('adminlte::page')

@section('title', $pageTitle)

@section('content_header')
    <div class="d-flex flex-wrap justify-content-between align-items-start">
        <div class="mb-2">
            <h1 class="h3 font-weight-bold text-primary mb-1">{{ ucfirst($heading) }}</h1>
            <p class="text-muted mb-0">{{ $intro }}</p>
        </div>
        <button class="btn btn-outline-primary mt-2 mt-md-0">
            <i class="fas fa-download"></i> Exportar bitácora
        </button>
    </div>
@endsection

@section('content')
    @php
        $eventCollection = collect($eventList ?? []);
        $porEstado       = $eventCollection->groupBy('estado')->map->count();
        $timelineSafe    = collect($timeline ?? []);
    @endphp

    {{-- Tarjetas de resumen --}}
    <div class="row mb-4">
        @foreach($stats as $stat)
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <span class="badge badge-{{ $stat['color'] }} mr-3 p-3 rounded-circle text-white">
                            <i class="{{ $stat['icon'] }}"></i>
                        </span>
                        <div>
                            <p class="text-muted text-uppercase small mb-1">{{ $stat['label'] }}</p>
                            <h3 class="mb-0 font-weight-bold">{{ $stat['value'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

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

    {{-- Timeline de últimas acciones --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h3 class="h6 mb-0">Últimas acciones</h3>
        </div>
        <div class="card-body">
            @if($timelineSafe->isEmpty())
                <p class="text-muted mb-0">
                    Aún no se han registrado acciones en la bitácora de citas.
                </p>
            @else
                <ul class="list-unstyled mb-0">
                    @foreach($timelineSafe as $item)
                        <li class="mb-3">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $item['descripcion'] ?? 'Acción' }}</strong>
                                <span class="badge badge-light">{{ $item['estado'] ?? '' }}</span>
                            </div>
                            <small class="text-muted">{{ $item['fecha'] ?? '' }}</small>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection
