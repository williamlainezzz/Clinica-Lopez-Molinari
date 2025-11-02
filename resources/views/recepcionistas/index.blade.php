@extends('adminlte::page')
@section('title','Recepcionistas')
@section('content_header')
  <div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0">Recepcionistas</h1>
    <button class="btn btn-primary" onclick="openNuevaRecepcionistaModal()"><i class="fas fa-user-tie"></i> Nueva recepcionista</button>
  </div>
@endsection
@section('content')
@php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Determinar columnas posibles para el "usuario" en tbl_usuario
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

// Consulta: traer recepcionistas (FK_COD_ROL = 4), correo principal (TIPO_CORREO=1) y primer teléfono
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

// Filtrar por rol = 4 si la columna existe
if (Schema::hasColumn('tbl_usuario','FK_COD_ROL')) {
    $rowsQuery->where('u.FK_COD_ROL', 4);
}

// Construir groupBy sin usar unpacking en la llamada
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

$rows = $rowsQuery->groupBy($groupBy)->get();
@endphp
<div class="card">
  <div class="card-body table-responsive p-0">
    <table class="table table-hover mb-0">
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Correo</th>
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
          $telefono = $r->telefono ?? '-';
          $estado = ($r->estado === null) ? 'Inactivo' : (intval($r->estado) === 1 ? 'Activo' : 'Inactivo');
          $usuario = $r->usuario ?? '-';
        @endphp
        <tr>
          <td>{{ $nombre }}</td>
          <td>{{ $correo }}</td>
          <td>{{ $telefono }}</td>
          <td><span class="badge badge-{{ $estado === 'Activo' ? 'success' : 'secondary' }}">{{ $estado }}</span></td>
          <td class="text-right">
            <!-- botón editar: incluir data-* y clase para abrir modal -->
            <button
              class="btn btn-sm btn-outline-primary openEditRecepBtn"
              type="button"
              data-id="{{ $r->id }}"
              data-nombre="{{ e($nombre) }}"
              data-correo="{{ e($correo) }}"
              data-usuario="{{ e($usuario) }}"
              data-telefono="{{ e($telefono) }}"
              data-estado="{{ e($estado) }}"
              title="Editar recepcionista">
              <i class="fas fa-edit"></i>
            </button>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<!-- Modal: Nueva recepcionista -->
<div class="modal fade" id="modalNuevaRecepcionista" tabindex="-1" role="dialog" aria-labelledby="modalNuevaRecepcionistaLabel" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalNuevaRecepcionistaLabel">Nueva recepcionista</h5>
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
        <button type="button" class="btn btn-primary" id="guardarRecepcionistaBtn">Guardar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Editar recepcionista -->
<div class="modal fade" id="modalEditarRecepcionista" tabindex="-1" role="dialog" aria-labelledby="modalEditarRecepcionistaLabel" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditarRecepcionistaLabel">Editar recepcionista</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="formEditarRecepcionista">
          <input type="hidden" id="edit_recep_id" name="id" value="">
          <input type="hidden" id="edit_recep_usuario_original" name="usuario_original" value="">
          <div class="form-group">
            <label>Nombre</label>
            <input type="text" class="form-control" id="edit_recep_nombre" name="nombre" required>
          </div>
          <div class="form-group">
            <label>Correo</label>
            <input type="email" class="form-control" id="edit_recep_correo" name="correo" required>
          </div>
          <div class="form-group">
            <label>Teléfono</label>
            <input type="text" class="form-control" id="edit_recep_telefono" name="telefono">
          </div>
          <div class="form-group">
            <label>Estado</label>
            <select class="form-control" id="edit_recep_estado" name="estado" required>
              <option value="Activo">Activo</option>
              <option value="Inactivo">Inactivo</option>
            </select>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="guardarEdicionRecepBtn">Guardar cambios</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('js')
<script>
function openNuevaRecepcionistaModal(){
  $('#modalNuevaRecepcionista').modal('show');
}

