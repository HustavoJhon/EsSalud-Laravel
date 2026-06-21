@extends('layouts.app')
@section('title', 'Chat de Ayuda')
@section('page_title', 'Chat de Ayuda')
@section('content')
<div class="flex h-[calc(100vh-10rem)] md:h-[calc(100vh-12rem)] gap-0 md:gap-4"
     x-data="chatApp()"
     x-on:resize.window="onResize()">

    {{-- Sidebar de conversaciones --}}
    <div x-cloak x-show="showSessions"
         x-transition:enter="transition-transform ease-out duration-200"
         x-transition:enter-start="-translate-x-full md:translate-x-0"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition-transform ease-in duration-150"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full md:translate-x-0"
         class="absolute md:relative z-20 inset-y-0 left-0 w-72 md:w-64 bg-white rounded-lg shadow-lg md:shadow p-4 flex flex-col shrink-0">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-semibold text-gray-800">Conversaciones</h3>
            <button @click="showSessions = false" class="md:hidden p-1 text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto space-y-1">
            <template x-for="session in sessions" :key="session.id">
                <button @click="loadSession(session.id); showSessions = false"
                    :class="{'bg-primary-100 border-primary-300': activeSession == session.id, 'hover:bg-gray-100': activeSession != session.id}"
                    class="w-full text-left p-3 md:p-2 rounded-lg text-sm border border-transparent transition-colors truncate">
                    <span x-text="session.title || 'Nueva conversación'"></span>
                </button>
            </template>
            <div x-show="sessions.length === 0" class="text-sm text-gray-400 p-2">
                Sin conversaciones aún
            </div>
        </div>
        <button @click="startNew(); showSessions = false"
            class="mt-3 w-full bg-primary-600 text-white py-3 md:py-2 rounded-lg text-sm hover:bg-primary-700 transition-colors">
            + Nueva Conversación
        </button>
    </div>

    {{-- Backdrop overlay on mobile --}}
    <div x-cloak x-show="showSessions" @click="showSessions = false"
         x-transition:enter="transition-opacity ease-out duration-200"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         class="md:hidden fixed inset-0 bg-black bg-opacity-40 z-10"></div>

    {{-- Área de chat --}}
    <div class="flex-1 bg-white rounded-lg shadow flex flex-col min-w-0">
        {{-- Header móvil --}}
        <div class="md:hidden flex items-center justify-between px-4 py-2 border-b border-gray-200 shrink-0">
            <button @click="showSessions = true" class="p-2 -ml-2 text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            <span class="text-sm font-medium text-gray-700 truncate flex-1 text-center" x-text="activeSessionTitle || 'Nuevo chat'"></span>
            <button @click="startNew()" class="p-2 -mr-2 text-primary-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
            </button>
        </div>

        {{-- Mensajes --}}
        <div class="flex-1 overflow-y-auto p-3 md:p-6 space-y-3 md:space-y-4" id="chat-messages" x-ref="messagesContainer">
            <template x-for="msg in messages" :key="msg.id || Math.random()">
                <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                    <div :class="msg.role === 'user'
                        ? 'bg-primary-600 text-white rounded-2xl rounded-br-md max-w-[85%] md:max-w-[70%]'
                        : 'bg-gray-100 text-gray-800 rounded-2xl rounded-bl-md max-w-[85%] md:max-w-[70%]'"
                        class="px-4 py-2.5 md:py-2 text-sm md:text-base shadow-sm">
                        <div x-text="msg.content" class="whitespace-pre-wrap break-words"></div>
                        <div x-show="msg.sources && msg.sources.length" class="mt-2 pt-2 border-t border-gray-300/50">
                            <div class="text-xs font-medium mb-1" :class="msg.role === 'user' ? 'text-white/70' : 'text-gray-500'">Fuentes:</div>
                            <template x-for="source in msg.sources" :key="source.document_name || source.title">
                                <div class="text-xs opacity-70" x-text="source.document_name || source.title"></div>
                            </template>
                        </div>
                        <div x-show="msg.role === 'assistant' && msg.message_id" class="mt-2 flex items-center space-x-3">
                            <button @click="feedback(msg.message_id, true)" class="text-xs text-gray-400 hover:text-green-500 flex items-center space-x-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path></svg>
                                <span class="hidden sm:inline">Útil</span>
                            </button>
                            <button @click="feedback(msg.message_id, false)" class="text-xs text-gray-400 hover:text-red-500 flex items-center space-x-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.096c.5 0 .905-.405.905-.904 0-.715.211-1.413.608-2.008L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5"></path></svg>
                                <span class="hidden sm:inline">No útil</span>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
            <div x-show="loading" class="flex justify-start">
                <div class="bg-gray-100 rounded-2xl rounded-bl-md px-4 py-3">
                    <div class="flex space-x-1.5">
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:0.15s"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:0.3s"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Input --}}
        <div class="border-t p-3 md:p-4 shrink-0">
            <form @submit.prevent="sendMessage()" class="flex space-x-2 md:space-x-3">
                <input type="text" x-model="input" placeholder="Escribe tu consulta..."
                    class="flex-1 px-4 py-3 md:py-2 border border-gray-300 rounded-full md:rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm outline-none"
                    :disabled="loading"
                    autocomplete="off">
                <button type="submit" :disabled="loading || !input.trim()"
                    class="bg-primary-600 text-white px-4 md:px-6 py-3 md:py-2 rounded-full md:rounded-lg hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed text-sm shrink-0 transition-colors">
                    <span class="hidden sm:inline">Enviar</span>
                    <svg class="sm:hidden w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function chatApp() {
    return {
        sessions: @json($sessions->toArray()),
        activeSession: @json($activeSession?->id ?? null),
        activeSessionTitle: @json($activeSession?->title ?? null),
        messages: [],
        input: '',
        loading: false,
        showSessions: window.innerWidth >= 768,

        async init() {
            if (this.activeSession) {
                await this.loadSession(this.activeSession);
            }
        },

        onResize() {
            this.showSessions = window.innerWidth >= 768;
        },

        async loadSession(id) {
            this.activeSession = id;
            this.showSessions = window.innerWidth >= 768;
            try {
                const resp = await fetch(`/chat/history/${id}`, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                const data = await resp.json();
                this.messages = Array.isArray(data) ? data : (data.messages || data.data || []);
                const s = this.sessions.find(s => s.id == id);
                this.activeSessionTitle = s?.title || null;
                this.$nextTick(() => this.scrollDown());
            } catch (e) {
                console.error(e);
            }
        },

        startNew() {
            this.activeSession = null;
            this.activeSessionTitle = null;
            this.messages = [];
            this.showSessions = window.innerWidth >= 768;
        },

        async sendMessage() {
            if (!this.input.trim() || this.loading) return;
            const msg = this.input.trim();
            this.input = '';
            this.loading = true;

            this.messages.push({ id: 'u' + Date.now(), role: 'user', content: msg, sources: [] });
            this.$nextTick(() => this.scrollDown());

            try {
                const resp = await fetch('/chat/message', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        session_id: this.activeSession,
                        message: msg,
                    }),
                });
                const data = await resp.json();

                if (!this.activeSession) {
                    this.activeSession = data.session_id;
                    await this.refreshSessions();
                    const s = this.sessions.find(s => s.id == data.session_id);
                    this.activeSessionTitle = s?.title || msg.substring(0, 30);
                }

                this.messages.push({
                    id: 'a' + Date.now(),
                    role: 'assistant',
                    content: data.response || data.answer || 'Sin respuesta',
                    sources: data.sources || [],
                    message_id: data.message_id || data.id || null,
                });
            } catch (e) {
                this.messages.push({
                    id: 'e' + Date.now(),
                    role: 'assistant',
                    content: 'Lo siento, ocurrió un error. Intenta de nuevo.',
                    sources: [],
                });
            } finally {
                this.loading = false;
                this.$nextTick(() => this.scrollDown());
            }
        },

        async feedback(messageId, helpful) {
            if (!messageId) return;
            try {
                await fetch(`/chat/feedback/${messageId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ helpful }),
                });
            } catch (e) {}
        },

        async refreshSessions() {
            try {
                const resp = await fetch('/chat/sessions', {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                const data = await resp.json();
                this.sessions = Array.isArray(data) ? data : (data.data || []);
            } catch (e) {}
        },

        scrollDown() {
            const el = this.$refs.messagesContainer;
            if (el) setTimeout(() => { el.scrollTop = el.scrollHeight; }, 50);
        }
    };
}
</script>
@endpush
@endsection
