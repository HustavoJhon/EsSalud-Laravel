@extends('layouts.app')
@section('title', isset($role) ? 'Editar Rol' : 'Nuevo Rol')
@section('page_title', isset($role) ? 'Editar Rol' : 'Nuevo Rol')
@section('content')
<div class="max-w-2xl mx-auto">
    <a href="{{ route('admin.roles.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-primary-600 mb-4">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        Volver a roles
    </a>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">{{ isset($role) ? "Editar: {$role->name}" : 'Crear Rol' }}</h2>
        <form method="POST" action="{{ isset($role) ? route('admin.roles.update', $role) : route('admin.roles.store') }}" class="space-y-6">
            @csrf
            @if(isset($role)) @method('PUT') @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombre del rol *</label>
                <input type="text" name="name" value="{{ old('name', $role->name ?? '') }}" required maxlength="50"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none uppercase"
                    {{ isset($role) && $role->name === 'SADM' ? 'readonly' : '' }}>
            </div>

            <div>
                <h3 class="text-sm font-medium text-gray-700 mb-3">Permisos</h3>
                <div class="space-y-4">
                    @foreach($permissions as $group => $perms)
                    <div class="border border-gray-100 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-2">{{ $group }}</h4>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach($perms as $perm)
                                <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer hover:text-primary-600 transition-colors">
                                    <input type="checkbox" name="permissions[]" value="{{ $perm->name }}"
                                        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                        {{ isset($rolePermissions) && in_array($perm->name, $rolePermissions) ? 'checked' : '' }}>
                                    <span class="truncate">{{ str_replace('-', ' ', $perm->name) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700">
                    {{ isset($role) ? 'Guardar Cambios' : 'Crear Rol' }}
                </button>
                <a href="{{ route('admin.roles.index') }}" class="px-6 py-2.5 border border-gray-200 text-gray-600 rounded-lg text-sm hover:bg-gray-50">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
