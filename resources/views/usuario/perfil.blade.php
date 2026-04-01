@extends('adminlte::page')

@section('title', 'Usuario')

@php
    $nombreCompleto = trim(collect([
        $user->persona->PRIMER_NOMBRE ?? '',
        $user->persona->SEGUNDO_NOMBRE ?? '',
        $user->persona->PRIMER_APELLIDO ?? '',
        $user->persona->SEGUNDO_APELLIDO ?? '',
    ])->filter()->implode(' '));

    $nombreMostrar = $nombreCompleto !== '' ? $nombreCompleto : 'Usuario sin nombre registrado';
    $correoMostrar = $correo ?: 'No registrado';
    $rolMostrar = $user->rol->NOM_ROL ?? 'No asignado';
    $iniciales = collect(explode(' ', $nombreCompleto !== '' ? $nombreCompleto : $user->USR_USUARIO))
        ->filter()
        ->take(2)
        ->map(fn ($segmento) => strtoupper(substr($segmento, 0, 1)))
        ->implode('');
@endphp

@section('content_header')
    <h1>Usuario / Perfil</h1>
@stop

@section('css')
<style>
    .profile-shell {
        display: grid;
        grid-template-columns: minmax(280px, 340px) minmax(0, 1fr);
        gap: 1.5rem;
    }

    .profile-panel,
    .security-panel,
    .questions-panel {
        border: 0;
        border-radius: 1rem;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
        overflow: hidden;
    }

    .profile-summary {
        background: linear-gradient(160deg, #0f766e 0%, #155e75 100%);
        color: #fff;
        padding: 2rem 1.5rem;
        height: 100%;
    }

    .profile-avatar {
        width: 72px;
        height: 72px;
        border-radius: 22px;
        background: rgba(255, 255, 255, 0.18);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        margin-bottom: 1rem;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.18);
    }

    .profile-kicker {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.16em;
        color: rgba(255, 255, 255, 0.72);
        margin-bottom: 0.75rem;
    }

    .profile-summary h2 {
        font-size: 1.6rem;
        font-weight: 700;
        margin-bottom: 0.4rem;
    }

    .profile-summary p {
        color: rgba(255, 255, 255, 0.8);
        margin-bottom: 0;
    }

    .profile-meta {
        margin-top: 1.5rem;
        display: grid;
        gap: 0.85rem;
    }

    .profile-meta-item {
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 0.9rem;
        padding: 0.9rem 1rem;
    }

    .profile-meta-item span {
        display: block;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: rgba(255, 255, 255, 0.68);
        margin-bottom: 0.3rem;
    }

    .profile-meta-item strong {
        display: block;
        font-size: 0.98rem;
        font-weight: 600;
        word-break: break-word;
    }

    .panel-header {
        padding: 1.35rem 1.5rem 0.5rem;
    }

    .panel-header h3 {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 700;
        color: #0f172a;
    }

    .panel-header p {
        margin: 0.35rem 0 0;
        color: #64748b;
    }

    .panel-body {
        padding: 0.5rem 1.5rem 1.5rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
    }

    .info-card {
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        padding: 1rem 1.1rem;
        background: #f8fafc;
    }

    .info-card span {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        color: #64748b;
        font-size: 0.82rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 0.55rem;
    }

    .info-card strong {
        display: block;
        color: #0f172a;
        font-size: 1rem;
        font-weight: 700;
        word-break: break-word;
    }

    .security-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.45fr) minmax(240px, 0.95fr);
        gap: 1.25rem;
        align-items: start;
    }

    .security-guide {
        border-radius: 1rem;
        background: linear-gradient(180deg, #f8fafc 0%, #eef6ff 100%);
        border: 1px solid #dbeafe;
        padding: 1.1rem 1.15rem;
    }

    .security-guide h4 {
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 0.75rem;
        color: #0f172a;
    }

    .security-guide ul {
        margin: 0;
        padding-left: 1rem;
        color: #475569;
    }

    .security-guide li + li {
        margin-top: 0.55rem;
    }

    .rule-list {
        display: grid;
        gap: 0.55rem;
        margin-top: 0.85rem;
    }

    .rule-item {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        padding: 0.75rem 0.85rem;
        border-radius: 0.85rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease;
    }

    .rule-item i {
        width: 1rem;
        text-align: center;
    }

    .rule-item.text-success {
        border-color: rgba(22, 163, 74, 0.25);
        background: rgba(22, 163, 74, 0.08);
    }

    .rule-item.text-danger {
        border-color: rgba(220, 38, 38, 0.14);
        background: rgba(248, 250, 252, 0.95);
    }

    .questions-list {
        display: grid;
        gap: 0.85rem;
    }

    .question-item {
        display: flex;
        gap: 0.9rem;
        align-items: flex-start;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        padding: 1rem 1.1rem;
        background: #fff;
    }

    .question-index {
        width: 34px;
        height: 34px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #e0f2fe;
        color: #0f766e;
        font-weight: 700;
        flex-shrink: 0;
    }

    .question-item p {
        margin: 0;
        color: #0f172a;
        font-weight: 600;
    }

    .question-item small {
        display: block;
        margin-top: 0.35rem;
        color: #64748b;
    }

    @media (max-width: 991.98px) {
        .profile-shell,
        .security-layout,
        .info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@stop

@section('content')
    @if(session('warning'))
        <x-adminlte-alert theme="warning" title="Aviso" dismissable>{{ session('warning') }}</x-adminlte-alert>
    @endif

    @if (session('status') === 'password-updated')
        <x-adminlte-alert theme="success" title="Exito" dismissable>Tu contrase&ntilde;a fue actualizada correctamente.</x-adminlte-alert>
    @endif

    @if ($errors->usuarioPassword->any())
        <x-adminlte-alert theme="danger" title="Errores" dismissable>
            <ul class="mb-0">@foreach ($errors->usuarioPassword->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </x-adminlte-alert>
    @endif

    <div class="profile-shell">
        <div class="card profile-panel mb-0">
            <div class="profile-summary">
                <div class="profile-avatar">{{ $iniciales !== '' ? $iniciales : 'US' }}</div>
                <div class="profile-kicker">
                    <i class="fas fa-user-shield"></i>
                    <span>Acceso personal</span>
                </div>
                <h2>{{ $nombreMostrar }}</h2>
                <p>Gestiona tu informacion de acceso, revisa tus datos y mant&eacute;n tu cuenta protegida desde un solo lugar.</p>

                <div class="profile-meta">
                    <div class="profile-meta-item">
                        <span>Usuario</span>
                        <strong>{{ $user->USR_USUARIO }}</strong>
                    </div>
                    <div class="profile-meta-item">
                        <span>Rol actual</span>
                        <strong>{{ $rolMostrar }}</strong>
                    </div>
                    <div class="profile-meta-item">
                        <span>Correo vinculado</span>
                        <strong>{{ $correoMostrar }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="card profile-panel mb-4">
                <div class="panel-header">
                    <h3>Resumen del perfil</h3>
                    <p>Informacion principal de tu cuenta dentro del sistema.</p>
                </div>
                <div class="panel-body">
                    <div class="info-grid">
                        <div class="info-card">
                            <span><i class="fas fa-id-badge"></i> Usuario</span>
                            <strong>{{ $user->USR_USUARIO }}</strong>
                        </div>
                        <div class="info-card">
                            <span><i class="fas fa-user-tag"></i> Rol</span>
                            <strong>{{ $rolMostrar }}</strong>
                        </div>
                        <div class="info-card">
                            <span><i class="fas fa-address-card"></i> Nombre completo</span>
                            <strong>{{ $nombreMostrar }}</strong>
                        </div>
                        <div class="info-card">
                            <span><i class="fas fa-envelope"></i> Correo electronico</span>
                            <strong>{{ $correoMostrar }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card security-panel mb-4">
                <div class="panel-header">
                    <h3>Seguridad de acceso</h3>
                    <p>Actualiza tu contrase&ntilde;a con una combinacion segura y facil de recordar para ti.</p>
                </div>
                <div class="panel-body">
                    <div class="security-layout">
                        <div>
                            <form method="POST" action="{{ route('usuario.password.update') }}" id="password-form">
                                @csrf
                                @method('PUT')

                                <div class="form-group">
                                    <label for="current_password">Contrase&ntilde;a actual</label>
                                    <input type="password" class="form-control" name="current_password" id="current_password" required>
                                </div>

                                <div class="form-group">
                                    <label for="password">Nueva contrase&ntilde;a</label>
                                    <input type="password" class="form-control" name="password" id="password" required>
                                </div>

                                <div class="form-group">
                                    <label for="password_confirmation">Confirmar nueva contrase&ntilde;a</label>
                                    <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" required>
                                    <small id="match-msg" class="text-danger">La confirmacion debe coincidir.</small>
                                </div>

                                <button class="btn btn-primary px-4">Actualizar contrase&ntilde;a</button>
                            </form>
                        </div>

                        <div class="security-guide">
                            <h4>Requisitos de seguridad</h4>
                            <ul>
                                <li>Evita usar informacion personal o claves anteriores.</li>
                                <li>Combina letras, numeros y simbolos para mayor fortaleza.</li>
                                <li>Si la compartiste o la olvidaste, actualizala de inmediato.</li>
                            </ul>

                            <div id="pw-rules" class="rule-list">
                                <div data-rule="len" class="rule-item text-danger"><i class="fas fa-circle"></i><span>Minimo 10 caracteres.</span></div>
                                <div data-rule="upper" class="rule-item text-danger"><i class="fas fa-circle"></i><span>Incluye una letra mayuscula.</span></div>
                                <div data-rule="lower" class="rule-item text-danger"><i class="fas fa-circle"></i><span>Incluye una letra minuscula.</span></div>
                                <div data-rule="num" class="rule-item text-danger"><i class="fas fa-circle"></i><span>Incluye un numero.</span></div>
                                <div data-rule="sym" class="rule-item text-danger"><i class="fas fa-circle"></i><span>Incluye un simbolo.</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card questions-panel mb-0">
                <div class="panel-header">
                    <h3>Preguntas de seguridad</h3>
                    <p>Estas preguntas ayudan a validar tu identidad cuando se requiere una verificacion adicional.</p>
                </div>
                <div class="panel-body">
                    @if($preguntas->isEmpty())
                        <div class="text-muted">No tienes preguntas de seguridad configuradas.</div>
                    @else
                        <div class="questions-list">
                            @foreach($preguntas as $indice => $p)
                                <div class="question-item">
                                    <div class="question-index">{{ $indice + 1 }}</div>
                                    <div>
                                        <p>{{ $p->pregunta->TEXTO_PREGUNTA ?? 'Pregunta no disponible' }}</p>
                                        <small>La respuesta permanece oculta por motivos de seguridad.</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
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

        const icon = el.querySelector('i');
        if (icon) {
            icon.classList.toggle('fa-check-circle', ok);
            icon.classList.toggle('fa-circle', !ok);
        }
    }

    function updateRules() {
        const val = pass.value || '';
        Object.entries(rules).forEach(([key, fn]) => {
            paint(document.querySelector(`[data-rule="${key}"]`), fn(val));
        });

        const match = conf.value.length > 0 && conf.value === val;
        const msg = document.getElementById('match-msg');
        msg.textContent = match ? 'Confirmacion correcta.' : 'La confirmacion debe coincidir.';
        paint(msg, match);
    }

    pass.addEventListener('input', updateRules);
    conf.addEventListener('input', updateRules);
    updateRules();
})();
</script>
@stop
