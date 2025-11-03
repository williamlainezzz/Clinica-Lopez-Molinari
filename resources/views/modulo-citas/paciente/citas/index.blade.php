@extends('adminlte::page')

@section('title', 'Citas  Paciente')

@section('content_header')
    <h1>Citas  Paciente</h1>
@stop

@section('content')
    {{-- Parciales vacíos por ahora (no rompen) --}}
    @include('modulo-citas.shared._filters')
    @include('modulo-citas.shared._table_citas')
@stop
