@extends('adminlte::page')
@section('title','Usuarios')

@section('content_header')
  <div class="d-flex align-items-center justify-content-between">
    <h1 class="m-0">Usuarios</h1>
    <button class="btn btn-primary" data-toggle="modal" data-target="#modalUsuario">
      <i class="fas fa-user-plus"></i> Nuevo usuario
    </button>
  </div>
@endsection

@section('content')
@php
$rows = [
  ['nom'=>'Guillermo Solís','usr'=>'gsolis','rol'=>'Administrador',  'tel'=>'9500-1122','estado'=>'Activo'],
  ['nom'=>'Alberto López',  'usr'=>'alopez','rol'=>'Doctor',         'tel'=>'9900-1122','estado'=>'Activo'],
  ['nom'=>'Ana Rivera',     'usr'=>'arivera','rol'=>'Paciente',      'tel'=>'9990-1234','estado'=>'Activo'],
  ['nom'=>'Laura Gómez',    'usr'=>'lgomez','rol'=>'Recepcionista',  'tel'=>'9901-7788','estado'=>'Inactivo'],
];
@endphp

<div class="card">
  <div class="card-body table-responsive p-0">
    <table class="table table-hover mb-0">
      <thead>
        <tr>
          <th>Nombre</th><th>Usuario</th><th>Rol</th><th>Teléfono</th><th>Estado</th>
          <th class="text-right">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @foreach($rows as $r)
        <tr>
          <td>{{ $r['nom'] }}</td>
          <td>{{ $r['usr'] }}</td>
          <td><span class="badge badge-info">{{ $r['rol'] }}</span></td>
          <td>{{ $r['tel'] }}</td>
          <td>
            <span class="badge {{ $r['estado']=='Activo' ? 'badge-success' : 'badge-secondary' }}">
              {{ $r['estado'] }}
            </span>
          </td>
          <td class="text-right">
            <button class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#modalVer">
              <i class="fas fa-eye"></i>
            </button>
            <button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#modalUsuario">
              <i class="fas fa-edit"></i>
            </button>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<!-- Modal crear/editar -->
<div class="modal fade" id="modalUsuario" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title">Usuario</h5>
      <button class="close" data-dismiss="modal"><span>&times;</span></button>
    </div>
    <div class="modal-body">
      <div class="form-group"><label>Nombre (persona)</label><input class="form-control" placeholder="Ej. Ana Rivera"></div>
      <div class="form-row">
        <div class="form-group col-md-6"><label>Usuario</label><input class="form-control" placeholder="usuario"></div>
        <div class="form-group col-md-6">
          <label>Rol</label>
          <select class="form-control">
            <option>Administrador</option>
            <option>Doctor</option>
            <option>Paciente</option>
            <option>Recepcionista</option>
          </select>
        </div>
      </div>
      <div class="form-group"><label>Teléfono</label><input class="form-control" placeholder="9999-9999"></div>
      <div class="form-group">
        <label>Estado</label>
        <select class="form-control"><option>Activo</option><option>Inactivo</option></select>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
      <button class="btn btn-primary">Guardar</button>
    </div>
  </div></div>
</div>

<!-- Modal ver (maqueta) -->
<div class="modal fade" id="modalVer" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Detalle de usuario</h5>
      <button class="close" data-dismiss="modal"><span>&times;</span></button>
    </div>
    <div class="modal-body">
      <dl class="row mb-0">
        <dt class="col-sm-4">Nombre</dt><dd class="col-sm-8">Guillermo Solís</dd>
        <dt class="col-sm-4">Usuario</dt><dd class="col-sm-8">gsolis</dd>
        <dt class="col-sm-4">Rol</dt><dd class="col-sm-8"><span class="badge badge-info">Administrador</span></dd>
        <dt class="col-sm-4">Teléfono</dt><dd class="col-sm-8">9500-1122</dd>
        <dt class="col-sm-4">Estado</dt><dd class="col-sm-8"><span class="badge badge-success">Activo</span></dd>
      </dl>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" data-dismiss="modal">Cerrar</button></div>
  </div></div>
</div>
@endsection
