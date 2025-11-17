@extends('adminlte::page')

@section('title', $pageTitle)

@section('content_header')
    <div class="d-flex flex-wrap justify-content-between align-items-start">
        <div class="mb-2">
            <h1 class="h3 font-weight-bold text-primary mb-1">{{ ucfirst($heading) }}</h1>
            <p class="text-muted mb-0">{{ $intro }}</p>
        </div>
        <button class="btn btn-outline-primary mt-2 mt-md-0" id="btnExportarBitacora">
            <i class="fas fa-download"></i> Exportar bitácora
        </button>
    </div>
@endsection

@section('content')
    @php
        $eventCollection = collect($eventList ?? []);
        $porEstado       = $eventCollection->groupBy('estado')->map->count();
        $timelineSafe    = collect($timeline ?? []);
    @endphp

    {{-- Resumen por estado --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <h3 class="h6 mb-0">Estados registrados</h3>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Estado</th>
                        <th class="text-right">Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($porEstado as $estado => $cantidad)
                        <tr>
                            <td>{{ $estado }}</td>
                            <td class="text-right font-weight-bold">{{ $cantidad }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center text-muted">
                                No hay estados registrados en este período.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection

@section('js')
<script>
    (function () {
        const timeline = @json($timelineSafe->values());
        const btn = document.getElementById('btnExportarBitacora');
        if (!btn) {
            return;
        }

        btn.addEventListener('click', function () {
            if (!timeline.length) {
                alert('No hay movimientos para exportar.');
                return;
            }
            const rows = timeline.map(item => ({
                Fecha: item.fecha || '',
                Evento: item.descripcion || '',
                Estado: item.estado || ''
            }));
            downloadCsv('bitacora-recepcion.csv', rows);
        });

        function downloadCsv(filename, rows) {
            if (!rows.length) return;
            const headers = Object.keys(rows[0]);
            const lines = [headers.join(',')].concat(rows.map(row => headers.map(h => wrap(row[h])).join(',')));
            const blob = new Blob(['\uFEFF' + lines.join('\n')], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.setAttribute('download', filename);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function wrap(value) {
            const str = (value ?? '').toString().replace(/"/g, '""');
            return '"' + str + '"';
        }
    })();
</script>
@endsection
