@extends('layouts.guest')
@section('title', 'Iniciar Sesión')
@section('content')
<h2 class="text-xl font-semibold text-gray-800 mb-6 text-center">Iniciar Sesión</h2>
<form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="mb-4">
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}" required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
            placeholder="correo@essalud.pe">
    </div>
    <div class="mb-4">
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
        <input type="password" name="password" id="password" required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
    </div>
    <div class="flex items-center justify-between mb-6">
        <label class="flex items-center text-sm text-gray-600">
            <input type="checkbox" name="remember" class="rounded border-gray-300 text-primary-600 mr-2">
            Recordarme
        </label>
        <a href="{{ route('password.request') }}" class="text-sm text-primary-600 hover:underline">¿Olvidaste tu contraseña?</a>
    </div>
    <button type="submit" class="w-full bg-primary-600 text-white py-2 rounded-lg hover:bg-primary-700 transition-colors font-medium">
        Ingresar
    </button>
    <p class="text-center text-sm text-gray-600 mt-4">
        ¿No tienes cuenta? <a href="{{ route('register') }}" class="text-primary-600 hover:underline">Regístrate</a>
    </p>
</form>
@endsection
