@extends('adminlte::page')
@section('title','Calendario')

@push('css')
  {{-- FullCalendar CSS correcto --}}
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.min.css" rel="stylesheet">
  <style>
    /* panel izquierdo tipo AdminLTE */
    .fc-sidebar {min-width: 300px}
    .external-event {padding:6px 10px; margin:6px 0; border-radius:6px; color:#fff; cursor:move}
    .color-swatch {width:28px;height:28px;border-radius:6px;display:inline-block;margin-right:6px;cursor:pointer;border:1px solid #ddd}
    .color-swatch.active {outline:2px solid #0003}
  </style>
@endpush

@section('content_header')
  <div class="d-flex align-items-center justify-content-between">
    <h1 class="m-0">Calendario</h1>
    @can('create', App\Models\Cita::class)
      <button class="btn btn-primary" data-toggle="modal" data-target="#modalCrear">
        <i class="fas fa-plus"></i> Nueva cita
      </button>
    @endcan
  </div>
@endsection

@section('content')
<div class="row">
  {{-- Sidebar como en el demo de AdminLTE --}}
  <div class="col-md-4 col-lg-3">
    <div class="card fc-sidebar">
      <div class="card-header"><strong>Eventos arrastrables</strong></div>
      <div class="card-body">
        @can('create', App\Models\Cita::class)
          <div id="external-events">
            <div class="external-event" data-title="Consulta"    style="background:#28a745">Consulta</div>
            <div class="external-event" data-title="Control"     style="background:#007bff">Control</div>
            <div class="external-event" data-title="Limpieza"    style="background:#17a2b8">Limpieza</div>
            <div class="external-event" data-title="Urgencia"    style="background:#dc3545">Urgencia</div>
            <div class="external-event" data-title="Evaluación"  style="background:#fd7e14">Evaluación</div>
          </div>
          <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" id="remove-after-drop">
            <label class="form-check-label" for="remove-after-drop">remover después de soltar</label>
          </div>
        @else
          <p class="text-muted mb-0">Sin permisos para crear.</p>
        @endcan
      </div>
    </div>

    <div class="card">
      <div class="card-header"><strong>Crear rápido</strong></div>
      <div class="card-body">
        @can('create', App\Models\Cita::class)
          <div class="mb-2">
            <span class="color-swatch active" data-color="#3788d8" style="background:#3788d8"></span>
            <span class="color-swatch"        data-color="#28a745" style="background:#28a745"></span>
            <span class="color-swatch"        data-color="#17a2b8" style="background:#17a2b8"></span>
            <span class="color-swatch"        data-color="#ffc107" style="background:#ffc107"></span>
            <span class="color-swatch"        data-color="#dc3545" style="background:#dc3545"></span>
          </div>
          <div class="input-group">
            <input id="quick-title" class="form-control" placeholder="Título/Motivo">
            <div class="input-group-append">
              <button id="quick-add" class="btn btn-primary">Add</button>
            </div>
          </div>
          <small class="text-muted d-block mt-2">Haz clic en un día para colocar el evento rápido.</small>
        @else
          <p class="text-muted mb-0">Sin permisos para crear.</p>
        @endcan
      </div>
    </div>
  </div>

  {{-- Calendario --}}
  <div class="col-md-8 col-lg-9">
    <div class="card">
      <div class="card-body">
        <div id="calendar"></div>
      </div>
    </div>
  </div>
</div>

{{-- Modal Crear (completo) --}}
<div class="modal fade" id="modalCrear" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" id="form-crear">
      <div class="modal-header">
        <h5 class="modal-title">Nueva cita</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="start" id="crear-start">
        <div class="form-group">
          <label>Paciente (COD_PERSONA)</label>
          <input class="form-control" name="FK_COD_PACIENTE" required placeholder="Ej. 7">
        </div>
        <div class="form-group">
          <label>Doctor (COD_PERSONA)</label>
          <input class="form-control" name="FK_COD_DOCTOR" required placeholder="Ej. 5">
        </div>
        <div class="form-group">
          <label>Motivo</label>
          <input class="form-control" name="MOT_CITA" maxlength="255">
        </div>
        <div class="form-group">
          <label>Estado</label>
          <select class="form-control" name="ESTADO_CITA">
            @foreach($estados as $id => $nom)
              <option value="{{ $id }}">{{ $nom }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary">Guardar</button>
      </div>
    </form>
  </div>
</div>

{{-- Modal Editar --}}
<div class="modal fade" id="modalEditar" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" id="form-editar">
      <div class="modal-header">
        <h5 class="modal-title">Editar cita</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="edit-id">
        <input type="hidden" name="start" id="edit-start">
        <div class="form-group">
          <label>Motivo</label>
          <input class="form-control" name="MOT_CITA" id="edit-motivo" maxlength="255">
        </div>
        <div class="form-group">
          <label>Estado</label>
          <select class="form-control" name="ESTADO_CITA" id="edit-estado">
            @foreach($estados as $id => $nom)
              <option value="{{ $id }}">{{ $nom }}</option>
            @endforeach
          </select>
        </div>
        <small class="text-muted d-block">*También puedes arrastrar el evento para reprogramar.</small>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <button class="btn btn-primary">Guardar</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('js')
  {{-- FullCalendar JS (bundle global) --}}
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      const DEFAULT_ESTADO = Number({{ array_key_first($estados) ?? 1 }}) || 1;

      // ==== colores quick-add ====
      let currentColor = '#3788d8';
      document.querySelectorAll('.color-swatch').forEach(sw => {
        sw.addEventListener('click', () => {
          document.querySelectorAll('.color-swatch').forEach(s => s.classList.remove('active'));
          sw.classList.add('active');
          currentColor = sw.dataset.color;
        });
      });

      // ==== hacer arrastrables los "external events" ====
      @can('create', App\Models\Cita::class)
      const containerEl = document.getElementById('external-events');
      if (containerEl && window.FullCalendar && FullCalendar.Draggable) {
        new FullCalendar.Draggable(containerEl, {
          itemSelector: '.external-event',
          eventData: function(el) {
            return {
              title: el.getAttribute('data-title'),
              backgroundColor: el.style.backgroundColor,
              borderColor: el.style.backgroundColor
            };
          }
        });
      }
      @endcan

      const calendarEl = document.getElementById('calendar');
      const calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'es',
        initialView: 'dayGridMonth',
        height: 'auto',
        editable: true,         // drag & drop
        droppable: @can('create', App\Models\Cita::class) true @else false @endcan, // drop externo
        selectable: @can('create', App\Models\Cita::class) true @else false @endcan,
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        events: {
          url: '{{ route('citas.events') }}',
          failure(res) {
            console.error('Fallo cargando eventos', res);
            alert('No se pudieron cargar los eventos');
          }
        },
        dateClick(info) {
          @can('create', App\Models\Cita::class)
            // quick-add si hay título, si no abre modal completo
            const titleEl = document.getElementById('quick-title');
            const title = (titleEl?.value || '').trim();
            if (title) {
              createEventQuick(info.dateStr + 'T09:00:00', title, currentColor);
            } else {
              document.getElementById('crear-start').value = info.dateStr + 'T09:00:00';
              $('#modalCrear').modal('show');
            }
          @endcan
        },
        drop(info) { // desde external events
          @can('create', App\Models\Cita::class)
            const title = info.draggedEl.getAttribute('data-title');
            const bg = info.draggedEl.style.backgroundColor;
            createEventQuick(info.dateStr + 'T09:00:00', title, bg, () => {
              if (document.getElementById('remove-after-drop')?.checked) {
                info.draggedEl.parentNode.remove();
              }
            });
          @endcan
        },
        eventClick(info) {
          const e = info.event;
          document.getElementById('edit-id').value = e.id;
          document.getElementById('edit-start').value = e.startStr;
          document.getElementById('edit-motivo').value = e.extendedProps.motivo || '';
          const map = @json($estados); // {id:'NOMBRE'}
          const found = Object.entries(map).find(([id, nom]) => nom === (e.extendedProps.estado || '').toUpperCase());
          document.getElementById('edit-estado').value = found ? found[0] : '';
          $('#modalEditar').modal('show');
        },
        eventDrop(info) {
          fetch(`{{ url('/citas/calendario/event') }}/${info.event.id}`, {
            method: 'PATCH',
            headers: {'Content-Type': 'application/json','X-CSRF-TOKEN': csrf},
            body: JSON.stringify({ start: info.event.start.toISOString() })
          }).then(r => { if (!r.ok) throw 0; })
            .catch(() => { alert('No se pudo reprogramar.'); info.revert(); });
        }
      });

      calendar.render();

      // quick add desde sidebar color + input
      document.getElementById('quick-add')?.addEventListener('click', () => {
        const sel = calendar.getDate(); // fecha visible
        const title = (document.getElementById('quick-title')?.value || '').trim();
        if (!title) return;
        const yyyy = sel.getFullYear(), mm = String(sel.getMonth()+1).padStart(2,'0'), dd = '01';
        createEventQuick(`${yyyy}-${mm}-${dd}T09:00:00`, title, currentColor);
      });

      function createEventQuick(startISO, title, color, after) {
        const payload = {
          start: startISO,        // el backend acepta "start"
          startISO: startISO,     // y también "startISO" (compat)
          MOT_CITA: title,
          ESTADO_CITA: DEFAULT_ESTADO
          // Si quieres forzar doctor/paciente por rol, se puede completar aquí más adelante
        };
        fetch(`{{ route('citas.calendar.create') }}`, {
          method: 'POST',
          headers: {'Content-Type': 'application/json','X-CSRF-TOKEN': csrf},
          body: JSON.stringify(payload)
        }).then(async r => {
          if (!r.ok) throw new Error(await r.text());
          const titleEl = document.getElementById('quick-title');
          if (titleEl) titleEl.value = '';
          calendar.refetchEvents();
          after && after();
        }).catch(err => {
          console.error(err);
          alert('Error creando la cita');
        });
      }

      // Modal Crear (completo)
      document.getElementById('form-crear')?.addEventListener('submit', function (e) {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(this).entries());
        fetch(`{{ route('citas.calendar.create') }}`, {
          method: 'POST',
          headers: {'Content-Type': 'application/json','X-CSRF-TOKEN': csrf},
          body: JSON.stringify(data)
        }).then(async r => {
          if (!r.ok) throw new Error(await r.text());
          $('#modalCrear').modal('hide');
          calendar.refetchEvents();
        }).catch(err => {
          console.error(err);
          alert('Error creando la cita');
        });
      });

      // Modal Editar
      document.getElementById('form-editar')?.addEventListener('submit', function (e) {
        e.preventDefault();
        const id = document.getElementById('edit-id').value;
        const data = Object.fromEntries(new FormData(this).entries());
        fetch(`{{ url('/citas/calendario/event') }}/${id}`, {
          method: 'PATCH',
          headers: {'Content-Type': 'application/json','X-CSRF-TOKEN': csrf},
          body: JSON.stringify(data)
        }).then(async r => {
          if (!r.ok) throw new Error(await r.text());
          $('#modalEditar').modal('hide');
          calendar.refetchEvents();
        }).catch(err => {
          console.error(err);
          alert('Error actualizando la cita');
        });
      });
    });
  </script>
@endpush
