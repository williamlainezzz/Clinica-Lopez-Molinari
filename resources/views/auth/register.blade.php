<x-guest-layout>
  <div class="mx-auto max-w-5xl pb-8">
    <div class="overflow-hidden rounded-[36px] border border-blue-100/80 bg-white/95 shadow-[0_34px_90px_rgba(15,23,42,0.16)] backdrop-blur">
      <div class="relative overflow-hidden border-b border-blue-950/10 bg-[linear-gradient(135deg,_#081634_0%,_#0d2f72_38%,_#1f5cc4_76%,_#4c8fff_100%)] px-6 py-7 text-white md:px-8 md:py-8">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_rgba(255,255,255,0.22),_transparent_28%),linear-gradient(180deg,_rgba(255,255,255,0.02)_0%,_rgba(255,255,255,0)_100%)]"></div>
        <div class="absolute -left-16 top-0 h-44 w-44 rounded-full bg-cyan-300/10 blur-3xl"></div>
        <div class="absolute -right-8 bottom-0 h-40 w-40 rounded-full bg-blue-100/20 blur-3xl"></div>

        <div class="relative flex items-center gap-5">
          <div class="flex items-start gap-4 md:gap-5">
            <div class="rounded-[28px] border border-white/25 bg-white px-4 py-3 shadow-[0_18px_45px_rgba(8,22,52,0.28)]">
              <x-application-logo class="w-auto" />
            </div>

            <div class="pt-1">
              <p class="text-[11px] font-semibold uppercase tracking-[0.3em] text-blue-100/95">Bienvenido</p>
              <h1 class="mt-2 text-[2.35rem] font-black tracking-tight text-white md:text-[2.85rem]">Registro al sistema</h1>
              <p class="mt-2 max-w-2xl text-sm leading-6 text-blue-50/90">
                Nos alegra tenerte aqui. Completa tus datos para crear tu acceso y comenzar a usar el sistema de la clinica.
              </p>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-[linear-gradient(180deg,_rgba(219,234,254,0.42)_0%,_rgba(255,255,255,0.98)_14%,_rgba(255,255,255,1)_100%)] p-6 md:p-6">
        <form id="register-page-form" method="POST" action="{{ route('register') }}" novalidate class="register-form space-y-6">
          @csrf

          <input type="hidden" name="doctor" value="{{ request('doctor') }}">
          <input type="hidden" name="doctor_id" value="{{ request('doctor_id') }}">

          <input type="hidden" id="PRIMER_NOMBRE" name="PRIMER_NOMBRE" value="{{ old('PRIMER_NOMBRE') }}">
          <input type="hidden" id="SEGUNDO_NOMBRE" name="SEGUNDO_NOMBRE" value="{{ old('SEGUNDO_NOMBRE') }}">
          <input type="hidden" id="PRIMER_APELLIDO" name="PRIMER_APELLIDO" value="{{ old('PRIMER_APELLIDO') }}">
          <input type="hidden" id="SEGUNDO_APELLIDO" name="SEGUNDO_APELLIDO" value="{{ old('SEGUNDO_APELLIDO') }}">
          <input type="hidden" name="DEPARTAMENTO" value="{{ old('DEPARTAMENTO') }}">
          <input type="hidden" name="MUNICIPIO" value="{{ old('MUNICIPIO') }}">
          <input type="hidden" name="COLONIA" value="{{ old('COLONIA') }}">

          <section class="rounded-3xl border border-blue-100 bg-white px-5 py-5 shadow-[0_16px_40px_rgba(18,58,133,0.10)]">
            <div class="mb-4">
              <p class="section-kicker">Paso 1</p>
              <h2 class="mt-2 text-lg font-bold text-slate-900">Datos personales</h2>
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

          <section class="rounded-3xl border border-blue-100 bg-white px-5 py-5 shadow-[0_16px_40px_rgba(18,58,133,0.10)]">
            <div class="mb-4">
              <p class="section-kicker">Paso 2</p>
              <h2 class="mt-2 text-lg font-bold text-slate-900">Seguridad de la cuenta</h2>
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

            <div class="mt-6 rounded-2xl border border-blue-200 bg-gradient-to-r from-blue-50 via-sky-50 to-cyan-50 px-4 py-4 text-sm text-slate-700 shadow-sm">
              <span class="block text-[11px] font-semibold uppercase tracking-[0.2em] text-blue-800">Usuario asignado</span>
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
                    <span class="font-semibold" x-text="match ? 'OK' : 'x'"></span>
                    Las contrasenas deben coincidir
                  </div>
                  <x-input-error :messages="$errors->register->get('password_confirmation')" class="mt-2" />
                </div>
              </div>

              <aside class="rounded-2xl border border-blue-200 bg-[linear-gradient(180deg,_#f6faff_0%,_#edf4ff_100%)] px-4 py-4 shadow-[inset_0_1px_0_rgba(255,255,255,0.8)]">
                <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-blue-800">Validacion</p>
                <h3 class="mt-2 font-semibold text-slate-900">Tu contrasena debe incluir</h3>
                <div class="mt-3 space-y-2 text-sm">
                  <div :class="hasLen ? 'text-green-600' : 'text-red-600'"><span class="font-semibold" x-text="hasLen ? 'OK' : 'x'"></span> Minimo 10 caracteres</div>
                  <div :class="hasUpper ? 'text-green-600' : 'text-red-600'"><span class="font-semibold" x-text="hasUpper ? 'OK' : 'x'"></span> Una letra mayuscula</div>
                  <div :class="hasLower ? 'text-green-600' : 'text-red-600'"><span class="font-semibold" x-text="hasLower ? 'OK' : 'x'"></span> Una letra minuscula</div>
                  <div :class="hasDigit ? 'text-green-600' : 'text-red-600'"><span class="font-semibold" x-text="hasDigit ? 'OK' : 'x'"></span> Un numero</div>
                  <div :class="hasSym ? 'text-green-600' : 'text-red-600'"><span class="font-semibold" x-text="hasSym ? 'OK' : 'x'"></span> Un simbolo</div>
                </div>
              </aside>
            </div>
          </section>

          <div class="mt-2 rounded-[24px] border border-blue-200/80 bg-[linear-gradient(135deg,_#f8fbff_0%,_#ffffff_45%,_#eef5ff_100%)] px-6 py-4 shadow-[0_10px_24px_rgba(18,58,133,0.08)]">
            <div class="flex items-center justify-between gap-3">
              <a href="{{ route('welcome') }}" class="text-sm font-medium text-slate-600 transition hover:text-blue-700">
                Cancelar
              </a>
              <x-primary-button id="register-page-submit" class="rounded-2xl bg-[linear-gradient(135deg,_#12214b_0%,_#17346e_100%)] px-6 py-3 text-sm font-semibold shadow-lg shadow-blue-900/25 hover:bg-[#183d85] focus:bg-[#183d85] active:bg-[#102957]">
                Crear cuenta
              </x-primary-button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    (function () {
      const maxLen = 50;
      let syncFrame = null;

      const form = document.getElementById('register-page-form');
      const submit = document.getElementById('register-page-submit');
      const fullNames = document.getElementById('NOMBRES_COMPLETOS');
      const fullSurnames = document.getElementById('APELLIDOS_COMPLETOS');
      const firstName = document.getElementById('PRIMER_NOMBRE');
      const secondName = document.getElementById('SEGUNDO_NOMBRE');
      const firstSurname = document.getElementById('PRIMER_APELLIDO');
      const secondSurname = document.getElementById('SEGUNDO_APELLIDO');
      const preview = document.getElementById('username-preview-inline');

      function splitFullName(value) {
        const parts = (value || '').trim().split(/\s+/).filter(Boolean);

        return {
          first: parts[0] || '',
          second: parts.slice(1).join(' '),
        };
      }

      function strip(value) {
        return (value || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '');
      }

      function buildUser(name, surname) {
        const first = (name || '').trim().charAt(0);
        const last = (surname || '').trim().replace(/\s+/g, '');
        let base = (first + last).toLowerCase();
        base = strip(base).replace(/[^a-z0-9]/g, '');

        return (base || 'user').slice(0, maxLen);
      }

      function syncHiddenFields() {
        syncFrame = null;

        const names = splitFullName(fullNames?.value || '');
        const surnames = splitFullName(fullSurnames?.value || '');

        if (firstName) firstName.value = names.first;
        if (secondName) secondName.value = names.second;
        if (firstSurname) firstSurname.value = surnames.first;
        if (secondSurname) secondSurname.value = surnames.second;

        if (preview) {
          preview.textContent = (names.first || surnames.first)
            ? buildUser(names.first, surnames.first)
            : '';
        }
      }

      function scheduleSync() {
        if (syncFrame !== null) {
          cancelAnimationFrame(syncFrame);
        }

        syncFrame = requestAnimationFrame(syncHiddenFields);
      }

      ['input', 'change'].forEach(function (eventName) {
        fullNames?.addEventListener(eventName, scheduleSync);
        fullSurnames?.addEventListener(eventName, scheduleSync);
      });

      form?.addEventListener('submit', function () {
        submit?.setAttribute('disabled', 'disabled');
        submit?.classList.add('opacity-70', 'cursor-not-allowed');
        if (submit) {
          submit.textContent = 'Creando cuenta...';
        }
      });

      scheduleSync();
    })();
  </script>
</x-guest-layout>
