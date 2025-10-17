{{-- resources/views/profile/edit.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Párrafo informativo (aquí va tu texto) --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h1 class="text-2xl font-semibold mb-2">Mi perfil</h1>
                    <p class="text-gray-600">
                        Aquí irá la edición de perfil (nombre, correo, contraseña, 2FA, etc.).
                    </p>
                    <a href="{{ route('dashboard') }}" class="inline-block mt-3 px-4 py-2 bg-blue-600 text-white rounded">
                        Volver al panel
                    </a>
                </div>
            </div>

            {{-- Formulario: actualizar datos de perfil --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Formulario: cambiar contraseña --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- Formulario: eliminar cuenta --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
