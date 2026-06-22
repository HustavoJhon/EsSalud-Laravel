@props(['route', 'icon', 'close' => null])

@php
    $isActive = request()->routeIs($route . '*') || request()->routeIs($route);
    $baseClasses = 'flex items-center rounded-lg text-sm font-medium transition-all duration-200';
    $activeClasses = 'bg-primary-700 text-white';
    $inactiveClasses = 'text-primary-100 hover:bg-primary-700 hover:text-white';
@endphp

<a href="{{ route($route) }}" @if($close) x-on:click="{{ $close }}" @endif
   class="{{ $baseClasses }} {{ $isActive ? $activeClasses : $inactiveClasses }}"
   :class="sidebarCollapsed ? 'justify-center p-3' : 'space-x-3 px-3 py-2'">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"></path>
    </svg>
    <span x-show="!sidebarCollapsed" x-transition:enter="transition-opacity duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">{{ $slot }}</span>
</a>
