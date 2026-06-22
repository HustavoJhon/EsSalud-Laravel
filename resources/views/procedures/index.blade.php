@extends('layouts.app')
@section('title', 'Trámites')
@section('page_title', 'Trámites')
@section('content')
{{-- Filters --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 md:p-5 mb-4">
    <form method="GET" action="{{ route('procedures.index') }}" class="flex flex-wrap items-end gap-2 md:gap-3">
        <div class="w-[110px] md:w-36">
            <label class="block text-xs font-medium text-gray-500 mb-1">Estado</label>
            <select name="status" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none">
                <option value="">Todos</option>
                @foreach($statuses as $status)
                    <option value="{{ $status->code }}" {{ request('status') == $status->code ? 'selected' : '' }}>{{ $status->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-[130px] md:w-40">
            <label class="block text-xs font-medium text-gray-500 mb-1">Tipo</label>
            <select name="type" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none">
                <option value="">Todos</option>
                @foreach($types as $type)
                    <option value="{{ $type->code }}" {{ request('type') == $type->code ? 'selected' : '' }}>{{ $type->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white text-sm rounded-lg hover:bg-primary-700 font-medium">Filtrar</button>
            @if(request()->anyFilled(['status','type']))
                <a href="{{ route('procedures.index') }}" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 self-end">Limpiar</a>
            @endif
        </div>
        <div class="ml-auto self-end">
            <a href="{{ route('procedures.create') }}" class="inline-flex items-center gap-1.5 bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span class="hidden sm:inline">Nuevo Trámite</span>
                <span class="sm:hidden">+</span>
            </a>
        </div>
    </form>
</div>

{{-- Desktop Table --}}
<div class="hidden md:block bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50 text-left text-gray-500 font-medium">
                <tr>
                    <th class="px-5 py-3 w-16">ID</th>
                    <th class="px-5 py-3">Tipo</th>
                    <th class="px-5 py-3">Estado</th>
                    <th class="px-5 py-3">Usuario</th>
                    <th class="px-5 py-3">Fecha</th>
                    <th class="px-5 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($procedures as $procedure)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3.5 text-gray-400">#{{ $procedure->id }}</td>
                    <td class="px-5 py-3.5">
                        <div class="font-medium text-gray-800">{{ $procedure->procedureType->name }}</div>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="inline-block px-2.5 py-1 text-xs font-medium rounded-full
                            @if($procedure->status->code === 'APROBADO') bg-green-100 text-green-700
                            @elseif($procedure->status->code === 'RECHAZADO') bg-red-100 text-red-700
                            @elseif($procedure->status->code === 'SUBSANACION') bg-orange-100 text-orange-700
                            @elseif($procedure->status->code === 'CANCELADO') bg-gray-100 text-gray-600
                            @elseif(in_array($procedure->status->code, ['PENDIENTE','EN_REVISION','RADICADO','EVALUACION'])) bg-blue-100 text-blue-700
                            @else bg-gray-100 text-gray-600 @endif">
                            {{ $procedure->status->name }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-gray-500">{{ $procedure->user->full_name ?? $procedure->user->name }}</td>
                    <td class="px-5 py-3.5 text-gray-400 text-xs whitespace-nowrap">{{ $procedure->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-5 py-3.5 text-right">
                        <a href="{{ route('procedures.show', $procedure) }}" class="text-primary-600 hover:text-primary-800 text-sm font-medium">Ver</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center">
                        <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <p class="text-gray-400 font-medium">No se encontraron trámites</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Mobile Cards --}}
<div class="md:hidden space-y-3">
    @forelse($procedures as $procedure)
    <a href="{{ route('procedures.show', $procedure) }}" class="block bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition-shadow active:scale-[0.99]">
        <div class="flex items-start justify-between gap-3 mb-3">
            <div class="flex-1 min-w-0">
                <h3 class="font-semibold text-gray-800 text-sm truncate">{{ $procedure->procedureType->name }}</h3>
                <p class="text-xs text-gray-400 mt-0.5">#{{ $procedure->id }} · {{ $procedure->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <span class="inline-block px-2.5 py-1 text-xs font-medium rounded-full shrink-0
                @if($procedure->status->code === 'APROBADO') bg-green-100 text-green-700
                @elseif($procedure->status->code === 'RECHAZADO') bg-red-100 text-red-700
                @elseif($procedure->status->code === 'SUBSANACION') bg-orange-100 text-orange-700
                @elseif($procedure->status->code === 'CANCELADO') bg-gray-100 text-gray-600
                @else bg-blue-100 text-blue-700 @endif">
                {{ $procedure->status->name }}
            </span>
        </div>
        <div class="flex items-center justify-between">
            <span class="text-xs text-gray-500">{{ $procedure->user->full_name ?? $procedure->user->name }}</span>
            <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </div>
    </a>
    @empty
    <div class="bg-white rounded-xl border border-gray-200 p-8 text-center">
        <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        </div>
        <p class="text-gray-400 font-medium">No se encontraron trámites</p>
        <a href="{{ route('procedures.create') }}" class="inline-block mt-2 text-primary-600 text-sm font-medium">Crear primer trámite</a>
    </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $procedures->links() }}
</div>
@endsection
