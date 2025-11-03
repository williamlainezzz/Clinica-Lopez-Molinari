@extends('adminlte::page')

@section('title', $pageTitle ?? 'Agenda')

@section('content_header')
    <h1>{{ $heading ?? 'Agenda' }}</h1>
@endsection

@section('content')

    {{-- Banner por rol/section --}}
    @includeIf($bannerPartial)

    <div class="card">
        <div class="card-header">
            {{-- Toolbar por rol/section --}}
            <div class="mb-3">
                @includeIf($toolbarPartial)
            </div>

            {{-- Filtros (GET) --}}
            <form id="filtrosForm" method="GET" action="{{ route($routeName) }}">
                <div class="row">
                    <div class="col-md-3">
                        <label class="mb-1">Desde</label>
                        <input type="date" class="form-control"
                               name="desde" value="{{ $filters['desde'] }}">
                    </div>
                    <div class="col-md-3">
                        <label class="mb-1">Hasta</label>
                        <input type="date" class="form-control"
                               name="hasta" value="{{ $filters['hasta'] }}">
                    </div>
                    <div class="col-md-3">
                        <label class="mb-1">Estado</label>
                        <select class="form-control" name="estado">
                            <option value="">Todos</option>
                            @foreach(($catalogoEstados ?? []) as $est)
                                <option value="{{ $est }}" {{ (isset($filters['estado']) && $filters['estado']===$est) ? 'selected' : '' }}>
                                    {{ $est }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="mb-1">Doctor</label>
                        <select class="form-control" name="doctor">
                            <option value="">Todos</option>
                            @foreach(($catalogoDoctores ?? []) as $doc)
                                <option value="{{ $doc }}" {{ (isset($filters['doctor']) && $filters['doctor']===$doc) ? 'selected' : '' }}>
                                    {{ $doc }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-3">
                    <button class="btn btn-primary">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="{{ route($routeName) }}" class="btn btn-secondary">
                        Limpiar
                    </a>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Paciente</th>
                            @if($showDoctorColumn)
                                <th>Doctor</th>
                            @endif
                            <th>Estado</th>
                            <th>Motivo</th>
                            @if($showActions)
                                <th style="width: 160px;">Acciones</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody id="citasTbody">
                        @forelse($rows as $row)
                            @php
                                $id = $loop->index + 1; // ID temporal (stub)
                                $estado = $row['estado'];
                                $badge = match ($estado) {
                                    'Confirmada' => 'success',
                                    'Pendiente'  => 'warning',
                                    'Cancelada'  => 'danger',
                                    default      => 'secondary',
                                };
                            @endphp
                            <tr>
                                <td>{{ $row['fecha'] }}</td>
                                <td>{{ $row['hora'] }}</td>
                                <td>{{ $row['paciente'] }}</td>

                                @if($showDoctorColumn)
                                    <td>{{ $row['doctor'] }}</td>
                                @endif

                                <td><span class="badge badge-{{ $badge }}">{{ $estado }}</span></td>
                                <td>{{ $row['motivo'] }}</td>

                                @if($showActions)
                                    <td class="text-nowrap">
                                        @switch($rol)
                                            @case('ADMIN')
                                            @case('RECEPCIONISTA')
                                                <a  href="{{ route('citas.show', $id) }}"
                                                    class="btn btn-xs btn-info"
                                                    title="Ver"
                                                    data-toggle="modal" data-target="#modalVer"
                                                    data-id="{{ $id }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <a  href="{{ route('citas.reprogramar.form', $id) }}"
                                                    class="btn btn-xs btn-warning"
                                                    title="Reprogramar"
                                                    data-toggle="modal" data-target="#modalReprogramar"
                                                    data-id="{{ $id }}">
                                                    <i class="fas fa-sync"></i>
                                                </a>

                                                <button type="button"
                                                    class="btn btn-xs btn-danger btn-cancelar"
                                                    title="Cancelar"
                                                    data-toggle="modal" data-target="#modalCancelar"
                                                    data-action="{{ route('citas.cancelar', $id) }}"
                                                    data-id="{{ $id }}">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                @break

                                            @case('DOCTOR')
                                                <a  href="{{ route('citas.show', $id) }}"
                                                    class="btn btn-xs btn-info"
                                                    title="Ver"
                                                    data-toggle="modal" data-target="#modalVer"
                                                    data-id="{{ $id }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <a  href="{{ route('citas.reprogramar.form', $id) }}"
                                                    class="btn btn-xs btn-warning"
                                                    title="Reprogramar"
                                                    data-toggle="modal" data-target="#modalReprogramar"
                                                    data-id="{{ $id }}">
                                                    <i class="fas fa-sync"></i>
                                                </a>

                                                <button type="button"
                                                    class="btn btn-xs btn-danger btn-cancelar"
                                                    title="Cancelar"
                                                    data-toggle="modal" data-target="#modalCancelar"
                                                    data-action="{{ route('citas.cancelar', $id) }}"
                                                    data-id="{{ $id }}">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                @break
                                        @endswitch
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 6 + ($showDoctorColumn?1:0) + ($showActions?1:0) }}" class="text-center text-muted">
                                    Sin resultados con los filtros actuales.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer">
            <small class="text-muted">
                (Demo) Estos datos son de prueba. Conectaremos a la BD en el siguiente bloque.
            </small>
        </div>
    </div>

    {{-- Modal: Ver --}}
    <div class="modal fade" id="modalVer" tabindex="-1" role="dialog" aria-labelledby="modalVerLabel" aria-hidden="true">
      <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
          <div class="modal-header bg-info">
            <h5 class="modal-title" id="modalVerLabel">Detalle de Cita (stub)</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            Cita <span id="ver-cita-id" class="font-weight-bold">#</span> — Aquí mostraremos los datos reales cuando conectemos a la BD.
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>

    {{-- Modal: Reprogramar --}}
    <div class="modal fade" id="modalReprogramar" tabindex="-1" role="dialog" aria-labelledby="modalReprogramarLabel" aria-hidden="true">
      <div class="modal-dialog modal-md" role="document">
        <form id="formReprogramar" method="POST">
          @csrf
          @method('PUT')
          <div class="modal-content">
            <div class="modal-header bg-warning">
              <h5 class="modal-title" id="modalReprogramarLabel">Reprogramar Cita (stub)</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              Cita <span id="repro-cita-id" class="font-weight-bold">#</span>
              <div class="form-group mt-3">
                <label>Nueva fecha</label>
                <input type="date" name="nueva_fecha" class="form-control">
              </div>
              <div class="form-group">
                <label>Nueva hora</label>
                <input type="time" name="nueva_hora" class="form-control">
              </div>
              <small class="text-muted">(Demo) Este formulario aún no guarda en BD.</small>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-warning">Guardar</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
          </div>
        </form>
      </div>
    </div>

    {{-- Modal: Cancelar --}}
    <div class="modal fade" id="modalCancelar" tabindex="-1" role="dialog" aria-labelledby="modalCancelarLabel" aria-hidden="true">
      <div class="modal-dialog modal-sm" role="document">
        <form id="formCancelar" method="POST">
          @csrf
          @method('DELETE')
          <div class="modal-content">
            <div class="modal-header bg-danger">
              <h5 class="modal-title" id="modalCancelarLabel">Cancelar Cita (stub)</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              ¿Seguro que deseas cancelar la cita <span id="cancel-cita-id" class="font-weight-bold">#</span>?
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-danger">Sí, cancelar</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
            </div>
          </div>
        </form>
      </div>
    </div>

