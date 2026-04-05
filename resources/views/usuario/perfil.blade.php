@extends('adminlte::page')

@section('title', 'Usuario')

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
        </section>
    </div>
@stop

@section('css')
<style>
    .profile-shell {
        display: grid;
        gap: 1.25rem;
    }

    .profile-section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #0f172a;
    }

    .profile-section-copy {
        color: #56708f;
        font-size: 1rem;
    }

    .profile-summary-header,
    .profile-security-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.35rem;
    }

    .profile-summary-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.65rem 0.9rem;
        border-radius: 999px;
        background: rgba(37, 99, 235, 0.08);
        color: #1d4ed8;
        font-weight: 700;
        white-space: nowrap;
    }

    .profile-summary-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
    }

    .profile-info-card {
        padding: 1.15rem 1.2rem;
        border-radius: 1rem;
        border: 1px solid rgba(191, 219, 254, 0.8);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(247, 250, 255, 0.96));
        min-height: 102px;
    }

    .profile-info-card--wide {
        grid-column: span 2;
    }

    .profile-info-label {
        display: inline-block;
        margin-bottom: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-size: 0.78rem;
        color: #5d7596;
        font-weight: 700;
    }

    .profile-info-value {
        display: block;
        color: #0f172a;
        font-size: 1.15rem;
        line-height: 1.35;
    }

    .profile-security-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.8fr) minmax(280px, 0.95fr);
        gap: 1.25rem;
        align-items: start;
    }

    .password-guidance-card {
        padding: 1.2rem;
        border-radius: 1rem;
        border: 1px solid rgba(191, 219, 254, 0.8);
        background: linear-gradient(180deg, rgba(248, 250, 255, 0.98), rgba(239, 246, 255, 0.96));
    }

    .password-guidance-card__header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.8rem;
    }

    .password-guidance-card__header h3 {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 700;
        color: #0f172a;
    }

    .password-guidance-copy {
        color: #5d7596;
    }

    .password-strength-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.4rem 0.75rem;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .strength-empty,
    .strength-low {
        background: rgba(248, 113, 113, 0.12);
        color: #dc2626;
    }

    .strength-medium {
        background: rgba(245, 158, 11, 0.14);
        color: #b45309;
    }

    .strength-high {
        background: rgba(34, 197, 94, 0.15);
        color: #15803d;
    }

    .password-rules {
        display: grid;
        gap: 0.7rem;
    }

    .password-rule {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        padding: 0.85rem 0.95rem;
        border-radius: 0.9rem;
        border: 1px solid rgba(203, 213, 225, 0.9);
        background: #ffffff;
        transition: all 0.18s ease;
    }

    .password-rule__icon {
        width: 1.4rem;
        text-align: center;
        flex-shrink: 0;
    }

    .password-rule.is-pending {
        color: #64748b;
    }

    .password-rule.is-pending .password-rule__icon {
        color: #94a3b8;
    }

    .password-rule.is-valid {
        border-color: rgba(74, 222, 128, 0.7);
        background: rgba(240, 253, 244, 0.95);
        color: #166534;
    }

    .password-rule.is-valid .password-rule__icon {
        color: #16a34a;
    }

    .password-rule.is-invalid {
        border-color: rgba(252, 165, 165, 0.9);
        background: rgba(254, 242, 242, 0.96);
        color: #dc2626;
    }

    .password-rule.is-invalid .password-rule__icon {
        color: #ef4444;
    }

    .password-match-hint {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.55rem 0.8rem;
        border-radius: 0.8rem;
        font-size: 0.92rem;
        font-weight: 600;
    }

    .password-match-hint.is-pending {
        background: rgba(226, 232, 240, 0.65);
        color: #64748b;
    }

    .password-match-hint.is-valid {
        background: rgba(220, 252, 231, 0.9);
        color: #166534;
    }

    .password-match-hint.is-invalid {
        background: rgba(254, 226, 226, 0.95);
        color: #dc2626;
    }

    .security-questions-list {
        display: grid;
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .security-question-item {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        padding: 0.95rem 1rem;
        border-radius: 0.9rem;
        border: 1px solid rgba(191, 219, 254, 0.75);
        background: rgba(248, 250, 255, 0.95);
        color: #1e3a8a;
        font-weight: 600;
    }

    .security-question-item i {
        color: #2563eb;
    }

    @media (max-width: 991.98px) {
        .profile-summary-grid,
        .profile-security-layout {
            grid-template-columns: 1fr;
        }

        .profile-info-card--wide {
            grid-column: span 1;
        }

        .profile-summary-header,
        .profile-security-header,
        .password-guidance-card__header {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@stop

@section('js')
<script>
(() => {
    const pass = document.getElementById('password');
    const conf = document.getElementById('password_confirmation');
    const matchMsg = document.getElementById('match-msg');
    const strengthBadge = document.getElementById('password-strength');

    if (!pass || !conf || !matchMsg || !strengthBadge) {
        return;
    }

    const rules = {
        len: value => value.length >= 10,
        upper: value => /[A-Z]/.test(value),
        lower: value => /[a-z]/.test(value),
        num: value => /\d/.test(value),
        sym: value => /[^A-Za-z0-9]/.test(value),
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

    pass.addEventListener('input', refresh);
    conf.addEventListener('input', refresh);
    refresh();
})();
</script>
@stop
