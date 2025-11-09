@extends('adminlte::page')

@section('title','Recepción · Gestión de personas')

@php
    $recepcionistas = [
        ['nombre' => 'Laura Gómez', 'turno' => 'Matutino',  'correo' => 'laura.gomez@clinica.test', 'telefono' => '9901-7788', 'estado' => 'Activo'],
        ['nombre' => 'Pedro Díaz',  'turno' => 'Vespertino','correo' => 'pedro.diaz@clinica.test',  'telefono' => '9702-5566', 'estado' => 'Activo'],
    ];

    $agendaHoy = [
        ['hora' => '08:30', 'paciente' => 'Ana Rivera',   'doctor' => 'Dr. Luis López',   'estado' => 'Confirmada'],
        ['hora' => '09:45', 'paciente' => 'Carlos Pérez', 'doctor' => 'Dra. Sofía Molina', 'estado' => 'Pendiente'],
        ['hora' => '11:15', 'paciente' => 'María Gómez',  'doctor' => 'Dr. Luis López',   'estado' => 'Confirmada'],
    ];

    $doctoresActivos = [
        ['nombre' => 'Dr. Luis López',   'especialidad' => 'Odontología General', 'ext' => '221'],
        ['nombre' => 'Dra. Sofía Molina', 'especialidad' => 'Ortodoncia',          'ext' => '224'],
        ['nombre' => 'Dr. Daniel Ortega','especialidad' => 'Endodoncia',           'ext' => '219'],
    ];
@endphp

@section('content_header')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
        <div class="mb-3 mb-lg-0">
            <h1 class="mb-1">Recepción y coordinación</h1>
            <p class="text-muted mb-0">Organiza doctores y pacientes desde un panel único.</p>
        </div>
        <div>
            <a class="btn btn-primary mr-2" href="{{ route('agenda.citas') }}"><i class="fas fa-plus"></i> Agendar cita</a>
            <a class="btn btn-outline-primary" href="{{ route('agenda.calendario') }}"><i class="fas fa-calendar-alt"></i> Ver calendario</a>
        </div>
    </div>
@endsection

@section('content')
    <div class="row g-3">
        <div class="col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-header border-0">
                    <h5 class="mb-0">Equipo de recepción</h5>
                    <small class="text-muted">Usuarios habilitados para gestionar citas y comunicación.</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="text-muted">
                                <tr>
                                    <th>Nombre</th>
                                    <th>Turno</th>
                                    <th>Correo</th>
                                    <th>Teléfono</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recepcionistas as $recepcionista)
                                    <tr>
                                        <td>{{ $recepcionista['nombre'] }}</td>
                                        <td>{{ $recepcionista['turno'] }}</td>
                                        <td>{{ $recepcionista['correo'] }}</td>
                                        <td>{{ $recepcionista['telefono'] }}</td>
                                        <td><span class="badge badge-success">{{ $recepcionista['estado'] }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card shadow-sm mb-3">
                <div class="card-header border-0">
                    <h5 class="mb-0">Agenda del día</h5>
                    <small class="text-muted">Resumen rápido de los turnos confirmados y pendientes.</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="text-muted">
                                <tr>
                                    <th>Hora</th>
                                    <th>Paciente</th>
                                    <th>Doctor</th>
                                    <th>Estado</th>
                                    <th class="text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($agendaHoy as $cita)
                                    @php
                                        $badge = $cita['estado'] === 'Confirmada' ? 'success' : ($cita['estado'] === 'Pendiente' ? 'warning' : 'secondary');
                                    @endphp
                                    <tr>
                                        <td>{{ $cita['hora'] }}</td>
                                        <td>{{ $cita['paciente'] }}</td>
                                        <td>{{ $cita['doctor'] }}</td>
                                        <td><span class="badge badge-{{ $badge }}">{{ $cita['estado'] }}</span></td>
                                        <td class="text-right">
                                            <button class="btn btn-xs btn-outline-info" title="Confirmar"><i class="fas fa-check"></i></button>
                                            <button class="btn btn-xs btn-outline-warning" title="Reprogramar"><i class="fas fa-sync"></i></button>
                                            <button class="btn btn-xs btn-outline-danger" title="Cancelar"><i class="fas fa-times"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header border-0">
                    <h5 class="mb-0">Doctores en turno</h5>
                    <small class="text-muted">Extensiones directas para confirmar citas o emergencias.</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="text-muted">
                                <tr>
                                    <th>Doctor</th>
                                    <th>Especialidad</th>
                                    <th>Extensión</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($doctoresActivos as $doctor)
                                    <tr>
                                        <td>{{ $doctor['nombre'] }}</td>
                                        <td>{{ $doctor['especialidad'] }}</td>
                                        <td>{{ $doctor['ext'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <small class="text-muted">Integra esta tabla con la disponibilidad de <code>tbl_persona</code> y las extensiones del consultorio.</small>
                </div>
            </div>
        </div>
    </div>

    <div class="callout callout-info mt-4">
        <h5 class="mb-1"><i class="fas fa-tasks"></i> Recomendaciones</h5>
        <ul class="mb-0 pl-3">
            <li>Sincroniza los cambios realizados aquí con el módulo de agenda para notificar a pacientes y doctores.</li>
            <li>Agrega filtros por doctor o rango de horas para jornadas con alta demanda.</li>
            <li>Integra recordatorios automáticos vía correo o SMS en futuras iteraciones.</li>
        </ul>
    </div>
@endsection
