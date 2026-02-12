@extends('adminlte::page')

@section('title', 'Editar usuario')

@section('content_header')
  <h1>Editar usuario</h1>
@stop

@section('content')
  @if(session('success')) <x-adminlte-alert theme="success" title="OK" dismissable>{{ session('success') }}</x-adminlte-alert> @endif
  @if ($errors->any())
    <x-adminlte-alert theme="danger" title="Errores" dismissable>
      <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </x-adminlte-alert>
  @endif

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('seguridad.usuarios.update', $u->COD_USUARIO) }}">
        @csrf @method('PUT')

        <div class="form-group">
          <label>Persona</label>
          <select name="FK_COD_PERSONA" class="form-control" required>
            @foreach($personas as $p)
              <option value="{{ $p->COD_PERSONA }}" @selected(old('FK_COD_PERSONA', $u->FK_COD_PERSONA)==$p->COD_PERSONA)>{{ $p->nombre }}</option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <label>Usuario</label>
          <input name="USR_USUARIO" class="form-control" value="{{ old('USR_USUARIO', $u->USR_USUARIO) }}" required>
        </div>

        <div class="form-group">
          <label>Rol</label>
          <select name="FK_COD_ROL" class="form-control" required>
            @foreach($roles as $r)
              <option value="{{ $r->COD_ROL }}" @selected(old('FK_COD_ROL', $u->FK_COD_ROL)==$r->COD_ROL)>{{ $r->NOM_ROL }}</option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <label>Estado</label>
          <select name="ESTADO_USUARIO" class="form-control" required>
            @foreach($estados as $e)
              <option value="{{ $e->id }}" @selected(old('ESTADO_USUARIO', $u->ESTADO_USUARIO)==$e->id)>{{ $e->txt }}</option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <div class="d-flex justify-content-between align-items-center">
            <label for="password">Nueva contraseña (opcional)</label>
            <button type="button" class="btn btn-sm btn-outline-primary" id="btn-generate-password">Generar segura</button>
          </div>
          <input type="password" id="password" name="password" class="form-control" placeholder="Dejar vacío para no cambiar">
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
          <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Repetir contraseña nueva">
          <small id="match-msg" class="text-danger">La confirmación debe coincidir.</small>
        </div>

        <a href="{{ route('seguridad.usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
        <button class="btn btn-primary">Actualizar</button>
      </form>
    </div>
  </div>

  @if(session('password_updated_modal'))
    <div class="modal fade" id="passwordUpdatedModal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header bg-success">
            <h5 class="modal-title">Contraseña actualizada</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          </div>
          <div class="modal-body">
            <p>Se actualizó la contraseña del usuario correctamente.</p>
            <p class="mb-1"><strong>Usuario:</strong> {{ session('password_updated_modal.usuario') }}</p>
            <p class="mb-2"><strong>Nueva contraseña:</strong></p>
            <div class="input-group">
              <input type="text" readonly id="newPasswordText" class="form-control" value="{{ session('password_updated_modal.password') }}">
              <div class="input-group-append">
                <button type="button" id="copyPasswordBtn" class="btn btn-outline-primary">Copiar</button>
              </div>
            </div>
            <small id="copyResult" class="text-success d-none">Copiada al portapapeles.</small>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Aceptar</button>
          </div>
        </div>
      </div>
    </div>
  @endif
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
      msg.textContent = (conf.value.length === 0 && val.length === 0) ? 'La confirmación debe coincidir.' : (match ? 'Confirmación correcta.' : 'La confirmación debe coincidir.');
      paint(msg, match || (conf.value.length === 0 && val.length === 0));
  }

  function generateSecurePassword() {
      const upper = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
      const lower = 'abcdefghijkmnopqrstuvwxyz';
      const numbers = '23456789';
      const symbols = '!@#$%^&*()-_=+[]{}';
      const all = upper + lower + numbers + symbols;

      const chars = [
          upper[Math.floor(Math.random() * upper.length)],
          lower[Math.floor(Math.random() * lower.length)],
          numbers[Math.floor(Math.random() * numbers.length)],
          symbols[Math.floor(Math.random() * symbols.length)],
      ];

      while (chars.length < 14) {
          chars.push(all[Math.floor(Math.random() * all.length)]);
      }

      pass.value = chars.sort(() => Math.random() - 0.5).join('');
      conf.value = pass.value;
      updateRules();
  }

  pass?.addEventListener('input', updateRules);
  conf?.addEventListener('input', updateRules);
  document.getElementById('btn-generate-password')?.addEventListener('click', generateSecurePassword);
  updateRules();

  @if(session('password_updated_modal'))
    $('#passwordUpdatedModal').modal('show');

    document.getElementById('copyPasswordBtn')?.addEventListener('click', async () => {
      const input = document.getElementById('newPasswordText');
      input.select();
      try {
        await navigator.clipboard.writeText(input.value);
      } catch (_) {
        document.execCommand('copy');
      }
      document.getElementById('copyResult').classList.remove('d-none');
    });
  @endif
})();
</script>
@stop
