@extends('layouts.app')
@section('title', 'Inicio')
@section('page_title', 'Dashboard')
@section('content')

@php
    $userId = Auth::id();
    $myProcedures = App\Models\Procedure::where('user_id', $userId);
    $totalProcedures = $myProcedures->count();
    $pendingProcedures = (clone $myProcedures)->whereHas('status', fn($q) => $q->whereIn('code', ['BORRADOR','PENDIENTE','EN_REVISION','SUBSANACION']))->count();
    $approvedProcedures = (clone $myProcedures)->whereHas('status', fn($q) => $q->where('code', 'APROBADO'))->count();
    $rejectedProcedures = (clone $myProcedures)->whereHas('status', fn($q) => $q->where('code', 'RECHAZADO'))->count();
    $totalDocuments = App\Models\Document::where('user_id', $userId)->count();
    $chatSessions = App\Models\ChatSession::where('user_id', $userId)->where('is_active', true)->count();
    $recentProcedures = App\Models\Procedure::where('user_id', $userId)->with(['procedureType','status'])->latest()->take(5)->get();
    $recentNews = App\Models\News::where('is_active', true)->latest()->take(3)->get();
@endphp

{{-- Welcome Banner --}}
<div class="bg-gradient-to-r from-primary-700 to-primary-500 rounded-xl shadow-lg p-6 md:p-8 mb-6 text-white">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl md:text-2xl font-bold">¡Hola, {{ Auth::user()->full_name ?? Auth::user()->name }}!</h2>
            <p class="text-primary-100 mt-1 text-sm md:text-base">
                Gestiona tus trámites, consulta el chatbot y mantente al día con EsSalud.
            </p>
        </div>
        <div class="flex gap-2 shrink-0">
            <a href="{{ route('procedures.create') }}" class="inline-flex items-center space-x-2 bg-white text-primary-700 px-3 md:px-4 py-2.5 rounded-lg font-medium text-xs md:text-sm hover:bg-primary-50 transition-colors shadow-sm touch-feedback">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span class="hidden xs:inline">Nuevo Trámite</span>
                <span class="xs:hidden">+</span>
            </a>
            <a href="{{ route('chat.index') }}" class="inline-flex items-center space-x-2 bg-primary-600 text-white px-3 md:px-4 py-2.5 rounded-lg font-medium text-xs md:text-sm hover:bg-primary-800 transition-colors border border-primary-400 touch-feedback">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                </svg>
                <span class="hidden xs:inline">Chatbot</span>
                <span class="xs:hidden">IA</span>
            </a>
        </div>
    </div>
</div>

