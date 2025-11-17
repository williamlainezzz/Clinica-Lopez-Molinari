<x-guest-layout>
    @php
        $errorBag = $errors->register ?? $errors;
        $preguntas = collect($preguntasSeg ?? []);
    @endphp
    <div class="min-h-screen flex flex-col items-center bg-slate-50 py-10 px-4">
        <div class="w-full max-w-4xl">
            <div class="mb-6 text-center">
                <h1 class="text-2xl font-semibold text-slate-800">Registro de paciente</h1>
                <p class="text-slate-500">Completa tus datos para agendar con la clínica.</p>
            </div>
            <div class="bg-white shadow-sm ring-1 ring-slate-200 rounded-2xl">
                <form method="POST" action="{{ route('register') }}" class="p-6 md:p-8 space-y-6">
                    @csrf
                    <input type="hidden" name="doctor" value="{{ $doctorUsername }}">
                    <input type="hidden" name="doctor_id" value="{{ $doctorPersonaId }}">

                    @if($doctorDisplay)
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3">
                            <p class="text-sm text-emerald-800">
                                Te registrarás como paciente del <strong>{{ $doctorDisplay }}</strong>.
                            </p>
                            <p class="text-xs text-emerald-700 mt-1">
                                Este formulario asignará tu historial directamente con su agenda.
                            </p>
                        </div>
                    @else
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                            <p class="text-sm text-slate-700">
                                Completa el registro para que podamos asignarte al doctor correspondiente.
                            </p>
                        </div>
                    @endif

                    <div>
                        <h2 class="text-sm font-semibold text-slate-700 mb-3">Datos personales</h2>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <x-input-label for="PRIMER_NOMBRE" :value="__('Primer nombre')" />
                                <x-text-input id="PRIMER_NOMBRE" type="text" name="PRIMER_NOMBRE"
                                              class="mt-1 block w-full"
                                              :value="old('PRIMER_NOMBRE')" required autofocus />
                                <x-input-error :messages="$errorBag->get('PRIMER_NOMBRE')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="SEGUNDO_NOMBRE" :value="__('Segundo nombre (opcional)')" />
                                <x-text-input id="SEGUNDO_NOMBRE" type="text" name="SEGUNDO_NOMBRE"
                                              class="mt-1 block w-full"
                                              :value="old('SEGUNDO_NOMBRE')" />
                                <x-input-error :messages="$errorBag->get('SEGUNDO_NOMBRE')" class="mt-2" />
                            </div>
                        </div>
                        <div class="grid gap-4 md:grid-cols-2 mt-4">
                            <div>
                                <x-input-label for="PRIMER_APELLIDO" :value="__('Primer apellido')" />
                                <x-text-input id="PRIMER_APELLIDO" type="text" name="PRIMER_APELLIDO"
                                              class="mt-1 block w-full"
                                              :value="old('PRIMER_APELLIDO')" required />
                                <x-input-error :messages="$errorBag->get('PRIMER_APELLIDO')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="SEGUNDO_APELLIDO" :value="__('Segundo apellido (opcional)')" />
                                <x-text-input id="SEGUNDO_APELLIDO" type="text" name="SEGUNDO_APELLIDO"
                                              class="mt-1 block w-full"
                                              :value="old('SEGUNDO_APELLIDO')" />
                                <x-input-error :messages="$errorBag->get('SEGUNDO_APELLIDO')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <x-input-label for="TIPO_GENERO" :value="__('Género')" />
                                <select id="TIPO_GENERO" name="TIPO_GENERO" class="mt-1 block w-full rounded-md border-slate-300" required>
                                    <option value="" disabled {{ old('TIPO_GENERO') ? '' : 'selected' }}>Seleccione...</option>
                                    <option value="1" {{ old('TIPO_GENERO')=='1' ? 'selected' : '' }}>Masculino</option>
                                    <option value="2" {{ old('TIPO_GENERO')=='2' ? 'selected' : '' }}>Femenino</option>
                                    <option value="3" {{ old('TIPO_GENERO')=='3' ? 'selected' : '' }}>Otro / Prefiero no decir</option>
                                </select>
                                <x-input-error :messages="$errorBag->get('TIPO_GENERO')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="NUM_TELEFONO" :value="__('Teléfono')" />
                                <x-text-input id="NUM_TELEFONO" type="text" name="NUM_TELEFONO"
                                              class="mt-1 block w-full"
                                              :value="old('NUM_TELEFONO')" placeholder="99991234" />
                                <x-input-error :messages="$errorBag->get('NUM_TELEFONO')" class="mt-2" />
                            </div>
                        </div>
                        <div class="grid gap-4 md:grid-cols-2 mt-4">
                            <div>
                                <x-input-label for="CIUDAD" :value="__('Ciudad')" />
                                <x-text-input id="CIUDAD" type="text" name="CIUDAD"
                                              class="mt-1 block w-full"
                                              :value="old('CIUDAD')" />
                                <x-input-error :messages="$errorBag->get('CIUDAD')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="COLONIA" :value="__('Colonia / Barrio')" />
                                <x-text-input id="COLONIA" type="text" name="COLONIA"
                                              class="mt-1 block w-full"
                                              :value="old('COLONIA')" />
                                <x-input-error :messages="$errorBag->get('COLONIA')" class="mt-2" />
                            </div>
                        </div>
                        <div class="mt-4">
                            <x-input-label for="REFERENCIA" :value="__('Dirección / Referencia')" />
                            <textarea id="REFERENCIA" name="REFERENCIA" rows="3"
                                      class="mt-1 block w-full rounded-md border-slate-300 focus:border-cyan-500 focus:ring-cyan-500"
                                      placeholder="Col. Centro, Casa #123">{{ old('REFERENCIA') }}</textarea>
                            <x-input-error :messages="$errorBag->get('REFERENCIA')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <h2 class="text-sm font-semibold text-slate-700 mb-3">Cuenta de acceso</h2>
                        <div id="username-pill" class="hidden bg-amber-50 border border-amber-200 rounded-md px-4 py-3 text-sm text-amber-800 mb-4">
                            <div class="font-semibold mb-1">Usuario asignado</div>
                            <div>
                                <span>Guárdalo para iniciar sesión:</span>
                                <code id="username-preview" class="ml-2 font-semibold"></code>
                            </div>
                        </div>
                        <div>
                            <x-input-label for="CORREO" :value="__('Correo electrónico')" />
                            <x-text-input id="CORREO" type="email" name="CORREO"
                                          class="mt-1 block w-full"
                                          :value="old('CORREO')" required />
                            <x-input-error :messages="$errorBag->get('CORREO')" class="mt-2" />
                        </div>
                        <div class="grid gap-4 md:grid-cols-2 mt-4">
                            <div>
                                <x-input-label for="password" :value="__('Contraseña')" />
                                <x-text-input id="password" type="password" name="password"
                                              class="mt-1 block w-full" required />
                                <x-input-error :messages="$errorBag->get('password')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="password_confirmation" :value="__('Confirmar contraseña')" />
                                <x-text-input id="password_confirmation" type="password" name="password_confirmation"
                                              class="mt-1 block w-full" required />
                            </div>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-sm font-semibold text-slate-700">Preguntas de seguridad</h2>
                        <p class="text-xs text-slate-500 mb-3">Se utilizarán para verificar tu identidad si necesitas recuperar el acceso.</p>
                        <div x-data="{ q1: '{{ old('PREGUNTA_1') }}', q2: '{{ old('PREGUNTA_2') }}', same() { return this.q1 && this.q2 && this.q1 === this.q2; } }" class="grid gap-4 md:grid-cols-2">
                            <div>
                                <x-input-label for="PREGUNTA_1" :value="__('Pregunta 1')" />
                                <select id="PREGUNTA_1" name="PREGUNTA_1" class="mt-1 block w-full rounded-md border-slate-300" x-model="q1" required>
                                    <option value="" disabled {{ old('PREGUNTA_1') ? '' : 'selected' }}>Seleccione...</option>
                                    @foreach($preguntas as $p)
                                        <option value="{{ $p->COD_PREGUNTA }}" {{ old('PREGUNTA_1') == $p->COD_PREGUNTA ? 'selected' : '' }}>
                                            {{ $p->TEXTO_PREGUNTA }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errorBag->get('PREGUNTA_1')" class="mt-2" />
                                <x-input-label for="RESPUESTA_1" :value="__('Respuesta 1')" class="mt-3" />
                                <x-text-input id="RESPUESTA_1" type="text" name="RESPUESTA_1"
                                              class="mt-1 block w-full" :value="old('RESPUESTA_1')" required />
                                <x-input-error :messages="$errorBag->get('RESPUESTA_1')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="PREGUNTA_2" :value="__('Pregunta 2')" />
                                <select id="PREGUNTA_2" name="PREGUNTA_2" class="mt-1 block w-full rounded-md border-slate-300" x-model="q2" required>
                                    <option value="" disabled {{ old('PREGUNTA_2') ? '' : 'selected' }}>Seleccione...</option>
                                    @foreach($preguntas as $p)
                                        <option value="{{ $p->COD_PREGUNTA }}" {{ old('PREGUNTA_2') == $p->COD_PREGUNTA ? 'selected' : '' }}>
                                            {{ $p->TEXTO_PREGUNTA }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errorBag->get('PREGUNTA_2')" class="mt-2" />
                                <x-input-label for="RESPUESTA_2" :value="__('Respuesta 2')" class="mt-3" />
                                <x-text-input id="RESPUESTA_2" type="text" name="RESPUESTA_2"
                                              class="mt-1 block w-full" :value="old('RESPUESTA_2')" required />
                                <x-input-error :messages="$errorBag->get('RESPUESTA_2')" class="mt-2" />
                            </div>
                            <div class="md:col-span-2" x-show="same()">
                                <div class="rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-700">
                                    Debes seleccionar preguntas distintas.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end">
                        <a href="{{ route('welcome') }}" class="text-sm text-slate-500 mr-3">Volver al inicio</a>
                        <x-primary-button class="px-6">Registrarme</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const maxLen = 50;
            const $nombre = document.getElementById('PRIMER_NOMBRE');
            const $apellido = document.getElementById('PRIMER_APELLIDO');
            const $pill = document.getElementById('username-pill');
            const $out = document.getElementById('username-preview');

            function strip(str) {
                return (str || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '');
            }

            function buildUser(nombre, apellido) {
                const first = (nombre || '').trim().charAt(0);
                const last = (apellido || '').trim().replace(/\s+/g, '');
                let base = (first + last).toLowerCase();
                base = strip(base).replace(/[^a-z0-9]/g, '');
                if (!base) base = 'user';
                return base.slice(0, maxLen);
            }

            function refresh() {
                const name = $nombre?.value || '';
                const last = $apellido?.value || '';
                if (!name.trim() && !last.trim()) {
                    $pill?.classList.add('hidden');
                    $out.textContent = '';
                    return;
                }
                $pill?.classList.remove('hidden');
                $out.textContent = buildUser(name, last);
            }

            ['input', 'change'].forEach(evt => {
                $nombre?.addEventListener(evt, refresh);
                $apellido?.addEventListener(evt, refresh);
            });
            refresh();
        })();
    </script>
</x-guest-layout>
