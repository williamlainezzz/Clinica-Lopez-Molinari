@extends('adminlte::page')

@section('title', 'Mi perfil')

@section('content_header')
    <h1>Mi perfil</h1>
@stop

@section('content')
    <div class="card card-primary card-outline">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Usuario</dt>
                <dd class="col-sm-9">{{ $user->USR_USUARIO }}</dd>

                <dt class="col-sm-3">Nombre</dt>
                <dd class="col-sm-9">{{ trim(($user->persona->PRIMER_NOMBRE ?? '') . ' ' . ($user->persona->SEGUNDO_NOMBRE ?? '') . ' ' . ($user->persona->PRIMER_APELLIDO ?? '') . ' ' . ($user->persona->SEGUNDO_APELLIDO ?? '')) }}</dd>

                <dt class="col-sm-3">Correo</dt>
                <dd class="col-sm-9">{{ $correo ?: 'No registrado' }}</dd>
            </dl>
        </div>
    </div>
@stop
