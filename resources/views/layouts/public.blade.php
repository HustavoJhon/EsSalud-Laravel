<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'EsSalud') - Plataforma de Trámites</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {50:'#e6f0fa',100:'#b3d4f2',200:'#80b8e9',300:'#4d9ce1',400:'#1a80d8',500:'#0066cc',600:'#0052a3',700:'#003d7a',800:'#002952',900:'#001429'},
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    {{-- Navbar --}}
    <header class="bg-white border-b border-gray-200 sticky top-0 z-40 shadow-sm">
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
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('home') }}" class="text-sm font-medium text-gray-600 hover:text-primary-600 hidden sm:block">Dashboard</a>
                        <a href="{{ route('procedures.index') }}" class="text-sm font-medium text-gray-600 hover:text-primary-600 hidden sm:block">Mis Trámites</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button class="text-sm font-medium text-red-600 hover:text-red-700">Salir</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-primary-600">Iniciar Sesión</a>
                        <a href="{{ route('register') }}" class="bg-primary-600 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-primary-700 transition-colors shadow-sm">Registrarse</a>
                    @endauth
                </div>
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
</body>
</html>