document.addEventListener('DOMContentLoaded', function(){
  // Reemplazado: enviar el formulario del partial a la misma ruta que usan los doctores
  $('#guardarRecepcionistaBtn').off('click').on('click', function(){
    const orderedFields = [
      'PRIMER_NOMBRE','SEGUNDO_NOMBRE',
      'PRIMER_APELLIDO','SEGUNDO_APELLIDO',
      'TIPO_GENERO','NUM_TELEFONO','TIPO_TELEFONO',
      'DEPARTAMENTO','MUNICIPIO',
      'CIUDAD','COLONIA','REFERENCIA',
      'CORREO','password','password_confirmation'
    ];

    const $modal = $('#modalNuevaRecepcionista');
    const fd = new FormData();
    fd.append('_token', '{{ csrf_token() }}');

    // Añadir campos en orden si existen
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

    // Añadir campo para indicar rol deseado (se usa en la closure /doctores para resolver FK_COD_ROL)
    fd.append('role_keywords', 'recepcionista,recepcionistas');

    // Añadir el resto de campos del modal (evitar duplicados)
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

    // Enviar a la misma ruta que usas para doctores
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
        $('#modalNuevaRecepcionista').modal('hide');
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
  $(document).on('click', '.openEditRecepBtn', function(){
    const $btn = $(this);
    $('#edit_recep_id').val($btn.data('id') || '');
    $('#edit_recep_nombre').val($btn.data('nombre') || '');
    $('#edit_recep_correo').val($btn.data('correo') || '');
    $('#edit_recep_telefono').val($btn.data('telefono') || '');
    const estado = $btn.data('estado') || 'Activo';
    $('#edit_recep_estado').val(estado === 'Activo' ? 'Activo' : 'Inactivo');
    // guardar usuario original para usar en la URL PUT
    $('#edit_recep_usuario_original').val($btn.data('usuario') || '');
    $('#modalEditarRecepcionista').modal('show');
  });

  // Guardar cambios: enviar PUT a /recepcionistas/{usuario_original} y actualizar UI si OK
  $('#guardarEdicionRecepBtn').on('click', function(){
    const id = $('#edit_recep_id').val();
    const nombre = $('#edit_recep_nombre').val();
    const correo = $('#edit_recep_correo').val();
    const telefono = $('#edit_recep_telefono').val();
    const estado = $('#edit_recep_estado').val();
    const originalUsuario = $('#edit_recep_usuario_original').val() || '';

    if (!originalUsuario) {
      alert('Usuario original no disponible. No se puede actualizar en BD.');
      return;
    }

    const payload = {
      nombre: nombre,
      correo: correo,
      usuario: originalUsuario, // manteniendo mismo usuario (puedes añadir campo para editarlo)
      telefono: telefono,
      estado: estado,
      _token: '{{ csrf_token() }}'
    };

    const putUrl = '{{ url("/recepcionistas") }}/' + encodeURIComponent(originalUsuario);

    $.ajax({
      url: putUrl,
      method: 'PUT',
      data: payload,
      headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'},
      success: function(res){
        if (res && res.success) {
          const $rowBtn = $('.openEditRecepBtn[data-id="'+id+'"]');
          if ($rowBtn.length) {
            $rowBtn.data('nombre', nombre).attr('data-nombre', nombre);
            $rowBtn.data('correo', correo).attr('data-correo', correo);
            $rowBtn.data('telefono', telefono).attr('data-telefono', telefono);
            $rowBtn.data('estado', estado).attr('data-estado', estado);
            const $tr = $rowBtn.closest('tr');
            $tr.find('td').eq(0).text(nombre);
            $tr.find('td').eq(1).text(correo);
            $tr.find('td').eq(2).text(telefono);
            $tr.find('td').eq(3).find('span.badge').removeClass('badge-success badge-secondary').addClass(estado === 'Activo' ? 'badge-success' : 'badge-secondary').text(estado);
          }
          $('#modalEditarRecepcionista').modal('hide');
          if (typeof toastr !== 'undefined') toastr.success('Tarea realizada con exito');
          else alert('Tarea realizada con exito');
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
  });

});
</script>
@endsection
