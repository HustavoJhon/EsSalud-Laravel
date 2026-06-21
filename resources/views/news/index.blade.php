@extends('layouts.app')
@section('title', 'Noticias')
@section('page_title', 'Noticias')
@section('content')
<div class="bg-white rounded-lg shadow p-4 mb-6">
    <form method="GET" action="{{ route('news.index') }}" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
            <input type="text" name="search" value="{{ request('search') }}"
                class="px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="Título o extracto...">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
            <select name="category" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <option value="">Todas</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->slug }}" {{ request('category') == $cat->slug ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white text-sm rounded-lg hover:bg-primary-700">Buscar</button>
            @if(request()->anyFilled(['search','category']))
                <a href="{{ route('news.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Limpiar</a>
            @endif
        </div>
    </form>
</div>

@can('news.create')
<a href="{{ route('news.create') }}" class="inline-flex items-center space-x-2 bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 mb-4">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
    <span>Nueva Noticia</span>
</a>
@endcan

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($news as $item)
        <a href="{{ route('news.show', $item) }}" class="bg-white rounded-lg shadow hover:shadow-md transition-shadow overflow-hidden">
            @if($item->image_url)
                <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="w-full h-48 object-cover">
            @else
                <div class="w-full h-48 bg-primary-100 flex items-center justify-center">
                    <svg class="w-12 h-12 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                    </svg>
                </div>
            @endif
            <div class="p-4">
                <div class="flex items-center space-x-2 mb-2">
                    @if($item->category)
                        <span class="text-xs bg-primary-50 text-primary-700 px-2 py-0.5 rounded">{{ $item->category->name }}</span>
                    @endif
                    <span class="text-xs text-gray-400">{{ $item->published_at?->format('d/m/Y') }}</span>
                </div>
                <h3 class="font-semibold text-gray-800 mb-1 line-clamp-2">{{ $item->title }}</h3>
                <p class="text-sm text-gray-500 line-clamp-3">{{ $item->excerpt ?? Str::limit(strip_tags($item->content), 150) }}</p>
            </div>
        </a>
    @empty
        <div class="col-span-full text-center py-12 text-gray-500">
            No se encontraron noticias.
        </div>
    @endforelse
</div>

<div class="mt-6">
    {{ $news->links() }}
</div>
@endsection
