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
  {{-- Fallbacks para que nunca truene si el controlador no inyecta alguna variable --}}
  @php
      $roles           = $roles           ?? collect();
      $objetos         = $objetos         ?? collect();
      $permisos        = $permisos        ?? collect();
      $selectedRoleId  = isset($selectedRoleId) ? (int)$selectedRoleId
                          : (int) request('rol_id', optional($roles->first())->COD_ROL);
  @endphp

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
            <option value="{{ $r->COD_ROL }}" {{ (int)$selectedRoleId === (int)$r->COD_ROL ? 'selected' : '' }}>
              {{ $r->NOM_ROL }}
            </option>
          @endforeach
        </select>

        <div class="input-group ml-auto" style="max-width:320px;">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
          </div>
          <input id="filtro" class="form-control" placeholder="Filtrar objetos...">
        </div>
      </form>
    </div>

    <form method="POST" action="{{ route('seguridad.permisos.update') }}">
      @csrf
      <input type="hidden" name="rol_id" value="{{ $selectedRoleId }}">

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
            @foreach($objetos as $o)
              @php
                // $permisos es un mapa: [rolId] => [objId] => registro con columnas VER/CREAR/EDITAR/ELIMINAR
                $p = optional(optional($permisos->get((int)$selectedRoleId))->get($o->COD_OBJETO));
              @endphp

              <tr>
                <td class="font-weight-600">{{ $o->NOM_OBJETO }}</td>

                @foreach (['VER','CREAR','EDITAR','ELIMINAR'] as $flag)
                  @php
                    $isOn   = $p && (int)($p->$flag ?? 0) === 1;
                    $name   = "permisos[{$o->COD_OBJETO}][$flag]";
                    $idSw   = "sw_{$o->COD_OBJETO}_{$flag}";
                  @endphp
                  <td class="text-center">
                    {{-- IMPORTANTE: el hidden manda "0" cuando el switch está apagado --}}
                    <input type="hidden" name="{{ $name }}" value="0">
                    <div class="custom-control custom-switch">
                      <input
                        type="checkbox"
                        class="custom-control-input"
                        id="{{ $idSw }}"
                        name="{{ $name }}"
                        value="1"
                        {{ $isOn ? 'checked' : '' }}
                      >
                      <label class="custom-control-label" for="{{ $idSw }}"></label>
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
  (function () {
    var input = document.getElementById('filtro');
    if (!input) return;
    input.addEventListener('input', function(){
      var q = (this.value || '').toLowerCase();
      document.querySelectorAll('#tablaPermisos tbody tr').forEach(function(tr){
        var txt = tr.firstElementChild ? tr.firstElementChild.innerText.toLowerCase() : '';
        tr.style.display = txt.indexOf(q) !== -1 ? '' : 'none';
      });
    });
  })();
</script>
@endpush