@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // VER
    $('#modalVer').on('show.bs.modal', function (event) {
        const btn = $(event.relatedTarget);
        const id  = btn.data('id') || '#';
        $('#ver-cita-id').text('#' + id);
    });

    // REPROGRAMAR
    $('#modalReprogramar').on('show.bs.modal', function (event) {
        const btn = $(event.relatedTarget);
        const id  = btn.data('id') || '#';
        $('#repro-cita-id').text('#' + id);
        // Acción del form (PUT)
        const action = btn.attr('href') || '{{ url('/agenda/citas') }}/' + id + '/reprogramar';
        $('#formReprogramar').attr('action', action);
    });

    // CANCELAR
    $('#modalCancelar').on('show.bs.modal', function (event) {
        const btn = $(event.relatedTarget);
        const id  = btn.data('id') || '#';
        $('#cancel-cita-id').text('#' + id);
        const action = btn.data('action') || '{{ url('/agenda/citas') }}/' + id;
        $('#formCancelar').attr('action', action);
    });
});
</script>
@endpush

@push('js')
<script>
document.addEventListener('DOMContentLoaded', async () => {
    const selEstado = document.querySelector('select[name="estado"]');
    const selDoctor = document.querySelector('select[name="doctor"]');

    if (!selEstado || !selDoctor) return; // por si cambian el markup

    // Leer selección actual desde la URL (para mantener el filtro después del refresh)
    const params = new URLSearchParams(location.search);
    const estadoSel = params.get('estado') || '';
    const doctorSel = params.get('doctor') || '';

    // Helper para limpiar y poner "Todos"
    const resetSelect = (el, labelTodos) => {
        el.dataset.prev = el.value || '';
        el.innerHTML = '';
        const opt = document.createElement('option');
        opt.value = '';
        opt.textContent = labelTodos || 'Todos';
        el.appendChild(opt);
    };

    // Helper para añadir <option>
    const addOption = (el, value, text) => {
        const o = document.createElement('option');
        o.value = value;
        o.textContent = text;
        el.appendChild(o);
    };

    // Cargar ESTADOS
    try {
        resetSelect(selEstado, 'Todos');
        const r = await fetch('/api/agenda/estados', { headers: { 'Accept': 'application/json' } });
        const j = await r.json();
        if (j.ok && Array.isArray(j.data)) {
            j.data.forEach(e => addOption(selEstado, e.id, e.nombre));
            selEstado.value = estadoSel || '';
        } else {
            selEstado.value = selEstado.dataset.prev || '';
        }
    } catch (err) {
        console.warn('No se pudo cargar estados', err);
        selEstado.value = selEstado.dataset.prev || '';
    }

    // Cargar DOCTORES
    try {
        resetSelect(selDoctor, 'Todos');
        const r = await fetch('/api/agenda/doctores', { headers: { 'Accept': 'application/json' } });
        const j = await r.json();
        if (j.ok && Array.isArray(j.data)) {
            j.data.forEach(d => addOption(selDoctor, (d.id ?? d.nombre), d.nombre));
            selDoctor.value = doctorSel || '';
        } else {
            selDoctor.value = selDoctor.dataset.prev || '';
        }
    } catch (err) {
        console.warn('No se pudo cargar doctores', err);
        selDoctor.value = selDoctor.dataset.prev || '';
    }
});
</script>
@endpush

