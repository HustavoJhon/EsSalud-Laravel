@extends('layouts.guest')
@section('title', 'Restablecer Contraseña')
@section('content')
<h2 class="text-xl font-semibold text-gray-800 mb-6 text-center">Restablecer Contraseña</h2>
<form method="POST" action="{{ route('password.update') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <div class="mb-4">
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
        <input type="email" name="email" id="email" value="{{ old('email', request('email')) }}" required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
    </div>
    <div class="mb-4">
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Nueva Contraseña</label>
        <input type="password" name="password" id="password" required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
    </div>
    <div class="mb-6">
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Contraseña</label>
        <input type="password" name="password_confirmation" id="password_confirmation" required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
    </div>
    <button type="submit" class="w-full bg-primary-600 text-white py-2 rounded-lg hover:bg-primary-700 transition-colors font-medium">
        Restablecer Contraseña
    </button>
</form>
@endsection
