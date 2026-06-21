<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'EsSalud') - Plataforma de Trámites</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {50:'#e6f0fa',100:'#b3d4f2',200:'#80b8e9',300:'#4d9ce1',400:'#1a80d8',500:'#0066cc',600:'#0052a3',700:'#003d7a',800:'#002952',900:'#001429'},
                        secondary: {50:'#e8f5e9',100:'#c8e6c9',200:'#a5d6a7',300:'#81c784',400:'#66bb6a',500:'#4caf50',600:'#43a047',700:'#388e3c',800:'#2e7d32',900:'#1b5e20'},
                    }
                }
            }
        }
    </script>
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="flex h-screen overflow-hidden">
        <aside class="w-64 bg-primary-800 text-white flex flex-col shadow-lg">
            <div class="p-4 border-b border-primary-700">
                <a href="{{ route('home') }}" class="text-2xl font-bold tracking-tight">EsSalud</a>
                <p class="text-primary-200 text-sm mt-1">Plataforma de Trámites</p>
            </div>
            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
                <x-sidebar-link route="home" icon="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1">
                    Inicio
                </x-sidebar-link>
                <x-sidebar-link route="procedures.index" icon="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    Trámites
                </x-sidebar-link>
                <x-sidebar-link route="procedures.create" icon="M12 6v6m0 0v6m0-6h6m-6 0H6">
                    Nuevo Trámite
                </x-sidebar-link>
                <x-sidebar-link route="documents.index" icon="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                    Documentos
                </x-sidebar-link>
                @can('news.create')
                <x-sidebar-link route="news.create" icon="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                    Nueva Noticia
                </x-sidebar-link>
                <x-sidebar-link route="chat.index" icon="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z">
                    Chat
                </x-sidebar-link>
                <x-sidebar-link route="faq.index" icon="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                    FAQ
                </x-sidebar-link>
                <x-sidebar-link route="news.index" icon="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z">
                    Noticias
                </x-sidebar-link>
            </nav>
            <div class="p-4 border-t border-primary-700">
                <div class="text-sm text-primary-200 truncate">{{ Auth::user()->full_name ?? Auth::user()->name }}</div>
                <span class="inline-block bg-primary-600 text-xs px-2 py-0.5 rounded mt-1">{{ Auth::user()->role }}</span>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between px-6 py-3">
                    <div class="flex items-center space-x-4">
                        <h1 class="text-lg font-semibold text-gray-800">@yield('page_title', 'Dashboard')</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('profile') }}" class="text-sm text-gray-600 hover:text-primary-600">
                            Mi Perfil
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-red-600 hover:text-red-800">
                                Cerrar Sesión
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6">
                @if(session('status'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('status') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
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
    @stack('scripts')
</body>
</html>
