@extends('adminlte::page')

@section('title', $pageTitle)

@section('content_header')
    <div class="d-flex flex-wrap justify-content-between align-items-start">
        <div class="mb-2">
            <h1 class="h3 font-weight-bold text-primary mb-1">{{ $heading }}</h1>
            <p class="text-muted mb-0">{{ $intro }}</p>
        </div>
        <button class="btn btn-outline-primary mt-2 mt-md-0"><i class="fas fa-file-medical"></i> Descargar resumen clínico</button>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-7 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h3 class="h6 mb-0">Historial del paciente destacado</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Motivo</th>
                                <th>Estado</th>
                                <th>Notas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($patientRecord['historial'] as $item)
                                <tr>
                                    <td>{{ $item['fecha'] }}</td>
                                    <td>{{ $item['motivo'] }}</td>
                                    <td><span class="badge badge-secondary">{{ $item['estado'] }}</span></td>
                                    <td>{{ $item['detalle'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xl-5 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h3 class="h6 mb-0">Paciente</h3>
                </div>
                <div class="card-body">
                    <p class="font-weight-bold mb-1">{{ $patientRecord['profile']['nombre'] }}</p>
                    <p class="text-muted mb-2">Código {{ $patientRecord['profile']['codigo'] }}</p>
                    <p class="text-muted mb-2">Doctor asignado: {{ $patientRecord['profile']['doctor'] }}</p>
                    <p class="text-muted mb-2">Correo: {{ $patientRecord['profile']['correo'] }}</p>
                    <p class="text-muted mb-2">Teléfono: {{ $patientRecord['profile']['telefono'] }}</p>
                    <div class="bg-light p-3 rounded">
                        <small class="text-uppercase text-muted">Próxima cita</small>
                        <p class="mb-1 font-weight-bold">{{ $patientRecord['profile']['proxima']['fecha'] }} · {{ $patientRecord['profile']['proxima']['hora'] }}</p>
                        <p class="mb-0 text-muted">{{ $patientRecord['profile']['proxima']['motivo'] }} · {{ $patientRecord['profile']['proxima']['estado'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h3 class="h6 mb-0">Línea de seguimiento</h3>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($timeline as $item)
                    <div class="col-md-4 mb-3">
                        <div class="p-3 border rounded h-100">
                            <small class="text-muted">{{ $item['fecha'] }}</small>
                            <p class="font-weight-bold mb-1">{{ $item['descripcion'] }}</p>
                            <span class="badge badge-light">{{ $item['estado'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
