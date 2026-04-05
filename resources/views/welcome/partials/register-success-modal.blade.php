<div x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4">
  <div class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm" @click="showRegisterSuccess=false"></div>

  <div x-transition class="modal-panel modal-card relative w-full max-w-lg">
    <div class="border-b border-slate-200 px-6 py-5">
      <div class="flex items-start justify-between gap-4">
        <div>
          <p class="section-kicker">Cuenta creada</p>
          <h3 class="mt-2 text-xl font-bold text-slate-900">Bienvenido al sistema</h3>
        </div>
        <button class="rounded-xl p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700" @click="showRegisterSuccess=false" aria-label="Cerrar">x</button>
      </div>
    </div>
    <div class="space-y-4 px-6 py-6">
      <div class="rounded-2xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-800">Tu cuenta fue creada correctamente.</div>
      <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Usuario generado</p>
        <p class="mt-2 font-mono text-lg font-semibold text-slate-900">{{ session('username_generado') }}</p>
      </div>
      <p class="text-sm leading-6 text-slate-600">Por seguridad, ahora inicia sesion con tus credenciales para continuar.</p>
    </div>
    <div class="flex justify-end border-t border-slate-200 px-6 py-4">
      <button type="button" class="inline-flex items-center justify-center rounded-2xl bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-800" @click="showRegisterSuccess=false">Aceptar</button>
    </div>
  </div>
</div>
