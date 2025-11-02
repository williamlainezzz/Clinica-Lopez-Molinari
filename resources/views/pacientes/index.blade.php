@extends('adminlte::page')
@section('title','Pacientes')
@section('content_header')
  <div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0">Pacientes</h1>
    <button class="btn btn-primary" onclick="openNuevoPacienteModal()"><i class="fas fa-user-injured"></i> Nuevo paciente</button>
  </div>
@endsection
@section('content')
@php
// Traer datos desde la BD: persona + usuario (estado) + telefono (primer teléfono) + direccion (concatenada) + correo principal
$personas = \Illuminate\Support\Facades\DB::table('tbl_persona as p')
    ->leftJoin('tbl_usuario as u', 'u.FK_COD_PERSONA', '=', 'p.COD_PERSONA')
    ->leftJoin('tbl_telefono as t', 't.FK_COD_PERSONA', '=', 'p.COD_PERSONA')
    ->leftJoin('tbl_direccion as d', 'd.FK_COD_PERSONA', '=', 'p.COD_PERSONA')
    // traer sólo el correo principal (TIPO_CORREO = 1). Ajusta el valor si tu esquema usa otro código.
    ->leftJoin('tbl_correo as c', function($join){
        $join->on('c.FK_COD_PERSONA', '=', 'p.COD_PERSONA')
             ->where('c.TIPO_CORREO', '=', 1);
    })
    ->selectRaw("
        p.COD_PERSONA as id,
        p.PRIMER_NOMBRE,
        p.PRIMER_APELLIDO,
        p.TIPO_GENERO,
        MIN(t.NUM_TELEFONO) as telefono,
        CONCAT_WS(' ', d.DEPARTAMENTO, d.MUNICIPIO, d.CIUDAD, d.COLONIA, d.REFERENCIA) as direccion,
        c.CORREO as correo,
        u.ESTADO_USUARIO as estado
    ")
    ->where('u.FK_COD_ROL', 5) // <- mostrar solo usuarios con rol = 5 (Pacientes)
    ->groupBy(
        'p.COD_PERSONA',
        'p.PRIMER_NOMBRE',
        'p.PRIMER_APELLIDO',
        'p.TIPO_GENERO',
        'u.ESTADO_USUARIO',
        'd.DEPARTAMENTO',
        'd.MUNICIPIO',
        'd.CIUDAD',
        'd.COLONIA',
        'd.REFERENCIA',
        'c.CORREO'
    )
    ->get();
@endphp

<div class="card">
  <div class="card-body table-responsive p-0">
    <table class="table table-hover mb-0">
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Correo</th>
          <th>Género</th>
          <th>Teléfono</th>
          <th>Dirección</th>
          <th>Estado</th>
          <th class="text-right">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @foreach($personas as $p)
        @php
          $nombre = trim(($p->PRIMER_NOMBRE ?? '') . ' ' . ($p->PRIMER_APELLIDO ?? ''));
          $genero = match((string)($p->TIPO_GENERO ?? '')) {
            '1' => 'Masculino',
            '2' => 'Femenino',
            '3' => 'Otro',
            default => (empty($p->TIPO_GENERO) ? 'No especificado' : $p->TIPO_GENERO),
          };
          $correo = $p->correo ? $p->correo : '-';
          $telefono = $p->telefono ? $p->telefono : '-';
          $direccion = $p->direccion ? $p->direccion : '-';
          $estado = ($p->estado === null) ? 'Inactivo' : (intval($p->estado) === 1 ? 'Activo' : 'Inactivo');
        @endphp
        <tr>
          <td>{{ $nombre }}</td>
          <td>{{ $correo }}</td>
          <td>{{ $genero }}</td>
          <td>{{ $telefono }}</td>
          <td>{{ $direccion }}</td>
          <td>
            <span class="badge badge-{{ $estado === 'Activo' ? 'success' : 'secondary' }}">{{ $estado }}</span>
          </td>
          <td class="text-right">
            <button class="btn btn-sm btn-outline-primary" title="Editar" onclick="openEditModal({{ $p->id }})"><i class="fas fa-edit"></i></button>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<!-- Modal: Nuevo paciente -->
<div class="modal fade" id="modalNuevoPaciente" tabindex="-1" role="dialog" aria-labelledby="modalNuevoPacienteLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalNuevoPacienteLabel">Nuevo paciente</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        {{-- Incluimos el formulario de registro (partial) --}}
        @include('personas._register_form')
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="guardarPacienteBtn">Guardar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Editar paciente -->
<div class="modal fade" id="modalEditarPaciente" tabindex="-1" role="dialog" aria-labelledby="modalEditarPacienteLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditarPacienteLabel">Editar paciente</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="formEditarPaciente">
          @csrf
          <input type="hidden" name="_method" value="PUT">
          <input type="hidden" id="edit_persona_id" name="persona_id" value="">

          <div class="form-group">
            <label>Nombre completo</label>
            <input type="text" class="form-control" id="edit_full_name" name="full_name" required>
            <div class="invalid-feedback" data-field="full_name"></div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Género</label>
              <select id="edit_TIPO_GENERO" name="TIPO_GENERO" class="form-control" required>
                <option value="1">Masculino</option>
                <option value="2">Femenino</option>
                <option value="3">Otro</option>
              </select>
              <div class="invalid-feedback" data-field="TIPO_GENERO"></div>
            </div>
            <div class="form-group col-md-6">
              <label>Teléfono</label>
              <input type="text" class="form-control" id="edit_NUM_TELEFONO" name="NUM_TELEFONO">
              <div class="invalid-feedback" data-field="NUM_TELEFONO"></div>
            </div>
          </div>

          <div class="form-group">
            <label>Dirección / Referencia</label>
            <textarea id="edit_REFERENCIA" name="REFERENCIA" rows="2" class="form-control"></textarea>
            <div class="invalid-feedback" data-field="REFERENCIA"></div>
          </div>

          <div class="form-group">
            <label>Estado</label>
            <select id="edit_ESTADO" name="ESTADO" class="form-control" required>
              <option value="Activo">Activo</option>
              <option value="Inactivo">Inactivo</option>
            </select>
            <div class="invalid-feedback" data-field="ESTADO"></div>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="guardarEdicionBtn">Guardar cambios</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('js')
<script>
function openNuevoPacienteModal(){
  $('#modalNuevoPaciente').modal('show');
}

function openEditModal(id) {
  // limpiar errores previos
  $('#formEditarPaciente .is-invalid').removeClass('is-invalid');
  $('#formEditarPaciente .invalid-feedback').text('');

  // obtener datos
  $.getJSON("{{ url('personas') }}/" + id + "/json")
    .done(function(data){
      $('#edit_persona_id').val(data.id || id);
      const full = [data.PRIMER_NOMBRE || '', data.PRIMER_APELLIDO || ''].filter(Boolean).join(' ');
      $('#edit_full_name').val(full);
      $('#edit_TIPO_GENERO').val(data.TIPO_GENERO ?? '');
      $('#edit_NUM_TELEFONO').val(data.telefono ?? '');
      $('#edit_REFERENCIA').val(data.direccion ?? '');
      $('#edit_ESTADO').val(data.estado == 1 ? 'Activo' : 'Inactivo');
      $('#modalEditarPaciente').modal('show');
    })
    .fail(function(xhr){
      alert('No se pudo cargar los datos del paciente.');
      console.error(xhr.responseText || xhr.statusText);
    });
}

document.addEventListener('DOMContentLoaded', function(){
  $('#guardarPacienteBtn').on('click', function(){
    const $form = $('#formRegistroPersona');
    const url = $form.attr('action');
    const data = $form.serialize();

    // LOG: mostrar payload serializado y campos clave antes de enviar
    console.log('[pacientes] enviando AJAX a:', url);
    console.log('[pacientes] datos serializados:', data);
    console.log('[pacientes] PRIMER_NOMBRE=', $form.find('[name="PRIMER_NOMBRE"]').val());
    console.log('[pacientes] PRIMER_APELLIDO=', $form.find('[name="PRIMER_APELLIDO"]').val());
    console.log('[pacientes] CORREO=', $form.find('[name="CORREO"]').val());
    console.log('[pacientes] NUM_TELEFONO=', $form.find('[name="NUM_TELEFONO"]').val());
    console.log('[pacientes] ESTADO=', $form.find('[name="ESTADO"]').val());
    console.log('[pacientes] username-preview=', document.getElementById('username-preview')?.textContent);

    // limpiar errores previos
    $form.find('.is-invalid').removeClass('is-invalid');
    $form.find('.invalid-feedback').text('');

    $.ajax({
      url: url,
      method: 'POST',
      data: data,
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
      },
      success: function(res) {
        $('#modalNuevoPaciente').modal('hide');
        if (typeof toastr !== 'undefined') {
          toastr.success('Tarea realizada con exito');
        } else {
          alert('Tarea realizada con exito');
        }
        // Opcional: recargar la página para mostrar nuevo registro
        setTimeout(function(){ location.reload(); }, 700);
      },
      error: function(xhr) {
        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
          const errors = xhr.responseJSON.errors;
          // Mostrar errores en los campos
          Object.keys(errors).forEach(function(key){
            // Mapea nombres de campos si tu backend devuelve otros (ej. 'PRIMER_NOMBRE')
            const field = $('[name="'+key+'"]');
            if (field.length) {
              field.addClass('is-invalid');
              const feedback = $form.find('.invalid-feedback[data-field="'+key+'"]');
              if (feedback.length) {
                feedback.text(errors[key].join(' '));
              } else {
                // fallback: insertar mensaje después del campo
                field.after('<div class="invalid-feedback d-block">'+errors[key].join(' ')+'</div>');
              }
            }
          });
          if (typeof toastr !== 'undefined') {
            toastr.error('Corrige los errores del formulario');
          }
        } else {
          // error inesperado
          if (typeof toastr !== 'undefined') {
            toastr.error('Error al crear paciente');
          } else {
            alert('Error al crear paciente');
          }
          console.error(xhr.responseText || xhr.statusText);
        }
      }
    });
  });

  $('#guardarEdicionBtn').on('click', function(){
    const id = $('#edit_persona_id').val();
    const url = "{{ url('personas') }}/" + id;
    const payload = {
      full_name: $('#edit_full_name').val(),
      TIPO_GENERO: $('#edit_TIPO_GENERO').val(),
      NUM_TELEFONO: $('#edit_NUM_TELEFONO').val(),
      REFERENCIA: $('#edit_REFERENCIA').val(),
      ESTADO: $('#edit_ESTADO').val()
    };

    // limpiar errores previos
    $('#formEditarPaciente .is-invalid').removeClass('is-invalid');
    $('#formEditarPaciente .invalid-feedback').text('');

    $.ajax({
      url: url,
      method: 'PUT',
      data: payload,
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()},
      success: function(res) {
        $('#modalEditarPaciente').modal('hide');
        if (typeof toastr !== 'undefined') toastr.success('Paciente actualizado');
        else alert('Paciente actualizado');
        setTimeout(function(){ location.reload(); }, 700);
      },
      error: function(xhr) {
        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
          const errors = xhr.responseJSON.errors;
          Object.keys(errors).forEach(function(key){
            const field = $('#formEditarPaciente').find('[name="'+key+'"]');
            if (field.length) {
              field.addClass('is-invalid');
              const feedback = $('#formEditarPaciente').find('.invalid-feedback[data-field="'+key+'"]');
              if (feedback.length) feedback.text(errors[key].join(' '));
              else field.after('<div class="invalid-feedback d-block">'+errors[key].join(' ')+'</div>');
            }
          });
        } else {
          console.error(xhr.responseText || xhr.statusText);
          alert('Error al actualizar paciente');
        }
      }
    });
  });
});
</script>
@endsection
