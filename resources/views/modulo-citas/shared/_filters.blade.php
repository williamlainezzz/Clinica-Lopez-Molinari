<form class="mb-3" method="GET" action="#">
  <div class="row">
    <div class="col-md-3">
      <label class="form-label">Desde</label>
      <input type="date" name="desde" class="form-control" />
    </div>
    <div class="col-md-3">
      <label class="form-label">Hasta</label>
      <input type="date" name="hasta" class="form-control" />
    </div>
    <div class="col-md-3">
      <label class="form-label">Estado</label>
      <select name="estado" class="form-control">
        <option value="">Todos</option>
        <option>Confirmada</option>
        <option>Pendiente</option>
        <option>Cancelada</option>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label">Doctor</label>
      <select name="doctor" class="form-control">
        <option value="">Todos</option>
        <option>Dr. López</option>
        <option>Dra. Molina</option>
      </select>
    </div>
  </div>
  <div class="mt-3 d-flex gap-2">
    <button class="btn btn-primary"><i class="fas fa-search me-1"></i> Filtrar</button>
    <a href="#" class="btn btn-outline-secondary">Limpiar</a>
  </div>
</form>
