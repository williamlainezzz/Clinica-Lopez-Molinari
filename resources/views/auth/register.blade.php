<x-guest-layout>
  <div class="min-h-screen flex flex-col items-center">
    <div class="w-full max-w-4xl md:max-w-5xl mx-auto mt-8 mb-12">
      <div class="bg-white shadow-sm ring-1 ring-slate-200 rounded-xl">
        <form method="POST" action="{{ route('register') }}" class="p-6 md:p-8">
          @csrf

          {{-- Header: título (sin la pastilla aquí) --}}
          <div class="flex items-center justify-between mb-6">
            <h1 class="text-xl md:text-2xl font-semibold text-slate-800">Crear cuenta</h1>
          </div>

          {{-- ===== DATOS PERSONALES ===== --}}
          <h2 class="text-sm font-semibold text-slate-700 mb-3">Datos personales</h2>

          {{-- Nombres (2 columnas) --}}
          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <x-input-label for="PRIMER_NOMBRE" :value="__('Primer nombre')" />
              <x-text-input id="PRIMER_NOMBRE" class="block mt-1 w-full" type="text" name="PRIMER_NOMBRE"
                            :value="old('PRIMER_NOMBRE')" placeholder="Ej. Ana" required autofocus />
              <x-input-error :messages="$errors->get('PRIMER_NOMBRE')" class="mt-2" />
            </div>
            <div>
              <x-input-label for="SEGUNDO_NOMBRE" :value="__('Segundo nombre (opcional)')" />
              <x-text-input id="SEGUNDO_NOMBRE" class="block mt-1 w-full" type="text" name="SEGUNDO_NOMBRE"
                            :value="old('SEGUNDO_NOMBRE')" placeholder="Ej. María" />
              <x-input-error :messages="$errors->get('SEGUNDO_NOMBRE')" class="mt-2" />
            </div>
          </div>

          {{-- Apellidos (2 columnas) --}}
          <div class="mt-4 grid gap-4 md:grid-cols-2">
            <div>
              <x-input-label for="PRIMER_APELLIDO" :value="__('Primer apellido')" />
              <x-text-input id="PRIMER_APELLIDO" class="block mt-1 w-full" type="text" name="PRIMER_APELLIDO"
                            :value="old('PRIMER_APELLIDO')" placeholder="Ej. Rivera" required />
              <x-input-error :messages="$errors->get('PRIMER_APELLIDO')" class="mt-2" />
            </div>
            <div>
              <x-input-label for="SEGUNDO_APELLIDO" :value="__('Segundo apellido (opcional)')" />
              <x-text-input id="SEGUNDO_APELLIDO" class="block mt-1 w-full" type="text" name="SEGUNDO_APELLIDO"
                            :value="old('SEGUNDO_APELLIDO')" placeholder="Ej. López" />
              <x-input-error :messages="$errors->get('SEGUNDO_APELLIDO')" class="mt-2" />
            </div>
          </div>

          {{-- Género + Teléfono (2 columnas) --}}
          <div class="mt-4 grid gap-4 md:grid-cols-2">
            <div>
              <x-input-label for="TIPO_GENERO" :value="__('Género')" />
              <select id="TIPO_GENERO" name="TIPO_GENERO" class="block mt-1 w-full rounded-md border-slate-300" required>
                <option value="" disabled {{ old('TIPO_GENERO') ? '' : 'selected' }}>Seleccione...</option>
                <option value="1" {{ old('TIPO_GENERO')=='1' ? 'selected' : '' }}>Masculino</option>
                <option value="2" {{ old('TIPO_GENERO')=='2' ? 'selected' : '' }}>Femenino</option>
                <option value="3" {{ old('TIPO_GENERO')=='3' ? 'selected' : '' }}>Otro / Prefiero no decir</option>
              </select>
              <x-input-error :messages="$errors->get('TIPO_GENERO')" class="mt-2" />
            </div>
            <div>
              <x-input-label for="NUM_TELEFONO" :value="__('Teléfono')" />
              <x-text-input id="NUM_TELEFONO" class="block mt-1 w-full" type="text" name="NUM_TELEFONO"
                            :value="old('NUM_TELEFONO')" placeholder="99991234" />
              <p class="text-[11px] text-slate-500 mt-1">Solo números. (8–10 dígitos).</p>
              <x-input-error :messages="$errors->get('NUM_TELEFONO')" class="mt-2" />
            </div>
          </div>

          {{-- Departamento + Municipio (2 columnas) --}}
          <div class="mt-4 grid gap-4 md:grid-cols-2">
            <div>
              <x-input-label for="DEPARTAMENTO" :value="__('Departamento')" />
              <x-text-input id="DEPARTAMENTO" class="block mt-1 w-full" type="text" name="DEPARTAMENTO"
                            :value="old('DEPARTAMENTO')" placeholder="Ej. Cortés" />
              <x-input-error :messages="$errors->get('DEPARTAMENTO')" class="mt-2" />
            </div>
            <div>
              <x-input-label for="MUNICIPIO" :value="__('Municipio')" />
              <x-text-input id="MUNICIPIO" class="block mt-1 w-full" type="text" name="MUNICIPIO"
                            :value="old('MUNICIPIO')" placeholder="Ej. San Pedro Sula" />
              <x-input-error :messages="$errors->get('MUNICIPIO')" class="mt-2" />
            </div>
          </div>

          {{-- Ciudad + Colonia (2 columnas) --}}
          <div class="mt-4 grid gap-4 md:grid-cols-2">
            <div>
              <x-input-label for="CIUDAD" :value="__('Ciudad')" />
              <x-text-input id="CIUDAD" class="block mt-1 w-full" type="text" name="CIUDAD"
                            :value="old('CIUDAD')" placeholder="Ej. San Pedro Sula" />
              <x-input-error :messages="$errors->get('CIUDAD')" class="mt-2" />
            </div>
            <div>
              <x-input-label for="COLONIA" :value="__('Colonia')" />
              <x-text-input id="COLONIA" class="block mt-1 w-full" type="text" name="COLONIA"
                            :value="old('COLONIA')" placeholder="Ej. Rivera Hernández" />
              <x-input-error :messages="$errors->get('COLONIA')" class="mt-2" />
            </div>
          </div>

          {{-- Dirección / Referencia (ancho) --}}
          <div class="mt-4">
            <x-input-label for="REFERENCIA" :value="__('Dirección / Referencia')" />
            <textarea id="REFERENCIA" name="REFERENCIA" rows="3"
                      class="mt-1 block w-full rounded-md border-slate-300 focus:border-cyan-500 focus:ring-cyan-500"
                      placeholder="Col. Centro, Calle 1 #123">{{ old('REFERENCIA') }}</textarea>
            <x-input-error :messages="$errors->get('REFERENCIA')" class="mt-2" />
          </div>

          {{-- ===== DATOS DE LA CUENTA ===== --}}
          <h2 class="text-sm font-semibold text-slate-700 mt-8 mb-3">Datos de la cuenta</h2>

          {{-- Correo (ancho) --}}
          <div>
            <x-input-label for="CORREO" :value="__('Correo electrónico')" />
            <x-text-input id="CORREO" class="block mt-1 w-full" type="email" name="CORREO"
                          :value="old('CORREO')" placeholder="tucorreo@ejemplo.com" required />
            <x-input-error :messages="$errors->get('CORREO')" class="mt-2" />
          </div>

          {{-- AVISO: Usuario autogenerado (movido aquí, antes de contraseña) --}}
          <div id="username-pill" class="hidden bg-amber-50 border border-amber-200 text-amber-800 rounded-md px-4 py-3 text-sm leading-5 mt-4">
            <div class="font-semibold mb-1">Este será tu usuario para iniciar sesión.</div>
            <div>
              <span class="mr-1">Anótalo y no lo olvides:</span>
              <code id="username-preview" class="px-2 py-0.5 rounded bg-white/70 border border-amber-200 font-semibold"></code>
            </div>
          </div>

          {{-- Password + Confirmación (2 columnas) --}}
          <div class="mt-4 grid gap-4 md:grid-cols-2">
            <div>
              <x-input-label for="password" :value="__('Contraseña')" />
              <x-text-input id="password" class="block mt-1 w-full" type="password" name="password"
                            required autocomplete="new-password" />
              <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            <div>
              <x-input-label for="password_confirmation" :value="__('Confirmar contraseña')" />
              <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                            name="password_confirmation" required autocomplete="new-password" />
            </div>
          </div>

          <div class="flex items-center justify-between mt-8">
            <a class="text-sm text-slate-600 hover:text-slate-900 underline" href="{{ route('login') }}">
              ¿Ya tienes cuenta? Inicia sesión
            </a>
            <x-primary-button class="px-5">Registrarme</x-primary-button>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- Script: genera y muestra el usuario en la nueva ubicación --}}
  <script>
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
        if ((n && n.trim()) || (a && a.trim())) {
          $pill.classList.remove('hidden');
          $out.textContent = u;
        } else {
          $pill.classList.add('hidden');
          $out.textContent = '';
        }
      }
      ['input','change'].forEach(evt => {
        $nombre?.addEventListener(evt, update);
        $apellido?.addEventListener(evt, update);
      });
      update();
    })();
  </script>
</x-guest-layout>
