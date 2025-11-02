<form id="formRegistroPersona" method="POST" action="{{ route('personas.store') }}">
  @csrf

  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="PRIMER_NOMBRE">Primer nombre</label>
      <input id="PRIMER_NOMBRE" type="text" class="form-control" name="PRIMER_NOMBRE" value="{{ old('PRIMER_NOMBRE') }}" required>
      <div class="invalid-feedback" data-field="PRIMER_NOMBRE"></div>
    </div>

    <div class="form-group col-md-6">
      <label for="SEGUNDO_NOMBRE">Segundo nombre</label>
      <input id="SEGUNDO_NOMBRE" type="text" class="form-control" name="SEGUNDO_NOMBRE" value="{{ old('SEGUNDO_NOMBRE') }}">
      <div class="invalid-feedback" data-field="SEGUNDO_NOMBRE"></div>
    </div>
  </div>

  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="PRIMER_APELLIDO">Primer apellido</label>
      <input id="PRIMER_APELLIDO" type="text" class="form-control" name="PRIMER_APELLIDO" value="{{ old('PRIMER_APELLIDO') }}" required>
      <div class="invalid-feedback" data-field="PRIMER_APELLIDO"></div>
    </div>

    <div class="form-group col-md-6">
      <label for="SEGUNDO_APELLIDO">Segundo apellido</label>
      <input id="SEGUNDO_APELLIDO" type="text" class="form-control" name="SEGUNDO_APELLIDO" value="{{ old('SEGUNDO_APELLIDO') }}">
      <div class="invalid-feedback" data-field="SEGUNDO_APELLIDO"></div>
    </div>
  </div>

  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="TIPO_GENERO">Género</label>
      <select id="TIPO_GENERO" name="TIPO_GENERO" class="form-control" required>
        <option value="" disabled {{ old('TIPO_GENERO') ? '' : 'selected' }}>Seleccione...</option>
        <option value="1" {{ old('TIPO_GENERO')=='1' ? 'selected' : '' }}>Masculino</option>
        <option value="2" {{ old('TIPO_GENERO')=='2' ? 'selected' : '' }}>Femenino</option>
        <option value="3" {{ old('TIPO_GENERO')=='3' ? 'selected' : '' }}>Otro / Prefiero no decir</option>
      </select>
      <div class="invalid-feedback" data-field="TIPO_GENERO"></div>
    </div>

    <div class="form-group col-md-6">
      <label for="NUM_TELEFONO">Teléfono</label>
      <input id="NUM_TELEFONO" type="text" class="form-control" name="NUM_TELEFONO" value="{{ old('NUM_TELEFONO') }}">
      <div class="invalid-feedback" data-field="NUM_TELEFONO"></div>
    </div>
  </div>

  <div class="form-group">
    <label for="REFERENCIA">Dirección / Referencia</label>
    <textarea id="REFERENCIA" name="REFERENCIA" rows="2" class="form-control">{{ old('REFERENCIA') }}</textarea>
    <div class="invalid-feedback" data-field="REFERENCIA"></div>
  </div>

  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="CORREO">Correo electrónico</label>
      <input id="CORREO" type="email" class="form-control" name="CORREO" value="{{ old('CORREO') }}" required>
      <div class="invalid-feedback" data-field="CORREO"></div>
    </div>

    <div class="form-group col-md-6">
      <label for="DEPARTAMENTO">Departamento</label>
      <input id="DEPARTAMENTO" type="text" class="form-control" name="DEPARTAMENTO" value="{{ old('DEPARTAMENTO') }}">
      <div class="invalid-feedback" data-field="DEPARTAMENTO"></div>
    </div>
  </div>

  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="MUNICIPIO">Municipio</label>
      <input id="MUNICIPIO" type="text" class="form-control" name="MUNICIPIO" value="{{ old('MUNICIPIO') }}">
      <div class="invalid-feedback" data-field="MUNICIPIO"></div>
    </div>
    <div class="form-group col-md-6">
      <label for="CIUDAD">Ciudad</label>
      <input id="CIUDAD" type="text" class="form-control" name="CIUDAD" value="{{ old('CIUDAD') }}">
      <div class="invalid-feedback" data-field="CIUDAD"></div>
    </div>
  </div>

  <div class="form-group mt-2">
    <label for="COLONIA">Colonia</label>
    <input id="COLONIA" type="text" class="form-control" name="COLONIA" value="{{ old('COLONIA') }}">
    <div class="invalid-feedback" data-field="COLONIA"></div>
  </div>

  {{-- Usuario autogenerado preview --}}
  <div id="username-pill" class="d-none bg-amber-50 border border-amber-200 text-amber-800 rounded-md px-4 py-3 text-sm leading-5 mb-3">
    <div class="font-semibold mb-1">Este será tu usuario para iniciar sesión.</div>
    <div>
      <span class="mr-1">Anótalo:</span>
      <code id="username-preview" class="px-2 py-0.5 rounded bg-white/70 border border-amber-200 font-semibold"></code>
    </div>
  </div>

  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="password">Contraseña</label>
      <input id="password" type="password" class="form-control" name="password" required>
      <div class="invalid-feedback" data-field="password"></div>
    </div>

    <div class="form-group col-md-6">
      <label for="password_confirmation">Confirmar contraseña</label>
      <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required>
    </div>
  </div>

  {{-- Estado (activo/inactivo) --}}
  <div class="form-group">
    <label for="ESTADO">Estado</label>
    <select id="ESTADO" name="ESTADO" class="form-control" required>
      <option value="Activo" selected>Activo</option>
      <option value="Inactivo">Inactivo</option>
    </select>
    <div class="invalid-feedback" data-field="ESTADO"></div>
  </div>
</form>

<script>
 // Lógica para generar y mostrar el usuario (misma que en register)
 (function () {
   const maxLen = 50;
   const $nombre = document.getElementById('PRIMER_NOMBRE');
   const $apellido = document.getElementById('PRIMER_APELLIDO');
   const $pill = document.getElementById('username-pill');
   const $out = document.getElementById('username-preview');

   function stripDiacritics(str) {
     return (str || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '');
   }
   function makeUsername(nombre, apellido) {
     const first = (nombre || '').trim().charAt(0);
     const last  = (apellido || '').trim().replace(/\s+/g, '');
     let base = (first + last).toLowerCase();
     base = stripDiacritics(base).replace(/[^a-z0-9]/g, '');
     if (!base) base = 'user';
     return base.slice(0, maxLen);
   }
   function update() {
     const n = $nombre?.value || '';
     const a = $apellido?.value || '';
     const u = makeUsername(n, a);
     // LOG: mostrar nombres y username generado para depuración
     console.log('[registro] nombre:', n, 'apellido:', a, 'username:', u);
     if ((n && n.trim()) || (a && a.trim())) {
       $pill.classList.remove('d-none');
       $out.textContent = u;
     } else {
       $pill.classList.add('d-none');
       $out.textContent = '';
     }
   }
   ['input','change'].forEach(evt => {
     $nombre?.addEventListener(evt, update);
     $apellido?.addEventListener(evt, update);
   });
   update();

  // Listener opcional al submit (solo para log; no previene envío)
  const $form = document.getElementById('formRegistroPersona');
  if ($form) {
    $form.addEventListener('submit', function (e) {
      try {
        const fd = new FormData($form);
        const obj = {};
        for (const [k, v] of fd.entries()) obj[k] = v;
        console.log('[registro] submit form data:', obj);
      } catch (err) {
        console.log('[registro] error al leer FormData', err);
      }
      // no preventDefault -> comportamiento original intacto
    });
  }
 })();
</script>
