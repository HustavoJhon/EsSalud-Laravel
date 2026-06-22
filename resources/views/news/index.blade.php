@extends('layouts.public')
@section('title', 'Noticias')
@section('content')
{{-- Header --}}
<section class="bg-gradient-to-br from-primary-800 to-primary-600 py-12 md:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 text-center">
        <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-3">Noticias EsSalud</h1>
        <p class="text-primary-100 text-sm md:text-base max-w-xl mx-auto">Mantente informado sobre avisos, actualizaciones y novedades</p>
    </div>
</section>

{{-- Filters --}}
<section class="bg-white border-b border-gray-200 sticky top-16 z-30">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3">
        <form method="GET" action="{{ route('news.index') }}" class="flex flex-wrap items-end gap-2 md:gap-3">
            <div class="flex-1 min-w-[140px]">
                <input type="text" name="search" value="{{ request('search') }}"
                    class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none bg-gray-50 focus:bg-white"
                    placeholder="Buscar noticias...">
            </div>
            <div class="w-[130px] md:w-40">
                <select name="category" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 outline-none bg-gray-50">
                    <option value="">Todas</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->slug }}" {{ request('category') == $cat->slug ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2.5 bg-primary-600 text-white text-sm rounded-xl hover:bg-primary-700 transition-colors font-medium">Buscar</button>
            @if(request()->anyFilled(['search','category']))
                <a href="{{ route('news.index') }}" class="px-3 py-2.5 text-sm text-gray-500 hover:text-gray-700">Limpiar</a>
            @endif
            @can('news.create')
            <a href="{{ route('news.create') }}" class="ml-auto flex items-center gap-1.5 px-4 py-2.5 bg-green-600 text-white text-sm rounded-xl hover:bg-green-700 transition-colors font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span class="hidden sm:inline">Nueva</span>
            </a>
            @endcan
        </form>
    </div>
</section>

{{-- News grid --}}
<section class="py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 md:gap-6">
            @forelse($news as $item)
                <a href="{{ route('news.show', $item) }}" class="group bg-white rounded-xl border border-gray-100 hover:shadow-lg hover:-translate-y-1 transition-all duration-200 overflow-hidden flex flex-col">
                    <div class="relative overflow-hidden">
                        @if($item->image_url)
                            <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="w-full h-44 sm:h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-44 sm:h-48 bg-gradient-to-br from-primary-100 to-primary-50 flex items-center justify-center">
                                <svg class="w-12 h-12 text-primary-200 group-hover:text-primary-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                            </div>
                        @endif
                    </div>
                    <div class="p-4 md:p-5 flex-1 flex flex-col">
                        <div class="flex items-center gap-2 mb-2">
                            @if($item->category)
                                <span class="text-xs bg-primary-50 text-primary-700 px-2 py-0.5 rounded-full font-medium">{{ $item->category->name }}</span>
                            @endif
                            <span class="text-xs text-gray-400">{{ $item->published_at ? $item->published_at->format('d/m/Y') : $item->created_at->format('d/m/Y') }}</span>
                        </div>
                        <h3 class="font-semibold text-gray-800 mb-1.5 line-clamp-2 group-hover:text-primary-600 transition-colors text-sm md:text-base">{{ $item->title }}</h3>
                        <p class="text-xs md:text-sm text-gray-500 line-clamp-2 flex-1">{{ $item->excerpt ?? Str::limit(strip_tags($item->content), 120) }}</p>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center py-16">
                    <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                    </div>
                    <p class="text-gray-400 font-medium">No se encontraron noticias</p>
                    <p class="text-gray-300 text-sm mt-1">Intenta con otros filtros</p>
                </div>
            @endforelse
        </div>
        <div class="mt-8">{{ $news->links() }}</div>
    </div>
</section>
@endsection
