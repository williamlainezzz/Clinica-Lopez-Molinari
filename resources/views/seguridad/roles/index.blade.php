@extends('adminlte::page')

@section('title', 'Roles')

@section('content_header')
    <h1>Roles</h1>
@stop

@section('content')
    @if(session('success')) <x-adminlte-alert theme="success" title="OK" dismissable>{{ session('success') }}</x-adminlte-alert> @endif
    @if(session('error'))   <x-adminlte-alert theme="danger"  title="Error" dismissable>{{ session('error') }}</x-adminlte-alert> @endif

    <a href="{{ route('seguridad.roles.create') }}" class="btn btn-primary mb-3">Nuevo rol</a>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Rol</th>
                        <th>Usuarios</th>
                        <th>Permisos</th>
                        <th style="width:150px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($roles as $r)
                    <tr>
                        <td>{{ $r->NOM_ROL }}</td>
                        <td><span class="badge bg-info">{{ $r->usuarios }}</span></td>
                        <td><span class="badge bg-secondary">{{ $r->permisos }}</span></td>
                        <td>
                            <a class="btn btn-sm btn-primary" href="{{ route('seguridad.roles.edit', $r->COD_ROL) }}"><i class="fa fa-edit"></i></a>
                            <form action="{{ route('seguridad.roles.destroy', $r->COD_ROL) }}" method="POST" style="display:inline-block" onsubmit="return confirm('¿Eliminar este rol?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @if($roles->hasPages())
        <div class="card-footer">{{ $roles->links() }}</div>
        @endif
    </div>
@stop
