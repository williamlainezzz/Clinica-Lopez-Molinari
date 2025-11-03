@php
  $citas = $citas ?? [
    ["fecha"=>"2025-11-12","hora"=>"08:30","paciente"=>"Ana Rivera","doctor"=>"Dr. López","estado"=>"Confirmada","motivo"=>"Limpieza"],
    ["fecha"=>"2025-11-12","hora"=>"09:00","paciente"=>"Carlos Pérez","doctor"=>"Dra. Molina","estado"=>"Pendiente","motivo"=>"Dolor de muela"],
    ["fecha"=>"2025-11-12","hora"=>"10:15","paciente"=>"María Gómez","doctor"=>"Dr. López","estado"=>"Cancelada","motivo"=>"Control"]
  ];
@endphp

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
        <tr>
          <td>{{ $c["fecha"] }}</td>
          <td>{{ $c["hora"] }}</td>
          <td>{{ $c["paciente"] }}</td>
          <td>{{ $c["doctor"] }}</td>
          <td>
            <span class="badge 
              @if($c["estado"] === "Confirmada") bg-success 
              @elseif($c["estado"] === "Pendiente") bg-warning 
              @else bg-danger @endif">
              {{ $c["estado"] }}
            </span>
          </td>
          <td>{{ $c["motivo"] }}</td>
          <td class="text-right">
            <button class="btn btn-xs btn-info" data-toggle="modal" data-target="#mdlDetalle">
              <i class="fas fa-eye"></i>
            </button>
            <button class="btn btn-xs btn-primary">
              <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-xs btn-danger">
              <i class="fas fa-times"></i>
            </button>
          </td>
        </tr>
        @empty
          <tr><td colspan="7" class="text-center text-muted">Sin resultados</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
