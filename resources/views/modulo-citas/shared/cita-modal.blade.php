@php
    $modalId    = $modalId ?? 'modalCitaDetalle';
    $readonly   = $readonly ?? false;
    $showCancel = $showCancel ?? !$readonly;
    $showConfirm= $showConfirm ?? !$readonly;
@endphp

<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <div>
                    <h5 class="modal-title fw-bold">Detalle de la cita</h5>
                    <p class="mb-0 text-muted small">Consulta la información completa antes de confirmar acciones.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <dl class="row mb-0">
                            <dt class="col-sm-4 text-muted small text-uppercase">Fecha</dt>
                            <dd class="col-sm-8" data-field="fecha">-</dd>
                            <dt class="col-sm-4 text-muted small text-uppercase">Hora</dt>
                            <dd class="col-sm-8" data-field="hora">-</dd>
                            <dt class="col-sm-4 text-muted small text-uppercase">Paciente</dt>
                            <dd class="col-sm-8" data-field="paciente">-</dd>
                            <dt class="col-sm-4 text-muted small text-uppercase">Doctor</dt>
                            <dd class="col-sm-8" data-field="doctor">-</dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl class="row mb-0">
                            <dt class="col-sm-4 text-muted small text-uppercase">Motivo</dt>
                            <dd class="col-sm-8" data-field="motivo">-</dd>
                            <dt class="col-sm-4 text-muted small text-uppercase">Estado</dt>
                            <dd class="col-sm-8" data-field="estado">-</dd>
                            <dt class="col-sm-4 text-muted small text-uppercase">Ubicación</dt>
                            <dd class="col-sm-8" data-field="ubicacion">-</dd>
                            <dt class="col-sm-4 text-muted small text-uppercase">Notas</dt>
                            <dd class="col-sm-8" data-field="nota">-</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                @if($showConfirm)
                    <button type="button" class="btn btn-primary">
                        <i class="fas fa-pen"></i>
                        Editar cita
                    </button>
                @endif
                @if($showCancel)
                    <button type="button" class="btn btn-outline-danger">
                        <i class="fas fa-times"></i>
                        Cancelar cita
                    </button>
                @endif
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>
        document.addEventListener('click', function (event) {
            const trigger = event.target.closest('[data-cita-target="{{ $modalId }}"]');
            if (!trigger) {
                return;
            }

            event.preventDefault();
            const raw = trigger.getAttribute('data-cita');
            if (!raw) {
                return;
            }

            let data;
            try {
                data = JSON.parse(raw);
            } catch (error) {
                return;
            }

            const modalElement = document.getElementById('{{ $modalId }}');
            if (!modalElement) {
                return;
            }

            const fechaField = modalElement.querySelector('[data-field="fecha"]');
            const horaField = modalElement.querySelector('[data-field="hora"]');
            const pacienteField = modalElement.querySelector('[data-field="paciente"]');
            const doctorField = modalElement.querySelector('[data-field="doctor"]');
            const motivoField = modalElement.querySelector('[data-field="motivo"]');
            const estadoField = modalElement.querySelector('[data-field="estado"]');
            const ubicacionField = modalElement.querySelector('[data-field="ubicacion"]');
            const notaField = modalElement.querySelector('[data-field="nota"]');

            const fecha = data.fecha ? new Date(data.fecha + 'T' + (data.hora ?? '00:00')).toLocaleDateString('es-HN', { year: 'numeric', month: 'long', day: 'numeric' }) : '-';

            if (fechaField) fechaField.textContent = fecha;
            if (horaField) horaField.textContent = data.hora ?? '-';
            if (pacienteField) pacienteField.textContent = data.paciente ?? '-';
            if (doctorField) doctorField.textContent = data.doctor ?? '-';
            if (motivoField) motivoField.textContent = data.motivo ?? '-';
            if (estadoField) estadoField.textContent = data.estado ?? '-';
            if (ubicacionField) ubicacionField.textContent = data.ubicacion ?? '-';
            if (notaField) notaField.textContent = data.nota ?? 'Sin observaciones';

            if (typeof bootstrap !== 'undefined') {
                const modalInstance = bootstrap.Modal.getOrCreateInstance(modalElement);
                modalInstance.show();
            }
        });
    </script>
@endpush
