@php
    $calendarId = $calendarId ?? 'agenda-calendar';
    $modalId    = $modalId ?? 'modalCitaDetalle';
@endphp

@once
    @push('css')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/main.min.css">
    @endpush

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/index.global.min.js"></script>
    @endpush
@endonce

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div id="{{ $calendarId }}" class="p-3"></div>
    </div>
    @if(!empty($legend))
        <div class="card-footer bg-white border-0">
            <div class="d-flex flex-wrap gap-3 align-items-center">
                <span class="text-muted text-uppercase small fw-semibold">Estados</span>
                @foreach($legend as $item)
                    <span class="badge rounded-pill" style="background-color: {{ $item['color'] }};">
                        {{ $item['label'] }}
                    </span>
                @endforeach
            </div>
        </div>
    @endif
</div>

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarElement = document.getElementById('{{ $calendarId }}');
            if (!calendarElement || typeof FullCalendar === 'undefined') {
                return;
            }

            const events = @json($events ?? []);

            const calendar = new FullCalendar.Calendar(calendarElement, {
                initialView: 'timeGridWeek',
                locale: 'es',
                height: 'auto',
                slotMinTime: '07:00:00',
                slotMaxTime: '19:00:00',
                nowIndicator: true,
                selectable: true,
                headerToolbar: {
                    start: 'title',
                    center: '',
                    end: 'prev,today,next,dayGridMonth,timeGridWeek,timeGridDay'
                },
                events,
                eventClick: function (info) {
                    const modalElement = document.getElementById('{{ $modalId }}');
                    if (!modalElement) {
                        return;
                    }

                    const data = info.event.extendedProps || {};
                    const start = info.event.start;

                    const formatOptions = { year: 'numeric', month: 'long', day: 'numeric' };
                    const fecha = start ? start.toLocaleDateString('es-HN', formatOptions) : data.fecha;

                    modalElement.querySelector('[data-field="fecha"]').textContent = fecha || '-';
                    modalElement.querySelector('[data-field="hora"]').textContent = data.hora || info.event.startStr?.substring(11, 16) || '-';
                    modalElement.querySelector('[data-field="paciente"]').textContent = data.paciente || info.event.title || '-';
                    modalElement.querySelector('[data-field="doctor"]').textContent = data.doctor || '-';
                    modalElement.querySelector('[data-field="motivo"]').textContent = data.motivo || '-';
                    modalElement.querySelector('[data-field="estado"]').textContent = data.estado || '-';
                    modalElement.querySelector('[data-field="ubicacion"]').textContent = data.ubicacion || '-';
                    modalElement.querySelector('[data-field="nota"]').textContent = data.nota || 'Sin observaciones';

                    if (typeof bootstrap !== 'undefined') {
                        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
                        modal.show();
                    }
                }
            });

            calendar.render();
        });
    </script>
@endpush
