<form class="card shadow-sm border-0 mb-4" method="GET" action="{{ $action ?? request()->url() }}">
    <div class="card-body">
        <div class="row g-3 align-items-end">
            <div class="col-12 col-md-4 col-lg-3">
                <label class="form-label text-muted text-uppercase small fw-semibold">Desde</label>
                <input type="date" name="desde" value="{{ request('desde') }}" class="form-control">
            </div>
            <div class="col-12 col-md-4 col-lg-3">
                <label class="form-label text-muted text-uppercase small fw-semibold">Hasta</label>
                <input type="date" name="hasta" value="{{ request('hasta') }}" class="form-control">
            </div>
            <div class="col-12 col-md-4 col-lg-3">
                <label class="form-label text-muted text-uppercase small fw-semibold">Estado</label>
                <select name="estado" class="form-select">
                    <option value="">Todos</option>
                    @foreach(($filters['estados'] ?? []) as $estado)
                        <option value="{{ $estado }}" @selected(request('estado') === $estado)>{{ $estado }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-4 col-lg-3">
                <label class="form-label text-muted text-uppercase small fw-semibold">Doctor</label>
                <select name="doctor" class="form-select">
                    <option value="">Todos</option>
                    @foreach(($filters['doctores'] ?? []) as $doctor)
                        <option value="{{ $doctor }}" @selected(request('doctor') === $doctor)>{{ $doctor }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-4 col-lg-3">
                <label class="form-label text-muted text-uppercase small fw-semibold">Paciente</label>
                <input type="text" name="paciente" value="{{ request('paciente') }}" placeholder="Nombre o código" class="form-control">
            </div>
            <div class="col-12 col-md-4 col-lg-3">
                <label class="form-label text-muted text-uppercase small fw-semibold">Acción</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                        Filtrar
                    </button>
                    <a href="{{ $action ?? request()->url() }}" class="btn btn-outline-secondary">
                        Limpiar
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
