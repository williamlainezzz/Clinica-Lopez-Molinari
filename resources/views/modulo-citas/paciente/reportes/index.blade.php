@extends('adminlte::page')

@section('title', $pageTitle)

@section('content_header')
    <div class="d-flex flex-wrap justify-content-between align-items-start">
        <div class="mb-2">
            <h1 class="h3 font-weight-bold text-primary mb-1">{{ $heading }}</h1>
            <p class="text-muted mb-0">{{ $intro }}</p>
        </div>
        <button class="btn btn-outline-primary mt-2 mt-md-0" id="btnDescargarHistorialPaciente">
            <i class="fas fa-file-download"></i> Descargar historial
        </button>
    </div>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Doctor</th>
                        <th>Motivo</th>
                        <th>Estado</th>
                        <th>Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patientRecord['historial'] ?? [] as $item)
                        @php
                            $estado = strtoupper($item['estado'] ?? '');
                            $badge = match ($estado) {
                                'CONFIRMADA' => 'success',
                                'COMPLETADA' => 'primary',
                                'CANCELADA'  => 'danger',
                                default      => 'secondary',
                            };
                        @endphp
                        <tr>
                            <td>{{ $item['fecha'] }}</td>
                            <td>{{ $item['doctor'] }}</td>
                            <td>{{ $item['motivo'] }}</td>
                            <td><span class="badge badge-{{ $badge }}">{{ $item['estado'] }}</span></td>
                            <td>{{ $item['detalle'] ?? 'Sin detalle' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Aún no tienes citas registradas.</td>
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
        const historial = @json($patientRecord['historial'] ?? []);
        const btn = document.getElementById('btnDescargarHistorialPaciente');
        if (!btn) {
            return;
        }
        btn.addEventListener('click', function () {
            if (!historial.length) {
                alert('No hay citas para exportar.');
                return;
            }
            const rows = historial.map(item => ({
                Fecha: item.fecha || '',
                Doctor: item.doctor || '',
                Motivo: item.motivo || '',
                Estado: item.estado || '',
                Detalle: item.detalle || ''
            }));
            downloadCsv('historial-paciente.csv', rows);
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
