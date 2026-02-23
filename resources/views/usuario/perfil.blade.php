@extends('adminlte::page')

@section('title', 'Usuario')

@section('content_header')
    <h1>Usuario / Perfil</h1>
@stop

@section('content')
    @if(session('warning'))
        <x-adminlte-alert theme="warning" title="Aviso" dismissable>{{ session('warning') }}</x-adminlte-alert>
    @endif

    @if (session('status') === 'password-updated')
        <x-adminlte-alert theme="success" title="Éxito" dismissable>Tu contraseña fue actualizada correctamente.</x-adminlte-alert>
    @endif

    @if ($errors->usuarioPassword->any())
        <x-adminlte-alert theme="danger" title="Errores" dismissable>
            <ul class="mb-0">@foreach ($errors->usuarioPassword->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </x-adminlte-alert>
    @endif

    <div class="card card-outline card-primary">
        <div class="card-header p-0 border-bottom-0">
            <ul class="nav nav-tabs" id="usuario-tabs" role="tablist">
                <li class="nav-item"><a class="nav-link active" id="tab-perfil" data-toggle="pill" href="#pane-perfil" role="tab">Mi perfil</a></li>
                <li class="nav-item"><a class="nav-link" id="tab-pwd" data-toggle="pill" href="#pane-pwd" role="tab">Cambiar contraseña</a></li>
                <li class="nav-item"><a class="nav-link" id="tab-preguntas" data-toggle="pill" href="#pane-preguntas" role="tab">Mis preguntas de seguridad</a></li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="usuario-tabs-content">
                <div class="tab-pane fade show active" id="pane-perfil" role="tabpanel">
                    <dl class="row mb-0">
                        <dt class="col-sm-3">Usuario</dt>
                        <dd class="col-sm-9">{{ $user->USR_USUARIO }}</dd>

                        <dt class="col-sm-3">Nombre</dt>
                        <dd class="col-sm-9">{{ trim(($user->persona->PRIMER_NOMBRE ?? '') . ' ' . ($user->persona->SEGUNDO_NOMBRE ?? '') . ' ' . ($user->persona->PRIMER_APELLIDO ?? '') . ' ' . ($user->persona->SEGUNDO_APELLIDO ?? '')) }}</dd>

                        <dt class="col-sm-3">Correo</dt>
                        <dd class="col-sm-9">{{ $correo ?: 'No registrado' }}</dd>

                        <dt class="col-sm-3">Rol</dt>
                        <dd class="col-sm-9">{{ $user->rol->NOM_ROL ?? 'No asignado' }}</dd>
                    </dl>
                </div>

                <div class="tab-pane fade" id="pane-pwd" role="tabpanel">
                    <form method="POST" action="{{ route('usuario.password.update') }}" id="password-form">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="current_password">Contraseña actual</label>
                            <input type="password" class="form-control" name="current_password" id="current_password" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Nueva contraseña</label>
                            <input type="password" class="form-control" name="password" id="password" required>
                        </div>

                        <div id="pw-rules" class="mb-3 small">
                            <div data-rule="len" class="text-danger">Mínimo 10 caracteres.</div>
                            <div data-rule="upper" class="text-danger">Incluye una letra mayúscula.</div>
                            <div data-rule="lower" class="text-danger">Incluye una letra minúscula.</div>
                            <div data-rule="num" class="text-danger">Incluye un número.</div>
                            <div data-rule="sym" class="text-danger">Incluye un símbolo.</div>
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Confirmar nueva contraseña</label>
                            <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" required>
                            <small id="match-msg" class="text-danger">La confirmación debe coincidir.</small>
                        </div>

                        <button class="btn btn-primary">Actualizar contraseña</button>
                    </form>
                </div>

                <div class="tab-pane fade" id="pane-preguntas" role="tabpanel">
                    @if($preguntas->isEmpty())
                        <p class="text-muted mb-0">No tienes preguntas de seguridad configuradas.</p>
                    @else
                        <ol class="mb-0 pl-3">
                            @foreach($preguntas as $p)
                                <li class="mb-2">{{ $p->pregunta->TEXTO_PREGUNTA ?? 'Pregunta no disponible' }}</li>
                            @endforeach
                        </ol>
                        <small class="text-muted">Por seguridad, las respuestas no se muestran.</small>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
(() => {
    const pass = document.getElementById('password');
    const conf = document.getElementById('password_confirmation');

    if (!pass || !conf) {
        return;
    }

    const rules = {
        len: v => v.length >= 10,
        upper: v => /[A-Z]/.test(v),
        lower: v => /[a-z]/.test(v),
        num: v => /\d/.test(v),
        sym: v => /[^A-Za-z0-9]/.test(v),
    };

    function paint(el, ok) {
        if (!el) return;
        el.classList.toggle('text-success', ok);
        el.classList.toggle('text-danger', !ok);
    }

    function updateRules() {
        const val = pass.value || '';
        Object.entries(rules).forEach(([key, fn]) => {
            paint(document.querySelector(`[data-rule="${key}"]`), fn(val));
        });

        const match = conf.value.length > 0 && conf.value === val;
        const msg = document.getElementById('match-msg');
        msg.textContent = match ? 'Confirmación correcta.' : 'La confirmación debe coincidir.';
        paint(msg, match);
    }

    pass.addEventListener('input', updateRules);
    conf.addEventListener('input', updateRules);
    updateRules();
})();
</script>
@stop
