@extends('adminlte::page')

@section('title', 'Usuario')

@php
    $nombre = trim(collect([
        optional($user->persona)->PRIMER_NOMBRE,
        optional($user->persona)->SEGUNDO_NOMBRE,
        optional($user->persona)->PRIMER_APELLIDO,
        optional($user->persona)->SEGUNDO_APELLIDO,
    ])->filter()->implode(' '));

    $nombreCorto = trim(collect([
        optional($user->persona)->PRIMER_NOMBRE,
        optional($user->persona)->PRIMER_APELLIDO,
    ])->filter()->implode(' '));

    $nombreMostrar = $nombre !== '' ? $nombre : ($user->USR_USUARIO ?? 'Usuario');
    $nombreSidebar = $nombreCorto !== '' ? $nombreCorto : $nombreMostrar;
@endphp

@section('content_header')
    <h1>Usuario / Perfil</h1>
@stop

@section('content')
    @if(session('warning'))
        <x-adminlte-alert theme="warning" title="Aviso" dismissable>{{ session('warning') }}</x-adminlte-alert>
    @endif

    @if (session('status') === 'password-updated')
        <x-adminlte-alert theme="success" title="Exito" dismissable>Tu contrasena fue actualizada correctamente.</x-adminlte-alert>
    @endif

    @if (session('status') === 'passkey-deleted')
        <x-adminlte-alert theme="success" title="Listo" dismissable>El dispositivo biometrico fue eliminado correctamente.</x-adminlte-alert>
    @endif

    @if ($errors->usuarioPassword->any())
        <x-adminlte-alert theme="danger" title="Errores" dismissable>
            <ul class="mb-0">@foreach ($errors->usuarioPassword->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </x-adminlte-alert>
    @endif

    <div class="profile-shell">
        <section class="card card-outline card-primary profile-summary-card">
            <div class="card-body">
                <div class="profile-summary-header">
                    <div>
                        <h2 class="profile-section-title mb-2">Resumen del perfil</h2>
                        <p class="profile-section-copy mb-0">Informacion principal de tu cuenta dentro del sistema.</p>
                    </div>
                    <div class="profile-summary-badge">
                        <i class="fas fa-user-shield mr-2"></i>
                        Acceso personal
                    </div>
                </div>

                <div class="profile-summary-grid">
                    <article class="profile-info-card profile-info-card--wide">
                        <span class="profile-info-label"><i class="fas fa-id-badge mr-2"></i>Nombre mostrado</span>
                        <strong class="profile-info-value">{{ $nombreSidebar }}</strong>
                    </article>

                    <article class="profile-info-card">
                        <span class="profile-info-label"><i class="fas fa-user mr-2"></i>Usuario</span>
                        <strong class="profile-info-value">{{ $user->USR_USUARIO }}</strong>
                    </article>

                    <article class="profile-info-card">
                        <span class="profile-info-label"><i class="fas fa-user-tag mr-2"></i>Rol</span>
                        <strong class="profile-info-value">{{ $user->rol->NOM_ROL ?? 'No asignado' }}</strong>
                    </article>

                    <article class="profile-info-card profile-info-card--wide">
                        <span class="profile-info-label"><i class="fas fa-address-card mr-2"></i>Nombre completo</span>
                        <strong class="profile-info-value">{{ $nombreMostrar }}</strong>
                    </article>

                    <article class="profile-info-card profile-info-card--wide">
                        <span class="profile-info-label"><i class="fas fa-envelope mr-2"></i>Correo electronico</span>
                        <strong class="profile-info-value">{{ $correo ?: 'No registrado' }}</strong>
                    </article>
                </div>
            </div>
        </section>

        <section class="card card-outline card-primary profile-security-card">
            <div class="card-body">
                <div class="profile-security-header">
                    <div>
                        <h2 class="profile-section-title mb-2">Seguridad de acceso</h2>
                        <p class="profile-section-copy mb-0">Actualiza tu contrasena con una guia visual que te indique si ya cumple cada requisito.</p>
                    </div>
                </div>

                <div class="profile-security-layout">
                    <div>
                        <form method="POST" action="{{ route('usuario.password.update') }}" id="password-form" novalidate>
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="current_password">Contrasena actual</label>
                                <input type="password" class="form-control" name="current_password" id="current_password" required>
                            </div>

                            <div class="form-group">
                                <label for="password">Nueva contrasena</label>
                                <input type="password" class="form-control" name="password" id="password" required>
                                <small class="form-text text-muted">Usa una combinacion segura y facil de recordar para ti.</small>
                            </div>

                            <div class="form-group mb-2">
                                <label for="password_confirmation">Confirmar nueva contrasena</label>
                                <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" required>
                            </div>

                            <div id="match-msg" class="password-match-hint is-pending">
                                <i class="fas fa-circle-notch mr-2"></i>
                                Esperando confirmacion de la nueva contrasena.
                            </div>

                            <button class="btn btn-primary mt-3">Actualizar contrasena</button>
                        </form>
                    </div>

                    <aside class="password-guidance-card">
                        <div class="password-guidance-card__header">
                            <h3>Requisitos de seguridad</h3>
                            <span id="password-strength" class="password-strength-badge strength-empty">Completa los campos</span>
                        </div>

                        <p class="password-guidance-copy mb-3">Cada regla cambia automaticamente cuando tu nueva contrasena ya la cumple.</p>

                        <div class="password-rules" id="pw-rules">
                            <div class="password-rule is-pending" data-rule="len">
                                <span class="password-rule__icon"><i class="fas fa-circle"></i></span>
                                <span class="password-rule__text">Minimo 10 caracteres</span>
                            </div>
                            <div class="password-rule is-pending" data-rule="upper">
                                <span class="password-rule__icon"><i class="fas fa-circle"></i></span>
                                <span class="password-rule__text">Al menos una letra mayuscula</span>
                            </div>
                            <div class="password-rule is-pending" data-rule="lower">
                                <span class="password-rule__icon"><i class="fas fa-circle"></i></span>
                                <span class="password-rule__text">Al menos una letra minuscula</span>
                            </div>
                            <div class="password-rule is-pending" data-rule="num">
                                <span class="password-rule__icon"><i class="fas fa-circle"></i></span>
                                <span class="password-rule__text">Al menos un numero</span>
                            </div>
                            <div class="password-rule is-pending" data-rule="sym">
                                <span class="password-rule__icon"><i class="fas fa-circle"></i></span>
                                <span class="password-rule__text">Al menos un simbolo</span>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </section>

        <section class="card card-outline card-primary passkey-card">
            <div class="card-body">
                <div class="passkey-panel">
                    <div>
                        <h2 class="profile-section-title mb-2">Inicio con biometria</h2>
                        <p class="profile-section-copy mb-2">Activa el acceso con la biometria, PIN o metodo seguro configurado en este dispositivo.</p>
                        <span class="passkey-count-badge">
                            <i class="fas fa-fingerprint mr-2"></i>
                            {{ $passkeyCount }} dispositivo{{ $passkeyCount === 1 ? '' : 's' }} activado{{ $passkeyCount === 1 ? '' : 's' }}
                        </span>
                    </div>

                    <div class="passkey-actions">
                        <button type="button" class="btn btn-primary" id="passkey-register-button">
                            <i class="fas fa-fingerprint mr-2"></i>
                            Activar inicio con biometria
                        </button>
                        @unless($passkeyTableReady)
                            <div class="passkey-message is-error">
                                La tabla de biometria aun no esta creada. Ejecuta la migracion antes de activar esta opcion.
                            </div>
                        @endunless
                        <div id="passkey-message" class="passkey-message" role="status"></div>
                    </div>
                </div>

                <div class="passkey-list">
                    @if($passkeys->isEmpty())
                        <div class="passkey-empty">
                            <i class="fas fa-fingerprint"></i>
                            <span>No hay dispositivos biometricos registrados todavia.</span>
                        </div>
                    @else
                        @foreach($passkeys as $passkey)
                            <article class="passkey-device">
                                <div class="passkey-device__icon">
                                    <i class="fas fa-fingerprint"></i>
                                </div>
                                <div class="passkey-device__body">
                                    <strong>{{ $passkey->NOMBRE ?: 'Dispositivo biometrico' }}</strong>
                                    <span>
                                        Registrado {{ optional($passkey->created_at)->format('d/m/Y H:i') ?? 'sin fecha' }}
                                        @if(!empty($passkey->TRANSPORTS))
                                            · {{ implode(', ', $passkey->TRANSPORTS) }}
                                        @endif
                                    </span>
                                </div>
                                <form method="POST" action="{{ route('webauthn.credentials.destroy', $passkey) }}" onsubmit="return confirm('Deseas eliminar el acceso biometrico de este dispositivo?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-trash-alt mr-1"></i>
                                        Eliminar
                                    </button>
                                </form>
                            </article>
                        @endforeach
                    @endif
                </div>
            </div>
        </section>

        <section class="card card-outline card-primary">
            <div class="card-body">
                <h2 class="profile-section-title mb-2">Preguntas de seguridad</h2>
                <p class="profile-section-copy">Estas preguntas siguen activas en tu cuenta. Por seguridad, las respuestas no se muestran.</p>

                @if($preguntas->isEmpty())
                    <p class="text-muted mb-0">No tienes preguntas de seguridad configuradas.</p>
                @else
                    <div class="security-questions-list">
                        @foreach($preguntas as $p)
                            <div class="security-question-item">
                                <i class="fas fa-shield-alt"></i>
                                <span>{{ $p->pregunta->TEXTO_PREGUNTA ?? 'Pregunta no disponible' }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
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

    .passkey-panel {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.25rem;
    }

    .passkey-count-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.55rem 0.8rem;
        border-radius: 999px;
        background: rgba(37, 99, 235, 0.08);
        color: #1d4ed8;
        font-weight: 700;
    }

    .passkey-actions {
        display: grid;
        gap: 0.75rem;
        justify-items: end;
        min-width: min(100%, 280px);
    }

    .passkey-message {
        display: none;
        width: 100%;
        padding: 0.65rem 0.8rem;
        border-radius: 0.75rem;
        font-size: 0.92rem;
        font-weight: 600;
    }

    .passkey-message.is-info {
        display: block;
        background: rgba(219, 234, 254, 0.9);
        color: #1d4ed8;
    }

    .passkey-message.is-success {
        display: block;
        background: rgba(220, 252, 231, 0.9);
        color: #166534;
    }

    .passkey-message.is-error {
        display: block;
        background: rgba(254, 226, 226, 0.95);
        color: #dc2626;
    }

    .passkey-list {
        display: grid;
        gap: 0.8rem;
        margin-top: 1.2rem;
    }

    .passkey-empty,
    .passkey-device {
        display: flex;
        align-items: center;
        gap: 0.9rem;
        padding: 0.95rem 1rem;
        border-radius: 0.9rem;
        border: 1px solid rgba(191, 219, 254, 0.75);
        background: rgba(248, 250, 255, 0.95);
    }

    .passkey-empty {
        color: #5d7596;
        font-weight: 600;
    }

    .passkey-device__icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.4rem;
        height: 2.4rem;
        flex-shrink: 0;
        border-radius: 999px;
        background: rgba(37, 99, 235, 0.1);
        color: #1d4ed8;
    }

    .passkey-device__body {
        display: grid;
        gap: 0.2rem;
        min-width: 0;
        flex: 1;
    }

    .passkey-device__body strong {
        color: #0f172a;
        line-height: 1.25;
        overflow-wrap: anywhere;
    }

    .passkey-device__body span {
        color: #64748b;
        font-size: 0.9rem;
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

    html[data-theme='dark'] .profile-section-title {
        color: #e5eefc;
    }

    html[data-theme='dark'] .profile-section-copy {
        color: #9fb1cb;
    }

    html[data-theme='dark'] .profile-summary-badge {
        background: rgba(37, 99, 235, 0.16);
        color: #93c5fd;
    }

    html[data-theme='dark'] .profile-info-card {
        border-color: rgba(96, 165, 250, 0.16);
        background: linear-gradient(180deg, rgba(15, 23, 42, 0.98), rgba(11, 18, 32, 0.96));
        box-shadow: inset 0 1px 0 rgba(147, 197, 253, 0.05);
    }

    html[data-theme='dark'] .profile-info-label {
        color: #8da5c8;
    }

    html[data-theme='dark'] .profile-info-value {
        color: #e5eefc;
    }

    html[data-theme='dark'] .passkey-count-badge {
        background: rgba(37, 99, 235, 0.16);
        color: #93c5fd;
    }

    html[data-theme='dark'] .passkey-message.is-info {
        background: rgba(30, 64, 175, 0.28);
        color: #bfdbfe;
    }

    html[data-theme='dark'] .passkey-message.is-success {
        background: rgba(20, 83, 45, 0.32);
        color: #bbf7d0;
    }

    html[data-theme='dark'] .passkey-message.is-error {
        background: rgba(127, 29, 29, 0.3);
        color: #fecaca;
    }

    html[data-theme='dark'] .passkey-empty,
    html[data-theme='dark'] .passkey-device {
        border-color: rgba(96, 165, 250, 0.16);
        background: linear-gradient(180deg, rgba(15, 23, 42, 0.98), rgba(11, 18, 32, 0.96));
        box-shadow: inset 0 1px 0 rgba(147, 197, 253, 0.05);
    }

    html[data-theme='dark'] .passkey-empty {
        color: #9fb1cb;
    }

    html[data-theme='dark'] .passkey-device__icon {
        background: rgba(37, 99, 235, 0.18);
        color: #93c5fd;
    }

    html[data-theme='dark'] .passkey-device__body strong {
        color: #e5eefc;
    }

    html[data-theme='dark'] .passkey-device__body span {
        color: #9fb1cb;
    }

    html[data-theme='dark'] .password-guidance-card {
        border-color: rgba(96, 165, 250, 0.16);
        background: linear-gradient(180deg, rgba(15, 23, 42, 0.98), rgba(11, 18, 32, 0.96));
        box-shadow: inset 0 1px 0 rgba(147, 197, 253, 0.05);
    }

    html[data-theme='dark'] .password-guidance-card__header h3 {
        color: #e5eefc;
    }

    html[data-theme='dark'] .password-guidance-copy {
        color: #9fb1cb;
    }

    html[data-theme='dark'] .password-rule {
        border-color: rgba(96, 165, 250, 0.14);
        background: rgba(15, 23, 42, 0.9);
        color: #c7d5eb;
    }

    html[data-theme='dark'] .password-rule.is-pending {
        color: #9fb1cb;
    }

    html[data-theme='dark'] .password-rule.is-pending .password-rule__icon {
        color: #7f93b4;
    }

    html[data-theme='dark'] .password-rule.is-valid {
        border-color: rgba(74, 222, 128, 0.32);
        background: rgba(20, 83, 45, 0.28);
        color: #bbf7d0;
    }

    html[data-theme='dark'] .password-rule.is-invalid {
        border-color: rgba(248, 113, 113, 0.3);
        background: rgba(127, 29, 29, 0.28);
        color: #fecaca;
    }

    html[data-theme='dark'] .password-match-hint.is-pending {
        background: rgba(51, 65, 85, 0.72);
        color: #cbd5e1;
    }

    html[data-theme='dark'] .password-match-hint.is-valid {
        background: rgba(20, 83, 45, 0.32);
        color: #bbf7d0;
    }

    html[data-theme='dark'] .password-match-hint.is-invalid {
        background: rgba(127, 29, 29, 0.3);
        color: #fecaca;
    }

    html[data-theme='dark'] .security-question-item {
        border-color: rgba(96, 165, 250, 0.16);
        background: linear-gradient(180deg, rgba(15, 23, 42, 0.98), rgba(11, 18, 32, 0.96));
        color: #cfe0ff;
        box-shadow: inset 0 1px 0 rgba(147, 197, 253, 0.05);
    }

    html[data-theme='dark'] .security-question-item i {
        color: #60a5fa;
    }

    @media (max-width: 991.98px) {
        .profile-summary-grid,
        .profile-security-layout {
            grid-template-columns: 1fr;
        }

        .passkey-panel {
            flex-direction: column;
            align-items: flex-start;
        }

        .passkey-actions {
            justify-items: stretch;
            width: 100%;
        }

        .passkey-device {
            align-items: flex-start;
            flex-direction: column;
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
    const button = document.getElementById('passkey-register-button');
    const message = document.getElementById('passkey-message');
    const passkeyTableReady = @json($passkeyTableReady);

    if (!button || !message) {
        return;
    }

    function showMessage(text, type = 'info') {
        message.textContent = text;
        message.className = `passkey-message is-${type}`;
    }

    if (!passkeyTableReady) {
        button.disabled = true;
        return;
    }

    function isInvalidWebAuthnHost(hostname) {
        return /^(?:\d{1,3}\.){3}\d{1,3}$/.test(hostname) || hostname.includes(':');
    }

    if (isInvalidWebAuthnHost(window.location.hostname)) {
        button.disabled = true;
        showMessage('Para activar biometria en pruebas locales, abre el sistema como http://localhost:8000 en lugar de 127.0.0.1.', 'error');
        return;
    }

    if (!window.PublicKeyCredential) {
        button.disabled = true;
        showMessage('Este navegador no soporta inicio con biometria.', 'error');
        return;
    }

    function base64UrlToBuffer(value) {
        const base64 = value.replace(/-/g, '+').replace(/_/g, '/');
        const padded = base64.padEnd(base64.length + ((4 - base64.length % 4) % 4), '=');
        const binary = atob(padded);
        const bytes = new Uint8Array(binary.length);

        for (let i = 0; i < binary.length; i += 1) {
            bytes[i] = binary.charCodeAt(i);
        }

        return bytes.buffer;
    }

    function bufferToBase64Url(buffer) {
        const bytes = new Uint8Array(buffer);
        let binary = '';

        bytes.forEach(byte => {
            binary += String.fromCharCode(byte);
        });

        return btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/g, '');
    }

    async function postJson(url, body = {}) {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify(body),
        });
        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            throw new Error(data.message || 'No se pudo completar la activacion biometrica.');
        }

        return data;
    }

    button.addEventListener('click', async () => {
        button.disabled = true;
        showMessage('Solicitando verificacion del dispositivo...');

        try {
            const optionsResponse = await postJson('{{ route('webauthn.register-options') }}');
            const publicKey = optionsResponse.publicKey;

            publicKey.challenge = base64UrlToBuffer(publicKey.challenge);
            publicKey.user.id = base64UrlToBuffer(publicKey.user.id);
            publicKey.excludeCredentials = (publicKey.excludeCredentials || []).map(credential => ({
                ...credential,
                id: base64UrlToBuffer(credential.id),
            }));

            const credential = await navigator.credentials.create({ publicKey });

            await postJson('{{ route('webauthn.register') }}', {
                id: credential.id,
                rawId: bufferToBase64Url(credential.rawId),
                type: credential.type,
                response: {
                    clientDataJSON: bufferToBase64Url(credential.response.clientDataJSON),
                    attestationObject: bufferToBase64Url(credential.response.attestationObject),
                    transports: typeof credential.response.getTransports === 'function'
                        ? credential.response.getTransports()
                        : ['internal'],
                },
            });

            showMessage('Inicio con biometria activado en este dispositivo.', 'success');
        } catch (error) {
            showMessage(error.message || 'No se pudo activar la biometria.', 'error');
        } finally {
            button.disabled = false;
        }
    });
})();
</script>

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

    function setState(element, valid, hasValue) {
        element.classList.remove('is-pending', 'is-valid', 'is-invalid');

        if (!hasValue) {
            element.classList.add('is-pending');
            element.querySelector('.password-rule__icon i').className = 'fas fa-circle';
            return;
        }

        if (valid) {
            element.classList.add('is-valid');
            element.querySelector('.password-rule__icon i').className = 'fas fa-check-circle';
            return;
        }

        element.classList.add('is-invalid');
        element.querySelector('.password-rule__icon i').className = 'fas fa-times-circle';
    }

    function updateStrength(completed, hasValue) {
        strengthBadge.className = 'password-strength-badge';

        if (!hasValue) {
            strengthBadge.classList.add('strength-empty');
            strengthBadge.textContent = 'Completa los campos';
            return;
        }

        if (completed <= 2) {
            strengthBadge.classList.add('strength-low');
            strengthBadge.textContent = 'Seguridad baja';
            return;
        }

        if (completed <= 4) {
            strengthBadge.classList.add('strength-medium');
            strengthBadge.textContent = 'Buen avance';
            return;
        }

        strengthBadge.classList.add('strength-high');
        strengthBadge.textContent = 'Lista para usar';
    }

    function updateMatch(passwordValue, confirmValue) {
        matchMsg.className = 'password-match-hint';

        if (!confirmValue.length) {
            matchMsg.classList.add('is-pending');
            matchMsg.innerHTML = '<i class="fas fa-circle-notch mr-2"></i>Esperando confirmacion de la nueva contrasena.';
            return;
        }

        if (passwordValue === confirmValue) {
            matchMsg.classList.add('is-valid');
            matchMsg.innerHTML = '<i class="fas fa-check-circle mr-2"></i>La confirmacion coincide correctamente.';
            return;
        }

        matchMsg.classList.add('is-invalid');
        matchMsg.innerHTML = '<i class="fas fa-times-circle mr-2"></i>La confirmacion aun no coincide.';
    }

    function refresh() {
        const value = pass.value || '';
        const confirmValue = conf.value || '';
        const entries = Object.entries(rules);
        let completed = 0;

        entries.forEach(([key, validator]) => {
            const element = document.querySelector(`[data-rule="${key}"]`);
            const valid = validator(value);

            if (valid) {
                completed += 1;
            }

            if (element) {
                setState(element, valid, value.length > 0);
            }
        });

        updateStrength(completed, value.length > 0);
        updateMatch(value, confirmValue);
    }

    pass.addEventListener('input', refresh);
    conf.addEventListener('input', refresh);
    refresh();
})();
</script>
@stop
