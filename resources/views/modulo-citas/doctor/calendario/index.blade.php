@extends('adminlte::page')

@section('title', 'Calendario  Doctor')

@section('content_header')
    <h1>Calendario  Doctor</h1>
@stop

@section('content')
    {{-- Parciales vacíos por ahora (no rompen) --}}
    @include('modulo-citas.shared._filters')
    @include('modulo-citas.shared._table_citas')
@stop
