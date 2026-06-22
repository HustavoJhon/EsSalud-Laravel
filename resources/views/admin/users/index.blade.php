@extends('layouts.app')
@section('title', 'Gestión de Usuarios')
@section('page_title', 'Gestión de Usuarios')
@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="p-4 md:p-6 border-b border-gray-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <form method="GET" class="flex flex-wrap items-end gap-2 flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre o email..."
                class="px-3 py-2 border border-gray-200 rounded-lg text-sm w-full sm:w-64 focus:ring-2 focus:ring-primary-500 outline-none">
            <select name="role" class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none">
                <option value="">Todos los roles</option>
                @foreach($roles as $r)
                    <option value="{{ $r->name }}" {{ request('role') == $r->name ? 'selected' : '' }}>{{ $r->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white text-sm rounded-lg hover:bg-primary-700">Filtrar</button>
            @if(request()->anyFilled(['search','role']))
                <a href="{{ route('admin.users.index') }}" class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700">Limpiar</a>
            @endif
        </form>
        <a href="{{ route('admin.users.create') }}" class="flex items-center gap-1.5 px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Nuevo Usuario
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500 font-medium">
                <tr>
                    <th class="px-4 md:px-6 py-3">Nombre</th>
                    <th class="px-4 md:px-6 py-3 hidden sm:table-cell">Email</th>
                    <th class="px-4 md:px-6 py-3 hidden md:table-cell">DNI</th>
                    <th class="px-4 md:px-6 py-3">Rol</th>
                    <th class="px-4 md:px-6 py-3">Estado</th>
                    <th class="px-4 md:px-6 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($users as $user)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 md:px-6 py-3">
                        <div class="font-medium text-gray-800">{{ $user->full_name ?? $user->name }}</div>
                    </td>
                    <td class="px-4 md:px-6 py-3 text-gray-500 hidden sm:table-cell">{{ $user->email }}</td>
                    <td class="px-4 md:px-6 py-3 text-gray-500 hidden md:table-cell">{{ $user->dni ?? '-' }}</td>
                    <td class="px-4 md:px-6 py-3">
                        @foreach($user->roles as $role)
                            <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full
                                @if($role->name === 'SADM') bg-red-100 text-red-700
                                @elseif($role->name === 'SUPV') bg-purple-100 text-purple-700
                                @elseif($role->name === 'OPER') bg-blue-100 text-blue-700
                                @elseif($role->name === 'GESDOC') bg-yellow-100 text-yellow-700
                                @else bg-gray-100 text-gray-600 @endif">
                                {{ $role->name }}
                            </span>
                        @endforeach
                    </td>
                    <td class="px-4 md:px-6 py-3">
                        <span class="inline-flex items-center gap-1 text-xs {{ $user->is_active ? 'text-green-600' : 'text-red-500' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $user->is_active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                            {{ $user->is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="px-4 md:px-6 py-3 text-right">
                        <a href="{{ route('admin.users.edit', $user) }}" class="text-primary-600 hover:text-primary-800 text-xs font-medium mr-3">Editar</a>
                        @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline" onsubmit="return confirm('¿Eliminar este usuario?')">
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
    <div class="p-4 border-t border-gray-100">
        {{ $users->links() }}
    </div>
</div>
@endsection
