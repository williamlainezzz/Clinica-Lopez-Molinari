@extends('adminlte::page')

@section('title', 'Mis preguntas de seguridad')

@section('content_header')
    <h1>Mis preguntas de seguridad</h1>
@stop

@section('content')
    <div class="card card-outline card-info">
        <div class="card-body">
            @if($preguntas->isEmpty())
                <p class="text-muted mb-0">No tienes preguntas de seguridad configuradas.</p>
            @else
                <ol class="mb-0 pl-3">
                    @foreach($preguntas as $p)
                        <li class="mb-2">{{ $p->pregunta->TEXTO_PREGUNTA ?? 'Pregunta no disponible' }}</li>
                    @endforeach
                </ol>
                <small class="text-muted">Por seguridad, las respuestas no se muestran.</small>
            @endif
        </div>
    </div>
@stop
