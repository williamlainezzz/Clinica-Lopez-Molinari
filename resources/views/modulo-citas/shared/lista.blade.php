@extends('adminlte::page')

@section('title', $pageTitle ?? 'Agenda')

@section('content_header')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
        <div class="mb-3 mb-md-0">
            <h1 class="mb-1">{{ $heading ?? 'Agenda' }}</h1>
            <p class="text-muted mb-0">Panel de citas diseñado para el rol <strong>{{ strtolower($rol ?? 'admin') }}</strong>.</p>
        </div>

        @if(!empty($nextAppointment))
            <div class="card shadow-sm mb-0">
                <div class="card-body py-2 px-3 d-flex align-items-center">
                    <small class="text-uppercase text-muted">Próxima cita</small>
                    <div class="d-flex align-items-center ml-3">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-3" style="width: 42px; height: 42px;">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div>
                            <div class="fw-bold">{{ $nextAppointment['fecha'] }} · {{ $nextAppointment['hora'] }}</div>
                            <div class="small text-muted">{{ $nextAppointment['paciente'] }} con {{ $nextAppointment['doctor'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('content')

    {{-- Banner por rol/section --}}
    @includeIf($bannerPartial)

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="small-box bg-gradient-primary">
                <div class="inner">
                    <h3 class="mb-0">{{ $stats['total'] ?? 0 }}</h3>
                    <p class="mb-0">Citas registradas</p>
                </div>
                <div class="icon"><i class="fas fa-notes-medical"></i></div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="small-box bg-gradient-success">
                <div class="inner">
                    <h3 class="mb-0">{{ $stats['confirmadas'] ?? 0 }}</h3>
                    <p class="mb-0">Confirmadas</p>
                </div>
                <div class="icon"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="small-box bg-gradient-warning">
                <div class="inner">
                    <h3 class="mb-0">{{ $stats['pendientes'] ?? 0 }}</h3>
                    <p class="mb-0">Pendientes</p>
                </div>
                <div class="icon"><i class="fas fa-hourglass-half"></i></div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="small-box bg-gradient-danger">
                <div class="inner">
                    <h3 class="mb-0">{{ $stats['canceladas'] ?? 0 }}</h3>
                    <p class="mb-0">Canceladas</p>
                </div>
                <div class="icon"><i class="fas fa-times-circle"></i></div>
            </div>
        </div>
    </div>

    @if($readOnly)
        <div class="callout callout-info">
            <h5 class="mb-1"><i class="fas fa-eye"></i> Modo lectura</h5>
            <p class="mb-0">Este rol solo puede consultar el historial de citas. Las acciones de creación o edición están deshabilitadas.</p>
        </div>
    @endif

    <div class="card">
        <div class="card-header bg-white border-bottom-0">
            {{-- Toolbar por rol/section --}}
            <div class="mb-3">
                @includeIf($toolbarPartial)
            </div>

            {{-- Filtros (GET) --}}
            <form method="GET" action="{{ route($routeName) }}">
                <div class="row">
                    <div class="col-md-3">
                        <label class="mb-1">Desde</label>
                        <input type="date" class="form-control"
                               name="desde" value="{{ $filters['desde'] }}">
                    </div>
                    <div class="col-md-3">
                        <label class="mb-1">Hasta</label>
                        <input type="date" class="form-control"
                               name="hasta" value="{{ $filters['hasta'] }}">
                    </div>
                    <div class="col-md-3">
                        <label class="mb-1">Estado</label>
                        <select class="form-control" name="estado">
                            <option value="">Todos</option>
                            @foreach(($catalogoEstados ?? []) as $est)
                                <option value="{{ $est }}" {{ (isset($filters['estado']) && $filters['estado']===$est) ? 'selected' : '' }}>
                                    {{ $est }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="mb-1">Doctor</label>
                        <select class="form-control" name="doctor">
                            <option value="">Todos</option>
                            @foreach(($catalogoDoctores ?? []) as $doc)
                                <option value="{{ $doc }}" {{ (isset($filters['doctor']) && $filters['doctor']===$doc) ? 'selected' : '' }}>
                                    {{ $doc }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-3">
                    <button class="btn btn-primary">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="{{ route($routeName) }}" class="btn btn-secondary">
                        Limpiar
                    </a>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Paciente</th>
                            @if($showDoctorColumn)
                                <th>Doctor</th>
                            @endif
                            <th>Estado</th>
                            <th>Motivo</th>
                            @if($showActions)
                                <th style="width: 160px;">Acciones</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $row)
                            @php
                                $estado = $row['estado'];
                                $badge = match ($estado) {
                                    'Confirmada' => 'success',
                                    'Pendiente'  => 'warning',
                                    'Cancelada'  => 'danger',
                                    default      => 'secondary',
                                };
                            @endphp
                            <tr>
                                <td>{{ $row['fecha'] }}</td>
                                <td>{{ $row['hora'] }}</td>
                                <td>{{ $row['paciente'] }}</td>

                                @if($showDoctorColumn)
                                    <td>{{ $row['doctor'] }}</td>
                                @endif

                                <td><span class="badge badge-{{ $badge }}">{{ $estado }}</span></td>
                                <td>{{ $row['motivo'] }}</td>

                                @if($showActions)
                                    <td class="text-nowrap">
                                        @if($canViewDetail)
                                            <button class="btn btn-xs btn-outline-info" title="Ver detalle" data-toggle="modal" data-target="#mdlDetalle">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        @endif

                                        @if($canManage)
                                            <button class="btn btn-xs btn-outline-primary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <button class="btn btn-xs btn-outline-warning" title="Reprogramar">
                                                <i class="fas fa-sync"></i>
                                            </button>

                                            <button class="btn btn-xs btn-outline-danger" title="Cancelar">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 6 + ($showDoctorColumn?1:0) + ($showActions?1:0) }}" class="text-center text-muted">
                                    Sin resultados con los filtros actuales.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer">
            <small class="text-muted">
                (Demo) Estos datos son de prueba. Conectaremos a la BD en el siguiente bloque.
            </small>
        </div>
    </div>
@endsection
