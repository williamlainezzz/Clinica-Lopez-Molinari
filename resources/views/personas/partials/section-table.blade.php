@php
    $columns = $section['columns'] ?? [];
    $rows = $section['rows'] ?? [];
    $actions = $section['actions'] ?? [];
    $showActions = $section['show_actions'] ?? false;
    $footnote = $section['footnote'] ?? null;
    $headerActions = $section['header_actions'] ?? [];
@endphp

<div class="mb-4 d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="h5 mb-1 text-primary">
            <i class="{{ $section['icon'] ?? 'fas fa-circle' }} mr-2"></i>
            {{ $section['label'] ?? 'Detalle' }}
        </h3>
        @if(!empty($section['description']))
            <p class="mb-0 text-muted">{{ $section['description'] }}</p>
        @endif
    </div>
    <div class="btn-group">
        @foreach($headerActions as $action)
            <a href="{{ $action['url'] ?? '#' }}" class="btn btn-sm {{ $action['class'] ?? 'btn-primary' }} {{ !empty($action['disabled']) ? 'disabled' : '' }}">
                <i class="{{ $action['icon'] ?? 'fas fa-circle' }} mr-1"></i>
                {{ $action['label'] ?? 'Acción' }}
            </a>
        @endforeach
    </div>
</div>

<div class="table-responsive shadow-sm rounded">
    <table class="table table-hover mb-0">
        <thead class="thead-light">
            <tr>
                @foreach($columns as $column)
                    <th class="{{ $column['class'] ?? '' }}">{{ $column['label'] }}</th>
                @endforeach
                @if($showActions)
                    <th class="text-center" style="width: {{ $section['actions_width'] ?? '170px' }};">Acciones</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    @foreach($columns as $column)
                        @php
                            $value = data_get($row, $column['key']);
                            $type = $column['type'] ?? 'text';
                        @endphp
                        <td>
                            @switch($type)
                                @case('badge')
                                    @php
                                        $label = is_array($value) ? ($value['label'] ?? '') : (string) $value;
                                        $variant = is_array($value) ? ($value['variant'] ?? 'secondary') : 'secondary';
                                    @endphp
                                    <span class="badge badge-{{ $variant }}">{{ $label }}</span>
                                    @break

                                @case('list')
                                    @if(is_array($value))
                                        <ul class="list-unstyled mb-0">
                                            @foreach($value as $item)
                                                <li><i class="fas fa-circle text-xs text-muted mr-1"></i>{{ $item }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        {{ $value }}
                                    @endif
                                    @break

                                @default
                                    {{ $value ?? '—' }}
                            @endswitch
                        </td>
                    @endforeach

                    @if($showActions)
                        <td class="text-nowrap text-center">
                            @foreach($actions as $action)
                                <button type="button" class="btn btn-xs btn-{{ $action['class'] ?? 'primary' }} {{ !empty($action['disabled']) ? 'disabled' : '' }}"
                                        title="{{ $action['label'] ?? 'Acción' }}">
                                    <i class="{{ $action['icon'] ?? 'fas fa-circle' }}"></i>
                                </button>
                            @endforeach
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($columns) + ($showActions ? 1 : 0) }}" class="text-center text-muted py-4">
                        No hay registros para mostrar en esta sección.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if(!empty($footnote))
    <p class="text-muted small mt-3 mb-0">
        <i class="fas fa-info-circle mr-1"></i>{{ $footnote }}
    </p>
@endif
