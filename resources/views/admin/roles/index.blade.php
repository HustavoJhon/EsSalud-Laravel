@extends('layouts.app')
@section('title', 'Gestión de Roles')
@section('page_title', 'Gestión de Roles')
@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="p-4 md:p-6 border-b border-gray-100 flex items-center justify-between">
        <h3 class="font-semibold text-gray-800">{{ $roles->count() }} roles</h3>
        <a href="{{ route('admin.roles.create') }}" class="flex items-center gap-1.5 px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Nuevo Rol
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500 font-medium">
                <tr>
                    <th class="px-6 py-3">Rol</th>
                    <th class="px-6 py-3 hidden sm:table-cell">Usuarios</th>
                    <th class="px-6 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($roles as $role)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-3">
                        <span class="font-medium text-gray-800">{{ $role->name }}</span>
                        <span class="text-xs text-gray-400 ml-2">{{ $role->permissions->count() }} permisos</span>
                    </td>
                    <td class="px-6 py-3 text-gray-500 hidden sm:table-cell">{{ $role->users_count }}</td>
                    <td class="px-6 py-3 text-right">
                        <a href="{{ route('admin.roles.edit', $role) }}" class="text-primary-600 hover:text-primary-800 text-xs font-medium mr-3">Editar</a>
                        @if(!in_array($role->name, ['SADM']))
                            <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" class="inline" onsubmit="return confirm('¿Eliminar este rol?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:text-red-700 text-xs font-medium">Eliminar</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
