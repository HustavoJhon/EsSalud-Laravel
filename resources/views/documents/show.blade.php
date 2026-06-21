@extends('layouts.app')
@section('title', 'Documento')
@section('page_title', 'Documento: ' . $document->original_name)
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-gray-500">Nombre Original</dt>
                <dd class="font-medium">{{ $document->original_name }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Tipo MIME</dt>
                <dd>{{ $document->mime_type }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Tamaño</dt>
                <dd>{{ number_format($document->file_size / 1024, 1) }} KB</dd>
            </div>
            <div>
                <dt class="text-gray-500">Versión</dt>
                <dd>{{ $document->version }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Categoría</dt>
                <dd>{{ $document->category?->name ?? 'Sin categoría' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Validado</dt>
                <dd>
                    @if($document->is_validated)
                        <span class="text-green-600">Sí - {{ $document->validated_at?->format('d/m/Y H:i') }}</span>
                    @else
                        <span class="text-gray-500">No</span>
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-gray-500">Subido por</dt>
                <dd>{{ $document->user->full_name ?? $document->user->name }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Trámite</dt>
                <dd>
                    @if($document->procedure_id)
                        <a href="{{ route('procedures.show', $document->procedure_id) }}" class="text-primary-600 hover:underline">
                            #{{ $document->procedure_id }}
                        </a>
                    @else
                        No asociado
                    @endif
                </dd>
            </div>
        </dl>

        @if($document->ocr_text)
            <div class="mt-6">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Texto OCR Extraído</h4>
                <pre class="text-xs text-gray-600 bg-gray-50 p-4 rounded-lg max-h-60 overflow-y-auto whitespace-pre-wrap">{{ $document->ocr_text }}</pre>
            </div>
        @endif

        @if($document->embeddings->isNotEmpty())
            <div class="mt-6">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Chunks de Embedding ({{ $document->embeddings->count() }})</h4>
                <div class="space-y-2 max-h-60 overflow-y-auto">
                    @foreach($document->embeddings as $emb)
                        <div class="p-2 bg-gray-50 rounded text-xs">
                            <span class="font-medium">Chunk #{{ $emb->chunk_index }}</span>
                            <span class="text-gray-500 ml-2">| Qdrant: {{ $emb->qdrant_point_id ?? 'No indexado' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="mt-6 flex space-x-3">
            <a href="{{ route('documents.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Volver</a>
            @if(!$document->is_validated && Auth::user()->can('document.validate'))
                <form method="POST" action="{{ route('documents.validate', $document) }}">
                    @csrf
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700">Validar</button>
                </form>
            @endif
            <form method="POST" action="{{ route('documents.destroy', $document) }}" onsubmit="return confirm('¿Eliminar documento?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700">Eliminar</button>
            </form>
        </div>
    </div>
</div>
@endsection
