@extends('adminlte::page')
@section('title','Doctores')
@section('content_header')
  <div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0">Doctores</h1>
    <button class="btn btn-primary" onclick="openNuevoDoctorModal()"><i class="fas fa-user-md"></i> Nuevo doctor</button>
  </div>
@endsection
@section('content')
@php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Determinar columnas disponibles para el "usuario"
$usernameCandidates = ['USERNAME','USR_USUARIO','USUARIO','USER','USR_NOMBRE','NOMBRE_USUARIO'];
$usernameParts = [];
$groupUsernameCols = [];
foreach ($usernameCandidates as $col) {
    if (Schema::hasColumn('tbl_usuario', $col)) {
        $usernameParts[] = "u.{$col}";
        $groupUsernameCols[] = "u.{$col}";
    }
}
$usernameSelect = count($usernameParts) ? 'COALESCE(' . implode(', ', $usernameParts) . ') as usuario' : 'NULL as usuario';
$estadoSelect = Schema::hasColumn('tbl_usuario','ESTADO_USUARIO') ? 'u.ESTADO_USUARIO as estado' : 'NULL as estado';

// Construir consulta base
$rowsQuery = DB::table('tbl_persona as p')
    ->leftJoin('tbl_usuario as u', 'u.FK_COD_PERSONA', '=', 'p.COD_PERSONA')
    ->leftJoin('tbl_telefono as t', 't.FK_COD_PERSONA', '=', 'p.COD_PERSONA')
    ->leftJoin('tbl_correo as c', function($join){
        $join->on('c.FK_COD_PERSONA', '=', 'p.COD_PERSONA')
             ->where('c.TIPO_CORREO', '=', 1);
    })
    ->selectRaw("
        p.COD_PERSONA as id,
        p.PRIMER_NOMBRE,
        p.PRIMER_APELLIDO,
        MIN(t.NUM_TELEFONO) as telefono,
        c.CORREO as correo,
        {$usernameSelect},
        {$estadoSelect}
    ");

// Filtrar por rol si existe la columna FK_COD_ROL
if (Schema::hasColumn('tbl_usuario','FK_COD_ROL')) {
    // Mostrar todos los usuarios cuyo FK_COD_ROL = 3
    $rowsQuery->where('u.FK_COD_ROL', 3);
}

// Construir array groupBy evitando unpacking en la llamada
$groupBy = [
    'p.COD_PERSONA',
    'p.PRIMER_NOMBRE',
    'p.PRIMER_APELLIDO',
    'c.CORREO'
];
if (!empty($groupUsernameCols)) {
    $groupBy = array_merge($groupBy, $groupUsernameCols);
}
if (Schema::hasColumn('tbl_usuario','ESTADO_USUARIO')) {
    $groupBy[] = 'u.ESTADO_USUARIO';
}

$rows = $rowsQuery
    ->groupBy($groupBy)
    ->get();
@endphp

<div class="card">
  <div class="card-body table-responsive p-0">
    <table class="table table-hover mb-0">
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Correo</th>
          <th>Especialidad</th>
          <th>Teléfono</th>
          <th>Estado</th>
          <th class="text-right">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @foreach($rows as $r)
        @php
          $nombre = trim(($r->PRIMER_NOMBRE ?? '') . ' ' . ($r->PRIMER_APELLIDO ?? ''));
          $correo = $r->correo ?? '-';
          $usuario = $r->usuario ?? '-';
          $telefono = $r->telefono ?? '-';
          $estado = ($r->estado === null) ? 'Inactivo' : (intval($r->estado) === 1 ? 'Activo' : 'Inactivo');
          $especialidad = '-';
        @endphp
        <tr>
          <td>{{ $nombre }}</td>
          <td>{{ $correo }}</td>
          <td>{{ $especialidad }}</td>
          <td>{{ $telefono }}</td>
          <td><span class="badge badge-{{ $estado === 'Activo' ? 'success' : 'secondary' }}">{{ $estado }}</span></td>
          <td class="text-right">
            <!-- boton edit: incluye atributos data con los valores de la fila -->
            <button
              class="btn btn-sm btn-outline-primary openEditDoctorBtn"
              type="button"
              data-id="{{ $r->id }}"
              data-nombre="{{ e($nombre) }}"
              data-correo="{{ e($correo) }}"
              data-usuario="{{ e($usuario) }}"
              data-especialidad="{{ e($especialidad) }}"
              data-telefono="{{ e($telefono) }}"
              data-estado="{{ e($estado) }}"
              title="Editar doctor">
              <i class="fas fa-edit"></i>
            </button>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<!-- Modal: Nuevo doctor -->
<div class="modal fade" id="modalNuevoDoctor" tabindex="-1" role="dialog" aria-labelledby="modalNuevoDoctorLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalNuevoDoctorLabel">Nuevo doctor</h5>
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
        <button type="button" class="btn btn-primary" id="guardarDoctorBtn">Guardar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Editar doctor (campos del encabezado) -->
<div class="modal fade" id="modalEditarDoctor" tabindex="-1" role="dialog" aria-labelledby="modalEditarDoctorLabel" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditarDoctorLabel">Editar doctor</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="formEditarDoctor">
          <input type="hidden" id="edit_doctor_id" name="id" value="">
          <div class="form-group">
            <label>Nombre</label>
            <input type="text" class="form-control" id="edit_doctor_nombre" name="nombre" required>
          </div>
          <div class="form-group">
            <label>Correo</label>
            <input type="email" class="form-control" id="edit_doctor_correo" name="correo" required>
          </div>
          <div class="form-group">
            <label>Especialidad</label>
            <input type="text" class="form-control" id="edit_doctor_especialidad" name="especialidad">
          </div>
          <div class="form-group">
            <label>Teléfono</label>
            <input type="text" class="form-control" id="edit_doctor_telefono" name="telefono">
          </div>
          <div class="form-group">
            <label>Estado</label>
            <select class="form-control" id="edit_doctor_estado" name="estado" required>
              <option value="Activo">Activo</option>
              <option value="Inactivo">Inactivo</option>
            </select>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="guardarEdicionDoctorBtn">Guardar cambios</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('js')
<script>
function openNuevoDoctorModal(){
  $('#modalNuevoDoctor').modal('show');
}

document.addEventListener('DOMContentLoaded', function(){
  $('#guardarDoctorBtn').on('click', function(){
    const orderedFields = [
      'PRIMER_NOMBRE','SEGUNDO_NOMBRE',
      'PRIMER_APELLIDO','SEGUNDO_APELLIDO',
      'TIPO_GENERO','NUM_TELEFONO','TIPO_TELEFONO',
      'DEPARTAMENTO','MUNICIPIO',
      'CIUDAD','COLONIA','REFERENCIA',
      'CORREO','password','password_confirmation'
    ];

    const $modal = $('#modalNuevoDoctor');
    const fd = new FormData();
    fd.append('_token', '{{ csrf_token() }}');

    orderedFields.forEach(name => {
      const $el = $modal.find('[name="'+name+'"]');
      if (!$el.length) return;
      if ($el.is(':file')) {
        const files = $el[0].files;
        for (let i=0;i<files.length;i++) fd.append(name, files[i]);
      } else {
        fd.append(name, $el.val());
      }
    });

    $modal.find('input,select,textarea').each(function(){
      const name = $(this).attr('name');
      if (!name) return;
      if (orderedFields.indexOf(name) !== -1) return;
      if (name === '_token') return;
      if ($(this).is(':file')) {
        const files = this.files;
        for (let i=0;i<files.length;i++) fd.append(name, files[i]);
      } else {
        fd.append(name, $(this).val());
      }
    });

    $.ajax({
      url: '{{ route("doctores.store") }}',
      type: 'POST',
      data: fd,
      processData: false,
      contentType: false,
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json'
      },
      success: function(response){
        $('#modalNuevoDoctor').modal('hide');
        if (response && response.success) {
          if (typeof toastr !== 'undefined') toastr.success('Tarea realizada con exito');
          else alert('Tarea realizada con exito');
          setTimeout(()=>location.reload(), 600);
        } else {
          const msg = response.message || 'Error al guardar';
          if (typeof toastr !== 'undefined') toastr.error(msg);
          else alert(msg);
        }
      },
      error: function(xhr){
        let msg = 'Error al guardar';
        if (xhr && xhr.responseJSON && xhr.responseJSON.errors) {
          msg = '';
          Object.keys(xhr.responseJSON.errors).forEach(function(k){
            msg += xhr.responseJSON.errors[k][0] + '\n';
          });
        } else if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
          msg = xhr.responseJSON.message;
        } else if (xhr && xhr.responseText) {
          msg = xhr.responseText;
        }
        if (typeof toastr !== 'undefined') toastr.error(msg);
        else alert(msg);
        console.error(xhr);
      }
    });
  });

  // abrir y poblar modal al hacer click en el botón de editar
  $(document).on('click', '.openEditDoctorBtn', function(){
    const $btn = $(this);
    $('#edit_doctor_id').val($btn.data('id') || '');
    $('#edit_doctor_nombre').val($btn.data('nombre') || '');
    $('#edit_doctor_correo').val($btn.data('correo') || '');
    $('#edit_doctor_especialidad').val($btn.data('especialidad') || '');
    $('#edit_doctor_telefono').val($btn.data('telefono') || '');
    const estado = $btn.data('estado') || 'Activo';
    $('#edit_doctor_estado').val(estado === 'Activo' ? 'Activo' : 'Inactivo');
    // guardar username original para usarlo al actualizar en BD
    const origUsuario = $btn.data('usuario') || '';
    $('#edit_doctor_usuario').remove(); // limpiar cualquier campo oculto previo
    // añadir input hidden para mantener original y permitir editar el usuario en modal si quieres
    $('<input>').attr({type:'hidden', id:'edit_doctor_usuario', name:'usuario_original', value: origUsuario}).appendTo('#formEditarDoctor');
    // opcional: también puedes prefill un campo visible para editar el username si añades uno al form
    $('#modalEditarDoctor').modal('show');
  });

  // Guardar cambios en UI (solo UI). Reemplaza por AJAX si quieres persistir.
  $('#guardarEdicionDoctorBtn').on('click', function(){
    const id = $('#edit_doctor_id').val();
    const nombre = $('#edit_doctor_nombre').val();
    const correo = $('#edit_doctor_correo').val();
    const especialidad = $('#edit_doctor_especialidad').val();
    const telefono = $('#edit_doctor_telefono').val();
    const estado = $('#edit_doctor_estado').val();
    // obtener usuario original desde input hidden
    const originalUsuario = $('#edit_doctor_usuario').val() || '';
    // si quieres permitir cambiar el usuario desde la modal, lee también el nuevo valor; aquí asumimos no-edit (pero payload envía 'usuario' con mismo valor)
    const nuevoUsuario = originalUsuario;

    // Enviar PUT al servidor para persistir
    if (!originalUsuario) {
      alert('Usuario original no disponible. No se puede actualizar en BD.');
      return;
    }
    const payload = {
      nombre: nombre,
      correo: correo,
      usuario: nuevoUsuario,
      telefono: telefono,
      estado: estado,
      _token: '{{ csrf_token() }}'
    };
    const putUrl = '{{ url("/doctores") }}/' + encodeURIComponent(originalUsuario);
    $.ajax({
      url: putUrl,
      method: 'PUT',
      data: payload,
      headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'},
      success: function(res) {
        if (res && res.success) {
          // aplicar cambios en la UI tras confirmación del servidor
          const $rowBtn = $('.openEditDoctorBtn[data-id="'+id+'"]');
          if ($rowBtn.length) {
            $rowBtn.data('nombre', nombre).attr('data-nombre', nombre);
            $rowBtn.data('correo', correo).attr('data-correo', correo);
            $rowBtn.data('especialidad', especialidad).attr('data-especialidad', especialidad);
            $rowBtn.data('telefono', telefono).attr('data-telefono', telefono);
            $rowBtn.data('estado', estado).attr('data-estado', estado);
            // actualizar celdas visibles
            const $tr = $rowBtn.closest('tr');
            $tr.find('td').eq(0).text(nombre);
            $tr.find('td').eq(1).text(correo);
            $tr.find('td').eq(2).text(especialidad);
            $tr.find('td').eq(3).text(telefono);
            $tr.find('td').eq(4).find('span.badge').removeClass('badge-success badge-secondary').addClass(estado === 'Activo' ? 'badge-success' : 'badge-secondary').text(estado);
          }
          $('#modalEditarDoctor').modal('hide');
          if (typeof toastr !== 'undefined') toastr.success(res.message || 'Doctor actualizado');
          else console.log('Doctor actualizado');
        } else {
          const msg = res.message || 'Error al actualizar';
          if (typeof toastr !== 'undefined') toastr.error(msg); else alert(msg);
        }
      },
      error: function(xhr){
        let msg = 'Error al actualizar';
        if (xhr && xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
        else if (xhr && xhr.responseText) msg = xhr.responseText;
        if (typeof toastr !== 'undefined') toastr.error(msg); else alert(msg);
        console.error(xhr);
      }
    });
    return;
    const $rowBtn = $('.openEditDoctorBtn[data-id="'+id+'"]');
    if ($rowBtn.length) {
      $rowBtn.data('nombre', nombre).attr('data-nombre', nombre);
      $rowBtn.data('correo', correo).attr('data-correo', correo);
      $rowBtn.data('especialidad', especialidad).attr('data-especialidad', especialidad);
      $rowBtn.data('telefono', telefono).attr('data-telefono', telefono);
      $rowBtn.data('estado', estado).attr('data-estado', estado);

      const $tr = $rowBtn.closest('tr');
      $tr.find('td').eq(0).text(nombre);
      $tr.find('td').eq(1).text(correo);
      $tr.find('td').eq(2).text(especialidad);
      $tr.find('td').eq(3).text(telefono);
      $tr.find('td').eq(4).find('span.badge').removeClass('badge-success badge-secondary').addClass(estado === 'Activo' ? 'badge-success' : 'badge-secondary').text(estado);
    }

    $('#modalEditarDoctor').modal('hide');
    if (typeof toastr !== 'undefined') toastr.success('Cambios aplicados (solo UI). Implementa AJAX para persistir.');
    else console.log('Cambios aplicados (solo UI). Implementa AJAX para persistir.');
  });

});
</script>
@endsection
