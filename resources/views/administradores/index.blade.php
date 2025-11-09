@extends('adminlte::page')

@section('title','Personas · Administrador')

@php
    $doctores = [
        ['nombre' => 'Dr. Luis López',   'especialidad' => 'Odontología General', 'correo' => 'dr.lopez@clinica.test',   'telefono' => '9900-1122', 'estado' => 'Activo'],
        ['nombre' => 'Dra. Sofía Molina', 'especialidad' => 'Ortodoncia',          'correo' => 'dra.molina@clinica.test', 'telefono' => '9800-3344', 'estado' => 'Activo'],
    ];

    $pacientes = [
        ['nombre' => 'Ana Rivera',   'doctor' => 'Dr. Luis López',   'correo' => 'ana.rivera@mail.com',   'telefono' => '9990-1234', 'estado' => 'Activo'],
        ['nombre' => 'Carlos Pérez', 'doctor' => 'Dra. Sofía Molina','correo' => 'carlos.perez@mail.com', 'telefono' => '8888-5678', 'estado' => 'En seguimiento'],
        ['nombre' => 'María Gómez',  'doctor' => 'Dr. Luis López',   'correo' => 'maria.gomez@mail.com',  'telefono' => '9855-8822', 'estado' => 'Inactivo'],
    ];

    $recepcionistas = [
        ['nombre' => 'Laura Gómez', 'correo' => 'laura.gomez@clinica.test', 'telefono' => '9901-7788', 'turno' => 'Matutino', 'estado' => 'Activo'],
        ['nombre' => 'Pedro Díaz',  'correo' => 'pedro.diaz@clinica.test',  'telefono' => '9702-5566', 'turno' => 'Vespertino', 'estado' => 'Activo'],
    ];

    $administradores = [
        ['nombre' => 'Guillermo Solís', 'usuario' => 'gsolis', 'correo' => 'gsolis@clinica.test', 'telefono' => '9500-1122', 'estado' => 'Activo'],
        ['nombre' => 'Andrea Torres',   'usuario' => 'atorres', 'correo' => 'atorres@clinica.test', 'telefono' => '9400-7788', 'estado' => 'Activo'],
    ];

    $estadisticas = [
        'doctores'       => count($doctores),
        'pacientes'      => count($pacientes),
        'recepcionistas' => count($recepcionistas),
        'administradores'=> count($administradores),
    ];
@endphp

@section('content_header')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
        <div class="mb-3 mb-lg-0">
            <h1 class="mb-1">Panel maestro de personas</h1>
            <p class="text-muted mb-0">Visión 360° de todos los actores del sistema para el rol administrador.</p>
        </div>
        <div class="text-lg-right">
            <span class="badge badge-light text-primary border border-primary px-3 py-2">Modo supervisor</span>
        </div>
    </div>
@endsection

