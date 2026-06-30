@extends('layouts.public')
@section('title', $news->title)
@section('content')
<article class="max-w-3xl mx-auto px-4 sm:px-6 py-8 md:py-12">
    {{-- Back link --}}
    <a href="{{ route('news.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-primary-600 mb-6 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        Volver a Noticias
    </a>

    {{-- Article --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @if($news->image_url)
            <img src="{{ $news->image_url }}" alt="{{ $news->title }}" class="w-full h-56 sm:h-72 md:h-96 object-cover">
        @else
            <div class="w-full h-32 sm:h-40 bg-gradient-to-r from-primary-600 to-primary-500"></div>
        @endif

        <div class="p-6 md:p-8">
            {{-- Meta --}}
            <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500 mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-primary-100 rounded-full flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <span class="font-medium text-gray-700">{{ $news->author->full_name ?? $news->author->name ?? 'EsSalud' }}</span>
                </div>
                <span class="text-gray-300">·</span>
                <div class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span>{{ $news->published_at ? $news->published_at->format('d \d\e F, Y \a \l\a\s H:i') : $news->created_at->format('d \d\e F, Y') }}</span>
                </div>
                @if($news->category)
                    <span class="text-gray-300 hidden sm:inline">·</span>
                    <span class="bg-primary-50 text-primary-700 px-2.5 py-0.5 rounded-full text-xs font-medium">{{ $news->category->name }}</span>
                @endif
            </div>

            {{-- Title --}}
            <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 mb-6 leading-tight">{{ $news->title }}</h1>

            {{-- Content --}}
            <div class="prose prose-sm md:prose-base max-w-none text-gray-700 leading-relaxed">
                {!! nl2br(e($news->content)) !!}
            </div>

            {{-- Actions --}}
            <div class="mt-10 pt-6 border-t border-gray-100 flex flex-wrap items-center gap-3">
                @can('news.update')
                    <a href="{{ route('news.edit', $news) }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-primary-600 text-white rounded-xl text-sm font-medium hover:bg-primary-700 transition-colors touch-feedback">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Editar
                    </a>
                @endcan
                @can('news.delete')
                    <form method="POST" action="{{ route('news.destroy', $news) }}" onsubmit="return confirm('¿Eliminar esta noticia?')" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2.5 border border-red-200 text-red-600 rounded-xl text-sm font-medium hover:bg-red-50 transition-colors touch-feedback">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Eliminar
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </div>
</article>
@endsection
