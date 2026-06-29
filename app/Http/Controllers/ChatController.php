<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    protected ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    protected function guestId(): ?string
    {
        return session()->getId();
    }

    protected function userId()
    {
        return Auth::id();
    }

    protected function ownerId(): ?string
    {
        return Auth::id() ?? $this->guestId();
    }

    public function index()
    {
        $query = ChatSession::where('is_active', true);

        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        } else {
            $query->where('guest_id', session()->getId());
        }

        $sessions = $query->latest()->get();
        $activeSession = $sessions->first();

        return view('chat.index', compact('sessions', 'activeSession'));
    }

    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'session_id' => 'nullable|exists:chat_sessions,id',
            'message' => 'required|string|max:5000',
        ]);

        $sessionId = $validated['session_id'] ?? null;

        if (!$sessionId) {
            $data = [
                'title' => mb_substr($validated['message'], 0, 100),
                'message_count' => 0,
            ];

            if (Auth::check()) {
                $data['user_id'] = Auth::id();
            } else {
                $data['guest_id'] = session()->getId();
            }

            $session = ChatSession::create($data);
            $sessionId = $session->id;
        } else {
            $session = ChatSession::findOrFail($sessionId);
            if (!Auth::check() && $session->guest_id !== session()->getId()) {
                abort(403);
            }
            if (Auth::check() && $session->user_id !== Auth::id()) {
                abort(403);
            }
        }

        ChatMessage::create([
            'session_id' => $sessionId,
            'role' => 'user',
            'content' => $validated['message'],
            'message_type' => 'text',
        ]);

        $response = $this->chatService->processQuestion($session, $validated['message']);

        $message = ChatMessage::create([
            'session_id' => $sessionId,
            'role' => 'assistant',
            'content' => $response['answer'],
            'message_type' => 'text',
            'sources' => $response['sources'] ?? null,
            'confidence' => $response['confidence'] ?? null,
            'latency_ms' => $response['latency_ms'] ?? null,
        ]);

        $session->update([
            'message_count' => $session->messages()->count(),
            'title' => $session->title ?? mb_substr($validated['message'], 0, 100),
        ]);

        return response()->json([
            'session_id' => $sessionId,
            'answer' => $response['answer'],
            'sources' => $response['sources'] ?? [],
            'confidence' => $response['confidence'] ?? 1,
            'latency_ms' => $response['latency_ms'] ?? 0,
            'type' => $response['type'] ?? 'rag',
            'message_id' => $message->id,
        ]);
    }

    public function getSessions()
    {
        $query = ChatSession::where('is_active', true);

        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        } else {
            $query->where('guest_id', session()->getId());
        }

        $sessions = $query->latest()->get();
        return response()->json($sessions);
    }

    public function getHistory(ChatSession $session)
    {
        if (!Auth::check() && $session->guest_id !== session()->getId()) {
            abort(403);
        }
        if (Auth::check() && $session->user_id !== Auth::id()) {
            abort(403);
        }

        $messages = $session->messages()->orderBy('created_at')->get();
        return response()->json($messages);
    }

    public function deleteSession(ChatSession $session)
    {
        if (!Auth::check() && $session->guest_id !== session()->getId()) {
            abort(403);
        }
        if (Auth::check() && $session->user_id !== Auth::id()) {
            abort(403);
        }

        $session->update(['is_active' => false]);
        return response()->json(['message' => 'Sesión eliminada.']);
    }

    public function feedback(Request $request, ChatMessage $message)
    {
        $session = $message->session;
        if (!Auth::check() && $session->guest_id !== session()->getId()) {
            abort(403);
        }
        if (Auth::check() && $session->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'helpful' => 'required|boolean',
            'comment' => 'nullable|string|max:1000',
        ]);

        $message->update([
            'feedback_helpful' => $validated['helpful'],
            'feedback_comment' => $validated['comment'] ?? null,
        ]);

        return response()->json(['message' => 'Feedback registrado.']);
    }
}