{{-- Stats Grid --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 md:gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-primary-500 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-xs text-gray-400 font-medium uppercase tracking-wider">Trámites</div>
                <div class="text-2xl font-bold text-gray-800 mt-0.5">{{ $totalProcedures }}</div>
            </div>
            <div class="w-9 h-9 bg-primary-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-yellow-500 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-xs text-gray-400 font-medium uppercase tracking-wider">En Proceso</div>
                <div class="text-2xl font-bold text-gray-800 mt-0.5">{{ $pendingProcedures }}</div>
            </div>
            <div class="w-9 h-9 bg-yellow-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-green-500 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-xs text-gray-400 font-medium uppercase tracking-wider">Aprobados</div>
                <div class="text-2xl font-bold text-gray-800 mt-0.5">{{ $approvedProcedures }}</div>
            </div>
            <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-red-500 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-xs text-gray-400 font-medium uppercase tracking-wider">Rechazados</div>
                <div class="text-2xl font-bold text-gray-800 mt-0.5">{{ $rejectedProcedures }}</div>
            </div>
            <div class="w-9 h-9 bg-red-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-purple-500 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-xs text-gray-400 font-medium uppercase tracking-wider">Documentos</div>
                <div class="text-2xl font-bold text-gray-800 mt-0.5">{{ $totalDocuments }}</div>
            </div>
            <div class="w-9 h-9 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-teal-500 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-xs text-gray-400 font-medium uppercase tracking-wider">Chats</div>
                <div class="text-2xl font-bold text-gray-800 mt-0.5">{{ $chatSessions }}</div>
            </div>
            <div class="w-9 h-9 bg-teal-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            </div>
        </div>
    </div>
</div>

{{-- Charts + Recent --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Status Distribution --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-base font-semibold text-gray-800 mb-4">Distribución de Trámites</h3>
        @if($totalProcedures > 0)
            @php
                $statuses = App\Models\ProcedureStatus::all()->keyBy('code');
                $counts = App\Models\Procedure::where('user_id', $userId)
                    ->selectRaw('procedure_status_id, COUNT(*) as count')
                    ->groupBy('procedure_status_id')->pluck('count','procedure_status_id');
                $colors = ['BORRADOR'=>'#94a3b8','PENDIENTE'=>'#3b82f6','EN_REVISION'=>'#eab308','SUBSANACION'=>'#f97316','APROBADO'=>'#22c55e','RECHAZADO'=>'#ef4444','CANCELADO'=>'#6b7280'];
            @endphp
            <div class="space-y-3">
                @foreach($statuses as $status)
                    @php $count = $counts[$status->id] ?? 0; $pct = $totalProcedures > 0 ? round(($count / $totalProcedures) * 100) : 0; @endphp
                    @if($count > 0)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">{{ $status->name }}</span>
                            <span class="font-medium text-gray-800">{{ $count }} ({{ $pct }}%)</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5">
                            <div class="h-2.5 rounded-full transition-all duration-500" style="width: {{ $pct }}%; background-color: {{ $colors[$status->code] ?? '#94a3b8' }}"></div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <p class="text-gray-400 text-sm">Sin trámites aún</p>
                <a href="{{ route('procedures.create') }}" class="text-primary-600 text-sm hover:underline mt-1 inline-block">Crear primer trámite</a>
            </div>
        @endif
    </div>

    {{-- Recent Procedures --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-800">Últimos Trámites</h3>
            <a href="{{ route('procedures.index') }}" class="text-xs text-primary-600 hover:underline">Ver todos</a>
        </div>
        @if($recentProcedures->isEmpty())
            <div class="text-center py-8">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p class="text-gray-400 text-sm">No tienes trámites aún</p>
            </div>
        @else
            <div class="space-y-2">
                @foreach($recentProcedures as $proc)
                    <a href="{{ route('procedures.show', $proc) }}" class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors border border-transparent hover:border-gray-200">
                        <div class="min-w-0 flex-1">
                            <div class="font-medium text-gray-800 text-sm truncate">{{ $proc->procedureType->name ?? 'Trámite #'.$proc->id }}</div>
                            <div class="text-xs text-gray-400">{{ $proc->created_at->diffForHumans() }}</div>
                        </div>
                        <span class="shrink-0 ml-3 inline-block px-2.5 py-1 text-xs font-medium rounded-full
                            @if($proc->status->code === 'APROBADO') bg-green-100 text-green-700
                            @elseif($proc->status->code === 'RECHAZADO') bg-red-100 text-red-700
                            @elseif($proc->status->code === 'SUBSANACION') bg-orange-100 text-orange-700
                            @elseif($proc->status->code === 'CANCELADO') bg-gray-100 text-gray-600
                            @elseif(in_array($proc->status->code, ['PENDIENTE','EN_REVISION'])) bg-blue-100 text-blue-700
                            @else bg-gray-100 text-gray-600 @endif">
                            {{ $proc->status->name }}
                        </span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Quick Links + News --}}
    <div class="flex flex-col gap-6">
        {{-- Quick Actions --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-base font-semibold text-gray-800 mb-4">Acceso Rápido</h3>
            <div class="grid grid-cols-2 gap-2">
                <a href="{{ route('procedures.create') }}" class="flex flex-col items-center p-3 rounded-xl bg-primary-50 hover:bg-primary-100 transition-colors text-center">
                    <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center mb-2">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <span class="text-xs font-medium text-primary-700">Nuevo Trámite</span>
                </a>
                <a href="{{ route('chat.index') }}" class="flex flex-col items-center p-3 rounded-xl bg-green-50 hover:bg-green-100 transition-colors text-center">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mb-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    </div>
                    <span class="text-xs font-medium text-green-700">Chat IA</span>
                </a>
                <a href="{{ route('documents.index') }}" class="flex flex-col items-center p-3 rounded-xl bg-purple-50 hover:bg-purple-100 transition-colors text-center">
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mb-2">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <span class="text-xs font-medium text-purple-700">Documentos</span>
                </a>
                <a href="{{ route('faq.index') }}" class="flex flex-col items-center p-3 rounded-xl bg-orange-50 hover:bg-orange-100 transition-colors text-center">
                    <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mb-2">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="text-xs font-medium text-orange-700">FAQ</span>
                </a>
            </div>
        </div>

        {{-- Recent News --}}
        @if($recentNews->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-800">Últimas Noticias</h3>
                <a href="{{ route('news.index') }}" class="text-xs text-primary-600 hover:underline">Ver todas</a>
            </div>
            <div class="space-y-3">
                @foreach($recentNews as $newsItem)
                    <a href="{{ route('news.show', $newsItem) }}" class="block p-3 rounded-lg hover:bg-gray-50 transition-colors border border-transparent hover:border-gray-200">
                        <div class="text-sm font-medium text-gray-800 line-clamp-2">{{ $newsItem->title }}</div>
                        <div class="flex items-center gap-2 mt-1.5">
                            <span class="text-xs text-gray-400">{{ $newsItem->published_at ? $newsItem->published_at->diffForHumans() : $newsItem->created_at->diffForHumans() }}</span>
                            @if($newsItem->category)
                            <span class="text-xs bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded">{{ $newsItem->category->name }}</span>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
