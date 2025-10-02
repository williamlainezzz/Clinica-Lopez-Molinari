<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registro — Clínica Dental</title>

  <!-- Tailwind por CDN (perfecto para prototipo) -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Estilos para que los campos se vean claros y consistentes -->
  <style>
    .field{
      background:#fff;
      border:1px solid #cbd5e1;   /* slate-300 */
      border-radius:0.5rem;        /* rounded-lg */
      padding:0.5rem 0.75rem;      /* px-3 py-2 */
      width:100%;
    }
    .field:focus{
      outline:none;
      border-color:#0ea5e9;        /* sky-500 */
      box-shadow:0 0 0 3px rgba(14,165,233,.25);
    }
    .card {
      background: #fff;
      border: 1px solid #e5e7eb;
      border-radius: 1rem;
      box-shadow: 0 10px 25px rgba(2, 6, 23, .06);
    }
  </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-sky-50 via-white to-teal-50 text-slate-800">

  <div class="max-w-3xl mx-auto my-10 card p-8">
    <div class="flex items-center gap-3 mb-6">
      <img src="{{ asset('images/logo_clinica.avif') }}" class="h-10 w-10 rounded-full shadow" alt="Logo">
      <h1 class="text-2xl font-semibold">Crear cuenta</h1>
    </div>

    {{-- Errores de validación --}}
    @if ($errors->any())
      <div class="mb-6 rounded-md border border-red-200 bg-red-50 p-4 text-red-700">
        <div class="font-medium mb-1">Hay errores en el formulario:</div>
        <ul class="list-disc list-inside text-sm">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('register') }}" id="formRegister" class="space-y-8">
      @csrf

      {{-- DATOS PERSONALES (TBL_PERSONA) --}}
      <div>
        <h2 class="text-lg font-semibold mb-3">Datos personales</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label for="primer_nombre" class="block text-sm font-medium">Primer nombre</label>
            <input id="primer_nombre" name="primer_nombre" type="text" required
                   class="field mt-1" placeholder="Ej. Ana" value="{{ old('primer_nombre') }}">
          </div>

          <div>
            <label for="segundo_nombre" class="block text-sm font-medium">Segundo nombre (opcional)</label>
            <input id="segundo_nombre" name="segundo_nombre" type="text"
                   class="field mt-1" placeholder="Ej. María" value="{{ old('segundo_nombre') }}">
          </div>

          <div>
            <label for="primer_apellido" class="block text-sm font-medium">Primer apellido</label>
            <input id="primer_apellido" name="primer_apellido" type="text" required
                   class="field mt-1" placeholder="Ej. Rivera" value="{{ old('primer_apellido') }}">
          </div>

          <div>
            <label for="segundo_apellido" class="block text-sm font-medium">Segundo apellido (opcional)</label>
            <input id="segundo_apellido" name="segundo_apellido" type="text"
                   class="field mt-1" placeholder="Ej. López" value="{{ old('segundo_apellido') }}">
          </div>

          <div>
            <label for="cod_genero" class="block text-sm font-medium">Género</label>
            <select id="cod_genero" name="cod_genero" required class="field mt-1">
              <option value="" disabled {{ old('cod_genero') ? '' : 'selected' }}>Seleccione…</option>
              <option value="1" {{ old('cod_genero')=='1' ? 'selected' : '' }}>Masculino</option>
              <option value="2" {{ old('cod_genero')=='2' ? 'selected' : '' }}>Femenino</option>
              <option value="3" {{ old('cod_genero')=='3' ? 'selected' : '' }}>Otro</option>
            </select>
          </div>

          <div>
            <label for="tel_persona" class="block text-sm font-medium">Teléfono</label>
            <input id="tel_persona" name="tel_persona" type="tel" required maxlength="10"
                   pattern="[0-9]{8,10}" placeholder="99991234"
                   class="field mt-1" value="{{ old('tel_persona') }}">
            <p class="text-xs text-gray-500 mt-1">Solo números (8–10 dígitos).</p>
          </div>

          <div class="md:col-span-2">
            <label for="dir_persona" class="block text-sm font-medium">Dirección</label>
            <textarea id="dir_persona" name="dir_persona" rows="3" required
                      class="field mt-1" placeholder="Col. Centro, Calle 1 #123">{{ old('dir_persona') }}</textarea>
          </div>
        </div>
      </div>

      {{-- DATOS DE LA CUENTA (Breeze) --}}
      <div>
        <h2 class="text-lg font-semibold mb-3">Datos de la cuenta</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          {{-- Breeze espera un campo "name" => lo completamos antes de enviar --}}
          <input type="hidden" name="name" id="name">

          <div class="md:col-span-2">
            <label for="email" class="block text-sm font-medium">Correo electrónico</label>
            <input id="email" name="email" type="email" required
                   class="field mt-1" placeholder="tucorreo@ejemplo.com" value="{{ old('email') }}">
          </div>

          <div>
            <label for="password" class="block text-sm font-medium">Contraseña</label>
            <input id="password" name="password" type="password" required autocomplete="new-password"
                   class="field mt-1" placeholder="••••••••">
          </div>

          <div>
            <label for="password_confirmation" class="block text-sm font-medium">Confirmar contraseña</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required
                   class="field mt-1" placeholder="••••••••">
          </div>
        </div>
      </div>

      <div class="flex items-center justify-between">
        <a href="{{ route('login') }}" class="text-sm text-sky-700 hover:underline">
          ¿Ya tienes cuenta? Inicia sesión
        </a>
        <button type="submit" class="px-6 py-3 rounded-xl bg-sky-600 text-white hover:bg-sky-700 shadow">
          Registrarme
        </button>
      </div>
    </form>
  </div>

  <script>
    // Armar el "name" que exige Breeze con los datos de persona (por si luego guardas la entidad Persona aparte)
    document.getElementById('formRegister').addEventListener('submit', function () {
      const pn = document.getElementById('primer_nombre').value.trim();
      const sn = document.getElementById('segundo_nombre').value.trim();
      const pa = document.getElementById('primer_apellido').value.trim();
      const sa = document.getElementById('segundo_apellido').value.trim();
      const parts = [pn, sn, pa, sa].filter(Boolean);
      document.getElementById('name').value = parts.join(' ');
    });
  </script>
</body>
</html>
