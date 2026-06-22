@extends('layouts.public')
@section('title', 'Preguntas Frecuentes')
@section('content')
{{-- Header --}}
<section class="bg-gradient-to-br from-primary-800 to-primary-600 py-12 md:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 text-center">
        <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-3">Preguntas Frecuentes</h1>
        <p class="text-primary-100 text-sm md:text-base max-w-xl mx-auto mb-6">Encuentra respuestas rápidas sobre trámites, subsidios, afiliaciones y más</p>
        <form method="GET" action="{{ route('faq.index') }}" class="max-w-lg mx-auto flex gap-2">
            <input type="text" name="search" value="{{ $search ?? '' }}"
                class="flex-1 px-4 py-3 rounded-xl border-0 text-sm focus:ring-2 focus:ring-white/50 bg-white/10 text-white placeholder:text-primary-200 outline-none backdrop-blur"
                placeholder="Buscar en 204 preguntas...">
            <button type="submit" class="bg-white text-primary-700 px-5 py-3 rounded-xl font-medium text-sm hover:bg-primary-50 transition-colors">Buscar</button>
            @if(request('search'))
                <a href="{{ route('faq.index') }}" class="text-primary-200 hover:text-white text-sm self-center ml-1">✕</a>
            @endif
        </form>
    </div>
</section>

{{-- Content --}}
<section class="py-10 md:py-14">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">
        @if(isset($faqs) && $faqs->isNotEmpty())
            <p class="text-sm text-gray-500 mb-6">Resultados para "{{ $search }}" — {{ $faqs->total() }} encontrados</p>
            <div class="space-y-3">
                @foreach($faqs as $faq)
                    <div class="bg-white rounded-xl border border-gray-100 hover:shadow-md transition-all duration-200" x-data="{ open: false }">
                        <div @click="open = !open" class="px-5 py-4 cursor-pointer flex items-start justify-between gap-4 select-none">
                            <div class="flex items-start gap-3 flex-1">
                                <div class="w-8 h-8 bg-primary-50 rounded-lg flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-primary-600 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-800 text-sm md:text-base" :class="open ? 'text-primary-700' : ''">{{ $faq->question }}</span>
                                    @if($faq->category)
                                        <span class="inline-block ml-2 text-xs bg-primary-50 text-primary-600 px-2 py-0.5 rounded-full">{{ $faq->category->name }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             class="px-5 pb-4 pl-16">
                            <p class="text-sm text-gray-600 leading-relaxed">{{ $faq->answer }}</p>
                            <div class="flex items-center gap-4 mt-3 text-xs text-gray-400">
                                <button onclick="event.preventDefault(); helpfulFaq({{ $faq->id }})" class="flex items-center gap-1 hover:text-green-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path></svg> Útil ({{ $faq->helpful_count }})
                                </button>
                                <button onclick="event.preventDefault(); notHelpfulFaq({{ $faq->id }})" class="flex items-center gap-1 hover:text-red-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.096c.5 0 .905-.405.905-.904 0-.715.211-1.413.608-2.008L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5"></path></svg> No útil ({{ $faq->not_helpful_count }})
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-8">{{ $faqs->links() }}</div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                <div class="lg:col-span-1">
                    <div class="sticky top-20 space-y-1">
                        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 pl-4">Categorías</div>
                        @foreach($categories as $cat)
                            @if($cat->faqs->isNotEmpty())
                                <a href="#cat-{{ $cat->id }}" class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                    <span class="text-base">{{ match($cat->icon) { 'people' => '👥', 'pregnant_woman' => '🤰', 'child_care' => '👶', 'church' => '⚰️', 'paid' => '💰', 'local_hospital' => '🏥', 'description' => '📄', 'account_circle' => '👤', 'help' => '❓', default => '📌' } }}</span>
                                    <span class="truncate">{{ $cat->name }}</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>

                <div class="lg:col-span-4 space-y-10">
                    @foreach($categories as $category)
                        @if($category->faqs->isNotEmpty())
                            <div id="cat-{{ $category->id }}">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-10 h-10 bg-primary-100 rounded-xl flex items-center justify-center text-xl shrink-0">
                                        {{ match($category->icon) { 'people' => '👥', 'pregnant_woman' => '🤰', 'child_care' => '👶', 'church' => '⚰️', 'paid' => '💰', 'local_hospital' => '🏥', 'description' => '📄', 'account_circle' => '👤', 'help' => '❓', default => '📌' } }}
                                    </div>
                                    <div>
                                        <h2 class="text-lg md:text-xl font-bold text-gray-900">{{ $category->name }}</h2>
                                        <p class="text-xs text-gray-400">{{ $category->faqs->count() }} preguntas</p>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    @foreach($category->faqs as $faq)
                                        <div class="bg-white rounded-xl border border-gray-100 hover:shadow-md transition-all duration-200" x-data="{ open: false }">
                                            <div @click="open = !open" class="px-5 py-4 cursor-pointer flex items-start justify-between gap-4 select-none">
                                                <div class="flex items-start gap-3 flex-1">
                                                    <svg class="w-5 h-5 text-gray-300 mt-0.5 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180 text-primary-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                    <span class="font-medium text-gray-800 text-sm md:text-base" :class="open ? 'text-primary-700' : ''">{{ $faq->question }}</span>
                                                </div>
                                            </div>
                                            <div x-show="open"
                                                 x-transition:enter="transition ease-out duration-200"
                                                 x-transition:enter-start="opacity-0"
                                                 x-transition:enter-end="opacity-100"
                                                 class="px-5 pb-5 pl-12">
                                                <p class="text-sm text-gray-600 leading-relaxed border-l-2 border-primary-200 pl-4">{{ $faq->answer }}</p>
                                                <div class="flex items-center gap-4 mt-3 text-xs text-gray-400">
                                                    <button onclick="event.preventDefault(); helpfulFaq({{ $faq->id }})" class="flex items-center gap-1 hover:text-green-500 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path></svg> Útil ({{ $faq->helpful_count }})
                                                    </button>
                                                    <button onclick="event.preventDefault(); notHelpfulFaq({{ $faq->id }})" class="flex items-center gap-1 hover:text-red-500 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.096c.5 0 .905-.405.905-.904 0-.715.211-1.413.608-2.008L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5"></path></svg> No útil ({{ $faq->not_helpful_count }})
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>

@push('scripts')
<script>
async function helpfulFaq(id) {
    try { await fetch(`/faq/${id}/helpful`, { method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'} }); } catch(e) {}
}
async function notHelpfulFaq(id) {
    try { await fetch(`/faq/${id}/not-helpful`, { method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'} }); } catch(e) {}
}
</script>
@endpush
@endsection
