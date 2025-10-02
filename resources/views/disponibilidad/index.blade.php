@extends('adminlte::page')
@section('title','Disponibilidad')

@section('content_header')
  <div class="d-flex align-items-center justify-content-between">
    <h1 class="m-0">Disponibilidad de doctores</h1>
    <button class="btn btn-primary" data-toggle="modal" data-target="#crearDisp"><i class="fas fa-plus"></i> Nueva disponibilidad</button>
  </div>
@endsection

@section('content')
@php
$rows = [
 ['fec'=>'2025-08-12','ini'=>'08:00','fin'=>'12:00','doc'=>'Dr. López','est'=>'Disponible'],
 ['fec'=>'2025-08-12','ini'=>'13:00','fin'=>'17:00','doc'=>'Dra. Molina','est'=>'Vacaciones'],
];
@endphp

<div class="card">
  <div class="card-body table-responsive p-0">
    <table class="table table-striped mb-0">
      <thead><tr><th>Fecha</th><th>Inicio</th><th>Fin</th><th>Doctor</th><th>Estado</th><th class="text-right">Acciones</th></tr></thead>
      <tbody>
        @foreach($rows as $r)
        <tr>
          <td>{{ $r['fec'] }}</td><td>{{ $r['ini'] }}</td><td>{{ $r['fin'] }}</td><td>{{ $r['doc'] }}</td>
          <td><span class="badge {{ $r['est']=='Disponible'?'badge-success':'badge-secondary' }}">{{ $r['est'] }}</span></td>
          <td class="text-right">
            <button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#crearDisp"><i class="fas fa-edit"></i></button>
            <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

{{-- Modal crear/editar --}}
<div class="modal fade" id="crearDisp" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Disponibilidad</h5><button class="close" data-dismiss="modal"><span>&times;</span></button></div>
    <div class="modal-body">
      <div class="form-group"><label>Doctor</label><select class="form-control"><option>Dr. López</option><option>Dra. Molina</option></select></div>
      <div class="form-row">
        <div class="form-group col-md-6"><label>Fecha</label><input type="date" class="form-control"></div>
        <div class="form-group col-md-3"><label>Inicio</label><input type="time" class="form-control"></div>
        <div class="form-group col-md-3"><label>Fin</label><input type="time" class="form-control"></div>
      </div>
      <div class="form-group"><label>Estado</label><select class="form-control"><option>Disponible</option><option>No Disponible</option><option>Vacaciones</option><option>Emergencia</option></select></div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" data-dismiss="modal">Cancelar</button><button class="btn btn-primary">Guardar</button></div>
  </div></div>
</div>
@endsection

