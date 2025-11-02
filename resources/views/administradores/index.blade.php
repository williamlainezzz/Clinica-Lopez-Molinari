@extends('adminlte::page')
@section('title','Administradores')
@section('content_header')
  <div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0">Administradores</h1>
    <button class="btn btn-primary" onclick="openNuevoAdminModal()"><i class="fas fa-user-shield"></i> Nuevo administrador</button>
  </div>
@endsection
@section('content')
@php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Determinar columnas posibles para el "usuario" en tbl_usuario
$usernameCandidates = ['USERNAME','USR_USUARIO','USUARIO','USER','USR_NOMBRE','NOMBRE_USUARIO'];

// Crear lista de columnas existentes para COALESCE y para groupBy
$usernameParts = [];
$groupUsernameCols = [];
foreach ($usernameCandidates as $col) {
    if (Schema::hasColumn('tbl_usuario', $col)) {
        $usernameParts[] = "u.{$col}";
        $groupUsernameCols[] = "u.{$col}";
    }
}

// Expresión para seleccionar usuario (o NULL si no hay columnas)
if (count($usernameParts)) {
    $usernameSelect = 'COALESCE(' . implode(', ', $usernameParts) . ') as usuario';
} else {
    $usernameSelect = 'NULL as usuario';
}

// Estado del usuario (si existe la columna)
$estadoSelect = Schema::hasColumn('tbl_usuario', 'ESTADO_USUARIO') ? 'u.ESTADO_USUARIO as estado' : 'NULL as estado';

// Construir groupBy base
$groupBy = [
    'p.COD_PERSONA',
    'p.PRIMER_NOMBRE',
    'p.SEGUNDO_NOMBRE',
    'p.PRIMER_APELLIDO',
    'p.SEGUNDO_APELLIDO',
    'p.TIPO_GENERO',
    'd.DEPARTAMENTO',
    'd.MUNICIPIO',
    'd.CIUDAD',
    'd.COLONIA',
    'd.REFERENCIA',
    'c.CORREO'
];

// Agregar columnas de usuario y estado al groupBy si existen
if (!empty($groupUsernameCols)) {
    $groupBy = array_merge($groupBy, $groupUsernameCols);
}
if (Schema::hasColumn('tbl_usuario', 'ESTADO_USUARIO')) {
    $groupBy[] = 'u.ESTADO_USUARIO';
}

// Construir consulta
$rowsQuery = DB::table('tbl_persona as p')
    ->leftJoin('tbl_usuario as u', 'u.FK_COD_PERSONA', '=', 'p.COD_PERSONA')
    ->leftJoin('tbl_telefono as t', 't.FK_COD_PERSONA', '=', 'p.COD_PERSONA')
    ->leftJoin('tbl_direccion as d', 'd.FK_COD_PERSONA', '=', 'p.COD_PERSONA')
    ->leftJoin('tbl_correo as c', function($join){
        $join->on('c.FK_COD_PERSONA', '=', 'p.COD_PERSONA')
             ->where('c.TIPO_CORREO', '=', 1);
    });

// Filtrar por rol si la columna existe
if (Schema::hasColumn('tbl_usuario', 'FK_COD_ROL')) {
    $rowsQuery->where('u.FK_COD_ROL', 1);
}

// Selección dinámica
$selectRaw = "
    p.COD_PERSONA as id,
    p.PRIMER_NOMBRE,
    p.SEGUNDO_NOMBRE,
    p.PRIMER_APELLIDO,
    p.SEGUNDO_APELLIDO,
    p.TIPO_GENERO,
    MIN(t.NUM_TELEFONO) as telefono,
    CONCAT_WS(' ', d.DEPARTAMENTO, d.MUNICIPIO, d.CIUDAD, d.COLONIA, d.REFERENCIA) as direccion,
    c.CORREO as correo,
    {$usernameSelect},
    {$estadoSelect}
";

$rows = $rowsQuery
    ->selectRaw($selectRaw)
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
          <th>Usuario</th>
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
        @endphp
        <tr>
          <td>{{ $nombre }}</td>
          <td>{{ $correo }}</td>
          <td>{{ $usuario }}</td>
          <td>{{ $telefono }}</td>
          <td><span class="badge badge-{{ $estado === 'Activo' ? 'success' : 'secondary' }}">{{ $estado }}</span></td>
          <td class="text-right">
            <!-- boton edit actualizado: incluye atributos data con los valores de la fila -->
            <button
              class="btn btn-sm btn-outline-primary openEditAdminBtn"
              type="button"
              data-id="{{ $r->id }}"
              data-nombre="{{ e($nombre) }}"
              data-correo="{{ e($correo) }}"
              data-usuario="{{ e($usuario) }}"
              data-telefono="{{ e($telefono) }}"
              data-estado="{{ e($estado) }}"
              title="Editar administrador">
              <i class="fas fa-edit"></i>
            </button>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<!-- Modal: Nuevo administrador -->
