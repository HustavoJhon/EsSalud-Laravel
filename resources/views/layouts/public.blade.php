<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>@yield('title', 'EsSalud') - Plataforma de Trámites</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    {{-- Navbar --}}
    <header class="bg-white border-b border-gray-200 sticky top-0 z-40 shadow-sm"
            x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-8">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5 shrink-0">
                        <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <span class="font-extrabold text-xl text-gray-900">EsSalud</span>
                    </a>
                    <nav class="hidden md:flex items-center gap-1">
                        <a href="{{ route('chat.index') }}" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-primary-600 rounded-lg hover:bg-gray-100 transition-colors {{ request()->routeIs('chat.*') ? 'text-primary-600 bg-primary-50' : '' }}">Chat</a>
                        <a href="{{ route('faq.index') }}" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-primary-600 rounded-lg hover:bg-gray-100 transition-colors {{ request()->routeIs('faq.*') ? 'text-primary-600 bg-primary-50' : '' }}">FAQ</a>
                        <a href="{{ route('news.index') }}" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-primary-600 rounded-lg hover:bg-gray-100 transition-colors {{ request()->routeIs('news.*') ? 'text-primary-600 bg-primary-50' : '' }}">Noticias</a>
                    </nav>
                </div>
                <div class="flex items-center gap-2 md:gap-3">
                    @auth
                        <a href="{{ route('home') }}" class="text-sm font-medium text-gray-600 hover:text-primary-600 hidden sm:block">Dashboard</a>
                        <a href="{{ route('procedures.index') }}" class="text-sm font-medium text-gray-600 hover:text-primary-600 hidden sm:block">Mis Trámites</a>
                        <form method="POST" action="{{ route('logout') }}" class="hidden sm:inline">
                            @csrf
                            <button class="text-sm font-medium text-red-600 hover:text-red-700">Salir</button>
                        </form>
                        <a href="{{ route('home') }}" class="sm:hidden p-2 text-gray-600 active:text-primary-600 touch-feedback-light" title="Dashboard">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-primary-600 hidden sm:inline">Iniciar Sesión</a>
                        <a href="{{ route('register') }}" class="bg-primary-600 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-primary-700 transition-colors shadow-sm hidden sm:inline-block">Registrarse</a>
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="sm:hidden p-2 text-gray-600 active:text-primary-600 touch-feedback-light">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                <path x-cloak x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    @endauth
                </div>
            </div>
            {{-- Mobile bottom sheet nav --}}
            <div x-cloak x-show="mobileMenuOpen" x-on:click.outside="mobileMenuOpen = false"
                 x-transition:enter="transition-all duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition-all duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="md:hidden border-t border-gray-100 bg-white pb-3 pt-1">
                <nav class="space-y-0.5 px-2">
                    <a href="{{ route('chat.index') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-700 active:bg-gray-100 transition-colors">Chat</a>
                    <a href="{{ route('faq.index') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-700 active:bg-gray-100 transition-colors">FAQ</a>
                    <a href="{{ route('news.index') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-700 active:bg-gray-100 transition-colors">Noticias</a>
                    @guest
                    <hr class="my-1 border-gray-100">
                    <a href="{{ route('login') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-primary-600 active:bg-primary-50 transition-colors">Iniciar Sesión</a>
                    <a href="{{ route('register') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-white bg-primary-600 active:bg-primary-700 transition-colors">Registrarse</a>
                    @endguest
                    @auth
                    <hr class="my-1 border-gray-100">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-red-600 active:bg-red-50 transition-colors">Salir</button>
                    </form>
                    @endauth
                </nav>
            </div>
        </div>
    </header>

    {{-- Content --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-gray-900 text-gray-400 py-12 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 bg-primary-600 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <span class="font-bold text-white">EsSalud</span>
                </div>
                <div class="flex items-center gap-6 text-sm">
                    <a href="{{ route('chat.index') }}" class="hover:text-white transition-colors">Chat</a>
                    <a href="{{ route('faq.index') }}" class="hover:text-white transition-colors">FAQ</a>
                    <a href="{{ route('news.index') }}" class="hover:text-white transition-colors">Noticias</a>
                    @guest
                    <a href="{{ route('login') }}" class="hover:text-white transition-colors">Iniciar Sesión</a>
                    @endguest
                </div>
            </div>
            <div class="text-center text-xs mt-6 text-gray-600">
                Plataforma de Atención al Asegurado &copy; {{ date('Y') }}
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
