@extends('adminlte::page')

@section('title', 'Objetos')

@section('content_header')
  <div class="d-flex align-items-center justify-content-between">
    <h1 class="h3 mb-0">
      <i class="fas fa-cubes mr-2 text-teal"></i> Objetos del sistema
    </h1>
    <button class="btn btn-primary" data-toggle="modal" data-target="#modalObjeto">
      <i class="fas fa-plus mr-1"></i> Nuevo objeto
    </button>
  </div>
@endsection

@section('content')
  {{-- Alertas --}}
  @if(session('ok'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fas fa-check-circle mr-1"></i> {{ session('ok') }}
      <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
  @endif

  <div class="card shadow-sm">
    <div class="card-header bg-white">
      <div class="input-group">
        <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-search"></i></span></div>
        <input id="q" class="form-control" placeholder="Filtrar por nombre, descripción o URL...">
      </div>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0" id="tablaObjetos">
          <thead class="thead-light">
            <tr>
              <th style="width:28%">Nombre</th>
              <th>Descripción</th>
              <th style="width:26%">URL</th>
              <th style="width:10%">Estado</th>
              <th style="width:10%" class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @foreach($objetos as $o)
              <tr>
                <td class="font-weight-600">{{ $o->NOM_OBJETO }}</td>
                <td>{{ $o->DESC_OBJETO }}</td>
                <td><code>{{ $o->URL_OBJETO }}</code></td>
                <td>
                  <span class="badge badge-{{ $o->ESTADO_OBJETO ? 'success':'secondary' }}">
                    {{ $o->ESTADO_OBJETO ? 'ACTIVO':'INACTIVO' }}
                  </span>
                </td>
                <td class="text-center">
                  <button class="btn btn-sm btn-outline-primary mr-1 btn-edit"
                          data-id="{{ $o->COD_OBJETO }}"
                          data-nom="{{ $o->NOM_OBJETO }}"
                          data-desc="{{ $o->DESC_OBJETO }}"
                          data-url="{{ $o->URL_OBJETO }}"
                          data-estado="{{ $o->ESTADO_OBJETO }}">
                    <i class="fas fa-edit"></i>
                  </button>
                  <form class="d-inline" method="POST" action="{{ route('seguridad.objetos.destroy',$o->COD_OBJETO) }}"
                        onsubmit="return confirm('¿Eliminar {{ $o->NOM_OBJETO }}?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Modal Crear/Editar --}}
  <div class="modal fade" id="modalObjeto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <form method="POST" action="{{ route('seguridad.objetos.store') }}">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title"><i class="fas fa-cube mr-2 text-primary"></i> <span id="lblModal">Nuevo objeto</span></h5>
            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <div class="modal-body">
            <div class="form-row">
              <div class="form-group col-md-6">
                <label>Nombre <span class="text-danger">*</span></label>
                <input name="NOM_OBJETO" id="inpNom" class="form-control" maxlength="150" required>
                <small class="text-muted">Usa prefijo, ej. <code>SEGURIDAD_ROLES</code></small>
              </div>
              <div class="form-group col-md-6">
                <label>URL <span class="text-danger">*</span></label>
                <input name="URL_OBJETO" id="inpUrl" class="form-control" maxlength="255" placeholder="/seguridad/roles" required>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-9">
                <label>Descripción</label>
                <input name="DESC_OBJETO" id="inpDesc" class="form-control" maxlength="255">
              </div>
              <div class="form-group col-md-3">
                <label>Estado</label>
                <select name="ESTADO_OBJETO" id="inpEstado" class="form-control">
                  <option value="1">ACTIVO</option>
                  <option value="0">INACTIVO</option>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-primary"><i class="fas fa-save mr-1"></i> Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@push('js')
<script>
  // Filtro simple
  document.getElementById('q').addEventListener('input', function(){
    const q = this.value.toLowerCase();
    document.querySelectorAll('#tablaObjetos tbody tr').forEach(tr=>{
      tr.style.display = tr.innerText.toLowerCase().includes(q) ? '' : 'none';
    });
  });

  // Prefill modal para editar (reusamos el store como upsert)
  document.querySelectorAll('.btn-edit').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      document.getElementById('lblModal').innerText = 'Editar objeto';
      document.getElementById('inpNom').value   = btn.dataset.nom;
      document.getElementById('inpDesc').value  = btn.dataset.desc || '';
      document.getElementById('inpUrl').value   = btn.dataset.url;
      document.getElementById('inpEstado').value= btn.dataset.estado;
      $('#modalObjeto').modal('show');
    });
  });

  // Limpiar al abrir “nuevo”
  $('#modalObjeto').on('show.bs.modal', function(e){
    if(!e.relatedTarget || !e.relatedTarget.classList.contains('btn-edit')){
      document.getElementById('lblModal').innerText = 'Nuevo objeto';
      document.getElementById('inpNom').value = '';
      document.getElementById('inpDesc').value= '';
      document.getElementById('inpUrl').value = '';
      document.getElementById('inpEstado').value='1';
    }
  });
</script>
@endpush
