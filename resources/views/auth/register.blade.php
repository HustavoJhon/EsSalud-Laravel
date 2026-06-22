@extends('layouts.guest')
@section('title', 'Crear Cuenta')
@section('content')
<div x-data="{ showPassword: false, showConfirm: false }">
    <h2 class="text-2xl font-bold text-gray-900 mb-1">Crear Cuenta</h2>
    <p class="text-sm text-gray-500 mb-6">Regístrate para acceder a la plataforma</p>

    <form method="POST" action="{{ route('register') }}" class="space-y-3.5">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <div class="sm:col-span-2">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Nombre completo</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm bg-gray-50 focus:bg-white outline-none transition-colors"
                    placeholder="Tu nombre completo">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Correo electrónico</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm bg-gray-50 focus:bg-white outline-none transition-colors"
                    placeholder="correo@ejemplo.com">
            </div>
            <div>
                <label for="dni" class="block text-sm font-medium text-gray-700 mb-1.5">DNI</label>
                <input type="text" name="dni" id="dni" value="{{ old('dni') }}" maxlength="20"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm bg-gray-50 focus:bg-white outline-none transition-colors"
                    placeholder="12345678">
            </div>
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5">Teléfono</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" maxlength="15"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm bg-gray-50 focus:bg-white outline-none transition-colors"
                    placeholder="999888777">
            </div>
            <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Contraseña</label>
                    <input :type="showPassword ? 'text' : 'password'" name="password" id="password" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm bg-gray-50 focus:bg-white outline-none transition-colors"
                        placeholder="Mínimo 8 caracteres">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Confirmar contraseña</label>
                    <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation" id="password_confirmation" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm bg-gray-50 focus:bg-white outline-none transition-colors"
                        placeholder="Repite tu contraseña">
                </div>
            </div>
        </div>

        <button type="submit" class="w-full bg-primary-600 text-white py-3 rounded-xl hover:bg-primary-700 transition-colors font-semibold shadow-sm shadow-primary-600/25 mt-2">
            Crear Cuenta
        </button>

        <p class="text-center text-sm text-gray-500">
            ¿Ya tienes cuenta? <a href="{{ route('login') }}" class="text-primary-600 hover:text-primary-700 font-medium">Inicia Sesión</a>
        </p>
    </form>
</div>
@endsection
