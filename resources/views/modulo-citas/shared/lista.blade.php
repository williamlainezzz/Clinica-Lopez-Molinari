@extends('adminlte::page')

@section('title', $pageTitle ?? 'Agenda')

@section('content_header')
    <h1>{{ $heading ?? 'Agenda' }}</h1>
@endsection

@section('content')

    {{-- Banner por rol/section --}}
    @includeIf($bannerPartial)

    <div class="card">
        <div class="card-header">
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
                                        {{-- Acciones por rol (aún sin conectar) --}}
                                        @switch($rol)
                                            @case('ADMIN')
                                            @case('RECEPCIONISTA')
                                                <a class="btn btn-xs btn-info"    title="Ver"><i class="fas fa-eye"></i></a>
                                                <a class="btn btn-xs btn-primary" title="Editar"><i class="fas fa-edit"></i></a>
                                                <a class="btn btn-xs btn-warning" title="Reprogramar"><i class="fas fa-sync"></i></a>
                                                <a class="btn btn-xs btn-danger"  title="Cancelar"><i class="fas fa-times"></i></a>
                                                @break

                                            @case('DOCTOR')
                                                <a class="btn btn-xs btn-info"    title="Ver"><i class="fas fa-eye"></i></a>
                                                <a class="btn btn-xs btn-warning" title="Reprogramar"><i class="fas fa-sync"></i></a>
                                                <a class="btn btn-xs btn-danger"  title="Cancelar"><i class="fas fa-times"></i></a>
                                                @break
                                        @endswitch
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
