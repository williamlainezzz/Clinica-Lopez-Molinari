@extends('adminlte::page')

@section('title', $pageTitle)

@section('content_header')
    <div class="d-flex flex-wrap justify-content-between align-items-start">
        <div class="mb-2">
            <h1 class="h3 font-weight-bold text-primary mb-1">{{ $heading }}</h1>
            <p class="text-muted mb-0">{{ $intro }}</p>
        </div>
        <div class="btn-group mt-2 mt-md-0">
            <button class="btn btn-primary"><i class="fas fa-user-plus"></i> Registrar paciente</button>
            <button class="btn btn-outline-secondary"><i class="fas fa-qrcode"></i> Generar QR</button>
        </div>
    </div>
@endsection

@section('content')
    <div class="row mb-4">
        @foreach($stats as $stat)
            <div class="col-md-4 mb-3">
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

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="h5 mb-0">Pacientes asignados</h3>
                        <small class="text-muted">Gestiona citas y notas clínicas</small>
                    </div>
                    <button class="btn btn-sm btn-outline-primary">Agregar cita rápida</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Paciente</th>
                                <th>Motivo</th>
                                <th>Próxima cita</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activeDoctor['pacientes'] as $paciente)
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
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-info">Ver ficha</button>
                                            <button class="btn btn-outline-success">Confirmar</button>
                                            <button class="btn btn-outline-danger">Cancelar</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h3 class="h6 mb-0">Notas rápidas</h3>
                </div>
                <div class="card-body">
                    <form>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Paciente</label>
                                <select class="form-control">
                                    @foreach($activeDoctor['pacientes'] as $paciente)
                                        <option>{{ $paciente['nombre'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Tipo</label>
                                <select class="form-control">
                                    <option>Seguimiento</option>
                                    <option>Indicaciones</option>
                                    <option>Laboratorio</option>
                                </select>
                            </div>
                            <div class="form-group col-12">
                                <label>Nota</label>
                                <textarea class="form-control" rows="2" placeholder="Observaciones clínicas"></textarea>
                            </div>
                        </div>
                        <button class="btn btn-primary">Guardar</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h3 class="h6 text-uppercase text-muted">Mi módulo</h3>
                    <p class="mb-2"><strong>{{ $activeDoctor['nombre'] }}</strong></p>
                    <p class="text-muted mb-2">{{ $activeDoctor['especialidad'] }}</p>
                    <p class="text-muted mb-0">{{ $activeDoctor['contacto'] }}</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h3 class="h6 mb-0">Compartir formulario</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">Comparte el enlace para que un paciente se registre y quede asignado automáticamente.</p>
                    <div class="bg-light p-3 rounded mb-2">
                        <small class="text-uppercase text-muted">Link</small>
                        <p class="mb-0">{{ $shareLink }}</p>
                    </div>
                    <div class="bg-light p-3 rounded mb-3">
                        <small class="text-uppercase text-muted">Código QR</small>
                        <p class="mb-0">{{ $shareCode }}</p>
                    </div>
                    <button class="btn btn-outline-primary btn-block">Copiar enlace</button>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h3 class="h6 mb-0">Pacientes disponibles</h3>
                </div>
                <div class="card-body">
                    @foreach($availablePatients as $patient)
                        <div class="mb-3">
                            <strong>{{ $patient['nombre'] }}</strong>
                            <p class="mb-1 text-muted">{{ $patient['motivo'] }}</p>
                            <small class="text-muted">Preferencia: {{ $patient['preferencia'] }}</small>
                            <div class="mt-2">
                                <button class="btn btn-sm btn-outline-success">Asignar</button>
                            </div>
                        </div>
                        @if(!$loop->last)
                            <hr>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
