<div class="modal fade" id="helpCenterModal" tabindex="-1" role="dialog" aria-labelledby="helpCenterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-white">
                <h5 class="modal-title font-weight-bold" id="helpCenterModalLabel">
                    <i class="fas fa-life-ring text-primary mr-2"></i>
                    Centro de Ayuda
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <p class="text-muted mb-3">
                    Accede a la documentación disponible para tu perfil dentro del sistema.
                </p>

                <div class="list-group list-group-flush">
                    <a
                        href="https://docs.google.com/document/d/1Ee8XcnWBRsZRncl9hAf7niF3LvdMzggA/edit?usp=sharing&ouid=117234764860920015272&rtpof=true&sd=true"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="list-group-item list-group-item-action d-flex align-items-center justify-content-between"
                    >
                        <span>
                            <i class="fas fa-book text-primary mr-2"></i>
                            Manual de usuario
                        </span>
                        <i class="fas fa-external-link-alt text-muted"></i>
                    </a>

                    @can('centro-ayuda.manual-tecnico.ver')
                        <a
                            href="https://docs.google.com/document/d/1xHSB0HH6QedYl0G37JETnUxzYfcHxpBW/edit?usp=sharing&ouid=117234764860920015272&rtpof=true&sd=true"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="list-group-item list-group-item-action d-flex align-items-center justify-content-between"
                        >
                            <span>
                                <i class="fas fa-tools text-info mr-2"></i>
                                Manual técnico
                            </span>
                            <i class="fas fa-external-link-alt text-muted"></i>
                        </a>
                    @endcan
                </div>
            </div>

            <div class="modal-footer bg-light justify-content-between">
                <small class="text-muted">
                    Los manuales se abrirán en una pestaña nueva.
                </small>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
