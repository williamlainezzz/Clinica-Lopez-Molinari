<div x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4">
  <div class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm" @click="showRegister=false"></div>

  <div x-transition class="modal-panel modal-card relative w-full max-w-5xl overflow-hidden">
    <style>
      .register-password-input::-ms-reveal,
      .register-password-input::-ms-clear {
        display: none;
      }
    </style>

    <div class="border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white px-6 py-5">
      <div class="flex items-start justify-between gap-4">
        <div>
          <p class="section-kicker">Nuevo registro</p>
          <h3 class="mt-2 text-2xl font-bold text-slate-900">Crear cuenta</h3>
          <p class="mt-2 text-sm text-slate-600">Completa solo la informacion esencial para ingresar al sistema de la clinica.</p>
        </div>
        <button class="rounded-xl p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700" @click="showRegister=false" aria-label="Cerrar">x</button>
      </div>
    </div>

    <div class="max-h-[85vh] overflow-y-auto p-6">
      <form id="welcome-register-form" method="POST" action="{{ route('register') }}" novalidate class="space-y-6">
        @csrf

        <input type="hidden" id="PRIMER_NOMBRE" name="PRIMER_NOMBRE" value="{{ old('PRIMER_NOMBRE') }}">
        <input type="hidden" id="SEGUNDO_NOMBRE" name="SEGUNDO_NOMBRE" value="{{ old('SEGUNDO_NOMBRE') }}">
        <input type="hidden" id="PRIMER_APELLIDO" name="PRIMER_APELLIDO" value="{{ old('PRIMER_APELLIDO') }}">
        <input type="hidden" id="SEGUNDO_APELLIDO" name="SEGUNDO_APELLIDO" value="{{ old('SEGUNDO_APELLIDO') }}">
        <input type="hidden" name="DEPARTAMENTO" value="{{ old('DEPARTAMENTO') }}">
        <input type="hidden" name="MUNICIPIO" value="{{ old('MUNICIPIO') }}">
        <input type="hidden" name="COLONIA" value="{{ old('COLONIA') }}">

        <section class="rounded-3xl border border-slate-200 bg-white px-5 py-5 shadow-sm">
          <div class="mb-4">
            <p class="section-kicker">Paso 1</p>
            <h4 class="mt-2 text-lg font-bold text-slate-900">Datos personales</h4>
            <p class="mt-1 text-sm text-slate-600">Haremos el registro con menos campos para que el proceso sea mas rapido y claro.</p>
          </div>

          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <x-input-label for="NOMBRES_COMPLETOS" :value="__('Nombres')" />
              <x-text-input
                id="NOMBRES_COMPLETOS"
                class="block mt-1 w-full"
                type="text"
                :value="trim(collect([old('PRIMER_NOMBRE'), old('SEGUNDO_NOMBRE')])->filter()->implode(' '))"
                required
                autofocus
              />
              @if($errors->register->has('PRIMER_NOMBRE') || $errors->register->has('SEGUNDO_NOMBRE'))
                <x-input-error :messages="array_merge($errors->register->get('PRIMER_NOMBRE'), $errors->register->get('SEGUNDO_NOMBRE'))" class="mt-2" />
              @endif
            </div>

            <div>
              <x-input-label for="APELLIDOS_COMPLETOS" :value="__('Apellidos')" />
              <x-text-input
                id="APELLIDOS_COMPLETOS"
                class="block mt-1 w-full"
                type="text"
                :value="trim(collect([old('PRIMER_APELLIDO'), old('SEGUNDO_APELLIDO')])->filter()->implode(' '))"
                required
              />
              @if($errors->register->has('PRIMER_APELLIDO') || $errors->register->has('SEGUNDO_APELLIDO'))
                <x-input-error :messages="array_merge($errors->register->get('PRIMER_APELLIDO'), $errors->register->get('SEGUNDO_APELLIDO'))" class="mt-2" />
              @endif
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
              <p class="mt-1 text-xs text-slate-500">Opcional. Solo numeros.</p>
              <x-input-error :messages="$errors->register->get('NUM_TELEFONO')" class="mt-2" />
            </div>
          </div>

          <div class="mt-4 grid gap-4 md:grid-cols-2">
            <div>
              <x-input-label for="CIUDAD" :value="__('Ciudad')" />
              <x-text-input id="CIUDAD" class="block mt-1 w-full" type="text" name="CIUDAD" :value="old('CIUDAD')" />
              <x-input-error :messages="$errors->register->get('CIUDAD')" class="mt-2" />
            </div>

            <div>
              <x-input-label for="CORREO" :value="__('Correo electronico')" />
              <x-text-input id="CORREO" type="email" name="CORREO" :value="old('CORREO')" required class="block mt-1 w-full {{ $errors->register->has('CORREO') || $errors->has('CORREO') ? 'is-invalid' : '' }}" />
              <x-input-error :messages="array_merge($errors->register->get('CORREO'), $errors->get('CORREO'))" class="mt-2" />
            </div>
          </div>

          <div class="mt-4">
            <x-input-label for="REFERENCIA" :value="__('Direccion / Referencia')" />
            <textarea id="REFERENCIA" name="REFERENCIA" rows="3" class="mt-1 block w-full rounded-md border-slate-300 focus:border-blue-500 focus:ring-blue-500">{{ old('REFERENCIA') }}</textarea>
            <x-input-error :messages="$errors->register->get('REFERENCIA')" class="mt-2" />
          </div>
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white px-5 py-5 shadow-sm">
          <div class="mb-4">
            <p class="section-kicker">Paso 2</p>
            <h4 class="mt-2 text-lg font-bold text-slate-900">Seguridad de la cuenta</h4>
            <p class="mt-1 text-sm text-slate-600">Estas preguntas nos ayudan a verificar tu identidad si luego necesitas recuperar tu contrasena.</p>
          </div>

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
              <div class="rounded-2xl border border-amber-200 bg-amber-50 px-3 py-3 text-sm text-amber-700">
                Las preguntas deben ser distintas.
              </div>
            </div>
          </div>

          <div class="mt-6 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-4 text-sm text-slate-700">
            <span class="block text-[11px] font-semibold uppercase tracking-[0.2em] text-blue-700">Usuario asignado</span>
            <p class="mt-2">
              Este sera tu usuario para iniciar sesion:
              <code id="username-preview-inline" class="font-semibold text-slate-900"></code>
            </p>
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
              get match()    { return this.pwd.length > 0 && this.pwd === this.confirm },
            }"
            class="mt-5 grid gap-4 md:grid-cols-[minmax(0,1.2fr)_minmax(280px,0.8fr)]"
          >
            <div class="space-y-4">
              <div>
                <x-input-label for="password" :value="__('Contrasena')" />
                <div class="relative">
                  <x-text-input id="password" name="password" x-bind:type="showPwd ? 'text' : 'password'" class="register-password-input block mt-1 w-full pr-10" required autocomplete="new-password" x-model.debounce.75ms="pwd" />
                  <button type="button" class="absolute inset-y-0 right-2 mt-1 flex items-center text-slate-500 hover:text-slate-700" @click="showPwd = !showPwd" :aria-label="showPwd ? 'Ocultar contrasena' : 'Mostrar contrasena'">
                    <svg x-show="!showPwd" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <svg x-show="showPwd" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3l18 18M10.58 10.58A3 3 0 0012 15a3 3 0 002.42-4.42M9.88 5.09A9.96 9.96 0 0112 5c4.477 0 8.268 2.943 9.542 7-.39 1.24-1.02 2.36-1.85 3.33M6.27 6.27C4.39 7.58 3.03 9.54 2.46 12c1.274 4.057 5.065 7 9.542 7a9.96 9.96 0 004.12-.87"/></svg>
                  </button>
                </div>
                <x-input-error :messages="$errors->register->get('password')" class="mt-2" />
              </div>

              <div>
                <x-input-label for="password_confirmation" :value="__('Confirmar contrasena')" />
                <div class="relative">
                  <x-text-input id="password_confirmation" name="password_confirmation" x-bind:type="showConfirm ? 'text' : 'password'" class="register-password-input block mt-1 w-full pr-10" required autocomplete="new-password" x-model.debounce.75ms="confirm" />
                  <button type="button" class="absolute inset-y-0 right-2 mt-1 flex items-center text-slate-500 hover:text-slate-700" @click="showConfirm = !showConfirm" :aria-label="showConfirm ? 'Ocultar confirmacion' : 'Mostrar confirmacion'">
                    <svg x-show="!showConfirm" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <svg x-show="showConfirm" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3l18 18M10.58 10.58A3 3 0 0012 15a3 3 0 002.42-4.42M9.88 5.09A9.96 9.96 0 0112 5c4.477 0 8.268 2.943 9.542 7-.39 1.24-1.02 2.36-1.85 3.33M6.27 6.27C4.39 7.58 3.03 9.54 2.46 12c1.274 4.057 5.065 7 9.542 7a9.96 9.96 0 004.12-.87"/></svg>
                  </button>
                </div>

                <div class="mt-2 text-sm" :class="match ? 'text-green-600' : 'text-red-600'">
                  <span class="font-semibold" x-text="match ? 'OK' : '•'"></span>
                  Las contrasenas deben coincidir
                </div>
                <x-input-error :messages="$errors->register->get('password_confirmation')" class="mt-2" />
              </div>
            </div>

            <aside class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
              <h5 class="font-semibold text-slate-900">Tu contrasena debe incluir</h5>
              <div class="mt-3 space-y-2 text-sm">
                <div :class="hasLen ? 'text-green-600' : 'text-red-600'"><span class="font-semibold" x-text="hasLen ? 'OK' : '•'"></span> Minimo 10 caracteres</div>
                <div :class="hasUpper ? 'text-green-600' : 'text-red-600'"><span class="font-semibold" x-text="hasUpper ? 'OK' : '•'"></span> Una letra mayuscula</div>
                <div :class="hasLower ? 'text-green-600' : 'text-red-600'"><span class="font-semibold" x-text="hasLower ? 'OK' : '•'"></span> Una letra minuscula</div>
                <div :class="hasDigit ? 'text-green-600' : 'text-red-600'"><span class="font-semibold" x-text="hasDigit ? 'OK' : '•'"></span> Un numero</div>
                <div :class="hasSym ? 'text-green-600' : 'text-red-600'"><span class="font-semibold" x-text="hasSym ? 'OK' : '•'"></span> Un simbolo</div>
              </div>
            </aside>
          </div>
        </section>

        <div class="sticky bottom-0 z-10 -mx-6 -mb-6 mt-6 border-t border-slate-200 bg-white/95 px-6 py-4 backdrop-blur">
          <div class="flex items-center justify-between gap-3">
            <button type="button" class="text-sm font-medium text-slate-600 transition hover:text-slate-900" @click="showRegister=false">
              Cancelar
            </button>
            <x-primary-button id="welcome-register-submit" class="px-6">Crear cuenta</x-primary-button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
