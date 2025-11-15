@extends('adminlte::page')

@section('title', 'Backups')

@section('content_header')
    <h1>Backups</h1>
@stop

@section('content')
    @if(session('success'))
        <x-adminlte-alert theme="success" title="OK" dismissable>
            {{ session('success') }}
        </x-adminlte-alert>
    @endif

    @if(session('error'))
        <x-adminlte-alert theme="danger" title="Error" dismissable>
            {{ session('error') }}
        </x-adminlte-alert>
    @endif

    {{-- Botón para generar backup: solo si tiene permiso CREAR en SEGURIDAD_BACKUPS --}}
    @if (function_exists('puede') && puede('SEGURIDAD_BACKUPS', 'CREAR'))
        <form action="{{ route('seguridad.backups.store') }}" method="POST" class="mb-3">
            @csrf
            <button class="btn btn-primary">
                <i class="fa fa-database"></i> Generar backup
            </button>
            {{-- Implementaremos en Paso 3 --}}
        </form>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Archivo</th>
                        <th>Tamaño</th>
                        <th>Estado</th>
                        <th>Usuario</th>
                        <th style="width:120px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($backups as $b)
                    <tr>
                        <td>{{ $b->created_at }}</td>
                        <td>{{ $b->NOMBRE_ARCHIVO }}</td>
                        <td>{{ number_format($b->TAMANIO_BYTES) }} bytes</td>
                        <td>
                            @if($b->ESTADO === 'OK')
                                <span class="badge bg-success">OK</span>
                            @else
                                <span class="badge bg-danger">{{ $b->ESTADO }}</span>
                            @endif
                        </td>
                        <td>{{ $b->usuario }}</td>
                        <td>
                            <a class="btn btn-sm btn-outline-primary"
                               href="{{ route('seguridad.backups.download', $b->COD_BACKUP) }}">
                               <i class="fa fa-download"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @if($backups->hasPages())
            <div class="card-footer">{{ $backups->links() }}</div>
        @endif
    </div>
@stop
