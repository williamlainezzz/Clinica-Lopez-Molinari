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
                <i class="fas fa-plus-circle"></i> Nueva cita
            </button>
            <button class="btn btn-outline-secondary">
                <i class="fas fa-file-export"></i> Exportar agenda
            </button>
        </div>
    </div>
@endsection

@section('content')
    {{-- Tarjetas de resumen --}}
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
                            <h3 class="mb-0 font-weight-bold">
                                {{ $stat['value'] }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row">
        {{-- Panel principal por doctor --}}
        <div class="col-xl-8">
            @forelse($doctorPanels as $doctor)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h3 class="h5 mb-1">{{ $doctor['nombre'] }}</h3>
                            <span class="text-muted">
                                {{ $doctor['especialidad'] }}
                                @if(!empty($doctor['contacto']))
                                    · {{ $doctor['contacto'] }}
                                @endif
                            </span>
                        </div>

                        {{-- Botones generales del doctor (luego los podemos usar para modales) --}}
                        <div class="btn-group btn-group-sm mt-3 mt-md-0">
                            <button class="btn btn-outline-primary">
                                <i class="fas fa-eye"></i> Ver
                            </button>
                            <button class="btn btn-outline-success">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button class="btn btn-outline-warning">
                                <i class="fas fa-sync"></i> Reprogramar
                            </button>
                            <button class="btn btn-outline-danger">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
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
                                        <th style="width: 240px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($doctor['pacientes'] as $paciente)
                                        @php
                                            $estado = strtoupper($paciente['estado'] ?? '');
                                            $estadoLabel = ucfirst(strtolower($estado));

                                            $badge = match($estado) {
                                                'CONFIRMADA' => 'success',
                                                'PENDIENTE'  => 'warning',
                                                'CANCELADA'  => 'danger',
                                                default      => 'secondary',
                                            };
                                        @endphp
                                        <tr>
                                            <td class="font-weight-bold">
                                                {{ $paciente['nombre'] }}
                                            </td>
                                            <td>{{ $paciente['motivo'] }}</td>
                                            <td>
                                                {{ $paciente['fecha'] }} · {{ $paciente['hora'] }}
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $badge }}">
                                                    {{ $estadoLabel }}
                                                </span>
                                            </td>
                                            <td class="text-muted">
                                                {{ $paciente['nota'] ?? '—' }}
                                            </td>
                                            <td>
                                                {{-- Confirmar: solo si está PENDIENTE --}}
                                                @if($estado === 'PENDIENTE')
                                                    <form method="POST"
                                                          action="{{ route('agenda.citas.confirmar', $paciente['cita_id']) }}"
                                                          class="d-inline">
                                                        @csrf
                                                        <button type="submit"
                                                                class="btn btn-xs btn-success"
                                                                onclick="return confirm('¿Confirmar esta cita?');">
                                                            <i class="fas fa-check"></i> Confirmar
                                                        </button>
                                                    </form>
                                                @endif

                                                {{-- Cancelar: mientras no esté ya CANCELADA --}}
                                                @if($estado !== 'CANCELADA')
                                                    <form method="POST"
                                                          action="{{ route('agenda.citas.cancelar', $paciente['cita_id']) }}"
                                                          class="d-inline ml-1">
                                                        @csrf
                                                        <button type="submit"
                                                                class="btn btn-xs btn-danger"
                                                                onclick="return confirm('¿Cancelar esta cita?');">
                                                            <i class="fas fa-times"></i> Cancelar
                                                        </button>
                                                    </form>
                                                @endif

                                                {{-- Reprogramar: más adelante podemos hacerlo con modal; por ahora dejamos el botón "dummy" --}}
                                                <button type="button"
                                                        class="btn btn-xs btn-warning ml-1"
                                                        disabled
                                                        title="Pronto podrás reprogramar desde aquí">
                                                    <i class="fas fa-sync"></i> Reprogramar
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">
                                                No hay citas registradas para este doctor.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-info">
                    Aún no hay citas registradas en el sistema.
                </div>
            @endforelse
        </div>

        {{-- Sidebar derecha --}}
        <div class="col-xl-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h3 class="h6 mb-0">Pacientes sin doctor asignado</h3>
                </div>
                <div class="card-body">
                    @forelse($availablePatients as $patient)
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="mb-1">{{ $patient['nombre'] }}</h5>
                                <p class="text-muted mb-0 small">
                                    {{ $patient['motivo'] }} · Preferencia: {{ $patient['preferencia'] }}
                                </p>
                                <small class="text-muted">
                                    Actualizado {{ $patient['ultima'] }}
                                </small>
                            </div>
                            <button class="btn btn-sm btn-outline-primary">
                                Asignar
                            </button>
                        </div>
                        @if(!$loop->last)
                            <hr>
                        @endif
                    @empty
                        <p class="text-muted mb-0">
                            No hay pacientes en espera sin doctor asignado.
                        </p>
                    @endforelse
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted">Workflow rápido</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <i class="fas fa-check-circle text-success mr-2"></i>
                            Confirmar citas de mañana
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-envelope text-primary mr-2"></i>
                            Enviar recordatorios SMS
                        </li>
                        <li>
                            <i class="fas fa-file-medical text-info mr-2"></i>
                            Revisar solicitudes de nuevos pacientes
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de "Nueva cita" (de momento solo maqueta) --}}
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
                            <label class="form-label">Paciente</label>
                            <input type="text" class="form-control" placeholder="Nombre completo" disabled>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">Doctor</label>
                            <select class="form-control" disabled>
                                @foreach($doctorPanels as $doctor)
                                    <option>{{ $doctor['nombre'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">Fecha</label>
                            <input type="date" class="form-control" value="{{ now()->toDateString() }}" disabled>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">Hora</label>
                            <input type="time" class="form-control" value="09:00" disabled>
                        </div>
                        <div class="form-group col-12 mb-0">
                            <label class="form-label">Motivo</label>
                            <textarea class="form-control" rows="2" placeholder="Detalle del procedimiento" disabled></textarea>
                        </div>
                    </div>
                    <small class="text-muted d-block mt-3">
                        * Más adelante conectamos este formulario para crear citas reales.
                    </small>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button class="btn btn-primary" disabled>Guardar</button>
                </div>
            </div>
        </div>
    </div>
@endsection
