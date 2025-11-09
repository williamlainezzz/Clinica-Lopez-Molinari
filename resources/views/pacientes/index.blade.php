@extends('adminlte::page')

@section('title','Pacientes')

@php
    $pacientes = [
        ['nombre' => 'Ana Rivera',   'genero' => 'Femenino', 'doctor' => 'Dr. Luis López',   'telefono' => '9990-1234', 'correo' => 'ana.rivera@mail.com',   'direccion' => 'Col. Centro',     'estado' => 'Activo',        'proxima_cita' => '2025-11-12 08:30'],
        ['nombre' => 'Carlos Pérez', 'genero' => 'Masculino','doctor' => 'Dra. Sofía Molina','telefono' => '8888-5678', 'correo' => 'carlos.perez@mail.com', 'direccion' => 'Col. La Paz',     'estado' => 'En seguimiento','proxima_cita' => '2025-11-19 09:15'],
        ['nombre' => 'María Gómez',  'genero' => 'Femenino', 'doctor' => 'Dr. Luis López',   'telefono' => '9855-8822', 'correo' => 'maria.gomez@mail.com',  'direccion' => 'Col. Kennedy',    'estado' => 'Inactivo',      'proxima_cita' => '—'],
    ];

    $resumen = [
        'total'          => count($pacientes),
        'activos'        => collect($pacientes)->where('estado', 'Activo')->count(),
        'seguimiento'    => collect($pacientes)->where('estado', 'En seguimiento')->count(),
        'inactivos'      => collect($pacientes)->where('estado', 'Inactivo')->count(),
    ];
@endphp

@section('content_header')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
        <div class="mb-3 mb-lg-0">
            <h1 class="mb-1">Pacientes del sistema</h1>
            <p class="text-muted mb-0">Control general de pacientes asignados a la clínica.</p>
        </div>
        <div>
            <a class="btn btn-primary" href="{{ route('agenda.citas') }}"><i class="fas fa-calendar-plus"></i> Agendar nueva cita</a>
        </div>
    </div>
@endsection

@section('content')
    <div class="row g-3 mb-3">
        <div class="col-sm-6 col-xl-3">
            <div class="small-box bg-gradient-primary">
                <div class="inner">
                    <h3>{{ $resumen['total'] }}</h3>
                    <p>Pacientes registrados</p>
                </div>
                <div class="icon"><i class="fas fa-users"></i></div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="small-box bg-gradient-success">
                <div class="inner">
                    <h3>{{ $resumen['activos'] }}</h3>
                    <p>Activos</p>
                </div>
                <div class="icon"><i class="fas fa-user-check"></i></div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="small-box bg-gradient-warning">
                <div class="inner">
                    <h3>{{ $resumen['seguimiento'] }}</h3>
                    <p>En seguimiento</p>
                </div>
                <div class="icon"><i class="fas fa-clipboard-list"></i></div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="small-box bg-gradient-secondary">
                <div class="inner">
                    <h3>{{ $resumen['inactivos'] }}</h3>
                    <p>Inactivos</p>
                </div>
                <div class="icon"><i class="fas fa-user-slash"></i></div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header border-0 d-flex flex-column flex-md-row align-items-md-center justify-content-between">
            <div>
                <h5 class="mb-0">Listado de pacientes</h5>
                <small class="text-muted">Datos preparados para enlazar con las tablas de personas y usuarios.</small>
            </div>
            <div class="btn-group mt-3 mt-md-0" role="group">
                <button class="btn btn-outline-secondary"><i class="fas fa-filter"></i> Filtros</button>
                <button class="btn btn-outline-secondary"><i class="fas fa-file-export"></i> Exportar</button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="text-muted">
                        <tr>
                            <th>Nombre</th>
                            <th>Género</th>
                            <th>Doctor asignado</th>
                            <th>Teléfono</th>
                            <th>Correo</th>
                            <th>Dirección</th>
                            <th>Próxima cita</th>
                            <th>Estado</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pacientes as $paciente)
                            @php
                                $badge = 'secondary';
                                if ($paciente['estado'] === 'Activo') $badge = 'success';
                                elseif ($paciente['estado'] === 'En seguimiento') $badge = 'warning';
                            @endphp
                            <tr>
                                <td>{{ $paciente['nombre'] }}</td>
                                <td>{{ $paciente['genero'] }}</td>
                                <td>{{ $paciente['doctor'] }}</td>
                                <td>{{ $paciente['telefono'] }}</td>
                                <td>{{ $paciente['correo'] }}</td>
                                <td>{{ $paciente['direccion'] }}</td>
                                <td>{{ $paciente['proxima_cita'] }}</td>
                                <td><span class="badge badge-{{ $badge }}">{{ $paciente['estado'] }}</span></td>
                                <td class="text-right">
                                    <button class="btn btn-xs btn-outline-info" title="Ver expediente"><i class="fas fa-folder-open"></i></button>
                                    <button class="btn btn-xs btn-outline-primary" title="Editar"><i class="fas fa-edit"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-light">
            <small class="text-muted">Integrar con <code>tbl_cita</code>, <code>tbl_persona</code> y <code>tbl_usuario</code> para mostrar datos reales.</small>
        </div>
    </div>

    <div class="callout callout-warning mt-4">
        <h5 class="mb-1"><i class="fas fa-lightbulb"></i> Tip funcional</h5>
        <p class="mb-0">Aprovecha este módulo para mantener actualizados los contactos y asignaciones. Las citas creadas desde recepción o el doctor aparecerán automáticamente con su respectivo paciente.</p>
    </div>
@endsection
