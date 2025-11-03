@extends('adminlte::page')

@section('title', $titulo ?? 'Agenda')

@section('content_header')
    <h1>{{ $titulo ?? 'Agenda' }}</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <form method="GET" action="{{ route($routeName) }}" class="row g-3 align-items-end" id="filtersForm">
                <div class="col-md-3">
                    <label class="form-label">Desde</label>
                    <input type="date" name="desde" value="{{ $filters['desde'] }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Hasta</label>
                    <input type="date" name="hasta" value="{{ $filters['hasta'] }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-control">
                        <option value="">Todos</option>
                        @foreach (['Confirmada','Pendiente','Cancelada'] as $e)
                            <option value="{{ $e }}" @selected($filters['estado'] === $e)>{{ $e }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Doctor</label>
                    <select name="doctor" class="form-control">
                        <option value="">Todos</option>
                        @foreach (['Dr. López','Dra. Molina'] as $d)
                            <option value="{{ $d }}" @selected($filters['doctor'] === $d)>{{ $d }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 mt-2">
                    <button class="btn btn-primary">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="{{ route($routeName) }}" class="btn btn-secondary">Limpiar</a>
                </div>
            </form>
        </div>

        @php
            $showActions = ($perms['view'] ?? false) || ($perms['edit'] ?? false) || ($perms['delete'] ?? false) || ($perms['schedule'] ?? false);
        @endphp

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Paciente</th>
                            <th>Doctor</th>
                            <th>Estado</th>
                            <th>Motivo</th>
                            @if($showActions)
                                <th style="width: 160px;">Acciones</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $r)
                            @php
                                $badge = match ($r['estado']) {
                                    'Confirmada' => 'success',
                                    'Pendiente'  => 'warning',
                                    'Cancelada'  => 'danger',
                                    default      => 'secondary',
                                };
                            @endphp
                            <tr>
                                <td>{{ $r['fecha'] }}</td>
                                <td>{{ $r['hora'] }}</td>
                                <td>{{ $r['paciente'] }}</td>
                                <td>{{ $r['doctor'] }}</td>
                                <td><span class="badge badge-{{ $badge }}">{{ $r['estado'] }}</span></td>
                                <td>{{ $r['motivo'] }}</td>

                                @if($showActions)
                                    <td class="text-nowrap">
                                        @if($perms['view'] ?? false)
                                            <a class="btn btn-xs btn-info" title="Ver"><i class="fas fa-eye"></i></a>
                                        @endif
                                        @if($perms['edit'] ?? false)
                                            <a class="btn btn-xs btn-primary" title="Editar"><i class="fas fa-edit"></i></a>
                                        @endif
                                        @if($perms['schedule'] ?? false)
                                            <a class="btn btn-xs btn-warning" title="Reprogramar"><i class="fas fa-calendar-alt"></i></a>
                                        @endif
                                        @if($perms['delete'] ?? false)
                                            <a class="btn btn-xs btn-danger" title="Eliminar"><i class="fas fa-trash"></i></a>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $showActions ? 7 : 6 }}" class="text-center text-muted">
                                    Sin resultados
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
