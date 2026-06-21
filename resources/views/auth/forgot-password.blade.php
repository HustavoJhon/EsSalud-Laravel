@extends('layouts.guest')
@section('title', 'Recuperar Contraseña')
@section('content')
<h2 class="text-xl font-semibold text-gray-800 mb-6 text-center">Recuperar Contraseña</h2>
<p class="text-sm text-gray-600 mb-6 text-center">Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.</p>
<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <div class="mb-6">
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}" required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
    </div>
    <button type="submit" class="w-full bg-primary-600 text-white py-2 rounded-lg hover:bg-primary-700 transition-colors font-medium">
        Enviar Enlace
    </button>
    <p class="text-center text-sm text-gray-600 mt-4">
        <a href="{{ route('login') }}" class="text-primary-600 hover:underline">Volver al inicio de sesión</a>
    </p>
</form>
@endsection
