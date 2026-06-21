@extends('layouts.app')
@section('title', 'Chat de Ayuda')
@section('page_title', 'Chat de Ayuda')
@section('content')
<div class="flex h-[calc(100vh-12rem)] gap-4" x-data="chatApp()">
    <div class="w-64 bg-white rounded-lg shadow p-4 flex flex-col shrink-0">
        <h3 class="font-semibold text-gray-800 mb-3">Conversaciones</h3>
        <div class="flex-1 overflow-y-auto space-y-1">
            <template x-for="session in sessions" :key="session.id">
                <button @click="loadSession(session.id)"
                    :class="{'bg-primary-100 border-primary-300': activeSession == session.id, 'hover:bg-gray-100': activeSession != session.id}"
                    class="w-full text-left p-2 rounded-lg text-sm border border-transparent transition-colors">
                    <span x-text="session.title || 'Nueva conversación'"></span>
                </button>
            </template>
            <div x-show="sessions.length === 0" class="text-sm text-gray-400 p-2">
                Sin conversaciones
            </div>
        </div>
        <button @click="startNew()" class="mt-3 w-full bg-primary-600 text-white py-2 rounded-lg text-sm hover:bg-primary-700">
            Nueva Conversación
        </button>
    </div>

    <div class="flex-1 bg-white rounded-lg shadow flex flex-col">
        <div class="flex-1 overflow-y-auto p-6 space-y-4" id="chat-messages" x-ref="messagesContainer">
            <template x-for="msg in messages" :key="msg.id">
                <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                    <div :class="msg.role === 'user'
                        ? 'bg-primary-600 text-white rounded-lg rounded-br-none max-w-[70%]'
                        : 'bg-gray-100 text-gray-800 rounded-lg rounded-bl-none max-w-[70%]'"
                        class="px-4 py-2 text-sm">
                        <div x-text="msg.content"></div>
                        <div x-show="msg.sources && msg.sources.length > 0" class="mt-2 pt-2 border-t border-gray-300">
                            <div class="text-xs text-gray-500 font-medium mb-1">Fuentes:</div>
                            <template x-for="source in msg.sources" :key="source.title">
                                <div class="text-xs text-gray-500" x-text="source.title"></div>
                            </template>
                        </div>
                        <div x-show="msg.role === 'assistant' && msg.id" class="mt-2 flex items-center space-x-2">
                            <button @click="feedback(msg.id, true)" class="text-xs text-gray-400 hover:text-green-500">Útil</button>
                            <button @click="feedback(msg.id, false)" class="text-xs text-gray-400 hover:text-red-500">No útil</button>
                        </div>
                    </div>
                </div>
            </template>
            <div x-show="loading" class="flex justify-start">
                <div class="bg-gray-100 rounded-lg px-4 py-2">
                    <div class="flex space-x-1">
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0s"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-t p-4">
            <form @submit.prevent="sendMessage()" class="flex space-x-3">
                <input type="text" x-model="input" placeholder="Escribe tu consulta..."
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 text-sm"
                    :disabled="loading">
                <button type="submit" :disabled="loading || !input.trim()"
                    class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed text-sm">
                    Enviar
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
        messages: [],
        input: '',
        loading: false,

        async init() {
            if (this.activeSession) {
                await this.loadSession(this.activeSession);
            }
        },

        async loadSession(id) {
            this.activeSession = id;
            try {
                const resp = await fetch(`/chat/${id}/history`);
                const data = await resp.json();
                this.messages = data;
                this.$nextTick(() => this.scrollDown());
            } catch (e) {
                console.error(e);
            }
        },

        startNew() {
            this.activeSession = null;
            this.messages = [];
        },

        async sendMessage() {
            if (!this.input.trim() || this.loading) return;
            const msg = this.input.trim();
            this.input = '';
            this.loading = true;

            this.messages.push({ id: null, role: 'user', content: msg });

            try {
                const resp = await fetch('/chat/send', {
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
                }

                this.messages.push({
                    id: data.message_id || null,
                    role: 'assistant',
                    content: data.answer,
                    sources: data.sources || [],
                });
            } catch (e) {
                this.messages.push({
                    id: null,
                    role: 'assistant',
                    content: 'Lo siento, ocurrió un error. Por favor intenta de nuevo.',
                });
            } finally {
                this.loading = false;
                this.$nextTick(() => this.scrollDown());
            }
        },

        async feedback(messageId, helpful) {
            try {
                await fetch(`/chat/messages/${messageId}/feedback`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ helpful }),
                });
            } catch (e) {
                console.error(e);
            }
        },

        async refreshSessions() {
            try {
                const resp = await fetch('/chat/sessions');
                this.sessions = await resp.json();
            } catch (e) {}
        },

        scrollDown() {
            const el = this.$refs.messagesContainer;
            if (el) el.scrollTop = el.scrollHeight;
        }
    };
}
</script>
@endpush
@endsection
