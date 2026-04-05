<div x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4">
  <div class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm" @click="showLogin=false"></div>

  <div x-transition class="modal-panel modal-card relative w-full max-w-md overflow-hidden">
    <div class="bg-gradient-to-r from-blue-800 to-blue-600 px-6 py-5 text-white">
      <div class="flex items-start justify-between gap-4">
        <div>
          <p class="text-xs font-semibold uppercase tracking-[0.24em] text-blue-100">Acceso seguro</p>
          <h3 class="mt-2 text-xl font-bold">Iniciar sesion</h3>
          <p class="mt-2 text-sm text-blue-50/90">Ingresa con tu usuario o correo y continua en el sistema.</p>
        </div>
        <button class="rounded-xl p-2 text-white/80 transition hover:bg-white/10 hover:text-white" @click="showLogin=false" aria-label="Cerrar">x</button>
      </div>
    </div>
    <div class="p-6">
      @if ($errors->login->any())
        <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">No pudimos iniciar sesion con los datos ingresados. Verifica tu usuario o correo y tu contrasena e intentalo nuevamente.</div>
      @endif

      <form method="POST" action="{{ route('login') }}" novalidate x-data="{ showPwd:false }" class="space-y-5">
        @csrf

        <div>
          <x-input-label for="login" :value="__('Usuario o correo')" />
          <x-text-input id="login" class="block mt-1 w-full" type="text" name="login" :value="old('login')" required autofocus autocomplete="username email" />
        </div>

        <div>
          <x-input-label for="password" :value="__('Contrasena')" />
          <div class="relative">
            <x-text-input id="password" name="password" x-bind:type="showPwd ? 'text' : 'password'" class="block mt-1 w-full pr-10" required autocomplete="current-password" />
            <button type="button" class="absolute inset-y-0 right-3 flex items-center text-slate-500 transition hover:text-slate-700" @click="showPwd = !showPwd" :aria-label="showPwd ? 'Ocultar contrasena' : 'Mostrar contrasena'">
              <svg x-show="!showPwd" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              <svg x-show="showPwd" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3l18 18M10.58 10.58A3 3 0 0012 15a3 3 0 002.42-4.42M9.88 5.09A9.96 9.96 0 0112 5c4.477 0 8.268 2.943 9.542 7-.39 1.24-1.02 2.36-1.85 3.33M6.27 6.27C4.39 7.58 3.03 9.54 2.46 12c1.274 4.057 5.065 7 9.542 7a9.96 9.96 0 004.12-.87"/></svg>
            </button>
          </div>
        </div>

        <div class="flex items-center justify-between gap-3">
          <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-slate-600">
            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-700 shadow-sm focus:ring-blue-500" name="remember">
            <span>{{ __('Recordarme') }}</span>
          </label>

          @if (Route::has('password.request'))
            <a class="text-sm font-medium text-blue-700 transition hover:text-blue-800" href="{{ route('password.request') }}">
              {{ __('Olvide mi contrasena') }}
            </a>
          @endif
        </div>

        <div class="pt-2">
          <x-primary-button class="w-full justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold shadow-lg shadow-slate-900/15 hover:bg-slate-800">
            {{ __('Iniciar sesion') }}
          </x-primary-button>
        </div>
      </form>
    </div>
  </div>
</div>
