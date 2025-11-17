<x-guest-layout>
  <div class="min-h-screen bg-slate-50 py-10 px-4">
    <div class="max-w-5xl mx-auto bg-white shadow-sm ring-1 ring-slate-200 rounded-2xl overflow-hidden">
      <div class="bg-cyan-600 text-white px-6 py-4 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-xl font-semibold">Registro de paciente</h1>
          <p class="text-sm text-cyan-100">Completa el formulario para crear tu acceso al portal.</p>
        </div>
        @if ($doctorInfo)
          <div class="mt-3 md:mt-0 bg-white/15 px-4 py-2 rounded-md text-sm">
            Registrado con: <span class="font-semibold">{{ $doctorInfo['nombre'] }}</span>
          </div>
        @endif
      </div>

      <form method="POST" action="{{ route('register') }}" class="p-6 md:p-8 space-y-6">
        @csrf
        <input type="hidden" name="doctor" value="{{ request('doctor') }}">
        <input type="hidden" name="doctor_id" value="{{ request('doctor_id', $doctorInfo['persona_id'] ?? '') }}">

        <div class="grid gap-4 md:grid-cols-2">
          <div>
            <x-input-label for="PRIMER_NOMBRE" :value="__('Nombres')" />
            <x-text-input id="PRIMER_NOMBRE" class="block mt-1 w-full" type="text" name="PRIMER_NOMBRE"
                          :value="old('PRIMER_NOMBRE')" placeholder="Ej. Ana María" required autofocus />
            <x-input-error :messages="($errors->register ?? $errors)->get('PRIMER_NOMBRE')" class="mt-2" />
          </div>
          <div>
            <x-input-label for="PRIMER_APELLIDO" :value="__('Apellidos')" />
            <x-text-input id="PRIMER_APELLIDO" class="block mt-1 w-full" type="text" name="PRIMER_APELLIDO"
                          :value="old('PRIMER_APELLIDO')" placeholder="Ej. López" required />
            <x-input-error :messages="($errors->register ?? $errors)->get('PRIMER_APELLIDO')" class="mt-2" />
          </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
          <div>
            <x-input-label for="TIPO_GENERO" :value="__('Género')" />
            <select id="TIPO_GENERO" name="TIPO_GENERO" class="mt-1 block w-full rounded-md border-slate-300" required>
              <option value="" disabled {{ old('TIPO_GENERO') ? '' : 'selected' }}>Seleccione...</option>
              <option value="1" {{ old('TIPO_GENERO')=='1' ? 'selected' : '' }}>Masculino</option>
              <option value="2" {{ old('TIPO_GENERO')=='2' ? 'selected' : '' }}>Femenino</option>
              <option value="3" {{ old('TIPO_GENERO')=='3' ? 'selected' : '' }}>Otro / Prefiero no decir</option>
            </select>
            <x-input-error :messages="($errors->register ?? $errors)->get('TIPO_GENERO')" class="mt-2" />
          </div>
          <div class="md:col-span-2">
            <x-input-label for="NUM_TELEFONO" :value="__('Teléfono')" />
            <x-text-input id="NUM_TELEFONO" class="block mt-1 w-full" type="text" name="NUM_TELEFONO"
                          :value="old('NUM_TELEFONO')" placeholder="99991234" />
            <p class="text-[11px] text-slate-500 mt-1">Solo números, 8-10 dígitos.</p>
            <x-input-error :messages="($errors->register ?? $errors)->get('NUM_TELEFONO')" class="mt-2" />
          </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
          <div>
            <x-input-label for="CIUDAD" :value="__('Ciudad')" />
            <x-text-input id="CIUDAD" class="block mt-1 w-full" type="text" name="CIUDAD"
                          :value="old('CIUDAD')" placeholder="Ej. San Pedro Sula" />
            <x-input-error :messages="($errors->register ?? $errors)->get('CIUDAD')" class="mt-2" />
          </div>
          <div>
            <x-input-label for="COLONIA" :value="__('Colonia / Barrio')" />
            <x-text-input id="COLONIA" class="block mt-1 w-full" type="text" name="COLONIA"
                          :value="old('COLONIA')" placeholder="Ej. Barrio Abajo" />
            <x-input-error :messages="($errors->register ?? $errors)->get('COLONIA')" class="mt-2" />
          </div>
        </div>

        <div>
          <x-input-label for="REFERENCIA" :value="__('Dirección completa')" />
          <textarea id="REFERENCIA" name="REFERENCIA" rows="3"
                    class="mt-1 block w-full rounded-md border-slate-300 focus:border-cyan-500 focus:ring-cyan-500"
                    placeholder="Calle, número de casa, referencias">{{ old('REFERENCIA') }}</textarea>
          <x-input-error :messages="($errors->register ?? $errors)->get('REFERENCIA')" class="mt-2" />
        </div>

        <div>
          <x-input-label for="CORREO" :value="__('Correo electrónico')" />
          <x-text-input id="CORREO" class="block mt-1 w-full" type="email" name="CORREO"
                        :value="old('CORREO')" placeholder="tucorreo@ejemplo.com" required />
          <x-input-error :messages="($errors->register ?? $errors)->get('CORREO')" class="mt-2" />
        </div>

        <div id="username-pill" class="hidden bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-md px-4 py-3 text-sm">
          <div class="font-semibold mb-1">Usuario sugerido</div>
          <p class="mb-0">Se generará automáticamente como: <code id="username-preview" class="font-semibold"></code></p>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
          <div>
            <x-input-label for="password" :value="__('Contraseña')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password"
                          required autocomplete="new-password" />
            <x-input-error :messages="($errors->register ?? $errors)->get('password')" class="mt-2" />
          </div>
          <div>
            <x-input-label for="password_confirmation" :value="__('Confirmar contraseña')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                          name="password_confirmation" required autocomplete="new-password" />
          </div>
        </div>

        <div>
          <h2 class="text-sm font-semibold text-slate-700">Preguntas de seguridad</h2>
          <p class="text-xs text-slate-500 mb-3">Elige dos preguntas diferentes y guarda tus respuestas.</p>
          <div class="grid gap-4 md:grid-cols-2" x-data="{ q1: '{{ old('PREGUNTA_1') }}' || '', q2: '{{ old('PREGUNTA_2') }}' || '' }">
            <div>
              <x-input-label for="PREGUNTA_1" :value="__('Pregunta 1')" />
              <select id="PREGUNTA_1" name="PREGUNTA_1" class="mt-1 block w-full rounded-md border-slate-300" x-model="q1" required>
                <option value="" disabled {{ old('PREGUNTA_1') ? '' : 'selected' }}>Seleccione...</option>
                @foreach ($preguntasSeg as $p)
                  <option value="{{ $p->COD_PREGUNTA }}" {{ old('PREGUNTA_1') == $p->COD_PREGUNTA ? 'selected' : '' }}>
                    {{ $p->TEXTO_PREGUNTA }}
                  </option>
                @endforeach
              </select>
              <x-input-error :messages="($errors->register ?? $errors)->get('PREGUNTA_1')" class="mt-2" />
              <x-input-label for="RESPUESTA_1" :value="__('Respuesta 1')" class="mt-3" />
              <x-text-input id="RESPUESTA_1" name="RESPUESTA_1" type="text"
                            class="block mt-1 w-full" required :value="old('RESPUESTA_1')" />
              <x-input-error :messages="($errors->register ?? $errors)->get('RESPUESTA_1')" class="mt-2" />
            </div>
            <div>
              <x-input-label for="PREGUNTA_2" :value="__('Pregunta 2')" />
              <select id="PREGUNTA_2" name="PREGUNTA_2" class="mt-1 block w-full rounded-md border-slate-300" x-model="q2" required>
                <option value="" disabled {{ old('PREGUNTA_2') ? '' : 'selected' }}>Seleccione...</option>
                @foreach ($preguntasSeg as $p)
                  <option value="{{ $p->COD_PREGUNTA }}" {{ old('PREGUNTA_2') == $p->COD_PREGUNTA ? 'selected' : '' }}>
                    {{ $p->TEXTO_PREGUNTA }}
                  </option>
                @endforeach
              </select>
              <x-input-error :messages="($errors->register ?? $errors)->get('PREGUNTA_2')" class="mt-2" />
              <x-input-label for="RESPUESTA_2" :value="__('Respuesta 2')" class="mt-3" />
              <x-text-input id="RESPUESTA_2" name="RESPUESTA_2" type="text"
                            class="block mt-1 w-full" required :value="old('RESPUESTA_2')" />
              <x-input-error :messages="($errors->register ?? $errors)->get('RESPUESTA_2')" class="mt-2" />
            </div>
          </div>
        </div>

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <a href="{{ route('login') }}" class="text-sm text-slate-600 hover:text-slate-900 underline">¿Ya tienes cuenta? Inicia sesión</a>
          <x-primary-button class="px-6">Registrarme</x-primary-button>
        </div>
      </form>
    </div>
  </div>

  <script>
    (function () {
      const maxLen = 50;
      const nombre = document.getElementById('PRIMER_NOMBRE');
      const apellido = document.getElementById('PRIMER_APELLIDO');
      const pill = document.getElementById('username-pill');
      const preview = document.getElementById('username-preview');

      function strip(str) {
        return (str || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '');
      }

      function buildUser(nom, ape) {
        const first = (nom || '').trim().charAt(0);
        const last = (ape || '').trim().replace(/\s+/g, '');
        let base = (first + last).toLowerCase();
        base = strip(base).replace(/[^a-z0-9]/g, '');
        if (!base) base = 'paciente';
        return base.slice(0, maxLen);
      }

      function update() {
        const user = buildUser(nombre?.value, apellido?.value);
        if ((nombre?.value || '').trim() || (apellido?.value || '').trim()) {
          pill.classList.remove('hidden');
          preview.textContent = user;
        } else {
          pill.classList.add('hidden');
          preview.textContent = '';
        }
      }

      ['input', 'change'].forEach(evt => {
        nombre?.addEventListener(evt, update);
        apellido?.addEventListener(evt, update);
      });

      update();
    })();
  </script>
</x-guest-layout>
