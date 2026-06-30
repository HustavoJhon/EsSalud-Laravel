@extends(Auth::check() ? 'layouts.app' : 'layouts.public')
@section('title', 'Chat de Ayuda')
@if(Auth::check())
@section('page_title', 'Chatbot IA')
@endif
@section('content')
<div class="flex h-[calc(100vh-10rem)] md:h-[calc(100vh-12rem)] gap-0 md:gap-4"
     x-data="chatApp()"
     x-on:resize.window="onResize()">

    {{-- Sidebar de conversaciones (siempre visible en desktop) --}}
    <div x-show="isDesktop || showMobileSessions"
         x-cloak
         x-transition:enter="transition-transform ease-out duration-200"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition-transform ease-in duration-150"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         class="absolute md:relative z-30 inset-y-0 left-0 w-72 md:w-72 lg:w-80 bg-white md:rounded-xl shadow-xl md:shadow-sm flex flex-col shrink-0 overflow-hidden border-r md:border border-gray-200">
        {{-- Sidebar header --}}
        <div class="bg-gradient-to-r from-primary-700 to-primary-600 text-white px-5 py-4 shrink-0">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-semibold">Conversaciones</h3>
                    <p class="text-primary-100 text-xs mt-0.5" x-text="sessions.length + ' sesiones'"></p>
                </div>
                <button @click="closeSidebar()" class="md:hidden p-1.5 text-primary-100 hover:text-white rounded-lg hover:bg-primary-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Sessions list --}}
        <div class="flex-1 overflow-y-auto p-3 space-y-1.5">
            <template x-for="session in sessions" :key="session.id">
                <div class="group relative flex items-center"
                     :class="{'bg-primary-50 rounded-lg': activeSession == session.id}">
                    <button @click="loadSession(session.id); closeSidebar()"
                        class="flex-1 text-left p-3 rounded-lg text-sm transition-colors truncate"
                        :class="activeSession == session.id ? 'font-medium text-primary-800' : 'text-gray-700 hover:bg-gray-100'">
                        <div class="flex items-start gap-2.5">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0"
                                 :class="activeSession == session.id ? 'bg-primary-200 text-primary-700' : 'bg-gray-100 text-gray-500'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="truncate" x-text="session.title || 'Nueva conversación'"></div>
                                <div class="text-xs text-gray-400 mt-0.5" x-text="formatDate(session.created_at)"></div>
                            </div>
                        </div>
                    </button>
                    <button @click="deleteSession(session.id)"
                        class="absolute right-2 opacity-0 group-hover:opacity-100 p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all"
                        title="Eliminar conversación">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </template>
            <div x-show="sessions.length === 0" class="text-center py-8">
                <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                <p class="text-gray-400 text-sm">Sin conversaciones</p>
                <p class="text-gray-300 text-xs mt-1">Escribe tu primera consulta</p>
            </div>
        </div>

        {{-- New session button --}}
        <div class="p-3 border-t border-gray-100 shrink-0">
            <button @click="startNew(); closeSidebar()"
                class="w-full bg-primary-600 text-white py-2.5 rounded-xl text-sm font-medium hover:bg-primary-700 transition-colors flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Nueva Conversación
            </button>
        </div>
    </div>

    {{-- Backdrop overlay (mobile only) --}}
    <div x-cloak x-show="!isDesktop && showMobileSessions" @click="showMobileSessions = false"
         x-transition:enter="transition-opacity ease-out duration-200"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         class="fixed inset-0 bg-black bg-opacity-50 z-20 md:hidden"></div>

    {{-- Área de chat --}}
    <div class="flex-1 bg-white md:rounded-xl shadow-sm flex flex-col min-w-0 border border-gray-200 md:border">
        {{-- Chat header --}}
        <div class="flex items-center justify-between px-4 md:px-6 py-3 border-b border-gray-100 shrink-0">
            <div class="flex items-center gap-3 min-w-0">
                <button @click="showMobileSessions = true" class="md:hidden p-1.5 -ml-1 text-gray-600 hover:text-primary-600 rounded-lg hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-primary-700 rounded-full flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-semibold text-gray-800 truncate" x-text="activeSessionTitle || 'Nuevo chat'"></div>
                        <div class="text-xs text-gray-400" x-show="activeSessionTitle">
                            <span x-text="messages.filter(m => m.role === 'assistant').length + ' respuestas'"></span>
                        </div>
                    </div>
                </div>
            </div>
            <button @click="startNew()" class="p-2 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors" title="Nuevo chat">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
            </button>
        </div>

        @guest
        <div class="mx-4 md:mx-6 mt-3 bg-amber-50 border border-amber-200 text-amber-700 text-xs px-3 py-2 rounded-lg flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H10m9.364-7.364A9 9 0 1112 3a9 9 0 017.364 4.636z"></path></svg>
            <span>Estás usando el chat como invitado. <a href="{{ route('login') }}" class="font-medium underline hover:text-amber-800">Inicia sesión</a> para guardar tus conversaciones.</span>
        </div>
        @endguest

        {{-- Empty state --}}
        <div x-show="messages.length === 0 && !loading" class="flex-1 flex flex-col items-center justify-center p-6 text-center">
            <div class="w-20 h-20 bg-gradient-to-br from-primary-100 to-primary-200 rounded-full flex items-center justify-center mb-4">
                <svg class="w-10 h-10 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-1">Asistente Virtual EsSalud</h3>
            <p class="text-gray-500 text-sm max-w-md mb-6">Pregúntame sobre trámites, subsidios, afiliaciones, requisitos y más.</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 w-full max-w-md">
                <button @click="quickAsk('¿Cuáles son los requisitos para afiliar a mi cónyuge?')" class="text-left p-3 bg-gray-50 hover:bg-primary-50 rounded-xl text-sm text-gray-600 hover:text-primary-700 transition-colors border border-gray-200 hover:border-primary-200">
                    ¿Cómo afiliar a mi cónyuge?
                </button>
                <button @click="quickAsk('¿Cuánto es el subsidio por maternidad y qué requisitos necesito?')" class="text-left p-3 bg-gray-50 hover:bg-primary-50 rounded-xl text-sm text-gray-600 hover:text-primary-700 transition-colors border border-gray-200 hover:border-primary-200">
                    ¿Subsidio por maternidad?
                </button>
                <button @click="quickAsk('¿Qué documentos necesito para el subsidio por lactancia?')" class="text-left p-3 bg-gray-50 hover:bg-primary-50 rounded-xl text-sm text-gray-600 hover:text-primary-700 transition-colors border border-gray-200 hover:border-primary-200">
                    ¿Subsidio por lactancia?
                </button>
                <button @click="quickAsk('¿Cómo puedo dar seguimiento a mi trámite?')" class="text-left p-3 bg-gray-50 hover:bg-primary-50 rounded-xl text-sm text-gray-600 hover:text-primary-700 transition-colors border border-gray-200 hover:border-primary-200">
                    ¿Seguimiento de trámite?
                </button>
            </div>
        </div>

        {{-- Mensajes --}}
        <div x-show="messages.length > 0" class="flex-1 overflow-y-auto p-4 md:p-6 space-y-4" id="chat-messages" x-ref="messagesContainer">
            <template x-for="msg in messages" :key="msg.id || Math.random()">
                <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start gap-2.5'">
                    {{-- Bot avatar --}}
                    <div x-show="msg.role === 'assistant'" class="w-7 h-7 bg-gradient-to-br from-primary-500 to-primary-700 rounded-full flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"></path>
                        </svg>
                    </div>
                    <div :class="msg.role === 'user'
                        ? 'bg-primary-600 text-white rounded-2xl rounded-br-md max-w-[85%] md:max-w-[70%]'
                        : 'bg-gray-100 text-gray-800 rounded-2xl rounded-bl-md max-w-[85%] md:max-w-[70%]'"
                        class="px-4 py-2.5 text-sm md:text-base shadow-sm">
                        <div x-text="msg.content" class="whitespace-pre-wrap break-words"></div>
                        <div x-show="msg.sources && msg.sources.length" class="mt-2 pt-2 border-t border-gray-400/20">
                            <div class="text-xs font-medium mb-1.5 opacity-70">Fuentes:</div>
                            <div class="flex flex-wrap gap-1.5">
                                <template x-for="source in msg.sources" :key="source.title">
                                    <button @click="quickAsk(source.title)"
                                        class="text-xs bg-white/20 hover:bg-white/30 px-2.5 py-1 rounded-full transition-colors text-left max-w-full"
                                        :class="msg.role === 'user' ? 'text-white border border-white/30' : 'text-primary-700 bg-primary-50 hover:bg-primary-100 border border-primary-200'"
                                        :title="source.title">
                                        <span x-text="source.title" class="line-clamp-1"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                        <div x-show="msg.role === 'assistant' && msg.message_id && msg.content" class="mt-2 flex items-center gap-3">
                            <button @click="feedback(msg.message_id, true)" class="text-xs text-gray-400 hover:text-green-500 flex items-center gap-1 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path></svg>
                                <span class="hidden sm:inline">Útil</span>
                            </button>
                            <button @click="feedback(msg.message_id, false)" class="text-xs text-gray-400 hover:text-red-500 flex items-center gap-1 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.096c.5 0 .905-.405.905-.904 0-.715.211-1.413.608-2.008L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5"></path></svg>
                                <span class="hidden sm:inline">No útil</span>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
            <div x-show="loading" class="flex justify-start gap-2.5">
                <div class="w-7 h-7 bg-gradient-to-br from-primary-500 to-primary-700 rounded-full flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"></path>
                    </svg>
                </div>
                <div class="bg-gray-100 rounded-2xl rounded-bl-md px-4 py-3">
                    <div class="flex space-x-1.5">
                        <div class="w-2 h-2 bg-gray-300 rounded-full animate-bounce"></div>
                        <div class="w-2 h-2 bg-gray-300 rounded-full animate-bounce" style="animation-delay:0.15s"></div>
                        <div class="w-2 h-2 bg-gray-300 rounded-full animate-bounce" style="animation-delay:0.3s"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Input --}}
        <div class="border-t border-gray-100 p-3 md:p-4 shrink-0">
            <form @submit.prevent="sendMessage()" class="flex items-end gap-2 md:gap-3">
                <div class="flex-1 relative">
                    <textarea x-model="input" @keydown.enter.prevent="!$event.shiftKey && sendMessage()"
                        placeholder="Escribe tu consulta aquí..."
                        rows="1"
                        class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm outline-none resize-none bg-gray-50 focus:bg-white transition-colors"
                        :disabled="loading"
                        x-ref="inputArea"
                        @input="$refs.inputArea.style.height = 'auto'; $refs.inputArea.style.height = ($refs.inputArea.scrollHeight > 120 ? 120 : $refs.inputArea.scrollHeight) + 'px'"
                        autocomplete="off"></textarea>
                </div>
                <button type="submit" :disabled="loading || !input.trim()"
                    class="bg-primary-600 text-white p-3 rounded-full hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed shrink-0 transition-all shadow-sm hover:shadow-md"
                    :class="{'scale-90': !input.trim()}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        sessions: @json($sessions->items()),
        activeSession: @json($activeSession?->id ?? null),
        activeSessionTitle: @json($activeSession?->title ?? null),
        messages: [],
        input: '',
        loading: false,
        showMobileSessions: false,
        isDesktop: window.innerWidth >= 768,
        isInitial: true,

        async init() {
            if (this.activeSession) {
                await this.loadSession(this.activeSession);
            }
            this.isInitial = false;
            this.$watch('messages', () => {
                if (!this.isInitial) this.scrollDown();
            });
        },

        onResize() {
            this.isDesktop = window.innerWidth >= 768;
        },

        closeSidebar() {
            if (!this.isDesktop) this.showMobileSessions = false;
        },

        async loadSession(id) {
            this.activeSession = id;
            try {
                const resp = await fetch(`/chat/history/${id}`, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                const data = await resp.json();
                this.messages = Array.isArray(data) ? data : (data.messages || data.data || []);
                const s = this.sessions.find(s => s.id == id);
                this.activeSessionTitle = s?.title || 'Chat';
            } catch (e) {
                console.error(e);
            }
        },

        startNew() {
            this.activeSession = null;
            this.activeSessionTitle = null;
            this.messages = [];
            this.$nextTick(() => this.$refs.inputArea?.focus());
        },

        quickAsk(question) {
            this.input = question;
            this.sendMessage();
        },

        async sendMessage() {
            if (!this.input.trim() || this.loading) return;
            const msg = this.input.trim();
            this.input = '';
            this.loading = true;
            this.$refs.inputArea.style.height = 'auto';

            this.messages.push({ id: 'u' + Date.now(), role: 'user', content: msg, sources: [] });
            this.scrollDown();

            try {
                const resp = await fetch('/chat/message', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ session_id: this.activeSession, message: msg }),
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
                    content: data.response || data.answer || 'Sin respuesta disponible.',
                    sources: data.sources || [],
                    message_id: data.message_id || data.id || null,
                });
            } catch (e) {
                this.messages.push({
                    id: 'e' + Date.now(), role: 'assistant',
                    content: 'Lo siento, ocurrió un error. Intenta de nuevo.',
                    sources: [],
                });
            } finally {
                this.loading = false;
                this.scrollDown();
            }
        },

        async deleteSession(id) {
            if (!confirm('¿Eliminar esta conversación?')) return;
            try {
                await fetch(`/chat/session/${id}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                if (this.activeSession == id) this.startNew();
                await this.refreshSessions();
            } catch (e) {}
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
                this.sessions = (Array.isArray(data) ? data : (data.data || [])).filter(Boolean);
            } catch (e) {}
        },

        formatDate(dateStr) {
            if (!dateStr) return '';
            const d = new Date(dateStr);
            const now = new Date();
            const diff = now - d;
            if (diff < 86400000) return d.toLocaleTimeString('es-PE', {hour:'2-digit',minute:'2-digit'});
            if (diff < 604800000) return d.toLocaleDateString('es-PE', {weekday:'short'});
            return d.toLocaleDateString('es-PE', {day:'numeric',month:'short'});
        },

        scrollDown() {
            this.$nextTick(() => {
                const el = this.$refs.messagesContainer;
                if (el) setTimeout(() => { el.scrollTop = el.scrollHeight; }, 100);
            });
        }
    };
}
</script>
@endpush
@endsection
