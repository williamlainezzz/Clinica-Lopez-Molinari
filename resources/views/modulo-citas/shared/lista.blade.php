@extends('adminlte::page')

@section('title', $titulo ?? 'Agenda')

@section('content_header')
    <h1>{{ $titulo ?? 'Agenda' }}</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <form method="GET" action="{{ route($routeName ?? 'agenda.citas') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label>Desde</label>
                        <input type="date" name="desde" class="form-control"
                               value="{{ $filters['desde'] ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <label>Hasta</label>
                        <input type="date" name="hasta" class="form-control"
                               value="{{ $filters['hasta'] ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <label>Estado</label>
                        <select name="estado" class="form-control">
                            @php $est = $filters['estado'] ?? ''; @endphp
                            <option value="" {{ $est==='' ? 'selected' : '' }}>Todos</option>
                            <option value="Confirmada" {{ $est==='Confirmada' ? 'selected' : '' }}>Confirmada</option>
                            <option value="Pendiente"  {{ $est==='Pendiente'  ? 'selected' : '' }}>Pendiente</option>
                            <option value="Cancelada"  {{ $est==='Cancelada'  ? 'selected' : '' }}>Cancelada</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Doctor</label>
                        <select name="doctor" class="form-control">
                            @php $doc = $filters['doctor'] ?? ''; @endphp
                            <option value="" {{ $doc==='' ? 'selected' : '' }}>Todos</option>
                            <option value="Dr. López"   {{ $doc==='Dr. López'   ? 'selected' : '' }}>Dr. López</option>
                            <option value="Dra. Molina" {{ $doc==='Dra. Molina' ? 'selected' : '' }}>Dra. Molina</option>
                        </select>
                    </div>
                </div>
                <div class="mt-3">
                    <button class="btn btn-primary"><i class="fas fa-search"></i> Filtrar</button>
                    <a href="{{ route($routeName ?? 'agenda.citas') }}" class="btn btn-secondary">Limpiar</a>
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
                            <th>Doctor</th>
                            <th>Estado</th>
                            <th>Motivo</th>
                            <th style="width: 120px;">Acciones</th>
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
                                <td class="text-nowrap">
                                    <a class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a>
                                    <a class="btn btn-xs btn-primary"><i class="fas fa-edit"></i></a>
                                    <a class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted">Sin resultados</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
