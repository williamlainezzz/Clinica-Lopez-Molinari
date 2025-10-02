@extends('adminlte::page')
@section('title','Notificaciones')

@section('content_header')
  <div class="d-flex align-items-center justify-content-between">
    <h1 class="m-0">Notificaciones</h1>
    <button class="btn btn-primary" data-toggle="modal" data-target="#crearNotif"><i class="fas fa-paper-plane"></i> Enviar</button>
  </div>
@endsection

@section('content')
@php
$rows = [
 ['fec'=>'2025-08-10 10:21','msg'=>'Recordatorio de cita','cita'=>'#1024 (Ana Rivera)','estado'=>'Enviada'],
 ['fec'=>'2025-08-09 16:05','msg'=>'Cita reprogramada','cita'=>'#1001 (Carlos Pérez)','estado'=>'Enviada'],
];
@endphp

<div class="card">
  <div class="card-body table-responsive p-0">
    <table class="table table-hover mb-0">
      <thead><tr><th>Fecha envío</th><th>Mensaje</th><th>Cita</th><th>Estado</th><th class="text-right">Acciones</th></tr></thead>
      <tbody>
        @foreach($rows as $r)
        <tr>
          <td>{{ $r['fec'] }}</td><td>{{ $r['msg'] }}</td><td>{{ $r['cita'] }}</td>
          <td><span class="badge badge-success">{{ $r['estado'] }}</span></td>
          <td class="text-right"><button class="btn btn-sm btn-outline-info"><i class="fas fa-sync-alt"></i></button></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<div class="modal fade" id="crearNotif" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Enviar notificación</h5><button class="close" data-dismiss="modal"><span>&times;</span></button></div>
    <div class="modal-body">
      <div class="form-group"><label>Cita</label><select class="form-control"><option>#1024 - Ana Rivera - 2025-08-12 08:30</option></select></div>
      <div class="form-group"><label>Mensaje</label><textarea class="form-control" rows="3" placeholder="Recordatorio de su cita..."></textarea></div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" data-dismiss="modal">Cancelar</button><button class="btn btn-primary">Enviar</button></div>
  </div></div>
</div>
@endsection
