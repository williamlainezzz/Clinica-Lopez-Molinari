@extends('adminlte::page')
@section('title', 'Editar rol')

@section('content_header')
    <h1>Editar rol</h1>
@stop

@section('content')

    @if(session('success'))
        <x-adminlte-alert theme="success" title="OK" dismissable>{{ session('success') }}</x-adminlte-alert>
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

    <div class="card">
        <form action="{{ route('seguridad.roles.update', $rol->COD_ROL) }}" method="POST">
            @csrf @method('PUT')
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Nombre del rol</label>
                    <input name="NOM_ROL" class="form-control" value="{{ old('NOM_ROL', $rol->NOM_ROL) }}" required>
                    @error('NOM_ROL') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                {{-- Si manejas descripción, descomenta:
                <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <input name="DESCRIPCION" class="form-control" value="{{ old('DESCRIPCION', $rol->DESCRIPCION ?? '') }}">
                    @error('DESCRIPCION') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                --}}
            </div>
            <div class="card-footer">
                <a href="{{ route('seguridad.roles.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Actualizar</button>
            </div>
        </form>
    </div>
@stop
