@extends('adminlte::page')

@section('title', $pageTitle)

@section('content_header')
    <div class="d-flex flex-wrap justify-content-between align-items-start">
        <div class="mb-2">
            <h1 class="h3 font-weight-bold text-primary mb-1">{{ $heading }}</h1>
            <p class="text-muted mb-0">{{ $intro }}</p>
        </div>
        <button class="btn btn-outline-primary mt-2 mt-md-0"><i class="fas fa-file-download"></i> Descargar historial</button>
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
                    @foreach($patientRecord['historial'] as $item)
                        <tr>
                            <td>{{ $item['fecha'] }}</td>
                            <td>{{ $item['doctor'] }}</td>
                            <td>{{ $item['motivo'] }}</td>
                            <td><span class="badge badge-secondary">{{ $item['estado'] }}</span></td>
                            <td>{{ $item['detalle'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