@section('content')
    <div class="row g-3">
        <div class="col-sm-6 col-xl-3">
            <div class="info-box bg-gradient-primary">
                <span class="info-box-icon"><i class="fas fa-user-md"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-uppercase">Doctores</span>
                    <span class="info-box-number">{{ $estadisticas['doctores'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="info-box bg-gradient-success">
                <span class="info-box-icon"><i class="fas fa-user-injured"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-uppercase">Pacientes</span>
                    <span class="info-box-number">{{ $estadisticas['pacientes'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="info-box bg-gradient-info">
                <span class="info-box-icon"><i class="fas fa-concierge-bell"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-uppercase">Recepción</span>
                    <span class="info-box-number">{{ $estadisticas['recepcionistas'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="info-box bg-gradient-secondary">
                <span class="info-box-icon"><i class="fas fa-user-shield"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-uppercase">Administradores</span>
                    <span class="info-box-number">{{ $estadisticas['administradores'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header border-0 pb-0">
            <ul class="nav nav-pills" id="personas-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="tab-doctores-tab" data-toggle="pill" href="#tab-doctores" role="tab" aria-controls="tab-doctores" aria-selected="true">
                        <i class="fas fa-user-md"></i> Doctores
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="tab-pacientes-tab" data-toggle="pill" href="#tab-pacientes" role="tab" aria-controls="tab-pacientes" aria-selected="false">
                        <i class="fas fa-user-injured"></i> Pacientes
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="tab-recepcion-tab" data-toggle="pill" href="#tab-recepcion" role="tab" aria-controls="tab-recepcion" aria-selected="false">
                        <i class="fas fa-concierge-bell"></i> Recepcionistas
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="tab-admins-tab" data-toggle="pill" href="#tab-admins" role="tab" aria-controls="tab-admins" aria-selected="false">
                        <i class="fas fa-user-shield"></i> Administradores
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="personas-tabsContent">
                <div class="tab-pane fade show active" id="tab-doctores" role="tabpanel" aria-labelledby="tab-doctores-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="text-muted">
                                <tr>
                                    <th>Nombre</th>
                                    <th>Especialidad</th>
                                    <th>Correo</th>
                                    <th>Teléfono</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($doctores as $doctor)
                                    <tr>
                                        <td>{{ $doctor['nombre'] }}</td>
                                        <td>{{ $doctor['especialidad'] }}</td>
                                        <td>{{ $doctor['correo'] }}</td>
                                        <td>{{ $doctor['telefono'] }}</td>
                                        <td><span class="badge badge-success">{{ $doctor['estado'] }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-pacientes" role="tabpanel" aria-labelledby="tab-pacientes-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="text-muted">
                                <tr>
                                    <th>Nombre</th>
                                    <th>Doctor asignado</th>
                                    <th>Correo</th>
                                    <th>Teléfono</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pacientes as $paciente)
                                    <tr>
                                        <td>{{ $paciente['nombre'] }}</td>
                                        <td>{{ $paciente['doctor'] }}</td>
                                        <td>{{ $paciente['correo'] }}</td>
                                        <td>{{ $paciente['telefono'] }}</td>
                                        <td>
                                            @php
                                                $badge = 'secondary';
                                                if ($paciente['estado'] === 'Activo') $badge = 'success';
                                                elseif ($paciente['estado'] === 'En seguimiento') $badge = 'warning';
                                            @endphp
                                            <span class="badge badge-{{ $badge }}">{{ $paciente['estado'] }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-recepcion" role="tabpanel" aria-labelledby="tab-recepcion-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="text-muted">
                                <tr>
                                    <th>Nombre</th>
                                    <th>Correo</th>
                                    <th>Teléfono</th>
                                    <th>Turno</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recepcionistas as $recepcionista)
                                    <tr>
                                        <td>{{ $recepcionista['nombre'] }}</td>
                                        <td>{{ $recepcionista['correo'] }}</td>
                                        <td>{{ $recepcionista['telefono'] }}</td>
                                        <td>{{ $recepcionista['turno'] }}</td>
                                        <td><span class="badge badge-success">{{ $recepcionista['estado'] }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-admins" role="tabpanel" aria-labelledby="tab-admins-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="text-muted">
                                <tr>
                                    <th>Nombre</th>
                                    <th>Usuario</th>
                                    <th>Correo</th>
                                    <th>Teléfono</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($administradores as $admin)
                                    <tr>
                                        <td>{{ $admin['nombre'] }}</td>
                                        <td>{{ $admin['usuario'] }}</td>
                                        <td>{{ $admin['correo'] }}</td>
                                        <td>{{ $admin['telefono'] }}</td>
                                        <td><span class="badge badge-success">{{ $admin['estado'] }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer bg-light">
            <small class="text-muted">Información de demostración pensada para conectar con las tablas <code>tbl_persona</code>, <code>tbl_usuario</code> y catálogos relacionados.</small>
        </div>
    </div>

    <div class="callout callout-primary mt-4">
        <h5 class="mb-1"><i class="fas fa-stream"></i> Siguientes pasos</h5>
        <ul class="mb-0 pl-3">
            <li>Vincular estas vistas con filtros reales (por rol, estado, especialidad).</li>
            <li>Agregar exportaciones rápidas (PDF/Excel) por tipo de persona.</li>
            <li>Incorporar accesos directos a la bitácora y a seguridad para revisar acciones recientes.</li>
        </ul>
    </div>
@endsection
