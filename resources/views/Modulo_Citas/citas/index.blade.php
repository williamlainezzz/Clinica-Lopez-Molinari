@extends('adminlte::page')
@section('title','Citas')

@section('content_header')
  <div class="d-flex align-items-center justify-content-between">
    <h1 class="m-0">Citas</h1>
    <div>
      <button class="btn btn-outline-secondary mr-2" data-toggle="modal" data-target="#filtroCitas"><i class="fas fa-filter"></i> Filtros</button>
      <button class="btn btn-primary" data-toggle="modal" data-target="#crearCita"><i class="fas fa-plus"></i> Nueva cita</button>
    </div>
  </div>
@endsection

@section('content')
@php
  $citas = [
    ['fec'=>'2025-08-12','hor'=>'08:30','pac'=>'Ana Rivera','doc'=>'Dr. López','est'=>'Confirmada','mot'=>'Limpieza dental'],
    ['fec'=>'2025-08-12','hor'=>'09:00','pac'=>'Carlos Pérez','doc'=>'Dra. Molina','est'=>'Pendiente','mot'=>'Dolor muela'],
    ['fec'=>'2025-08-12','hor'=>'10:15','pac'=>'María Díaz','doc'=>'Dr. López','est'=>'Cancelada','mot'=>'Control'],
  ];
@endphp

{{-- 🔎 Barra de búsqueda / ordenar columnas / imprimir --}}
@include('components.table-tools')

{{-- ✅ Botón de exportación CSV (lo pedido en la imagen) --}}
<a href="{{ route('export.citas.csv') }}" class="btn btn-success mb-3">Exportar CSV</a>

<div class="card">
  <div class="card-body table-responsive p-0">
    <table class="table table-hover mb-0">
      <thead>
        <tr>
          <th>Fecha</th><th>Hora</th><th>Paciente</th><th>Doctor</th><th>Estado</th><th>Motivo</th><th class="text-right">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @foreach($citas as $c)
        <tr>
          <td>{{ $c['fec'] }}</td>
          <td>{{ $c['hor'] }}</td>
          <td>{{ $c['pac'] }}</td>
          <td>{{ $c['doc'] }}</td>
          <td>
            <span class="badge
              @if($c['est']=='Confirmada') badge-info
              @elseif($c['est']=='Pendiente') badge-warning
              @elseif($c['est']=='Cancelada') badge-danger
              @else badge-secondary @endif">
              {{ $c['est'] }}
            </span>
          </td>
          <td>{{ $c['mot'] }}</td>
          <td class="text-right">
            <button class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#verCita"><i class="fas fa-eye"></i></button>
            <button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#editarCita"><i class="fas fa-edit"></i></button>
            <button class="btn btn-sm btn-outline-danger"><i class="fas fa-times"></i></button>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

{{-- MODAL: Filtros --}}
<div class="modal fade" id="filtroCitas" tabindex="-1">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Filtrar citas</h5>
      <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
    <div class="modal-body">
      <div class="form-row">
        <div class="form-group col-md-4"><label>Desde</label><input type="date" class="form-control"></div>
        <div class="form-group col-md-4"><label>Hasta</label><input type="date" class="form-control"></div>
        <div class="form-group col-md-4"><label>Estado</label>
          <select class="form-control"><option>Todos</option><option>Confirmada</option><option>Pendiente</option><option>Cancelada</option><option>Reprogramada</option></select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group col-md-6"><label>Doctor</label><select class="form-control"><option>Todos</option><option>Dr. López</option><option>Dra. Molina</option></select></div>
        <div class="form-group col-md-6"><label>Paciente</label><input class="form-control" placeholder="Nombre o usuario"></div>
      </div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" data-dismiss="modal">Cerrar</button><button class="btn btn-primary">Aplicar</button></div>
  </div></div>
</div>

{{-- MODAL: Crear cita --}}
<div class="modal fade" id="crearCita" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Nueva cita</h5>
      <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
    <div class="modal-body">
      <div class="form-group"><label>Paciente</label><input class="form-control" placeholder="Buscar paciente"></div>
      <div class="form-row">
        <div class="form-group col-md-6"><label>Fecha</label><input type="date" class="form-control"></div>
        <div class="form-group col-md-6"><label>Hora</label><input type="time" class="form-control"></div>
      </div>
      <div class="form-group"><label>Doctor</label><select class="form-control"><option>Dr. López</option><option>Dra. Molina</option></select></div>
      <div class="form-group"><label>Motivo</label><input class="form-control" placeholder="Ej. Limpieza dental"></div>
      <div class="form-group"><label>Estado</label><select class="form-control"><option>Confirmada</option><option>Pendiente</option><option>Cancelada</option><option>Reprogramada</option></select></div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" data-dismiss="modal">Cancelar</button><button class="btn btn-primary">Guardar</button></div>
  </div></div>
</div>

{{-- MODALES de ver/editar (maqueta) --}}
<div class="modal fade" id="verCita" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
  <div class="modal-header"><h5 class="modal-title">Detalle de cita</h5><button class="close" data-dismiss="modal"><span>&times;</span></button></div>
  <div class="modal-body">
    <dl class="row mb-0">
      <dt class="col-sm-4">Paciente</dt><dd class="col-sm-8">Ana Rivera</dd>
      <dt class="col-sm-4">Doctor</dt><dd class="col-sm-8">Dr. López</dd>
      <dt class="col-sm-4">Fecha / Hora</dt><dd class="col-sm-8">2025-08-12 08:30</dd>
      <dt class="col-sm-4">Estado</dt><dd class="col-sm-8"><span class="badge badge-info">Confirmada</span></dd>
      <dt class="col-sm-4">Motivo</dt><dd class="col-sm-8">Limpieza dental</dd>
    </dl>
  </div>
  <div class="modal-footer"><button class="btn btn-secondary" data-dismiss="modal">Cerrar</button></div>
</div></div></div>

<div class="modal fade" id="editarCita" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
  <div class="modal-header"><h5 class="modal-title">Editar cita</h5><button class="close" data-dismiss="modal"><span>&times;</span></button></div>
  <div class="modal-body">
    <div class="form-row">
      <div class="form-group col-md-6"><label>Fecha</label><input type="date" class="form-control" value="2025-08-12"></div>
      <div class="form-group col-md-6"><label>Hora</label><input type="time" class="form-control" value="08:30"></div>
    </div>
    <div class="form-group"><label>Estado</label><select class="form-control"><option selected>Confirmada</option><option>Pendiente</option><option>Cancelada</option></select></div>
    <div class="form-group"><label>Motivo</label><input class="form-control" value="Limpieza dental"></div>
  </div>
  <div class="modal-footer"><button class="btn btn-secondary" data-dismiss="modal">Cancelar</button><button class="btn btn-primary">Guardar cambios</button></div>
</div></div></div>
@endsection
