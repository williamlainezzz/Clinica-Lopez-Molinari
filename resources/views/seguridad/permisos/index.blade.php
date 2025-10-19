@extends('adminlte::page')

@section('title', 'Permisos')

@section('content_header')
  <div class="d-flex align-items-center justify-content-between">
    <h1 class="h3 mb-0">
      <i class="fas fa-user-shield mr-2 text-indigo"></i> Permisos por rol / objeto
    </h1>
    <a href="{{ route('seguridad.objetos.index') }}" class="btn btn-outline-secondary">
      <i class="fas fa-cubes mr-1"></i> Gestionar objetos
    </a>
  </div>
@endsection

@section('content')
  @if(session('ok'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fas fa-check-circle mr-1"></i> {{ session('ok') }}
      <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
  @endif

  <div class="card shadow-sm">
    <div class="card-header bg-white">
      <form method="GET" action="{{ route('seguridad.permisos.index') }}" class="form-inline">
        <label class="mr-2 mb-0">Rol:</label>
        <select name="rol_id" class="form-control mr-3" onchange="this.form.submit()">
          @foreach($roles as $r)
            <option value="{{ $r->COD_ROL }}" {{ request('rol_id', $roles->first()->COD_ROL)==$r->COD_ROL?'selected':'' }}>
              {{ $r->NOM_ROL }}
            </option>
          @endforeach
        </select>

        <div class="input-group ml-auto" style="max-width:320px;">
          <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-search"></i></span></div>
          <input id="filtro" class="form-control" placeholder="Filtrar objetos...">
        </div>
      </form>
    </div>

    <form method="POST" action="{{ route('seguridad.permisos.update') }}">
      @csrf
      <input type="hidden" name="rol_id" value="{{ request('rol_id', $roles->first()->COD_ROL) }}">

      <div class="table-responsive">
        <table class="table table-sm table-hover mb-0" id="tablaPermisos">
          <thead class="thead-light">
            <tr>
              <th>Objeto</th>
              <th class="text-center" style="width:10%">Ver</th>
              <th class="text-center" style="width:10%">Crear</th>
              <th class="text-center" style="width:10%">Editar</th>
              <th class="text-center" style="width:10%">Eliminar</th>
            </tr>
          </thead>
          <tbody>
            @php $rolId = (int) request('rol_id', $roles->first()->COD_ROL); @endphp
            @foreach($objetos as $o)
              @php $p = optional(optional($permisos->get($rolId))->get($o->COD_OBJETO)); @endphp
              <tr>
                <td class="font-weight-600">{{ $o->NOM_OBJETO }}</td>

                @foreach (['VER','CREAR','EDITAR','ELIMINAR'] as $flag)
                  @php
                    $checked = $p && $p->$flag ? 'checked' : '';
                    $name = "permisos[{$o->COD_OBJETO}][$flag]";
                  @endphp
                  <td class="text-center">
                    <div class="custom-control custom-switch">
                      <input type="checkbox" class="custom-control-input" id="sw_{{ $o->COD_OBJETO }}_{{ $flag }}" name="{{ $name }}" {{ $checked }}>
                      <label class="custom-control-label" for="sw_{{ $o->COD_OBJETO }}_{{ $flag }}"></label>
                    </div>
                  </td>
                @endforeach

              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="card-footer d-flex justify-content-end">
        <button class="btn btn-primary">
          <i class="fas fa-save mr-1"></i> Guardar cambios
        </button>
      </div>
    </form>
  </div>
@endsection

@push('js')
<script>
  // Filtro de objetos en la tabla
  document.getElementById('filtro').addEventListener('input', function(){
    const q = this.value.toLowerCase();
    document.querySelectorAll('#tablaPermisos tbody tr').forEach(tr=>{
      tr.style.display = tr.firstElementChild.innerText.toLowerCase().includes(q) ? '' : 'none';
    });
  });
</script>
@endpush
