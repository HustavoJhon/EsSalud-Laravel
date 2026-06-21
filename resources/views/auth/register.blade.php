@extends('layouts.guest')
@section('title', 'Registro')
@section('content')
<h2 class="text-xl font-semibold text-gray-800 mb-6 text-center">Crear Cuenta</h2>
<form method="POST" action="{{ route('register') }}">
    @csrf
    <div class="mb-4">
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo</label>
        <input type="text" name="name" id="name" value="{{ old('name') }}" required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
    </div>
    <div class="mb-4">
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}" required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
    </div>
    <div class="mb-4">
        <label for="dni" class="block text-sm font-medium text-gray-700 mb-1">DNI</label>
        <input type="text" name="dni" id="dni" value="{{ old('dni') }}" maxlength="20"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
    </div>
    <div class="mb-4">
        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" maxlength="15"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
    </div>
    <div class="mb-4">
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
        <input type="password" name="password" id="password" required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
    </div>
    <div class="mb-6">
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Contraseña</label>
        <input type="password" name="password_confirmation" id="password_confirmation" required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
    </div>
    <button type="submit" class="w-full bg-primary-600 text-white py-2 rounded-lg hover:bg-primary-700 transition-colors font-medium">
        Registrarse
    </button>
    <p class="text-center text-sm text-gray-600 mt-4">
        ¿Ya tienes cuenta? <a href="{{ route('login') }}" class="text-primary-600 hover:underline">Inicia Sesión</a>
    </p>
</form>
@endsection
