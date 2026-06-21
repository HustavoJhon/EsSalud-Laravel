@extends('layouts.app')
@section('title', 'Inicio')
@section('page_title', 'Bienvenido, ' . (Auth::user()->full_name ?? Auth::user()->name))
@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    @php
        $myProcedures = App\Models\Procedure::where('user_id', Auth::id());
        $totalProcedures = $myProcedures->count();
        $pendingProcedures = (clone $myProcedures)->whereHas('status', fn($q) => $q->whereIn('code', ['BORRADOR','RADICADO','EVALUACION','SUBSANACION']))->count();
        $approvedProcedures = (clone $myProcedures)->whereHas('status', fn($q) => $q->where('code', 'APROBADO'))->count();
        $totalDocuments = App\Models\Document::where('user_id', Auth::id())->count();
    @endphp
    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-sm text-gray-500 font-medium">Total Trámites</div>
        <div class="text-3xl font-bold text-primary-600 mt-1">{{ $totalProcedures }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-sm text-gray-500 font-medium">En Proceso</div>
        <div class="text-3xl font-bold text-yellow-500 mt-1">{{ $pendingProcedures }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-sm text-gray-500 font-medium">Aprobados</div>
        <div class="text-3xl font-bold text-green-500 mt-1">{{ $approvedProcedures }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-sm text-gray-500 font-medium">Documentos</div>
        <div class="text-3xl font-bold text-purple-500 mt-1">{{ $totalDocuments }}</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Acciones Rápidas</h3>
        <div class="grid grid-cols-2 gap-3">
            <a href="{{ route('procedures.create') }}" class="flex items-center space-x-3 p-3 bg-primary-50 rounded-lg hover:bg-primary-100 transition-colors">
                <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span class="font-medium text-primary-700">Nuevo Trámite</span>
            </a>
            <a href="{{ route('procedures.index') }}" class="flex items-center space-x-3 p-3 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <span class="font-medium text-yellow-700">Mis Trámites</span>
            </a>
            <a href="{{ route('chat.index') }}" class="flex items-center space-x-3 p-3 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                </svg>
                <span class="font-medium text-green-700">Chat de Ayuda</span>
            </a>
            <a href="{{ route('faq.index') }}" class="flex items-center space-x-3 p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-medium text-purple-700">Preguntas Frecuentes</span>
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Mis Últimos Trámites</h3>
        @php $recentProcedures = App\Models\Procedure::where('user_id', Auth::id())->with(['procedureType','status'])->latest()->take(5)->get(); @endphp
        @if($recentProcedures->isEmpty())
            <p class="text-gray-500 text-sm">No tienes trámites aún. <a href="{{ route('procedures.create') }}" class="text-primary-600 hover:underline">Crea tu primer trámite</a>.</p>
        @else
            <div class="space-y-3">
                @foreach($recentProcedures as $proc)
                    <a href="{{ route('procedures.show', $proc) }}" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div>
                            <div class="font-medium text-gray-800">{{ $proc->procedureType->name }}</div>
                            <div class="text-xs text-gray-500">{{ $proc->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                        <span class="inline-block px-2 py-1 text-xs font-medium rounded-full
                            @if($proc->status->code === 'APROBADO') bg-green-100 text-green-800
                            @elseif($proc->status->code === 'RECHAZADO') bg-red-100 text-red-800
                            @elseif($proc->status->code === 'SUBSANACION') bg-orange-100 text-orange-800
                            @elseif($proc->status->code === 'CANCELADO') bg-gray-100 text-gray-800
                            @else bg-blue-100 text-blue-800 @endif">
                            {{ $proc->status->name }}
                        </span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
