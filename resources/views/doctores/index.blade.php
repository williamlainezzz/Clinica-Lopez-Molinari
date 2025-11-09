@extends('adminlte::page')

@section('title','Perfil del doctor')

@php
    $doctor = [
        'nombre'        => 'Dr. Luis López',
        'codigo'        => 'DOC-045',
        'especialidad'  => 'Odontología General',
        'correo'        => 'dr.lopez@clinica.test',
        'telefono'      => '9900-1122',
        'consultorio'   => 'Consultorio 03',
        'horario'       => 'Lunes a Viernes · 08:00 - 16:00',
        'estado'        => 'Activo',
    ];

    $indicadores = [
        ['titulo' => 'Citas de la semana', 'valor' => 18, 'color' => 'primary', 'icono' => 'calendar-week'],
        ['titulo' => 'Pacientes activos',  'valor' => 42, 'color' => 'success', 'icono' => 'heartbeat'],
        ['titulo' => 'Reprogramaciones',   'valor' => 3,  'color' => 'warning', 'icono' => 'sync'],
    ];

    $citas = [
        ['fecha' => '2025-11-12', 'hora' => '08:30', 'paciente' => 'Ana Rivera',  'estado' => 'Confirmada', 'motivo' => 'Limpieza dental'],
        ['fecha' => '2025-11-12', 'hora' => '09:15', 'paciente' => 'María Gómez', 'estado' => 'Pendiente',  'motivo' => 'Control anual'],
        ['fecha' => '2025-11-12', 'hora' => '11:00', 'paciente' => 'Pedro Díaz',  'estado' => 'Confirmada', 'motivo' => 'Ortodoncia'],
    ];

    $pacientes = [
        ['nombre' => 'Ana Rivera',  'ultima_cita' => '2025-11-05', 'proxima_cita' => '2025-11-12', 'estado' => 'Activo'],
        ['nombre' => 'Carlos Pérez','ultima_cita' => '2025-10-22', 'proxima_cita' => '2025-11-19', 'estado' => 'En seguimiento'],
        ['nombre' => 'María Gómez', 'ultima_cita' => '2025-08-10', 'proxima_cita' => '—',          'estado' => 'Inactivo'],
    ];
@endphp

@section('content_header')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
        <div class="mb-3 mb-lg-0">
            <h1 class="mb-1">Agenda del Dr. {{ $doctor['nombre'] }}</h1>
            <p class="text-muted mb-0">Resumen integral de tus pacientes, citas y desempeño semanal.</p>
        </div>
        <div>
            <a class="btn btn-success mr-2"><i class="fas fa-plus"></i> Registrar cita</a>
            <a class="btn btn-outline-primary" href="{{ route('agenda.calendario') }}"><i class="fas fa-calendar-alt"></i> Abrir calendario</a>
        </div>
    </div>
@endsection

@section('content')
    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="mb-3">Perfil profesional</h5>
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted">Código</dt>
                        <dd class="col-7">{{ $doctor['codigo'] }}</dd>

                        <dt class="col-5 text-muted">Especialidad</dt>
                        <dd class="col-7">{{ $doctor['especialidad'] }}</dd>

                        <dt class="col-5 text-muted">Correo</dt>
                        <dd class="col-7">{{ $doctor['correo'] }}</dd>

                        <dt class="col-5 text-muted">Teléfono</dt>
                        <dd class="col-7">{{ $doctor['telefono'] }}</dd>

                        <dt class="col-5 text-muted">Consultorio</dt>
                        <dd class="col-7">{{ $doctor['consultorio'] }}</dd>

                        <dt class="col-5 text-muted">Horario</dt>
                        <dd class="col-7">{{ $doctor['horario'] }}</dd>

                        <dt class="col-5 text-muted">Estado</dt>
                        <dd class="col-7"><span class="badge badge-success">{{ $doctor['estado'] }}</span></dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="row g-3">
                @foreach($indicadores as $item)
                    <div class="col-sm-4">
                        <div class="small-box bg-gradient-{{ $item['color'] }}">
                            <div class="inner">
                                <h3>{{ $item['valor'] }}</h3>
                                <p>{{ $item['titulo'] }}</p>
                            </div>
                            <div class="icon"><i class="fas fa-{{ $item['icono'] }}"></i></div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="card shadow-sm">
                <div class="card-header border-0">
                    <h5 class="mb-0">Citas del día</h5>
                    <small class="text-muted">Sincronizadas con la agenda central del módulo</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="text-muted">
                                <tr>
                                    <th>Hora</th>
                                    <th>Paciente</th>
                                    <th>Motivo</th>
                                    <th>Estado</th>
                                    <th class="text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($citas as $cita)
                                    @php
                                        $badge = match ($cita['estado']) {
                                            'Confirmada' => 'success',
                                            'Pendiente'  => 'warning',
                                            'Cancelada'  => 'danger',
                                            default      => 'secondary',
                                        };
                                    @endphp
                                    <tr>
                                        <td>{{ $cita['hora'] }}</td>
                                        <td>{{ $cita['paciente'] }}</td>
                                        <td>{{ $cita['motivo'] }}</td>
                                        <td><span class="badge badge-{{ $badge }}">{{ $cita['estado'] }}</span></td>
                                        <td class="text-right">
                                            <button class="btn btn-xs btn-outline-info" title="Ver"><i class="fas fa-eye"></i></button>
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
        </div>
    </div>

    <div class="card shadow-sm mt-4">
        <div class="card-header border-0">
            <h5 class="mb-0">Pacientes asignados</h5>
            <small class="text-muted">Listado de pacientes relacionados a tu usuario con estado clínico de referencia.</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="text-muted">
                        <tr>
                            <th>Paciente</th>
                            <th>Última cita</th>
                            <th>Próxima cita</th>
                            <th>Estado</th>
                            <th class="text-right">Seguimiento</th>
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
                                <td>{{ $paciente['ultima_cita'] }}</td>
                                <td>{{ $paciente['proxima_cita'] }}</td>
                                <td><span class="badge badge-{{ $badge }}">{{ $paciente['estado'] }}</span></td>
                                <td class="text-right">
                                    <button class="btn btn-xs btn-outline-primary"><i class="fas fa-notes-medical"></i> Plan</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-light">
            <small class="text-muted">Estos datos se alimentarán de las tablas <code>tbl_cita</code> y <code>tbl_persona</code> para mostrar pacientes reales.</small>
        </div>
    </div>
@endsection
