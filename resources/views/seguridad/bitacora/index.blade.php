@extends('adminlte::page')

@section('title', 'Bitácora')

@section('content_header')
    <h1>Bitácora</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Objeto</th>
                        <th>Acción</th>
                        <th>Descripción</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($bitacora as $b)
                    <tr>
                        <td>{{ $b->created_at }}</td>
                        <td>{{ $b->usuario }}</td>
                        <td>{{ $b->OBJETO }}</td>
                        <td><span class="badge bg-primary">{{ $b->ACCION }}</span></td>
                        <td>{{ $b->DESCRIPCION }}</td>
                        <td>{{ $b->IP }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @if($bitacora->hasPages())
        <div class="card-footer">{{ $bitacora->links() }}</div>
        @endif
    </div>
@stop