{{-- NUEVO: carga de tabla desde la API con filtros --}}
@push('js')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form   = document.getElementById('filtrosForm');
    const tbody  = document.getElementById('citasTbody');

    const SHOW_DOCTOR = {{ $showDoctorColumn ? 'true' : 'false' }};
    const SHOW_ACTION = {{ $showActions ? 'true' : 'false' }};
    const ROL         = @json($rol);

    const estadoBadge = (estado) => {
        switch (estado) {
            case 'Confirmada': return 'success';
            case 'Pendiente':  return 'warning';
            case 'Cancelada':  return 'danger';
            default:           return 'secondary';
        }
    };

    const qs = () => {
        const p = new URLSearchParams(new FormData(form));
        ['desde','hasta','estado','doctor'].forEach(k => { if (!p.get(k)) p.delete(k); });
        return p.toString();
    };

    const pintarVacio = (msg = 'Sin resultados con los filtros actuales.') => {
        const colspan = 6 + (SHOW_DOCTOR ? 1 : 0) + (SHOW_ACTION ? 1 : 0);
        tbody.innerHTML = `<tr><td colspan="${colspan}" class="text-center text-muted">${msg}</td></tr>`;
    };

    const pintarLoading = () => {
        const colspan = 6 + (SHOW_DOCTOR ? 1 : 0) + (SHOW_ACTION ? 1 : 0);
        tbody.innerHTML = `<tr><td colspan="${colspan}" class="text-center text-muted">
            <i class="fas fa-spinner fa-spin"></i> Cargando...
        </td></tr>`;
    };

    const accionesHTML = (id) => {
        if (!SHOW_ACTION) return '';
        if (ROL === 'ADMIN' || ROL === 'RECEPCIONISTA' || ROL === 'DOCTOR') {
            return `
                <td class="text-nowrap">
                    <a  href="{{ url('agenda/citas') }}/${id}"
                        class="btn btn-xs btn-info"
                        title="Ver"
                        data-toggle="modal" data-target="#modalVer"
                        data-id="${id}">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a  href="{{ url('agenda/citas') }}/${id}/reprogramar"
                        class="btn btn-xs btn-warning"
                        title="Reprogramar"
                        data-toggle="modal" data-target="#modalReprogramar"
                        data-id="${id}">
                        <i class="fas fa-sync"></i>
                    </a>
                    <button type="button"
                        class="btn btn-xs btn-danger btn-cancelar"
                        title="Cancelar"
                        data-toggle="modal" data-target="#modalCancelar"
                        data-action="{{ url('agenda/citas') }}/${id}"
                        data-id="${id}">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            `;
        }
        return SHOW_ACTION ? '<td></td>' : '';
    };

    const filaHTML = (cita, idx) => {
        const id     = cita.id ?? (idx + 1);
        const badge  = estadoBadge(cita.estado);
        return `
            <tr>
                <td>${cita.fecha ?? ''}</td>
                <td>${cita.hora ?? ''}</td>
                <td>${cita.paciente ?? ''}</td>
                ${SHOW_DOCTOR ? `<td>${cita.doctor ?? ''}</td>` : ``}
                <td><span class="badge badge-${badge}">${cita.estado ?? ''}</span></td>
                <td>${cita.motivo ?? ''}</td>
                ${accionesHTML(id)}
            </tr>
        `;
    };

    const cargarCitas = async () => {
        pintarLoading();
        try {
            const url = '/api/agenda/citas' + (qs() ? `?${qs()}` : '');
            const r   = await fetch(url, { headers: { 'Accept': 'application/json' } });
            const j   = await r.json();
            if (!j.ok) throw new Error('Respuesta inválida');

            const data = Array.isArray(j.data) ? j.data : [];
            if (!data.length) return pintarVacio();

            tbody.innerHTML = data.map((c, i) => filaHTML(c, i)).join('');
        } catch (err) {
            console.error(err);
            pintarVacio('No se pudo cargar la lista. Intenta nuevamente.');
        }
    };

    // Carga inicial según los filtros actuales de la URL
    cargarCitas();

    // Intercepta el submit para refrescar con la API sin recargar
    form.addEventListener('submit', (ev) => {
        ev.preventDefault();
        cargarCitas();
        const q = qs();
        const newUrl = q ? (`${location.pathname}?${q}`) : location.pathname;
        window.history.replaceState({}, '', newUrl);
    });
});
</script>
@endpush
