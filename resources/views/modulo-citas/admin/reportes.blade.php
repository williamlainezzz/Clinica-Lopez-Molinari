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
                <i class="fas fa-download"></i>
                Exportar reporte
            </button>
            <button class="btn btn-outline-primary shadow-sm">
                <i class="fas fa-envelope"></i>
                Enviar resumen
            </button>
        </div>
    </div>
@endsection

@section('content')
    @include('modulo-citas.shared.summary-cards', ['metrics' => $metrics])

    <div class="row g-4">
        <div class="col-12 col-xl-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title fw-semibold mb-0">Productividad por doctor</h5>
                    <small class="text-muted">Comparativo semanal de citas gestionadas.</small>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach($topDoctors as $doctor)
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="fw-semibold mb-0">{{ $doctor['doctor'] }}</h6>
                                    <small class="text-muted">{{ $doctor['confirmadas'] }} confirmadas · {{ $doctor['pendientes'] }} pendientes</small>
                                </div>
                                <span class="badge bg-primary rounded-pill">{{ $doctor['total'] }} citas</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title fw-semibold mb-0">Bitácora de cambios</h5>
                    <small class="text-muted">Actualizaciones recientes realizadas por el equipo.</small>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        @foreach($lastUpdates as $evento)
                            <li class="mb-3 d-flex gap-3">
                                <span class="badge bg-{{ $evento['tipo'] }} rounded-pill align-self-start">
                                    <i class="fas fa-circle"></i>
                                </span>
                                <div>
                                    <h6 class="fw-semibold mb-1">{{ $evento['titulo'] }}</h6>
                                    <p class="mb-1 text-muted">{{ $evento['detalle'] }}</p>
                                    <small class="text-muted">{{ \Illuminate\Support\Carbon::parse($evento['fecha'])->format('d/m/Y H:i') }}</small>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
