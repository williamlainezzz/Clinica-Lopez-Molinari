@once
    @push('js')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('.js-event-pill').forEach(pill => {
                    pill.addEventListener('click', () => {
                        const payload = pill.dataset.event ? JSON.parse(pill.dataset.event) : null;
                        if (!payload) return;
                        const modal = document.getElementById('modalEventoAgenda');
                        if (!modal) return;
                        modal.querySelector('[data-field="titulo"]').textContent = `${payload.paciente} · ${payload.hora}`;
                        modal.querySelector('[data-field="doctor"]').textContent = payload.doctor;
                        modal.querySelector('[data-field="estado"]').textContent = payload.estado;
                        modal.querySelector('[data-field="motivo"]').textContent = payload.motivo;
                        modal.querySelector('[data-field="fecha"]').textContent = payload.fecha;
                        modal.querySelector('[data-field="hora"]').textContent = payload.hora;
                        modal.querySelector('[data-field="ubicacion"]').textContent = payload.ubicacion ?? 'Consultorio';
                    });
                });
            });
        </script>
    @endpush
@endonce
