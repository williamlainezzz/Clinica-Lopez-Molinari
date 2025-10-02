@extends('adminlte::page')
@section('title','Permisos')

@section('content_header')
  <div class="d-flex align-items-center justify-content-between">
    <h1 class="m-0">Permisos por rol / objeto</h1>
    <button class="btn btn-primary" data-toggle="modal" data-target="#crearPermiso"><i class="fas fa-plus"></i> Nuevo</button>
  </div>
@endsection

@section('content')
@php
$rows = [
 ['rol'=>'Administrador','obj'=>'GESTION_CITAS','sel'=>true,'ins'=>true,'upd'=>true,'del'=>true,'estado'=>'ACTIVO'],
 ['rol'=>'Doctor','obj'=>'GESTION_CITAS','sel'=>true,'ins'=>true,'upd'=>false,'del'=>false,'estado'=>'ACTIVO'],
 ['rol'=>'Recepcionista','obj'=>'GESTION_USUARIOS','sel'=>true,'ins'=>false,'upd'=>false,'del'=>false,'estado'=>'INACTIVO'],
];
@endphp

<div class="card">
  <div class="card-body table-responsive p-0">
    <table class="table table-striped mb-0">
      <thead>
        <tr>
          <th>Rol</th><th>Objeto</th>
          <th>Ver</th><th>Crear</th><th>Editar</th><th>Eliminar</th>
          <th>Estado</th><th class="text-right">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @foreach($rows as $r)
        <tr>
          <td>{{ $r['rol'] }}</td><td>{{ $r['obj'] }}</td>
          <td><input type="checkbox" {{ $r['sel']?'checked':'' }}></td>
          <td><input type="checkbox" {{ $r['ins']?'checked':'' }}></td>
          <td><input type="checkbox" {{ $r['upd']?'checked':'' }}></td>
          <td><input type="checkbox" {{ $r['del']?'checked':'' }}></td>
          <td><span class="badge {{ $r['estado']=='ACTIVO'?'badge-success':'badge-secondary' }}">{{ $r['estado'] }}</span></td>
          <td class="text-right">
            <button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#crearPermiso"><i class="fas fa-edit"></i></button>
            <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<div class="modal fade" id="crearPermiso" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Permiso</h5><button class="close" data-dismiss="modal"><span>&times;</span></button></div>
    <div class="modal-body">
      <div class="form-group"><label>Rol</label><select class="form-control"><option>Administrador</option><option>Doctor</option><option>Paciente</option><option>Recepcionista</option></select></div>
      <div class="form-group"><label>Objeto</label><select class="form-control"><option>GESTION_CITAS</option><option>GESTION_USUARIOS</option></select></div>
      <div class="form-row">
        <div class="col"><div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id="sel"><label class="custom-control-label" for="sel">Ver</label></div></div>
        <div class="col"><div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id="ins"><label class="custom-control-label" for="ins">Crear</label></div></div>
        <div class="col"><div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id="upd"><label class="custom-control-label" for="upd">Editar</label></div></div>
        <div class="col"><div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id="del"><label class="custom-control-label" for="del">Eliminar</label></div></div>
      </div>
      <div class="form-group mt-3"><label>Estado</label><select class="form-control"><option>ACTIVO</option><option>INACTIVO</option></select></div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" data-dismiss="modal">Cancelar</button><button class="btn btn-primary">Guardar</button></div>
  </div></div>
</div>
@endsection
