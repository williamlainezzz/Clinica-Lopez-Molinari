@extends('adminlte::page')

@section('title', $pageTitle)

@section('content_header')
    <div class="d-flex flex-wrap justify-content-between align-items-start">
        <div class="mb-2">
            <h1 class="h3 font-weight-bold text-primary mb-1">{{ ucfirst($heading) }}</h1>
            <p class="text-muted mb-0">{{ $intro }}</p>
        </div>
        <div class="btn-group mt-2 mt-md-0">
            <button class="btn btn-primary" data-toggle="modal" data-target="#modalNuevaCita">
                <i class="fas fa-plus-circle"></i> Agendar
            </button>
            <button class="btn btn-outline-secondary">
                <i class="fas fa-print"></i> Imprimir listado
            </button>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        @foreach($stats as $stat)
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
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

    <div class="row">
        <div class="col-xl-8">
            @foreach($doctorPanels as $doctor)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h3 class="h5 mb-1">{{ $doctor['nombre'] }}</h3>
                            <span class="text-muted">{{ $doctor['especialidad'] }} · {{ $doctor['contacto'] }}</span>
                        </div>
                        <div class="btn-group btn-group-sm mt-3 mt-md-0">
                            <button class="btn btn-outline-primary">Ver</button>
                            <button class="btn btn-outline-success">Confirmar</button>
                            <button class="btn btn-outline-danger">Cancelar</button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Paciente</th>
                                        <th>Motivo</th>
                                        <th>Fecha</th>
                                        <th>Estado</th>
                                        <th>Notas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($doctor['pacientes'] as $paciente)
                                        @php
                                            $badge = match($paciente['estado']) {
                                                'Confirmada' => 'success',
                                                'Pendiente'  => 'warning',
                                                'Cancelada'  => 'danger',
                                                default      => 'secondary',
                                            };
                                        @endphp
                                        <tr>
                                            <td class="font-weight-bold">{{ $paciente['nombre'] }}</td>
                                            <td>{{ $paciente['motivo'] }}</td>
                                            <td>{{ $paciente['fecha'] }} · {{ $paciente['hora'] }}</td>
                                            <td><span class="badge badge-{{ $badge }}">{{ $paciente['estado'] }}</span></td>
                                            <td class="text-muted">{{ $paciente['nota'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="col-xl-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h3 class="h6 mb-0">Solicitudes en espera</h3>
                </div>
                <div class="card-body">
                    @foreach($availablePatients as $patient)
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="mb-1">{{ $patient['nombre'] }}</h5>
                                <p class="text-muted mb-0 small">{{ $patient['motivo'] }}</p>
                                <small class="text-muted">Preferencia: {{ $patient['preferencia'] }}</small>
                            </div>
                            <button class="btn btn-sm btn-outline-primary">Asignar</button>
                        </div>
                        @if(!$loop->last)
                            <hr>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted">Recordatorios</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3"><i class="fas fa-phone text-success mr-2"></i> Llamar a pacientes pendientes</li>
                        <li class="mb-3"><i class="fas fa-envelope text-primary mr-2"></i> Enviar correos de confirmación</li>
                        <li><i class="fas fa-file-export text-info mr-2"></i> Compartir agenda con administración</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalNuevaCita" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crear cita</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Paciente</label>
                            <input type="text" class="form-control" placeholder="Nombre completo">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Doctor</label>
                            <select class="form-control">
                                @foreach($doctorPanels as $doctor)
                                    <option>{{ $doctor['nombre'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Fecha</label>
                            <input type="date" class="form-control" value="2025-11-12">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Hora</label>
                            <input type="time" class="form-control" value="09:00">
                        </div>
                        <div class="form-group col-12 mb-0">
                            <label>Motivo</label>
                            <textarea class="form-control" rows="2" placeholder="Motivo de la cita"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary">Guardar</button>
                </div>
            </div>
        </div>
    </div>
@endsection
