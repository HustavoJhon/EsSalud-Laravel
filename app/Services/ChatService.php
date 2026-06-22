<?php

namespace App\Services;

use App\Models\ChatSession;
use App\Models\Faq;
use Illuminate\Support\Facades\Cache;

class ChatService
{
    protected OpenAIService $openai;
    protected QdrantService $qdrant;
    protected array $stopwords = [
        'de','la','el','en','los','las','un','una','y','o','a','que','es','por','para',
        'con','no','se','del','al','lo','su','si','mi','tu','le','me','como','cuando',
        'donde','pero','mas','muy','ya','hay','son','era','fue','ser','han','sin',
        'sobre','entre','desde','hasta','porque','cual','cuales','todo','todos',
        'esta','estan','este','ese','eso','the','and','for','with',
    ];

    public function __construct(OpenAIService $openai, QdrantService $qdrant)
    {
        $this->openai = $openai;
        $this->qdrant = $qdrant;
    }

    public function processQuestion(ChatSession $session, string $question): array
    {
        $startTime = microtime(true);

        // 1. Fast keyword match on FAQs
        $faqResult = $this->matchFaqKeywords($question);
        if ($faqResult && $faqResult['score'] >= 0.3) {
            return [
                'answer' => $faqResult['answer'],
                'sources' => [['title' => $faqResult['question'], 'url' => null]],
                'confidence' => 0.85,
                'latency_ms' => (int)((microtime(true) - $startTime) * 1000),
                'type' => 'faq',
            ];
        }

        // 2. Search context for RAG
        $context = $this->searchContext($question);

        // 3. Build conversation history
        $history = $session->messages()
            ->orderBy('created_at')
            ->get()
            ->map(fn($m) => ['role' => $m->role, 'content' => $m->content])
            ->toArray();

        $messages = array_merge($history, [
            ['role' => 'user', 'content' => $question],
        ]);

        // 4. Get answer from OpenAI with context
        $answer = $this->openai->chatCompletion($messages, $context);

        $latency = (int)((microtime(true) - $startTime) * 1000);

        $sources = array_map(fn($c) => [
            'title' => $c['title'] ?? 'Documento',
            'url' => $c['url'] ?? null,
        ], $context);

        return [
            'answer' => $answer,
            'sources' => $sources,
            'confidence' => !empty($context) ? 0.8 : 0.5,
            'latency_ms' => $latency,
            'type' => !empty($context) ? 'rag' : 'no_result',
        ];
    }

    /**
     * Fast keyword matching on FAQs. Returns best match if score >= threshold.
     */
    protected function matchFaqKeywords(string $question): ?array
    {
        $questionLower = mb_strtolower($question);
        $questionWords = $this->extractWords($questionLower);

        // Cache FAQs for 5 minutes
        $faqs = Cache::remember('active_faqs_keywords', 300, function () {
            return Faq::where('is_active', true)
                ->whereNotNull('keywords')
                ->get(['id', 'question', 'answer', 'keywords']);
        });

        $bestScore = 0;
        $bestFaq = null;

        foreach ($faqs as $faq) {
            $keywords = $faq->keywords;
            if (empty($keywords)) continue;

            if (is_string($keywords)) {
                $keywords = json_decode($keywords, true) ?? [];
            }

            if (empty($keywords)) continue;

            $matches = 0;
            foreach ($keywords as $keyword) {
                $kw = mb_strtolower($keyword);
                // Check if keyword appears as whole word or substring
                if (mb_strpos($questionLower, $kw) !== false) {
                    $matches++;
                }
            }

            $total = count($keywords);
            if ($matches > 0 && $total > 0) {
                $score = $matches / $total;
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestFaq = [
                        'question' => $faq->question,
                        'answer' => $faq->answer,
                        'score' => $score,
                    ];
                }
            }
        }

        return $bestFaq;
    }

    protected function extractWords(string $text): array
    {
        preg_match_all('/[a-záéíóúñ]{4,}/u', $text, $matches);
        return array_diff($matches[0] ?? [], $this->stopwords);
    }

    protected function searchContext(string $question): array
    {
        $results = [];

        // FAQ search with SQL LIKE as secondary context
        $faqs = Faq::where('is_active', true)
            ->where(function ($q) use ($question) {
                $words = $this->extractWords(mb_strtolower($question));
                foreach ($words as $word) {
                    $q->orWhere('question', 'like', "%{$word}%");
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

        // Qdrant search
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
