@extends('layouts.public')
@section('title', 'Inicio')
@section('content')

@php
    $faqCount = App\Models\Faq::where('is_active', true)->count();
    $newsCount = App\Models\News::where('is_active', true)->count();
    $recentNews = App\Models\News::where('is_active', true)->latest()->take(3)->get();
    $faqCategories = App\Models\FaqCategory::has('faqs')->withCount('faqs')->orderBy('sort_order')->get();
@endphp

{{-- Hero --}}
<section class="relative overflow-hidden bg-gradient-to-br from-primary-800 via-primary-700 to-primary-600">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 left-10 w-80 h-80 bg-white rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 right-10 w-96 h-96 bg-primary-300 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-white rounded-full blur-2xl"></div>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-20 md:py-28 relative z-10">
        <div class="text-center max-w-3xl mx-auto">
            <div class="w-16 h-16 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            </div>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white tracking-tight mb-4">Plataforma de Atención al Asegurado</h1>
            <p class="text-lg md:text-xl text-primary-100 mb-8 max-w-2xl mx-auto">Gestiona tus trámites, consulta al asistente virtual y mantente informado. Todo en un solo lugar.</p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                @auth
                    <a href="{{ route('procedures.index') }}" class="w-full sm:w-auto bg-white text-primary-700 px-8 py-3.5 rounded-xl font-semibold hover:bg-primary-50 transition-colors shadow-lg text-center">Mis Trámites</a>
                    <a href="{{ route('chat.index') }}" class="w-full sm:w-auto bg-primary-500 text-white px-8 py-3.5 rounded-xl font-semibold hover:bg-primary-400 transition-colors border border-primary-400 text-center">Chatbot IA</a>
                @else
                    <a href="{{ route('register') }}" class="w-full sm:w-auto bg-white text-primary-700 px-8 py-3.5 rounded-xl font-semibold hover:bg-primary-50 transition-colors shadow-lg text-center">Crear Cuenta Gratis</a>
                    <a href="{{ route('login') }}" class="w-full sm:w-auto bg-primary-500 text-white px-8 py-3.5 rounded-xl font-semibold hover:bg-primary-400 transition-colors border border-primary-400 text-center">Iniciar Sesión</a>
                    <a href="{{ route('chat.index') }}" class="w-full sm:w-auto bg-white/20 text-white px-8 py-3.5 rounded-xl font-semibold hover:bg-white/30 transition-colors border border-white/40 text-center">Chatbot IA</a>
                @endauth
            </div>
        </div>
    </div>
</section>

{{-- Features --}}
<section class="py-16 md:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-3">Todo lo que necesitas</h2>
            <p class="text-gray-500 max-w-2xl mx-auto">Herramientas diseñadas para hacer tus trámites más rápidos y sencillos</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-gray-50 rounded-2xl p-6 hover:shadow-md transition-shadow border border-gray-100">
                <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Trámites en Línea</h3>
                <p class="text-sm text-gray-500">Crea, envía y da seguimiento a tus trámites sin salir de casa. 6 tipos disponibles.</p>
            </div>
            <div class="bg-gray-50 rounded-2xl p-6 hover:shadow-md transition-shadow border border-gray-100">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Chatbot Inteligente</h3>
                <p class="text-sm text-gray-500">Asistente virtual 24/7 con {{ $faqCount }} preguntas frecuentes. Respuestas instantáneas.</p>
            </div>
            <div class="bg-gray-50 rounded-2xl p-6 hover:shadow-md transition-shadow border border-gray-100">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Documentos Digitales</h3>
                <p class="text-sm text-gray-500">Sube tus documentos, valida requisitos y ten todo organizado en un solo lugar.</p>
            </div>
            <div class="bg-gray-50 rounded-2xl p-6 hover:shadow-md transition-shadow border border-gray-100">
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Noticias y Avisos</h3>
                <p class="text-sm text-gray-500">Mantente al día con noticias, avisos importantes y actualizaciones de EsSalud.</p>
            </div>
        </div>
    </div>
</section>

{{-- FAQ Categories + News --}}
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- FAQ Categories --}}
            <div>
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Preguntas Frecuentes</h2>
                    <a href="{{ route('faq.index') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">Ver todas →</a>
                </div>
                <div class="space-y-3">
                    @foreach($faqCategories->take(5) as $cat)
                        <a href="{{ route('faq.index') }}" class="flex items-center justify-between p-4 bg-white rounded-xl hover:shadow-md transition-shadow border border-gray-100">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-primary-50 rounded-lg flex items-center justify-center">
                                    <span class="text-lg">{{ match($cat->icon) { 'people' => '👥', 'pregnant_woman' => '🤰', 'child_care' => '👶', 'church' => '⚰️', 'paid' => '💰', 'local_hospital' => '🏥', 'description' => '📄', 'account_circle' => '👤', 'help' => '❓', default => '📌' } }}</span>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-800 text-sm">{{ $cat->name }}</div>
                                    <div class="text-xs text-gray-400">{{ $cat->faqs_count }} preguntas</div>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Recent News --}}
            <div>
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Últimas Noticias</h2>
                    <a href="{{ route('news.index') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">Ver todas →</a>
                </div>
                @if($recentNews->isEmpty())
                    <div class="bg-white rounded-xl p-8 text-center border border-gray-100">
                        <p class="text-gray-400">No hay noticias publicadas aún.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($recentNews as $item)
                            <a href="{{ route('news.show', $item) }}" class="block bg-white rounded-xl p-5 hover:shadow-md transition-shadow border border-gray-100">
                                <h3 class="font-semibold text-gray-800 mb-1.5 line-clamp-2">{{ $item->title }}</h3>
                                <p class="text-sm text-gray-500 line-clamp-2 mb-2">{{ $item->excerpt ?? Str::limit(strip_tags($item->content), 120) }}</p>
                                <div class="flex items-center gap-3 text-xs text-gray-400">
                                    <span>{{ $item->published_at ? $item->published_at->format('d/m/Y') : $item->created_at->format('d/m/Y') }}</span>
                                    @if($item->category)
                                        <span class="bg-gray-100 px-2 py-0.5 rounded-full">{{ $item->category->name }}</span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- CTA --}}
@guest
<section class="py-16 bg-white">
    <div class="max-w-3xl mx-auto text-center px-4 sm:px-6">
        <h2 class="text-3xl font-bold text-gray-900 mb-3">¿Listo para empezar?</h2>
        <p class="text-gray-500 mb-8">Crea tu cuenta gratis y accede a todos los servicios.</p>
        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-primary-600 text-white px-8 py-3.5 rounded-xl font-semibold hover:bg-primary-700 transition-colors shadow-lg shadow-primary-600/25">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            Crear Cuenta Gratis
        </a>
    </div>
</section>
@endguest
@endsection
