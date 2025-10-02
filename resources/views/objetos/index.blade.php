@extends('adminlte::page')
@section('title','Objetos')

@section('content_header')
  <div class="d-flex align-items-center justify-content-between">
    <h1 class="m-0">Objetos</h1>
    <button class="btn btn-primary" data-toggle="modal" data-target="#crearObjeto"><i class="fas fa-plus"></i> Nuevo objeto</button>
  </div>
@endsection

@section('content')
@php
$rows = [
 ['nom'=>'GESTION_CITAS','desc'=>'Acceso a citas','url'=>'/agenda/citas','estado'=>'ACTIVO'],
 ['nom'=>'GESTION_USUARIOS','desc'=>'ABM de usuarios','url'=>'/personas/usuarios','estado'=>'ACTIVO'],
];
@endphp

<div class="card">
 <div class="card-body table-responsive p-0">
  <table class="table table-hover mb-0">
    <thead><tr><th>Nombre</th><th>Descripción</th><th>URL</th><th>Estado</th><th class="text-right">Acciones</th></tr></thead>
    <tbody>
      @foreach($rows as $r)
      <tr>
        <td>{{ $r['nom'] }}</td><td>{{ $r['desc'] }}</td><td><code>{{ $r['url'] }}</code></td>
        <td><span class="badge {{ $r['estado']=='ACTIVO'?'badge-success':'badge-secondary' }}">{{ $r['estado'] }}</span></td>
        <td class="text-right">
          <button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#crearObjeto"><i class="fas fa-edit"></i></button>
          <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
 </div>
</div>

<div class="modal fade" id="crearObjeto" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Objeto</h5><button class="close" data-dismiss="modal"><span>&times;</span></button></div>
    <div class="modal-body">
      <div class="form-group"><label>Nombre (código)</label><input class="form-control" placeholder="GESTION_CITAS"></div>
      <div class="form-group"><label>Descripción</label><input class="form-control" placeholder="Acceso a citas"></div>
      <div class="form-group"><label>URL</label><input class="form-control" placeholder="/agenda/citas"></div>
      <div class="form-group"><label>Estado</label><select class="form-control"><option>ACTIVO</option><option>INACTIVO</option></select></div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" data-dismiss="modal">Cancelar</button><button class="btn btn-primary">Guardar</button></div>
  </div></div>
</div>
@endsection
