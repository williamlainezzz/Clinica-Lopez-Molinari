<form method="GET" action="{{ route($routeName) }}" class="mb-3">
  <div class="row g-2">
    <div class="col-sm-3">
      <label class="form-label">Desde</label>
      <input type="date" name="desde" value="{{ $filters['desde'] ?? '' }}" class="form-control">
    </div>
    <div class="col-sm-3">
      <label class="form-label">Hasta</label>
      <input type="date" name="hasta" value="{{ $filters['hasta'] ?? '' }}" class="form-control">
    </div>
    <div class="col-sm-3">
      <label class="form-label">Estado</label>
      <select name="estado" class="form-control">
        <option value="">Todos</option>
        @foreach (['Confirmada','Pendiente','Cancelada'] as $opt)
          <option value="{{ $opt }}" {{ ($filters['estado'] ?? '') === $opt ? 'selected' : '' }}>
            {{ $opt }}
          </option>
        @endforeach
      </select>
    </div>
    <div class="col-sm-3">
      <label class="form-label">Doctor</label>
      <select name="doctor" class="form-control">
        <option value="">Todos</option>
        @foreach (['Dr. López','Dra. Molina'] as $opt)
          <option value="{{ $opt }}" {{ ($filters['doctor'] ?? '') === $opt ? 'selected' : '' }}>
            {{ $opt }}
          </option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="mt-3">
    <button class="btn btn-primary">
      <i class="fas fa-filter"></i> Filtrar
    </button>

    <a href="{{ route($routeName) }}" class="btn btn-secondary">
      Limpiar
    </a>
  </div>
</form>
