@extends('adminlte::page')

@section('title', $pageTitle)

@section('content_header')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
    <div>
        <h1 class="m-0">{{ $pageTitle }}</h1>
        @if(!empty($pageDescription))
            <p class="text-muted mb-0">{{ $pageDescription }}</p>
        @endif
    </div>
    @can('seguridad.usuarios.ver')
        <a href="{{ route('seguridad.usuarios.index') }}" class="btn btn-primary shadow-sm mt-3 mt-md-0">
            <i class="fas fa-user-shield mr-2"></i>
            Gestionar usuarios
        </a>
    @endcan
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12 col-sm-6 col-lg-3 mb-3">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $resumen['total'] }}</h3>
                <p>Total de personas</p>
            </div>
            <div class="icon"><i class="fas fa-users"></i></div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3 mb-3">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $resumen['activos'] }}</h3>
                <p>Con cuenta activa</p>
            </div>
            <div class="icon"><i class="fas fa-user-check"></i></div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3 mb-3">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $resumen['inactivos'] }}</h3>
                <p>Cuentas inactivas</p>
            </div>
            <div class="icon"><i class="fas fa-user-times"></i></div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3 mb-3">
        <div class="small-box bg-secondary">
            <div class="inner">
                <h3>{{ $resumen['sinCuenta'] }}</h3>
                <p>Sin usuario asignado</p>
            </div>
            <div class="icon"><i class="fas fa-user-tag"></i></div>
        </div>
    </div>
</div>

<div class="card card-outline card-primary">
    <div class="card-header">
        <form class="w-100" method="GET">
            <div class="row">
                <div class="col-md-6 mb-2 mb-md-0">
                    <label for="q" class="sr-only">Buscar</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <input type="search" name="q" id="q" class="form-control" value="{{ $filtros['q'] }}" placeholder="Buscar por nombre, usuario, correo o teléfono">
                    </div>
                </div>
                <div class="col-md-4 mb-2 mb-md-0">
                    <label for="estado" class="sr-only">Estado</label>
                    <select name="estado" id="estado" class="form-control">
                        <option value="todos" @selected($filtros['estado'] === 'todos')>Todos los estados</option>
                        <option value="activos" @selected($filtros['estado'] === 'activos')>Solo activos</option>
                        <option value="inactivos" @selected($filtros['estado'] === 'inactivos')>Solo inactivos</option>
                        <option value="sin_usuario" @selected($filtros['estado'] === 'sin_usuario')>Sin usuario</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-stretch">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-filter mr-1"></i> Aplicar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="thead-light">
                    <tr>
                        <th>Nombre</th>
                        <th>Contacto</th>
                        <th>Dirección</th>
                        <th>Usuario / Rol</th>
                        @if($citasLabel)
                            <th>{{ $citasLabel }}</th>
                        @endif
                        <th class="text-right">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($personas as $persona)
                        <tr>
                            <td>
                                <div class="font-weight-bold">{{ $persona->nombre }}</div>
                                <div class="text-muted small">{{ $persona->genero }}</div>
                            </td>
                            <td>
                                @php($correos = $persona->contactos['correos'])
                                @php($telefonos = $persona->contactos['telefonos'])
                                <div class="d-flex flex-column">
                                    <div>
                                        <i class="fas fa-envelope text-muted mr-1"></i>
                                        @if(count($correos))
                                            @foreach($correos as $correo)
                                                <span class="badge badge-light text-wrap mb-1">{{ $correo }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted small">Sin correo</span>
                                        @endif
                                    </div>
                                    <div>
                                        <i class="fas fa-phone text-muted mr-1"></i>
                                        @if(count($telefonos))
                                            @foreach($telefonos as $telefono)
                                                <span class="badge badge-light text-wrap mb-1">{{ $telefono }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted small">Sin teléfono</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php($direcciones = $persona->contactos['direcciones'])
                                @if(count($direcciones))
                                    @foreach($direcciones as $direccion)
                                        <div class="text-wrap">{{ $direccion }}</div>
                                    @endforeach
                                @else
                                    <span class="text-muted small">Sin dirección registrada</span>
                                @endif
                            </td>
                            <td>
                                @if($persona->usuario)
                                    <div class="font-weight-bold">{{ $persona->usuario }}</div>
                                    <div class="text-muted small">{{ $persona->rol ?? 'Sin rol asignado' }}</div>
                                @else
                                    <span class="badge badge-secondary">Sin usuario</span>
                                @endif
                            </td>
                            @if($citasLabel)
                                <td>
                                    @if($persona->citas)
                                        <span class="badge badge-info">{{ $persona->citas['proximas'] }} próximas</span>
                                        <div class="text-muted small">{{ $persona->citas['total'] }} totales</div>
                                    @else
                                        <span class="text-muted small">Sin citas registradas</span>
                                    @endif
                                </td>
                            @endif
                            <td class="text-right">
                                <span class="badge badge-{{ $persona->estado['class'] }}">{{ $persona->estado['label'] }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $citasLabel ? 6 : 5 }}" class="text-center py-5 text-muted">
                                <i class="far fa-smile-beam fa-2x mb-2"></i>
                                <p class="mb-0">No se encontraron registros con los filtros seleccionados.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center flex-column flex-md-row">
        <div class="text-muted small mb-2 mb-md-0">
            Mostrando {{ $personas->firstItem() ?? 0 }} - {{ $personas->lastItem() ?? 0 }} de {{ $personas->total() }} registros
        </div>
        {{ $personas->links() }}
    </div>
</div>
@endsection
