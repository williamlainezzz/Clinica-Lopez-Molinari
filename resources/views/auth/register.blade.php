<x-guest-layout>
    @php
        $oldNombres = trim(collect([old('PRIMER_NOMBRE'), old('SEGUNDO_NOMBRE')])->filter()->implode(' '));
        $oldApellidos = trim(collect([old('PRIMER_APELLIDO'), old('SEGUNDO_APELLIDO')])->filter()->implode(' '));
        $departamentos = array_keys($hondurasLocations ?? []);
        $selectedDept = old('DEPARTAMENTO');
        $selectedCity = old('CIUDAD');
    @endphp

    <div class="space-y-8">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Nuevo perfil</p>
                <h1 class="text-2xl font-semibold text-slate-800">Registro de usuario</h1>
                <p class="text-sm text-slate-600 mt-1">Completa tus datos con un diseño limpio y profesional.</p>
            </div>
            <div class="px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 shadow-sm">
                <p class="text-xs uppercase tracking-[0.12em] font-semibold">Usuario asignado</p>
                <div class="mt-1 text-lg font-semibold" id="usernamePreview">Pendiente</div>
            </div>
        </div>

        <form method="POST" action="{{ route('register') }}" novalidate id="registerForm" class="space-y-7">
            @csrf
            <input type="hidden" name="PRIMER_NOMBRE" id="PRIMER_NOMBRE_REAL">
            <input type="hidden" name="SEGUNDO_NOMBRE" id="SEGUNDO_NOMBRE_REAL">
            <input type="hidden" name="PRIMER_APELLIDO" id="PRIMER_APELLIDO_REAL">
            <input type="hidden" name="SEGUNDO_APELLIDO" id="SEGUNDO_APELLIDO_REAL">

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="space-y-3">
                    <p class="text-sm font-semibold text-slate-700">Identidad</p>
                    <div class="space-y-2">
                        <x-input-label for="NOMBRES_COMPLETOS" :value="__('Nombres (primer y segundo)')" />
                        <x-text-input id="NOMBRES_COMPLETOS" name="NOMBRES_COMPLETOS" class="block mt-1 w-full" type="text" value="{{ old('NOMBRES_COMPLETOS', $oldNombres) }}" required />
                        <x-input-error :messages="$errors->get('PRIMER_NOMBRE')" class="mt-1" />
                        <x-input-error :messages="$errors->get('SEGUNDO_NOMBRE')" class="mt-1" />
                    </div>
                    <div class="space-y-2">
                        <x-input-label for="APELLIDOS_COMPLETOS" :value="__('Apellidos (primer y segundo)')" />
                        <x-text-input id="APELLIDOS_COMPLETOS" name="APELLIDOS_COMPLETOS" class="block mt-1 w-full" type="text" value="{{ old('APELLIDOS_COMPLETOS', $oldApellidos) }}" required />
                        <x-input-error :messages="$errors->get('PRIMER_APELLIDO')" class="mt-1" />
                        <x-input-error :messages="$errors->get('SEGUNDO_APELLIDO')" class="mt-1" />
                    </div>
                </div>
                <div class="space-y-3">
                    <p class="text-sm font-semibold text-slate-700">Contacto</p>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <x-input-label for="TIPO_GENERO" :value="__('Género')" />
                            <select id="TIPO_GENERO" name="TIPO_GENERO" class="mt-1 block w-full rounded-md border-slate-300" required>
                                <option value="" disabled {{ old('TIPO_GENERO') ? '' : 'selected' }}>Seleccione...</option>
                                <option value="1" {{ old('TIPO_GENERO')=='1' ? 'selected' : '' }}>Masculino</option>
                                <option value="2" {{ old('TIPO_GENERO')=='2' ? 'selected' : '' }}>Femenino</option>
                                <option value="3" {{ old('TIPO_GENERO')=='3' ? 'selected' : '' }}>Otro / Prefiero no decir</option>
                            </select>
                            <x-input-error :messages="$errors->get('TIPO_GENERO')" class="mt-1" />
                        </div>
                        <div class="space-y-2">
                            <x-input-label for="NUM_TELEFONO" :value="__('Teléfono')" />
                            <x-text-input id="NUM_TELEFONO" class="block mt-1 w-full" type="text" name="NUM_TELEFONO" :value="old('NUM_TELEFONO')" />
                            <p class="text-[11px] text-slate-500">Solo números, 8–10 dígitos.</p>
                            <x-input-error :messages="$errors->get('NUM_TELEFONO')" class="mt-1" />
                        </div>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <x-input-label for="DEPARTAMENTO" :value="__('Departamento')" />
                            <select id="DEPARTAMENTO" name="DEPARTAMENTO" class="mt-1 block w-full rounded-md border-slate-300" required>
                                <option value="" disabled {{ $selectedDept ? '' : 'selected' }}>Seleccione...</option>
                                @foreach ($departamentos as $dep)
                                    <option value="{{ $dep }}" {{ $selectedDept === $dep ? 'selected' : '' }}>{{ $dep }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('DEPARTAMENTO')" class="mt-1" />
                        </div>
                        <div class="space-y-2">
                            <x-input-label for="CIUDAD" :value="__('Ciudad')" />
                            <select id="CIUDAD" name="CIUDAD" class="mt-1 block w-full rounded-md border-slate-300" required>
                                <option value="" disabled {{ $selectedCity ? '' : 'selected' }}>Seleccione un departamento...</option>
                                @if ($selectedDept && ($hondurasLocations[$selectedDept] ?? false))
                                    @foreach ($hondurasLocations[$selectedDept] as $city)
                                        <option value="{{ $city }}" {{ $selectedCity === $city ? 'selected' : '' }}>{{ $city }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <x-input-error :messages="$errors->get('CIUDAD')" class="mt-1" />
                        </div>
                    </div>
                    <div class="space-y-2">
                        <x-input-label for="REFERENCIA" :value="__('Dirección y referencia')" />
                        <textarea id="REFERENCIA" name="REFERENCIA" rows="3" class="mt-1 block w-full rounded-md border-slate-300 focus:border-cyan-500 focus:ring-cyan-500">{{ old('REFERENCIA') }}</textarea>
                        <x-input-error :messages="$errors->get('REFERENCIA')" class="mt-1" />
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <p class="text-sm font-semibold text-slate-700">Datos de acceso</p>
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="space-y-2">
                        <x-input-label for="CORREO" :value="__('Correo electrónico')" />
                        <x-text-input id="CORREO" class="block mt-1 w-full" type="email" name="CORREO" :value="old('CORREO')" required />
                        <x-input-error :messages="$errors->get('CORREO')" class="mt-1" />
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <x-input-label for="password" :value="__('Contraseña')" />
                            <span class="text-[11px] text-slate-500">Validaciones en tiempo real</span>
                        </div>
                        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                        <ul class="text-xs text-slate-500 space-y-1" id="passwordHints">
                            <li data-rule="length">• Al menos 8 caracteres</li>
                            <li data-rule="upper">• Una mayúscula</li>
                            <li data-rule="lower">• Una minúscula</li>
                            <li data-rule="number">• Un número</li>
                        </ul>
                    </div>
                    <div class="space-y-2">
                        <x-input-label for="password_confirmation" :value="__('Confirmar contraseña')" />
                        <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <p class="text-sm font-semibold text-slate-700">Preguntas de seguridad</p>
                <p class="text-xs text-slate-500">Selecciona dos preguntas distintas y guarda respuestas memorables.</p>
                <div class="grid gap-4 md:grid-cols-2" x-data="{ q1: '{{ old('PREGUNTA_1') }}' || '', q2: '{{ old('PREGUNTA_2') }}' || '' }">
                    <div class="space-y-2">
                        <x-input-label for="PREGUNTA_1" :value="__('Pregunta 1')" />
                        <select id="PREGUNTA_1" name="PREGUNTA_1" class="mt-1 block w-full rounded-md border-slate-300" x-model="q1" required>
                            <option value="" disabled {{ old('PREGUNTA_1') ? '' : 'selected' }}>Seleccione...</option>
                            @foreach ($preguntasSeg as $p)
                                <option value="{{ $p->COD_PREGUNTA }}" {{ old('PREGUNTA_1') == $p->COD_PREGUNTA ? 'selected' : '' }}>{{ $p->TEXTO_PREGUNTA }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('PREGUNTA_1')" class="mt-1" />
                        <x-input-label for="RESPUESTA_1" :value="__('Respuesta 1')" class="mt-3" />
                        <x-text-input id="RESPUESTA_1" name="RESPUESTA_1" type="text" class="block mt-1 w-full" required :value="old('RESPUESTA_1')" />
                        <x-input-error :messages="$errors->get('RESPUESTA_1')" class="mt-1" />
                    </div>
                    <div class="space-y-2">
                        <x-input-label for="PREGUNTA_2" :value="__('Pregunta 2')" />
                        <select id="PREGUNTA_2" name="PREGUNTA_2" class="mt-1 block w-full rounded-md border-slate-300" x-model="q2" required>
                            <option value="" disabled {{ old('PREGUNTA_2') ? '' : 'selected' }}>Seleccione...</option>
                            @foreach ($preguntasSeg as $p)
                                <option value="{{ $p->COD_PREGUNTA }}" {{ old('PREGUNTA_2') == $p->COD_PREGUNTA ? 'selected' : '' }}>{{ $p->TEXTO_PREGUNTA }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('PREGUNTA_2')" class="mt-1" />
                        <x-input-label for="RESPUESTA_2" :value="__('Respuesta 2')" class="mt-3" />
                        <x-text-input id="RESPUESTA_2" name="RESPUESTA_2" type="text" class="block mt-1 w-full" required :value="old('RESPUESTA_2')" />
                        <x-input-error :messages="$errors->get('RESPUESTA_2')" class="mt-1" />
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-4">
                <a class="text-sm text-slate-600 hover:text-slate-900 underline" href="{{ route('login') }}">¿Ya tienes cuenta? Inicia sesión</a>
                <x-primary-button type="button" id="openRegisterModal" class="px-6">Registrarme</x-primary-button>
            </div>
        </form>
    </div>

    <div id="welcomeModal" class="fixed inset-0 hidden items-center justify-center z-50">
        <div class="absolute inset-0 bg-slate-900/60" id="closeModal"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl border border-slate-100 w-full max-w-md mx-4 p-6 animate-modal">
            <div class="flex items-center gap-3 mb-4">
                <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-sky-500 text-white flex items-center justify-center shadow-lg">✔</div>
                <div>
                    <p class="text-xs uppercase tracking-[0.16em] text-slate-500">¡Bienvenido(a)!</p>
                    <h2 class="text-xl font-semibold text-slate-800">Cuenta lista para crear</h2>
                </div>
            </div>
            <p class="text-sm text-slate-600 mb-3">Confirma tu registro y guarda tu usuario asignado.</p>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-slate-800 font-semibold flex items-center justify-between">
                <span>Usuario asignado</span>
                <code id="modalUsername" class="px-3 py-1 rounded-lg bg-white border border-slate-200 text-indigo-700"></code>
            </div>
            <div class="flex flex-wrap items-center justify-end gap-3 mt-6">
                <button type="button" class="text-sm text-slate-600 hover:text-slate-900" id="cancelModal">Seguir editando</button>
                <x-primary-button id="confirmSubmit" class="px-5">Continuar con registro</x-primary-button>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const map = @json($hondurasLocations ?? []);
            const nameInput = document.getElementById('NOMBRES_COMPLETOS');
            const lastInput = document.getElementById('APELLIDOS_COMPLETOS');
            const userPreview = document.getElementById('usernamePreview');
            const modalUsername = document.getElementById('modalUsername');
            const deptSelect = document.getElementById('DEPARTAMENTO');
            const citySelect = document.getElementById('CIUDAD');
            const registerForm = document.getElementById('registerForm');
            const modal = document.getElementById('welcomeModal');
            const openModal = document.getElementById('openRegisterModal');
            const closeModal = document.getElementById('closeModal');
            const cancelModal = document.getElementById('cancelModal');
            const confirmSubmit = document.getElementById('confirmSubmit');
            const hiddenFirst = document.getElementById('PRIMER_NOMBRE_REAL');
            const hiddenSecond = document.getElementById('SEGUNDO_NOMBRE_REAL');
            const hiddenLastFirst = document.getElementById('PRIMER_APELLIDO_REAL');
            const hiddenLastSecond = document.getElementById('SEGUNDO_APELLIDO_REAL');
            const password = document.getElementById('password');
            const hints = document.querySelectorAll('#passwordHints [data-rule]');

            function splitName(text) {
                const parts = (text || '').trim().split(/\s+/).filter(Boolean);
                const first = parts[0] || '';
                const second = parts.length > 1 ? parts.slice(1).join(' ') : '';
                return { first, second };
            }

            function updateHiddenNames() {
                const n = splitName(nameInput?.value || '');
                const a = splitName(lastInput?.value || '');
                if (hiddenFirst) hiddenFirst.value = n.first;
                if (hiddenSecond) hiddenSecond.value = n.second;
                if (hiddenLastFirst) hiddenLastFirst.value = a.first;
                if (hiddenLastSecond) hiddenLastSecond.value = a.second;
                return { n, a };
            }

            function buildUsername(nombre, apellido) {
                const strip = (str) => (str || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '');
                const first = (nombre || '').trim().charAt(0);
                const last = (apellido || '').trim().replace(/\s+/g, '');
                let base = (first + last).toLowerCase();
                base = strip(base).replace(/[^a-z0-9]/g, '');
                return base || 'usuario';
            }

            function updateUsernamePreview() {
                const { n, a } = updateHiddenNames();
                const username = buildUsername(n.first, a.first);
                if (userPreview) userPreview.textContent = username;
                if (modalUsername) modalUsername.textContent = username;
            }

            function renderCities(dept) {
                if (!citySelect) return;
                citySelect.innerHTML = '';
                const placeholder = document.createElement('option');
                placeholder.textContent = dept ? 'Seleccione una ciudad...' : 'Seleccione un departamento...';
                placeholder.value = '';
                placeholder.disabled = true;
                placeholder.selected = true;
                citySelect.appendChild(placeholder);
                const cities = map[dept] || [];
                cities.forEach((city) => {
                    const opt = document.createElement('option');
                    opt.value = city;
                    opt.textContent = city;
                    if (city === @json($selectedCity)) {
                        opt.selected = true;
                    }
                    citySelect.appendChild(opt);
                });
            }

            function toggleModal(show) {
                if (!modal) return;
                modal.classList.toggle('hidden', !show);
                modal.classList.toggle('flex', show);
            }

            function validatePassword(value) {
                const rules = {
                    length: value.length >= 8,
                    upper: /[A-Z]/.test(value),
                    lower: /[a-z]/.test(value),
                    number: /[0-9]/.test(value),
                };
                hints.forEach((item) => {
                    const rule = item.getAttribute('data-rule');
                    if (rules[rule]) {
                        item.classList.add('text-emerald-600', 'font-semibold');
                        item.classList.remove('text-slate-500');
                    } else {
                        item.classList.remove('text-emerald-600', 'font-semibold');
                        item.classList.add('text-slate-500');
                    }
                });
            }

            if (nameInput) nameInput.addEventListener('input', updateUsernamePreview);
            if (lastInput) lastInput.addEventListener('input', updateUsernamePreview);

            if (deptSelect) {
                deptSelect.addEventListener('change', (e) => {
                    renderCities(e.target.value);
                });
            }

            if (password) {
                password.addEventListener('input', (e) => validatePassword(e.target.value));
                validatePassword(password.value || '');
            }

            if (openModal) {
                openModal.addEventListener('click', (e) => {
                    e.preventDefault();
                    updateUsernamePreview();
                    toggleModal(true);
                });
            }

            [closeModal, cancelModal].forEach((btn) => {
                btn?.addEventListener('click', (e) => {
                    e.preventDefault();
                    toggleModal(false);
                });
            });

            if (confirmSubmit && registerForm) {
                confirmSubmit.addEventListener('click', (e) => {
                    e.preventDefault();
                    updateHiddenNames();
                    toggleModal(false);
                    registerForm.submit();
                });
            }

            renderCities(@json($selectedDept));
            updateUsernamePreview();
        })();
    </script>
</x-guest-layout>
