@php
    $items = [
        ['key' => 'total', 'label' => 'Citas totales', 'icon' => 'fa-calendar-check', 'color' => 'primary'],
        ['key' => 'confirmadas', 'label' => 'Confirmadas', 'icon' => 'fa-check-circle', 'color' => 'success'],
        ['key' => 'pendientes', 'label' => 'Pendientes', 'icon' => 'fa-clock', 'color' => 'warning'],
        ['key' => 'proximas', 'label' => 'Próximas', 'icon' => 'fa-flag', 'color' => 'info'],
        ['key' => 'canceladas', 'label' => 'Canceladas', 'icon' => 'fa-ban', 'color' => 'danger'],
        ['key' => 'completadas', 'label' => 'Completadas', 'icon' => 'fa-check', 'color' => 'secondary'],
    ];
@endphp

<div class="row g-3 mb-4">
    @foreach($items as $item)
        @continue(!isset($metrics[$item['key']]))
        <div class="col-12 col-md-6 col-xl-2">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start gap-3">
                        <span class="badge bg-{{ $item['color'] }} rounded-3 p-3">
                            <i class="fas {{ $item['icon'] }} fa-lg"></i>
                        </span>
                        <div>
                            <p class="text-muted text-uppercase fw-semibold mb-1 small">{{ $item['label'] }}</p>
                            <h3 class="fw-bold mb-0">{{ $metrics[$item['key']] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
