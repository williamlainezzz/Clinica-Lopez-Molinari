@extends('adminlte::page')

@section('title', 'Cambiar contraseña')

@section('content_header')
    <h1>Cambiar contraseña</h1>
@stop

@section('content')
    @if(session('warning'))
        <div class="alert alert-warning border-0 shadow-sm" role="alert">
            <div class="d-flex align-items-start">
                <div class="mr-3">
                    <i class="fas fa-shield-alt fa-2x"></i>
                </div>
                <div>
                    <h2 class="h5 font-weight-bold mb-2">Actualiza tu contraseña para continuar</h2>
                    <p class="mb-2">{{ session('warning') }}</p>
                    <p class="mb-0">
                        Ingresa tu contraseña actual, crea una nueva contraseña que cumpla las reglas de seguridad
                        y confirma el cambio. Al finalizar podrás continuar usando el sistema normalmente.
                    </p>
                </div>
            </div>
        </div>
    @endif

    @if (session('status') === 'password-updated')
        <x-adminlte-alert theme="success" title="Éxito" dismissable>Tu contraseña fue actualizada correctamente.</x-adminlte-alert>
    @endif

    @if ($errors->usuarioPassword->any())
        <x-adminlte-alert theme="danger" title="Errores" dismissable>
            <ul class="mb-0">@foreach ($errors->usuarioPassword->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </x-adminlte-alert>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h3 class="h6 mb-1">Cambio de contraseña requerido</h3>
            <p class="text-muted mb-0">
                Por seguridad, la nueva contraseña debe ser diferente y cumplir con los requisitos indicados.
            </p>
        </div>
        <div class="card-body">
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
    </div>
@stop

@section('js')
<script>
(() => {
    const pass = document.getElementById('password');
    const conf = document.getElementById('password_confirmation');
    const rules = {
        len: v => v.length >= 10,
        upper: v => /[A-Z]/.test(v),
        lower: v => /[a-z]/.test(v),
        num: v => /\d/.test(v),
        sym: v => /[^A-Za-z0-9]/.test(v),
    };

    function paint(el, ok) {
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
