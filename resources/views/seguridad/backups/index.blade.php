@extends('adminlte::page')

@section('title', 'Backups')

@section('content_header')
    <h1>Backups</h1>
@stop

@section('content')
    @if(session('success'))
        <x-adminlte-alert theme="success" title="OK" dismissable>
            {{ session('success') }}
        </x-adminlte-alert>
    @endif

    @if(session('error'))
        <x-adminlte-alert theme="danger" title="Error" dismissable>
            {{ session('error') }}
        </x-adminlte-alert>
    @endif

    @if (function_exists('puede') && puede('SEGURIDAD_BACKUPS', 'CREAR'))
        <form action="{{ route('seguridad.backups.store') }}" method="POST" class="mb-3">
            @csrf
            <button class="btn btn-primary">
                <i class="fa fa-database"></i> Generar backup
            </button>
        </form>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Archivo</th>
                        <th>Tamaño</th>
                        <th>Estado</th>
                        <th>Usuario</th>
                        <th style="width:220px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($backups as $b)
                    <tr>
                        <td>{{ $b->created_at }}</td>
                        <td>{{ $b->NOMBRE_ARCHIVO }}</td>
                        <td>{{ number_format($b->TAMANIO_BYTES) }} bytes</td>
                        <td>
                            @if($b->ESTADO === 'OK')
                                <span class="badge bg-success">OK</span>
                            @else
                                <span class="badge bg-danger">{{ $b->ESTADO }}</span>
                            @endif
                        </td>
                        <td>{{ $b->usuario }}</td>
                        <td>
                            <a class="btn btn-sm btn-outline-primary"
                               title="Descargar respaldo"
                               href="{{ route('seguridad.backups.download', $b->COD_BACKUP) }}">
                               <i class="fa fa-download"></i>
                            </a>

                            @if (function_exists('puede') && puede('SEGURIDAD_BACKUPS', 'EDITAR') && $b->ESTADO === 'OK')
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-danger"
                                    title="Restaurar respaldo"
                                    data-toggle="modal"
                                    data-target="#restoreBackupModal"
                                    data-action="{{ route('seguridad.backups.restore', $b->COD_BACKUP) }}"
                                    data-file="{{ $b->NOMBRE_ARCHIVO }}"
                                >
                                    <i class="fa fa-undo"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @if($backups->hasPages())
            <div class="card-footer">{{ $backups->links() }}</div>
        @endif
    </div>

    @if (function_exists('puede') && puede('SEGURIDAD_BACKUPS', 'EDITAR'))
        <div class="modal fade" id="restoreBackupModal" tabindex="-1" role="dialog" aria-labelledby="restoreBackupModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="POST" id="restoreBackupForm">
                        @csrf
                        <div class="modal-header bg-danger">
                            <h5 class="modal-title" id="restoreBackupModalLabel">Restaurar copia de seguridad</h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-2">
                                Esta acción reemplazará la base de datos actual con el contenido del respaldo seleccionado.
                            </p>
                            <p class="mb-3 font-weight-bold text-danger">
                                Usa esta opción solo si estás completamente seguro.
                            </p>
                            <div class="form-group">
                                <label>Archivo a restaurar</label>
                                <input type="text" class="form-control" id="restoreBackupFile" readonly>
                            </div>
                            <div class="form-group mb-0">
                                <label for="confirm_backup">
                                    Escribe exactamente el nombre del archivo para confirmar
                                </label>
                                <input
                                    type="text"
                                    name="confirm_backup"
                                    id="confirm_backup"
                                    class="form-control"
                                    autocomplete="off"
                                    required
                                >
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger">Restaurar respaldo</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@stop

@section('js')
<script>
    (function () {
        const modal = document.getElementById('restoreBackupModal');
        if (!modal) {
            return;
        }

        $('#restoreBackupModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const action = button.data('action');
            const file = button.data('file');

            document.getElementById('restoreBackupForm').setAttribute('action', action);
            document.getElementById('restoreBackupFile').value = file;
            document.getElementById('confirm_backup').value = '';
        });
    })();
</script>
@stop
