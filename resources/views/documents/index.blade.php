@extends('layouts.app')
@section('title', 'Documentos')
@section('page_title', 'Documentos')
@section('content')
<div class="bg-white rounded-lg shadow p-4 mb-6">
    <form method="GET" action="{{ route('documents.index') }}" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Trámite</label>
            <select name="procedure_id" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <option value="">Todos</option>
                @foreach($procedures as $proc)
                    <option value="{{ $proc->id }}" {{ request('procedure_id') == $proc->id ? 'selected' : '' }}>
                        #{{ $proc->id }} - {{ $proc->procedureType->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
            <select name="category_id" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <option value="">Todas</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white text-sm rounded-lg hover:bg-primary-700">Filtrar</button>
        </div>
    </form>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Documento</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoría</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trámite</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tamaño</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($documents as $doc)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm">
                    <a href="{{ route('documents.show', $doc) }}" class="text-primary-600 hover:underline font-medium">
                        {{ $doc->original_name }}
                    </a>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $doc->category?->name ?? '-' }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">
                    @if($doc->procedure_id)
                        <a href="{{ route('procedures.show', $doc->procedure_id) }}" class="text-primary-600 hover:underline">
                            #{{ $doc->procedure_id }}
                        </a>
                    @else
                        -
                    @endif
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ number_format($doc->file_size / 1024, 1) }} KB</td>
                <td class="px-6 py-4">
                    @if($doc->is_validated)
                        <span class="text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded">Validado</span>
                    @else
                        <span class="text-xs bg-gray-100 text-gray-800 px-2 py-0.5 rounded">Pendiente</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $doc->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-8 text-center text-gray-500">No se encontraron documentos.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $documents->links() }}
</div>
@endsection
