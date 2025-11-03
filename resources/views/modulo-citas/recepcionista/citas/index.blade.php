@extends('adminlte::page')
@section('title', 'Citas Recepción')

@section('content_header')
  <h1>Citas Recepción</h1>
@endsection

@section('content')
  @include('modulo-citas.shared._filters')
  @include('modulo-citas.shared._table_citas')
  @include('modulo-citas.shared._modal_detalle')
@endsection
