<div x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4">
  <div class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm" @click="showRegister=false"></div>

  <div x-transition class="modal-panel modal-card relative w-full max-w-6xl overflow-hidden">
    <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-5">
      <div><p class="section-kicker">Nuevo registro</p><h3 class="mt-2 text-xl font-bold text-slate-900">Crear cuenta</h3></div>
      <button class="rounded-xl p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700" @click="showRegister=false" aria-label="Cerrar">x</button>
    </div>

    <div class="max-h-[85vh] overflow-y-auto p-6">
      <form method="POST" action="{{ route('register') }}" novalidate>
        @csrf

        <div class="mb-5 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
          <div><p class="section-kicker">Seccion 1</p><h2 class="mt-2 text-lg font-bold text-slate-900">Datos personales</h2><p class="mt-1 text-sm text-slate-600">Ingresa tu informacion principal para crear tu perfil dentro del sistema.</p></div>
          <div id="username-pill" class="hidden rounded-2xl border border-blue-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm">
            <span class="block text-[11px] font-semibold uppercase tracking-[0.2em] text-blue-700">Usuario asignado</span>
            <code id="username-preview" class="font-semibold"></code>
          </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
          <div>
            <x-input-label for="PRIMER_NOMBRE" :value="__('Primer nombre')" />
            <x-text-input id="PRIMER_NOMBRE" class="block mt-1 w-full" type="text" name="PRIMER_NOMBRE" :value="old('PRIMER_NOMBRE')" required autofocus />
            <x-input-error :messages="$errors->register->get('PRIMER_NOMBRE')" class="mt-2" />
          </div>
          <div>
            <x-input-label for="SEGUNDO_NOMBRE" :value="__('Segundo nombre (opcional)')" />
            <x-text-input id="SEGUNDO_NOMBRE" class="block mt-1 w-full" type="text" name="SEGUNDO_NOMBRE" :value="old('SEGUNDO_NOMBRE')" />
            <x-input-error :messages="$errors->register->get('SEGUNDO_NOMBRE')" class="mt-2" />
          </div>
        </div>

        <div class="mt-4 grid gap-4 md:grid-cols-2">
          <div>
            <x-input-label for="PRIMER_APELLIDO" :value="__('Primer apellido')" />
            <x-text-input id="PRIMER_APELLIDO" class="block mt-1 w-full" type="text" name="PRIMER_APELLIDO" :value="old('PRIMER_APELLIDO')" required />
            <x-input-error :messages="$errors->register->get('PRIMER_APELLIDO')" class="mt-2" />
          </div>
          <div>
            <x-input-label for="SEGUNDO_APELLIDO" :value="__('Segundo apellido (opcional)')" />
            <x-text-input id="SEGUNDO_APELLIDO" class="block mt-1 w-full" type="text" name="SEGUNDO_APELLIDO" :value="old('SEGUNDO_APELLIDO')" />
            <x-input-error :messages="$errors->register->get('SEGUNDO_APELLIDO')" class="mt-2" />
          </div>
        </div>

        <div class="mt-4 grid gap-4 md:grid-cols-2">
          <div>
            <x-input-label for="TIPO_GENERO" :value="__('Genero')" />
            <select id="TIPO_GENERO" name="TIPO_GENERO" class="block mt-1 w-full rounded-md border-slate-300" required>
              <option value="" disabled {{ old('TIPO_GENERO') ? '' : 'selected' }}>Seleccione...</option>
              <option value="1" {{ old('TIPO_GENERO')=='1' ? 'selected' : '' }}>Masculino</option>
              <option value="2" {{ old('TIPO_GENERO')=='2' ? 'selected' : '' }}>Femenino</option>
              <option value="3" {{ old('TIPO_GENERO')=='3' ? 'selected' : '' }}>Otro / Prefiero no decir</option>
            </select>
            <x-input-error :messages="$errors->register->get('TIPO_GENERO')" class="mt-2" />
          </div>
          <div>
            <x-input-label for="NUM_TELEFONO" :value="__('Telefono')" />
            <x-text-input id="NUM_TELEFONO" class="block mt-1 w-full" type="text" name="NUM_TELEFONO" :value="old('NUM_TELEFONO')" />
            <x-input-error :messages="$errors->register->get('NUM_TELEFONO')" class="mt-2" />
          </div>
        </div>

        <div class="mt-4 grid gap-4 md:grid-cols-2">
          <div>
            <x-input-label for="DEPARTAMENTO" :value="__('Departamento')" />
            <x-text-input id="DEPARTAMENTO" class="block mt-1 w-full" type="text" name="DEPARTAMENTO" :value="old('DEPARTAMENTO')" />
            <x-input-error :messages="$errors->register->get('DEPARTAMENTO')" class="mt-2" />
          </div>
          <div>
            <x-input-label for="MUNICIPIO" :value="__('Municipio')" />
            <x-text-input id="MUNICIPIO" class="block mt-1 w-full" type="text" name="MUNICIPIO" :value="old('MUNICIPIO')" />
            <x-input-error :messages="$errors->register->get('MUNICIPIO')" class="mt-2" />
          </div>
        </div>

        <div class="mt-4 grid gap-4 md:grid-cols-2">
          <div>
            <x-input-label for="CIUDAD" :value="__('Ciudad')" />
            <x-text-input id="CIUDAD" class="block mt-1 w-full" type="text" name="CIUDAD" :value="old('CIUDAD')" />
            <x-input-error :messages="$errors->register->get('CIUDAD')" class="mt-2" />
          </div>
          <div>
            <x-input-label for="COLONIA" :value="__('Colonia')" />
            <x-text-input id="COLONIA" class="block mt-1 w-full" type="text" name="COLONIA" :value="old('COLONIA')" />
            <x-input-error :messages="$errors->register->get('COLONIA')" class="mt-2" />
          </div>
        </div>

        <div class="mt-4">
          <x-input-label for="REFERENCIA" :value="__('Direccion / Referencia')" />
          <textarea id="REFERENCIA" name="REFERENCIA" rows="3" class="mt-1 block w-full rounded-md border-slate-300 focus:border-blue-500 focus:ring-blue-500">{{ old('REFERENCIA') }}</textarea>
          <x-input-error :messages="$errors->register->get('REFERENCIA')" class="mt-2" />
        </div>

        <div class="mt-6">
          <x-input-label for="CORREO" :value="__('Correo electronico')" />
          <x-text-input id="CORREO" type="email" name="CORREO" :value="old('CORREO')" required class="block mt-1 w-full {{ ($errors->register ?? $errors)->has('CORREO') ? 'is-invalid' : '' }}" />
          <x-input-error :messages="($errors->register ?? $errors)->get('CORREO')" class="mt-2" />
        </div>

        <h3 class="text-sm font-semibold text-slate-700 mt-8 mb-2">Preguntas de seguridad</h3>
        <p class="text-xs text-slate-600 mb-3">
          Elige dos preguntas y escribe tus respuestas. Se usaran para verificar tu identidad al restablecer la contrasena.
        </p>

        <div
          x-data="{
            q1: '{{ old('PREGUNTA_1') }}' || '',
            q2: '{{ old('PREGUNTA_2') }}' || '',
            same() { return this.q1 && this.q2 && this.q1 === this.q2; }
          }"
          class="grid gap-4 md:grid-cols-2"
        >
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
            <x-input-error :messages="$errors->register->get('PREGUNTA_1')" class="mt-2" />

            <x-input-label for="RESPUESTA_1" :value="__('Respuesta a la pregunta 1')" class="mt-3" />
            <x-text-input id="RESPUESTA_1" name="RESPUESTA_1" type="text" class="block mt-1 w-full" required :value="old('RESPUESTA_1')" />
            <x-input-error :messages="$errors->register->get('RESPUESTA_1')" class="mt-2" />
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
            <x-input-error :messages="$errors->register->get('PREGUNTA_2')" class="mt-2" />

            <x-input-label for="RESPUESTA_2" :value="__('Respuesta a la pregunta 2')" class="mt-3" />
            <x-text-input id="RESPUESTA_2" name="RESPUESTA_2" type="text" class="block mt-1 w-full" required :value="old('RESPUESTA_2')" />
            <x-input-error :messages="$errors->register->get('RESPUESTA_2')" class="mt-2" />
          </div>

          <div class="md:col-span-2" x-show="same()">
            <div class="rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-700">
              Las preguntas deben ser distintas.
            </div>
          </div>
        </div>

        <h3 class="text-sm font-semibold text-slate-700 mt-8 mb-2">Usuario asignado</h3>
        <div class="mb-3 text-xs text-slate-600">
          Este sera tu usuario para iniciar sesion: <code class="font-semibold" id="username-preview-inline"></code>
        </div>

        <div
          x-data="{
            pwd: '',
            confirm: '',
            showPwd: false,
            showConfirm: false,
            get hasLen()   { return this.pwd.length >= 10 },
            get hasUpper() { return /[A-Z]/.test(this.pwd) },
            get hasLower() { return /[a-z]/.test(this.pwd) },
            get hasDigit() { return /\d/.test(this.pwd) },
            get hasSym()   { return /[^A-Za-z0-9]/.test(this.pwd) },
            get match()    { return this.pwd.length>0 && this.pwd === this.confirm },
          }"
          class="space-y-4"
        >
          <div>
            <x-input-label for="password" :value="__('Contrasena')" />
            <div class="relative">
              <x-text-input id="password" name="password" x-bind:type="showPwd ? 'text' : 'password'" class="block mt-1 w-full pr-10" required autocomplete="new-password" x-model="pwd" />
              <button type="button" class="absolute inset-y-0 right-2 mt-1 flex items-center text-slate-500 hover:text-slate-700" @click="showPwd = !showPwd" :aria-label="showPwd ? 'Ocultar contrasena' : 'Mostrar contrasena'">
                <svg x-show="!showPwd" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <svg x-show="showPwd" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3l18 18M10.58 10.58A3 3 0 0012 15a3 3 0 002.42-4.42M9.88 5.09A9.96 9.96 0 0112 5c4.477 0 8.268 2.943 9.542 7-.39 1.24-1.02 2.36-1.85 3.33M6.27 6.27C4.39 7.58 3.03 9.54 2.46 12c1.274 4.057 5.065 7 9.542 7a9.96 9.96 0 004.12-.87"/></svg>
              </button>
            </div>

            <ul class="mt-3 grid gap-1 text-sm">
              <li :class="hasLen ? 'text-green-600' : 'text-red-600'"><span class="font-semibold" x-text="hasLen ? '✓' : '•'"></span> Minimo 10 caracteres</li>
              <li :class="(hasUpper && hasLower) ? 'text-green-600' : 'text-red-600'"><span class="font-semibold" x-text="(hasUpper && hasLower) ? '✓' : '•'"></span> Mayusculas y minusculas</li>
              <li :class="hasDigit ? 'text-green-600' : 'text-red-600'"><span class="font-semibold" x-text="hasDigit ? '✓' : '•'"></span> Al menos un numero</li>
              <li :class="hasSym ? 'text-green-600' : 'text-red-600'"><span class="font-semibold" x-text="hasSym ? '✓' : '•'"></span> Al menos un simbolo</li>
            </ul>
          </div>

          <div>
            <x-input-label for="password_confirmation" :value="__('Confirmar contrasena')" />
            <div class="relative">
              <x-text-input id="password_confirmation" name="password_confirmation" x-bind:type="showConfirm ? 'text' : 'password'" class="block mt-1 w-full pr-10" required autocomplete="new-password" x-model="confirm" />
              <button type="button" class="absolute inset-y-0 right-2 mt-1 flex items-center text-slate-500 hover:text-slate-700" @click="showConfirm = !showConfirm" :aria-label="showConfirm ? 'Ocultar confirmacion' : 'Mostrar confirmacion'">
                <svg x-show="!showConfirm" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <svg x-show="showConfirm" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3l18 18M10.58 10.58A3 3 0 0012 15a3 3 0 002.42-4.42M9.88 5.09A9.96 9.96 0 0112 5c4.477 0 8.268 2.943 9.542 7-.39 1.24-1.02 2.36-1.85 3.33M6.27 6.27C4.39 7.58 3.03 9.54 2.46 12c1.274 4.057 5.065 7 9.542 7a9.96 9.96 0 004.12-.87"/></svg>
              </button>
            </div>

            <div class="mt-1 text-sm" :class="match ? 'text-green-600' : 'text-red-600'">
              <span class="font-semibold" x-text="match ? '✓' : '•'"></span>
              Las contrasenas deben coincidir
            </div>

            <x-input-error :messages="$errors->register->get('password')" class="mt-2" />
            <x-input-error :messages="$errors->register->get('password_confirmation')" class="mt-2" />
          </div>
        </div>

        <div class="flex items-center justify-end mt-6">
          <x-primary-button class="px-5">Registrarme</x-primary-button>
        </div>
      </form>
    </div>
  </div>
</div>
