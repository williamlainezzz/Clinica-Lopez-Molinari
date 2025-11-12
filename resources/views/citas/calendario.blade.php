@extends('adminlte::page')
@section('title','Agenda')

@push('css')
  {{-- FullCalendar CSS (CDN) --}}
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
@endpush

@section('content_header')
  <div class="d-flex align-items-center justify-content-between">
    <h1 class="m-0">Agenda</h1>
    @can('create', App\Models\Cita::class)
      <button class="btn btn-primary" data-toggle="modal" data-target="#modalCrear">
        <i class="fas fa-plus"></i> Nueva cita
      </button>
    @endcan
  </div>
@endsection

@section('content')
<div class="card">
  <div class="card-body">
    <div id="calendar"></div>
  </div>
</div>

{{-- Modal Crear --}}
<div class="modal fade" id="modalCrear" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" id="form-crear">
      <div class="modal-header">
        <h5 class="modal-title">Crear cita</h5>
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
          <input class="form-control" name="MOT_CITA" maxlength="255" placeholder="Motivo de la cita">
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
        <small class="text-muted d-block">
          *Para reprogramar también puedes arrastrar el evento en el calendario.
        </small>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <button class="btn btn-primary">Guardar cambios</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('js')
  {{-- FullCalendar (global build) --}}
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

      const calendarEl = document.getElementById('calendar');
      const calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'es',
        initialView: 'dayGridMonth',
        height: 'auto',
        selectable: @can('create', App\Models\Cita::class) true @else false @endcan,
        editable: true, // permite arrastrar eventos
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        events: {
          url: '{{ route('citas.events') }}',
          failure() { alert('No se pudieron cargar los eventos'); }
        },
        dateClick(info) {
          // Pre-cargar fecha/hora (09:00 por defecto)
          const start = info.dateStr.length === 10 ? info.dateStr + 'T09:00:00' : info.dateStr;
          document.getElementById('crear-start').value = start;
          $('#modalCrear').modal('show');
        },
        eventClick(info) {
          const e = info.event;
          document.getElementById('edit-id').value = e.id;
          document.getElementById('edit-start').value = e.startStr;
          document.getElementById('edit-motivo').value = e.extendedProps.motivo || '';
          const estadoSel = document.getElementById('edit-estado');
          estadoSel.value = Object.entries(@json($estados))
            .find(([id, nom]) => nom === (e.extendedProps.estado || '').toUpperCase())?.[0] ?? '';
          $('#modalEditar').modal('show');
        },
        eventDrop(info) {
          // Reprogramación por drag & drop
          fetch(`{{ url('/citas/calendario/event') }}/${info.event.id}`, {
            method: 'PATCH',
            headers: {'Content-Type': 'application/json','X-CSRF-TOKEN': csrf},
            body: JSON.stringify({ start: info.event.start.toISOString() })
          }).then(r => {
            if (!r.ok) throw new Error();
          }).catch(() => {
            alert('No se pudo reprogramar.');
            info.revert();
          });
        }
      });

      calendar.render();

      // Crear
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
        }).catch(() => alert('Error creando la cita'));
      });

      // Editar
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
        }).catch(() => alert('Error actualizando la cita'));
      });
    });
  </script>
@endpush
