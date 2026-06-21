<?php

namespace App\Services;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\Faq;

class ChatService
{
    protected OpenAIService $openai;
    protected QdrantService $qdrant;

    public function __construct(OpenAIService $openai, QdrantService $qdrant)
    {
        $this->openai = $openai;
        $this->qdrant = $qdrant;
    }

    public function processQuestion(ChatSession $session, string $question): array
    {
        $startTime = microtime(true);

        $context = $this->searchContext($question);

        $history = $session->messages()
            ->orderBy('created_at')
            ->get()
            ->map(fn($m) => ['role' => $m->role, 'content' => $m->content])
            ->toArray();

        $messages = array_merge($history, [
            ['role' => 'user', 'content' => $question],
        ]);

        $answer = $this->openai->chatCompletion($messages, $context);

        $latency = (int)((microtime(true) - $startTime) * 1000);

        $sources = array_map(fn($c) => [
            'title' => $c['title'] ?? 'Documento',
            'url' => $c['url'] ?? null,
        ], $context);

        return [
            'answer' => $answer,
            'sources' => $sources,
            'confidence' => !empty($context) ? 0.85 : 0.6,
            'latency_ms' => $latency,
        ];
    }

    protected function searchContext(string $question): array
    {
        $results = [];

        $faqs = Faq::where('is_active', true)
            ->where(function ($q) use ($question) {
                $words = explode(' ', strtolower($question));
                foreach ($words as $word) {
                    if (strlen($word) > 3) {
                        $q->orWhere('question', 'like', "%{$word}%")
                            ->orWhere('keywords', 'like', "%{$word}%");
                    }
                }
            })
            ->limit(3)
            ->get();

        foreach ($faqs as $faq) {
            $results[] = [
                'content' => "Pregunta: {$faq->question}\nRespuesta: {$faq->answer}",
                'title' => $faq->question,
                'url' => null,
            ];
        }

        try {
            $embedding = $this->openai->embedding($question);
            $qdrantResults = $this->qdrant->search($embedding, 3);

            foreach ($qdrantResults as $hit) {
                if (isset($hit['payload']['content'])) {
                    $results[] = [
                        'content' => $hit['payload']['content'],
                        'title' => $hit['payload']['title'] ?? 'Documento',
                        'url' => $hit['payload']['url'] ?? null,
                    ];
                }
            }
        } catch (\Exception $e) {
        }

        return array_slice($results, 0, 5);
    }
}
