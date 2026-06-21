@extends('layouts.app')
@section('title', 'Nueva Noticia')
@section('page_title', 'Nueva Noticia')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('news.store') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                <input type="text" name="title" value="{{ old('title') }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                <select name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">Sin categoría</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Extracto</label>
                <textarea name="excerpt" rows="2" maxlength="500"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">{{ old('excerpt') }}</textarea>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Contenido</label>
                <textarea name="content" rows="10" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">{{ old('content') }}</textarea>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">URL de Imagen</label>
                <input type="url" name="image_url" value="{{ old('image_url') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="https://...">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Publicación</label>
                <input type="datetime-local" name="published_at" value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
            <div class="flex space-x-3">
                <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700">Publicar</button>
                <a href="{{ route('news.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
