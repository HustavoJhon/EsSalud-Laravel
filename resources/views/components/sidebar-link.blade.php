@props(['route', 'icon'])

@php
    $isActive = request()->routeIs($route);
    $classes = $isActive
        ? 'bg-primary-700 text-white'
        : 'text-primary-100 hover:bg-primary-700 hover:text-white';
@endphp

<a href="{{ route($route) }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ $classes }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"></path>
    </svg>
    <span>{{ $slot }}</span>
</a>
