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
                <i class="fas fa-user-plus"></i>
                Registrar paciente
            </button>
            <button class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-file-alt"></i>
                Subir historial clínico
            </button>
        </div>
    </div>
@endsection

@section('content')
    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title fw-semibold mb-0">Pacientes asignados</h5>
                    <small class="text-muted">Da seguimiento a tus planes de tratamiento activos.</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Paciente</th>
                                    <th>Plan</th>
                                    <th>Última cita</th>
                                    <th>Próxima cita</th>
                                    <th>Estado</th>
                                    <th>Contacto</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assignedPatients as $paciente)
                                    <tr>
                                        <td>
                                            <strong>{{ $paciente['nombre'] }}</strong>
                                            <div class="text-muted small">{{ $paciente['edad'] }} años</div>
                                        </td>
                                        <td>{{ $paciente['plan'] }}</td>
                                        <td>{{ $paciente['ultima_cita'] }}</td>
                                        <td>{{ $paciente['proxima_cita'] }}</td>
                                        <td>
                                            @php
                                                $badge = match ($paciente['estado']) {
                                                    'Activa' => 'bg-success',
                                                    'En tratamiento' => 'bg-info',
                                                    default => 'bg-secondary',
                                                };
                                            @endphp
                                            <span class="badge {{ $badge }}">{{ $paciente['estado'] }}</span>
                                        </td>
                                        <td class="text-nowrap">
                                            <div class="small text-muted">{{ $paciente['telefono'] }}</div>
                                            <div class="small text-muted">{{ $paciente['correo'] }}</div>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-xs btn-outline-primary">
                                                    <i class="fas fa-calendar-plus"></i>
                                                </button>
                                                <button class="btn btn-xs btn-outline-secondary">
                                                    <i class="fas fa-notes-medical"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="fw-semibold mb-3">Invita a tus pacientes</h5>
                    <p class="text-muted">{{ $invitations['descripcion'] }}</p>
                    <div class="mb-3">
                        <label class="form-label text-muted text-uppercase small fw-semibold">Enlace directo</label>
                        <div class="input-group">
                            <input type="text" class="form-control" value="{{ $invitations['link'] }}" readonly>
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted text-uppercase small fw-semibold">Código de invitación</label>
                        <div class="input-group">
                            <input type="text" class="form-control" value="{{ $invitations['codigo'] }}" readonly>
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="fas fa-qrcode"></i>
                            </button>
                        </div>
                    </div>
                    <div class="border rounded-3 p-3 bg-light text-center">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white shadow-sm mb-2" style="width: 96px; height: 96px;">
                            <i class="fas fa-qrcode fa-2x text-primary"></i>
                        </span>
                        <p class="mb-0 text-muted small">Comparte el código QR en tu consultorio para registros rápidos.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-xl-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title fw-semibold mb-0">Pacientes disponibles</h5>
                    <small class="text-muted">Pacientes registrados sin doctor asignado.</small>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach($availablePatients as $paciente)
                            <div class="list-group-item px-0 d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="fw-semibold mb-1">{{ $paciente['nombre'] }}</h6>
                                    <p class="mb-1 text-muted">{{ $paciente['motivo'] }}</p>
                                    <small class="text-muted">En espera desde {{ $paciente['desde'] }}</small>
                                </div>
                                <button class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-user-check"></i>
                                    Asignar
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="fw-semibold mb-3">Recomendaciones</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <i class="fas fa-check text-success me-2"></i>
                            Actualiza el plan de tratamiento después de cada cita.
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-check text-success me-2"></i>
                            Utiliza el enlace de invitación para asignar pacientes automáticamente.
                        </li>
                        <li>
                            <i class="fas fa-check text-success me-2"></i>
                            Programa recordatorios personalizados desde la agenda.
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
