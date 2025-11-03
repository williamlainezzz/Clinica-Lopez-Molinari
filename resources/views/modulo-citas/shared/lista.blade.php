@extends('adminlte::page')

@section('title', $titulo ?? 'Agenda')

@section('content_header')
    <h1>{{ $titulo ?? 'Agenda' }}</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-3">
                    <label>Desde</label>
                    <input type="date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label>Hasta</label>
                    <input type="date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label>Estado</label>
                    <select class="form-control">
                        <option>Todos</option>
                        <option>Confirmada</option>
                        <option>Pendiente</option>
                        <option>Cancelada</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Doctor</label>
                    <select class="form-control">
                        <option>Todos</option>
                        <option>Dr. López</option>
                        <option>Dra. Molina</option>
                    </select>
                </div>
            </div>
            <div class="mt-3">
                <button class="btn btn-primary"><i class="fas fa-search"></i> Filtrar</button>
                <button class="btn btn-secondary">Limpiar</button>
            </div>
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
                        <tr>
                            <td>2025-11-12</td>
                            <td>08:30</td>
                            <td>Ana Rivera</td>
                            <td>Dr. López</td>
                            <td><span class="badge badge-success">Confirmada</span></td>
                            <td>Limpieza</td>
                            <td class="text-nowrap">
                                <a class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a>
                                <a class="btn btn-xs btn-primary"><i class="fas fa-edit"></i></a>
                                <a class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <td>2025-11-12</td>
                            <td>09:00</td>
                            <td>Carlos Pérez</td>
                            <td>Dra. Molina</td>
                            <td><span class="badge badge-warning">Pendiente</span></td>
                            <td>Dolor de muela</td>
                            <td class="text-nowrap">
                                <a class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a>
                                <a class="btn btn-xs btn-primary"><i class="fas fa-edit"></i></a>
                                <a class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <td>2025-11-12</td>
                            <td>10:15</td>
                            <td>María Gómez</td>
                            <td>Dr. López</td>
                            <td><span class="badge badge-danger">Cancelada</span></td>
                            <td>Control</td>
                            <td class="text-nowrap">
                                <a class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a>
                                <a class="btn btn-xs btn-primary"><i class="fas fa-edit"></i></a>
                                <a class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
