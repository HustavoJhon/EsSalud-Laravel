@extends('layouts.app')
@section('title', $news->title)
@section('page_title', $news->title)
@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        @if($news->image_url)
            <img src="{{ $news->image_url }}" alt="{{ $news->title }}" class="w-full max-h-96 object-cover rounded-lg mb-6">
        @endif

        <div class="flex items-center space-x-3 text-sm text-gray-500 mb-6">
            <span>{{ $news->author->full_name ?? $news->author->name }}</span>
            <span>·</span>
            <span>{{ $news->published_at?->format('d/m/Y H:i') }}</span>
            @if($news->category)
                <span>·</span>
                <span class="bg-primary-50 text-primary-700 px-2 py-0.5 rounded text-xs">{{ $news->category->name }}</span>
            @endif
        </div>

        <div class="prose max-w-none">
            {!! nl2br(e($news->content)) !!}
        </div>

        <div class="mt-8 flex items-center space-x-3 border-t pt-6">
            <a href="{{ route('news.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Volver</a>
            @can('news.update')
                <a href="{{ route('news.edit', $news) }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg text-sm hover:bg-primary-700">Editar</a>
            @endcan
        </div>
    </div>
</div>
@endsection