<div class="modal fade" id="modalNuevoAdmin" tabindex="-1" role="dialog" aria-labelledby="modalNuevoAdminLabel" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalNuevoAdminLabel">Nuevo administrador</h5>
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
        <button type="button" class="btn btn-primary" id="guardarAdminBtn">Guardar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Editar administrador (campos de la cabecera: Nombre, Correo, Usuario, Teléfono, Estado) -->
<div class="modal fade" id="modalEditarAdmin" tabindex="-1" role="dialog" aria-labelledby="modalEditarAdminLabel" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditarAdminLabel">Editar administrador</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="formEditarAdmin">
          <input type="hidden" name="id" id="edit_admin_id" value="">
          <div class="form-group">
            <label>Nombre</label>
            <input type="text" class="form-control" id="edit_nombre" name="nombre" required>
          </div>
          <div class="form-group">
            <label>Correo</label>
            <input type="email" class="form-control" id="edit_correo" name="correo" required>
          </div>
          <div class="form-group">
            <label>Usuario</label>
            <input type="text" class="form-control" id="edit_usuario" name="usuario" required>
          </div>
          <div class="form-group">
            <label>Teléfono</label>
            <input type="text" class="form-control" id="edit_telefono" name="telefono">
          </div>
          <div class="form-group">
            <label>Estado</label>
            <select class="form-control" id="edit_estado" name="estado" required>
              <option value="Activo">Activo</option>
              <option value="Inactivo">Inactivo</option>
            </select>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <!-- Por ahora solo cierra; puedes cambiar para enviar AJAX al endpoint que uses -->
        <button type="button" class="btn btn-primary" id="guardarEdicionAdminBtn">Guardar cambios</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('js')
<script>
function openNuevoAdminModal(){
  $('#modalNuevoAdmin').modal('show');
}

