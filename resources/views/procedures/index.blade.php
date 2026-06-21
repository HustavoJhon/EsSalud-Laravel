@extends('layouts.app')
@section('title', 'Trámites')
@section('page_title', 'Trámites')
@section('content')
<div class="bg-white rounded-lg shadow p-4 mb-6">
    <form method="GET" action="{{ route('procedures.index') }}" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
            <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <option value="">Todos</option>
                @foreach($statuses as $status)
                    <option value="{{ $status->code }}" {{ request('status') == $status->code ? 'selected' : '' }}>
                        {{ $status->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
            <select name="type" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <option value="">Todos</option>
                @foreach($types as $type)
                    <option value="{{ $type->code }}" {{ request('type') == $type->code ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white text-sm rounded-lg hover:bg-primary-700">Filtrar</button>
            @if(request()->anyFilled(['status','type']))
                <a href="{{ route('procedures.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Limpiar</a>
            @endif
        </div>
    </form>
</div>

<a href="{{ route('procedures.create') }}" class="inline-flex items-center space-x-2 bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 mb-4">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
    <span>Nuevo Trámite</span>
</a>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($procedures as $procedure)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm text-gray-900">#{{ $procedure->id }}</td>
                <td class="px-6 py-4 text-sm text-gray-900">{{ $procedure->procedureType->name }}</td>
                <td class="px-6 py-4">
                    <span class="inline-block px-2 py-1 text-xs font-medium rounded-full
                        @if($procedure->status->code === 'APROBADO') bg-green-100 text-green-800
                        @elseif($procedure->status->code === 'RECHAZADO') bg-red-100 text-red-800
                        @elseif($procedure->status->code === 'SUBSANACION') bg-orange-100 text-orange-800
                        @elseif($procedure->status->code === 'CANCELADO') bg-gray-100 text-gray-800
                        @else bg-blue-100 text-blue-800 @endif">
                        {{ $procedure->status->name }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $procedure->user->full_name ?? $procedure->user->name }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $procedure->created_at->format('d/m/Y H:i') }}</td>
                <td class="px-6 py-4 text-sm">
                    <a href="{{ route('procedures.show', $procedure) }}" class="text-primary-600 hover:text-primary-900 font-medium">Ver</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                    No se encontraron trámites.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $procedures->links() }}
</div>
@endsection
