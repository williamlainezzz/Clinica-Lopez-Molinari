@extends('adminlte::page')

@section('title', 'Usuarios')

@section('content_header')
    <h1>Usuarios</h1>
    <small class="text-muted">Administración de cuentas y roles</small>
@stop

@section('content')

    {{-- Mensajes (éxito/errores) --}}
    @if(session('success'))
        <x-adminlte-alert theme="success" title="OK" dismissable>
            {{ session('success') }}
        </x-adminlte-alert>
    @endif
    @if($errors->any())
        <x-adminlte-alert theme="danger" title="Error" dismissable>
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </x-adminlte-alert>
    @endif

    {{-- Filtros --}}
    <div class="card card-outline card-primary mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('seguridad.usuarios.index') }}">
                <div class="row align-items-end">
                    <div class="col-md-5 mb-2">
                        <label class="mb-1">Buscar</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                            <input type="text" name="q" class="form-control"
                                   placeholder="Nombre, usuario o correo"
                                   value="{{ $filtros['q'] ?? '' }}">
                        </div>
                    </div>

                    <div class="col-md-3 mb-2">
                        <label class="mb-1">Rol</label>
                        <select name="rol_id" class="form-control">
                            <option value="">— Todos —</option>
                            @foreach ($roles as $r)
                                <option value="{{ $r->COD_ROL }}"
                                    {{ (string)($filtros['rol_id'] ?? '') === (string)$r->COD_ROL ? 'selected' : '' }}>
                                    {{ $r->NOM_ROL }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 mb-2">
                        <label class="mb-1">Estado</label>
                        <select name="estado" class="form-control">
                            <option value="">— Todos —</option>
                            @foreach ($estados as $e)
                                <option value="{{ $e->COD_ESTADO_USUARIO }}"
                                    {{ (string)($filtros['estado'] ?? '') === (string)$e->COD_ESTADO_USUARIO ? 'selected' : '' }}>
                                    {{ $e->ESTADO_USUARIO }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 mb-2 text-right">
                        <button class="btn btn-primary btn-block">
                            <i class="fas fa-filter mr-1"></i> Aplicar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fas fa-users mr-1"></i> Listado
            </div>
            <div class="card-tools">
    <a href="{{ route('seguridad.usuarios.create') }}" class="btn btn-sm btn-primary">
        <i class="fas fa-user-plus mr-1"></i> Nuevo usuario
    </a>
</div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 32px;"></th>
                            <th>Nombre</th>
                            <th>Usuario</th>
                            <th>Correo</th>
                            <th>Teléfono</th>
                            <th>Rol</th>
                            <th class="text-center">Estado</th>
                            <th class="text-right" style="width: 120px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $u)
                            <tr>
                                <td class="text-muted"><i class="fas fa-user"></i></td>
                                <td>{{ $u->nombre }}</td>
                                <td>{{ $u->USR_USUARIO }}</td>
                                <td>
                                    @if($u->correo)
                                        <a href="mailto:{{ $u->correo }}">{{ $u->correo }}</a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $u->telefono ?: '—' }}</td>
                                <td>
                                    @if($u->rol)
                                        <span class="badge badge-info">{{ $u->rol }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if((int)$u->ESTADO_USUARIO === 1)
                                        <span class="badge badge-success">Activo</span>
                                    @else
                                        <span class="badge badge-secondary">Inactivo</span>
                                    @endif
                                </td>
                                <td class="text-right">
    <div class="btn-group btn-group-sm" role="group">
        {{-- Editar --}}
        <a href="{{ route('seguridad.usuarios.edit', $u->COD_USUARIO) }}"
           class="btn btn-outline-primary" title="Editar">
            <i class="fas fa-edit"></i>
        </a>

        {{-- Eliminar (desactivar) --}}
        <form action="{{ route('seguridad.usuarios.destroy', $u->COD_USUARIO) }}"
              method="POST" class="d-inline"
              onsubmit="return confirm('¿Desactivar este usuario?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger" title="Eliminar">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    </div>
</td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Sin resultados</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-3 py-2">
                {!! $usuarios->onEachSide(1)->links() !!}
            </div>
        </div>
    </div>

@stop
