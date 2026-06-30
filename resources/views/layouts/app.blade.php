<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>@yield('title', 'EsSalud') - Plataforma de Trámites</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        .sidebar-enter { transition: transform 0.2s ease-out; }
        .sidebar-leave { transition: transform 0.15s ease-in; }
        @media (max-width: 768px) {
            .table-responsive { display: block; width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen pb-20 md:pb-0" x-data="{ sidebarOpen: false, sidebarCollapsed: false }"
      @sidebar-toggle.window="sidebarCollapsed = !sidebarCollapsed">
    <div class="flex h-screen overflow-hidden">
    <!-- Mobile sidebar backdrop -->
    <div x-cloak x-show="sidebarOpen" x-on:click="sidebarOpen = false"
         x-transition:enter="transition-opacity ease-out duration-200"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-150"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden">
    </div>

    <!-- Sidebar -->
    <aside x-cloak x-show="sidebarOpen" x-on:click.outside="sidebarOpen = false"
           x-transition:enter="transition-transform ease-out duration-200"
           x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
           x-transition:leave="transition-transform ease-in duration-150"
           x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
           class="fixed inset-y-0 left-0 z-50 w-72 bg-primary-800 text-white flex flex-col shadow-2xl md:hidden">
        <div class="p-4 border-b border-primary-700 flex items-center justify-between">
            <div>
                <a href="{{ route('home') }}" class="text-2xl font-bold tracking-tight" x-on:click="sidebarOpen = false">EsSalud</a>
                <p class="text-primary-200 text-sm mt-1">Plataforma de Trámites</p>
            </div>
            <button x-on:click="sidebarOpen = false" class="text-white p-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
            <x-sidebar-link route="home" icon="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1" close="sidebarOpen = false">
                Inicio
            </x-sidebar-link>
            <x-sidebar-link route="procedures.index" icon="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" close="sidebarOpen = false">
                Trámites
            </x-sidebar-link>
            <x-sidebar-link route="procedures.create" icon="M12 6v6m0 0v6m0-6h6m-6 0H6" close="sidebarOpen = false">
                Nuevo Trámite
            </x-sidebar-link>
            <x-sidebar-link route="documents.index" icon="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" close="sidebarOpen = false">
                Documentos
            </x-sidebar-link>
            @can('news.create')
            <x-sidebar-link route="news.create" icon="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" close="sidebarOpen = false">
                Nueva Noticia
            </x-sidebar-link>
            @endcan
            <x-sidebar-link route="chat.index" icon="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" close="sidebarOpen = false">
                Chat
            </x-sidebar-link>
            <x-sidebar-link route="faq.index" icon="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" close="sidebarOpen = false">
                FAQ
            </x-sidebar-link>
            <x-sidebar-link route="news.index" icon="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" close="sidebarOpen = false">
                Noticias
            </x-sidebar-link>
            @can('manage-users')
            <x-sidebar-link route="admin.users.index" icon="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" close="sidebarOpen = false">
                Usuarios
            </x-sidebar-link>
            <x-sidebar-link route="admin.roles.index" icon="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" close="sidebarOpen = false">
                Roles
            </x-sidebar-link>
            @endcan
        </nav>
        <div class="p-4 border-t border-primary-700">
            <div class="text-sm text-primary-200 truncate">{{ Auth::user()->full_name ?? Auth::user()->name }}</div>
            <span class="inline-block bg-primary-600 text-xs px-2 py-0.5 rounded mt-1">{{ Auth::user()->role }}</span>
        </div>
    </aside>

    <!-- Desktop sidebar (collapsible on md+) -->
    <aside class="hidden md:flex flex-col bg-primary-800 text-white shadow-lg shrink-0 transition-all duration-300"
           :class="sidebarCollapsed ? 'w-16' : 'w-64'">
        <!-- Header -->
        <div class="flex items-center border-b border-primary-700" :class="sidebarCollapsed ? 'p-3 justify-center' : 'p-4 justify-between'">
            <div x-show="!sidebarCollapsed">
                <a href="{{ route('home') }}" class="text-2xl font-bold tracking-tight">EsSalud</a>
                <p class="text-primary-200 text-sm mt-1">Plataforma</p>
            </div>
            <button @click="sidebarCollapsed = !sidebarCollapsed"
                    class="text-primary-200 hover:text-white transition-colors p-1 rounded hover:bg-primary-700 shrink-0"
                    :class="sidebarCollapsed ? '' : ''"
                    title="Colapsar menú">
                <svg class="w-5 h-5 transition-transform duration-300" :class="sidebarCollapsed ? 'rotate-180' : ''"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                </svg>
            </button>
        </div>

        <!-- Nav -->
        <nav class="flex-1 overflow-y-auto py-4 space-y-1" :class="sidebarCollapsed ? 'px-2' : 'px-3'">
            <x-sidebar-link route="home" icon="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1">Inicio</x-sidebar-link>
            <x-sidebar-link route="procedures.index" icon="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">Trámites</x-sidebar-link>
            <x-sidebar-link route="procedures.create" icon="M12 6v6m0 0v6m0-6h6m-6 0H6">Nuevo Trámite</x-sidebar-link>
            <x-sidebar-link route="documents.index" icon="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">Documentos</x-sidebar-link>
            @can('news.create')
            <x-sidebar-link route="news.create" icon="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">Nueva Noticia</x-sidebar-link>
            @endcan
            <x-sidebar-link route="chat.index" icon="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z">Chat</x-sidebar-link>
            <x-sidebar-link route="faq.index" icon="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">FAQ</x-sidebar-link>
            <x-sidebar-link route="news.index" icon="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z">Noticias</x-sidebar-link>
            @can('manage-users')
            <x-sidebar-link route="admin.users.index" icon="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">Usuarios</x-sidebar-link>
            <x-sidebar-link route="admin.roles.index" icon="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">Roles</x-sidebar-link>
            @endcan
        </nav>

        <!-- Footer -->
        <div class="border-t border-primary-700" :class="sidebarCollapsed ? 'p-2' : 'p-4'">
            <div x-show="!sidebarCollapsed" class="text-sm text-primary-200 truncate">{{ Auth::user()->full_name ?? Auth::user()->name }}</div>
            <span x-show="!sidebarCollapsed" class="inline-block bg-primary-600 text-xs px-2 py-0.5 rounded mt-1">{{ Auth::user()->role }}</span>
            <div x-show="sidebarCollapsed" class="flex justify-center" title="{{ Auth::user()->full_name ?? Auth::user()->name }}">
                <svg class="w-6 h-6 text-primary-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
        </div>
    </aside>

    <!-- Main content area -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white shadow-sm border-b border-gray-200 shrink-0">
            <div class="flex items-center justify-between px-3 sm:px-6 py-2 sm:py-3">
                <div class="flex items-center space-x-2 sm:space-x-4">
                    <!-- Mobile hamburger -->
                    <button x-on:click="sidebarOpen = true" class="md:hidden p-2 -ml-2 text-gray-600 hover:text-primary-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <h1 class="text-base sm:text-lg font-semibold text-gray-800 truncate">@yield('page_title', 'Dashboard')</h1>
                </div>
                <div class="flex items-center space-x-2 sm:space-x-4">
                    <a href="{{ route('profile') }}" class="text-xs sm:text-sm text-gray-600 hover:text-primary-600 hidden sm:block">
                        Mi Perfil
                    </a>
                    <a href="{{ route('profile') }}" class="md:hidden p-2 text-gray-600 hover:text-primary-600" title="Mi Perfil">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-xs sm:text-sm text-red-600 hover:text-red-800">
                            Salir
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-3 sm:p-6">
            @if(session('status'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded text-sm sm:text-base">
                    {{ session('status') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded text-sm sm:text-base">
                    <ul class="list-disc ml-4">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
    </div>

    <!-- Mobile bottom navigation -->
    <nav class="md:hidden fixed bottom-0 inset-x-0 z-30 safe-area-bottom bg-white border-t border-gray-200 shadow-[0_-2px_10px_rgba(0,0,0,0.08)]">
        <div class="flex items-center justify-around h-16">
            <a href="{{ route('home') }}"
               class="flex flex-col items-center justify-center h-full min-w-[3rem] px-2 {{ request()->routeIs('home') ? 'bottom-nav-active' : 'text-gray-400' }} touch-feedback-light">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"/>
                </svg>
                <span class="text-[10px] mt-0.5 font-medium">Inicio</span>
            </a>
            <a href="{{ route('procedures.index') }}"
               class="flex flex-col items-center justify-center h-full min-w-[3rem] px-2 {{ request()->routeIs('procedures.*') && !request()->routeIs('procedures.create') ? 'bottom-nav-active' : 'text-gray-400' }} touch-feedback-light">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="text-[10px] mt-0.5 font-medium">Trámites</span>
            </a>
            <a href="{{ route('chat.index') }}"
               class="flex flex-col items-center justify-center h-full min-w-[3rem] px-2 -mt-4 relative">
                <div class="w-12 h-12 bg-primary-600 rounded-full flex items-center justify-center shadow-lg shadow-primary-600/30 active:scale-90 transition-transform duration-150 {{ request()->routeIs('chat.*') ? 'ring-4 ring-primary-200' : '' }}">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                </div>
                <span class="text-[10px] mt-1 font-medium {{ request()->routeIs('chat.*') ? 'text-primary-600' : 'text-gray-400' }}">Chat</span>
            </a>
            <a href="{{ route('procedures.create') }}"
               class="flex flex-col items-center justify-center h-full min-w-[3rem] px-2 text-gray-400 touch-feedback-light">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="text-[10px] mt-0.5 font-medium">Nuevo</span>
            </a>
            <button onclick="toggleMoreMenu()"
                    class="flex flex-col items-center justify-center h-full min-w-[3rem] px-2 {{ request()->routeIs('faq.*') || request()->routeIs('news.*') || request()->routeIs('documents.*') || request()->routeIs('admin.*') || request()->routeIs('profile') ? 'bottom-nav-active' : 'text-gray-400' }} touch-feedback-light">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                </svg>
                <span class="text-[10px] mt-0.5 font-medium">Más</span>
            </button>
        </div>
    </nav>

    {{-- More menu overlay + bottom sheet --}}
    <div id="more-overlay" class="hidden fixed inset-0 z-40">
        <div class="absolute inset-0 bg-black/50" onclick="closeMoreMenu()"></div>
        <div id="more-menu" class="absolute bottom-0 left-0 right-0 bg-white rounded-t-2xl shadow-xl px-3 pb-8 pt-2 safe-area-bottom animate-slide-up">
            <div class="flex justify-center mb-1">
                <div class="w-10 h-1 bg-gray-300 rounded-full"></div>
            </div>
            <div class="space-y-0.5">
                <a href="{{ route('faq.index') }}" onclick="closeMoreMenu()"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm {{ request()->routeIs('faq.*') ? 'text-primary-600 bg-primary-50' : 'text-gray-700' }} active:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="font-medium">FAQ</span>
                    <span class="ml-auto text-xs text-gray-400">Preguntas frecuentes</span>
                </a>
                <a href="{{ route('news.index') }}" onclick="closeMoreMenu()"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm {{ request()->routeIs('news.*') ? 'text-primary-600 bg-primary-50' : 'text-gray-700' }} active:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                    <span class="font-medium">Noticias</span>
                    <span class="ml-auto text-xs text-gray-400">Avisos y novedades</span>
                </a>
                <a href="{{ route('documents.index') }}" onclick="closeMoreMenu()"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm {{ request()->routeIs('documents.*') ? 'text-primary-600 bg-primary-50' : 'text-gray-700' }} active:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <span class="font-medium">Documentos</span>
                    <span class="ml-auto text-xs text-gray-400">Archivos y validaciones</span>
                </a>
                <a href="{{ route('profile') }}" onclick="closeMoreMenu()"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm {{ request()->routeIs('profile') ? 'text-primary-600 bg-primary-50' : 'text-gray-700' }} active:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span class="font-medium">Mi Perfil</span>
                    <span class="ml-auto text-xs text-gray-400">Configuración</span>
                </a>
            </div>
        </div>
    </div>

    <script>
        function toggleMoreMenu() {
            const overlay = document.getElementById('more-overlay');
            overlay.classList.toggle('hidden');
            document.body.style.overflow = overlay.classList.contains('hidden') ? '' : 'hidden';
        }
        function closeMoreMenu() {
            document.getElementById('more-overlay').classList.add('hidden');
            document.body.style.overflow = '';
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeMoreMenu();
        });
    </script>

    @stack('scripts')
</body>
</html>
