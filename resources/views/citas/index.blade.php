@extends('adminlte::page')
@section('title','Citas')

@section('content_header')
  <div class="d-flex align-items-center justify-content-between">
    <h1 class="m-0">Citas</h1>
    <div>
      <button class="btn btn-outline-secondary mr-2" data-toggle="modal" data-target="#filtroCitas">
        <i class="fas fa-filter"></i> Filtros
      </button>
      @can('create', App\Models\Cita::class)
      <button class="btn btn-primary" data-toggle="modal" data-target="#crearCita">
        <i class="fas fa-plus"></i> Nueva cita
      </button>
      @endcan
    </div>
  </div>
@endsection

@section('content')
{{-- Herramientas de tabla (si tienes el componente) --}}
@includeIf('components.table-tools')

{{-- Exportar CSV conservando filtros actuales --}}
<a href="{{ route('export.citas.csv', request()->query()) }}" class="btn btn-success mb-3">
  Exportar CSV
</a>

<div class="card">
  <div class="card-body table-responsive p-0">
    <table class="table table-hover mb-0">
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Hora</th>
          <th>Paciente</th>
          <th>Doctor</th>
          <th>Estado</th>
          <th>Motivo</th>
          <th class="text-right">Acciones</th>
        </tr>
      </thead>
      <tbody>
      @forelse($citas as $c)
        @php
          $estadoNom = $estados[$c->ESTADO_CITA] ?? '—';
          $badge = match ($estadoNom) {
            'CONFIRMADA' => 'badge-info',
            'PENDIENTE'  => 'badge-warning',
            'CANCELADA'  => 'badge-danger',
            'EN_CURSO'   => 'badge-primary',
            'COMPLETADA' => 'badge-success',
            'NO_SHOW'    => 'badge-dark',
            default      => 'badge-secondary',
          };
        @endphp
        <tr>
          <td>{{ $c->FEC_CITA }}</td>
          <td>{{ $c->HOR_CITA }}</td>
          <td>{{ $c->paciente->PRIMER_NOMBRE ?? '' }} {{ $c->paciente->PRIMER_APELLIDO ?? '' }}</td>
          <td>{{ $c->doctor->PRIMER_NOMBRE ?? '' }} {{ $c->doctor->PRIMER_APELLIDO ?? '' }}</td>
          <td><span class="badge {{ $badge }}">{{ $estadoNom }}</span></td>
          <td>{{ $c->MOT_CITA }}</td>
          <td class="text-right">
            <button class="btn btn-sm btn-outline-info js-ver" data-id="{{ $c->COD_CITA }}">
              <i class="fas fa-eye"></i>
            </button>
            @can('update', $c)
            <button class="btn btn-sm btn-outline-primary js-editar" data-id="{{ $c->COD_CITA }}">
              <i class="fas fa-edit"></i>
            </button>
            @endcan
            @can('delete', $c)
            <form action="#" class="d-inline" onsubmit="return false;">
              <button class="btn btn-sm btn-outline-danger js-eliminar" data-id="{{ $c->COD_CITA }}">
                <i class="fas fa-times"></i>
              </button>
            </form>
            @endcan
          </td>
        </tr>
      @empty
        <tr><td colspan="7" class="text-center text-muted p-4">Sin resultados</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer pb-0">
    {{ $citas->withQueryString()->links() }}
  </div>
</div>

{{-- Tus MODALES existentes pueden quedarse. Aquí solo un hook mínimo para "ver". --}}
@push('js')
<script>
document.addEventListener('click', async (e) => {
  const ver = e.target.closest('.js-ver');
  if (ver) {
    const id = ver.dataset.id;
    const res = await fetch(`{{ url('/citas') }}/${id}`);
    if (!res.ok) return alert('No se pudo cargar la cita');
    const data = await res.json();
    // TODO: Rellena tu modal #verCita con data (paciente, doctor, fecha/hora, motivo, estado)
    $('#verCita').modal('show');
  }

  // TODO: wire para editar/eliminar (cuando habilitemos endpoints)
});
</script>
@endpush
@endsection
