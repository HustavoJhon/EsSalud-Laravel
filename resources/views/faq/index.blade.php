@extends('layouts.app')
@section('title', 'Preguntas Frecuentes')
@section('page_title', 'Preguntas Frecuentes')
@section('content')
<div class="mb-6">
    <form method="GET" action="{{ route('faq.index') }}" class="flex space-x-3">
        <input type="text" name="search" value="{{ $search ?? '' }}"
            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 text-sm"
            placeholder="Buscar en preguntas frecuentes...">
        <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg text-sm hover:bg-primary-700">
            Buscar
        </button>
        @if(request('search'))
            <a href="{{ route('faq.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 flex items-center">Limpiar</a>
        @endif
    </form>
</div>

@if(isset($faqs) && $faqs->isNotEmpty())
    <div class="space-y-4">
        @foreach($faqs as $faq)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-semibold text-gray-800 mb-2">{{ $faq->question }}</h3>
                <p class="text-sm text-gray-600 mb-3">{{ $faq->answer }}</p>
                <div class="flex items-center space-x-3 text-xs text-gray-400">
                    @if($faq->category)
                        <span class="bg-primary-50 text-primary-700 px-2 py-0.5 rounded">{{ $faq->category->name }}</span>
                    @endif
                    <span>{{ $faq->view_count }} vistas</span>
                    <button onclick="helpfulFaq({{ $faq->id }})" class="hover:text-green-500">👍 {{ $faq->helpful_count }}</button>
                    <button onclick="notHelpfulFaq({{ $faq->id }})" class="hover:text-red-500">👎 {{ $faq->not_helpful_count }}</button>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-4">
        {{ $faqs->links() }}
    </div>
@else
    <div class="space-y-8">
        @foreach($categories as $category)
            @if($category->faqs->isNotEmpty())
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center space-x-2">
                        <span>{{ $category->name }}</span>
                    </h2>
                    <div class="space-y-3">
                        @foreach($category->faqs as $faq)
                            <details class="bg-white rounded-lg shadow group">
                                <summary class="px-6 py-4 cursor-pointer font-medium text-gray-800 hover:text-primary-600 transition-colors flex items-center justify-between">
                                    {{ $faq->question }}
                                    <svg class="w-5 h-5 text-gray-400 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </summary>
                                <div class="px-6 pb-4 text-sm text-gray-600">
                                    <p>{{ $faq->answer }}</p>
                                    <div class="flex items-center space-x-3 mt-3 text-xs text-gray-400">
                                        <button onclick="helpfulFaq({{ $faq->id }})" class="hover:text-green-500">👍 Útil ({{ $faq->helpful_count }})</button>
                                        <button onclick="notHelpfulFaq({{ $faq->id }})" class="hover:text-red-500">👎 No útil ({{ $faq->not_helpful_count }})</button>
                                    </div>
                                </div>
                            </details>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </div>
@endif

@push('scripts')
<script>
async function helpfulFaq(id) {
    try {
        await fetch(`/faq/${id}/helpful`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        });
    } catch(e) {}
}
async function notHelpfulFaq(id) {
    try {
        await fetch(`/faq/${id}/not-helpful`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        });
    } catch(e) {}
}
</script>
@endpush
@endsection