document.addEventListener('DOMContentLoaded', function(){
  $('#guardarAdminBtn').on('click', function(){
    // Orden deseado de los campos (ajusta si tu partial usa otros nombres)
    const orderedFields = [
      'PRIMER_NOMBRE','SEGUNDO_NOMBRE',
      'PRIMER_APELLIDO','SEGUNDO_APELLIDO',
      'TIPO_GENERO','NUM_TELEFONO',
      'DEPARTAMENTO','MUNICIPIO',
      'CIUDAD','COLONIA','REFERENCIA',
      'CORREO','password','password_confirmation'
    ];

    // Buscar el formulario dentro del modal (si existe) o tomar inputs directos
    const $modal = $('#modalNuevoAdmin');
    const $form = $modal.find('form').first();
    const formEl = $form.length ? $form[0] : null;

    const fd = new FormData();

    // Añadir CSRF (por seguridad, aunque se añade en headers también)
    fd.append('_token', '{{ csrf_token() }}');

    // Añadir campos en el orden definido si existen en el modal
    orderedFields.forEach(name => {
      const $el = $modal.find('[name="'+name+'"]');
      if (!$el.length) return;
      // Si es input file, adjuntar archivos
      if ($el.is(':file')) {
        const files = $el[0].files;
        for (let i=0;i<files.length;i++) fd.append(name, files[i]);
      } else {
        fd.append(name, $el.val());
      }
    });

    // Añadir el resto de campos que no están en orderedFields (evitar duplicados)
    $modal.find('input,select,textarea').each(function(){
      const name = $(this).attr('name');
      if (!name) return;
      if (orderedFields.indexOf(name) !== -1) return;
      // evitar re-append del token campo si existe
      if (name === '_token') return;
      if ($(this).is(':file')) {
        const files = this.files;
        for (let i=0;i<files.length;i++) fd.append(name, files[i]);
      } else {
        fd.append(name, $(this).val());
      }
    });

    // Enviar datos al servidor vía AJAX (multipart/form-data)
    $.ajax({
      url: '{{ route("administradores.store") }}',
      type: 'POST',
      data: fd,
      processData: false,
      contentType: false,
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      success: function(response) {
        $('#modalNuevoAdmin').modal('hide');
        if (typeof toastr !== 'undefined') {
          toastr.success('Tarea realizada con exito');
        } else {
          alert('Tarea realizada con exito');
        }
        location.reload();
      },
      error: function(xhr){
        let msg = 'Error al guardar';
        if (xhr && xhr.responseJSON) {
          if (xhr.responseJSON.errors) {
            msg = '';
            Object.keys(xhr.responseJSON.errors).forEach(function(k){
              msg += xhr.responseJSON.errors[k][0] + '\n';
            });
          } else if (xhr.responseJSON.message) {
            msg = xhr.responseJSON.message;
          } else if (xhr.responseText) {
            msg = xhr.responseText;
          }
        }
        if (typeof toastr !== 'undefined') {
          toastr.error(msg);
        } else {
          alert(msg);
        }
      }
    });
  });

  // abrir y poblar modal al hacer click en el botón de editar
  $(document).on('click', '.openEditAdminBtn', function(){
    const $btn = $(this);
    $('#edit_admin_id').val($btn.data('id') || '');
    $('#edit_nombre').val($btn.data('nombre') || '');
    $('#edit_correo').val($btn.data('correo') || '');
    $('#edit_usuario').val($btn.data('usuario') || '');
    // Guardar el usuario original para localizar el registro en la BD al guardar
    $('#edit_usuario').data('original', $btn.data('usuario') || '').attr('data-original', $btn.data('usuario') || '');
    $('#edit_telefono').val($btn.data('telefono') || '');
    const estado = $btn.data('estado') || 'Activo';
    $('#edit_estado').val(estado === 'Activo' ? 'Activo' : 'Inactivo');

    $('#modalEditarAdmin').modal('show');
  });

  // comportamiento para el botón "Guardar cambios": enviar PUT por username original y sólo aplicar cambios si el servidor responde OK
  $('#guardarEdicionAdminBtn').off('click').on('click', function(){
    const id = $('#edit_admin_id').val();
    const nombre = $('#edit_nombre').val();
    const correo = $('#edit_correo').val();
    const usuario = $('#edit_usuario').val();
    const telefono = $('#edit_telefono').val();
    const estado = $('#edit_estado').val();

    // Validación mínima cliente
    if (!usuario) {
      alert('El campo usuario es obligatorio');
      return;
    }

    const payload = {
      nombre: nombre,
      correo: correo,
      usuario: usuario,
      telefono: telefono,
      estado: estado,
      _token: '{{ csrf_token() }}'
    };

    // Determinar username original: preferir el stored data-original del input; si no existe tomar del botón de la fila
    const $rowBtn = $('.openEditAdminBtn[data-id="'+id+'"]');
    const originalFromInput = $('#edit_usuario').data('original');
    const originalFromBtn = $rowBtn.data('usuario');
    const originalUsuario = (typeof originalFromInput !== 'undefined' && originalFromInput) ? originalFromInput : (originalFromBtn || usuario);

    const putUrl = '{{ url("/administradores") }}/' + encodeURIComponent(originalUsuario);

    $.ajax({
      url: putUrl,
      method: 'PUT',
      data: payload,
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json'
      },
      success: function(res) {
        if (res && res.success) {
          // aplicar cambios en la UI sólo después de confirmación del servidor
          if ($rowBtn.length) {
            $rowBtn.data('nombre', nombre).attr('data-nombre', nombre);
            $rowBtn.data('correo', correo).attr('data-correo', correo);
            // actualizar también el data-original a la nueva cuenta (para próximas ediciones)
            $rowBtn.data('usuario', usuario).attr('data-usuario', usuario);
            $('#edit_usuario').data('original', usuario).attr('data-original', usuario);
            $rowBtn.data('telefono', telefono).attr('data-telefono', telefono);
            $rowBtn.data('estado', estado).attr('data-estado', estado);

            const $tr = $rowBtn.closest('tr');
            $tr.find('td').eq(0).text(nombre);
            $tr.find('td').eq(1).text(correo);
            $tr.find('td').eq(2).text(usuario);
            $tr.find('td').eq(3).text(telefono);
            $tr.find('td').eq(4).find('span.badge').removeClass('badge-success badge-secondary').addClass(estado === 'Activo' ? 'badge-success' : 'badge-secondary').text(estado);
          }

          $('#modalEditarAdmin').modal('hide');
          if (typeof toastr !== 'undefined') toastr.success(res.message || 'Administrador actualizado');
          else console.log('Administrador actualizado');
        } else {
          const msg = res.message || 'Error al actualizar';
          if (typeof toastr !== 'undefined') toastr.error(msg);
          else alert(msg);
        }
      },
      error: function(xhr){
        let msg = 'Error al actualizar';
        if (xhr && xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
        else if (xhr && xhr.responseText) msg = xhr.responseText;
        if (typeof toastr !== 'undefined') toastr.error(msg);
        else alert(msg);
        console.error(xhr);
      }
    });
  });
});
</script>
@endsection
