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
        'esta','estan','este','ese','eso','the','and','for','with','una','por','mis',
        'tengo','necesito','saber','hacer','puedo','donde','cuanto','cuales','tiene',
    ];

    // Synonym map to expand user queries
    protected array $synonyms = [
        'licencia' => ['licencia','descanso','permiso','reposo'],
        'maternidad' => ['maternidad','maternal','embarazo','gestante','prenatal','postnatal'],
        'lactancia' => ['lactancia','lactante','lactar','amamantar','leche'],
        'sepelio' => ['sepelio','fallecido','fallece','defuncion','muerte','funeraria'],
        'afiliar' => ['afiliar','afiliacion','inscribir','registrar','empadronar'],
        'tramite' => ['tramite','trámite','solicitud','expediente','proceso'],
        'subsidio' => ['subsidio','pago','cobro','monto','prestacion','beneficio'],
        'pension' => ['pension','jubilacion','jubilado','pensionista','cesante'],
        'cita' => ['cita','consultorio','medico','medica','consulta','atencion'],
    ];

    public function __construct(OpenAIService $openai, QdrantService $qdrant)
    {
        $this->openai = $openai;
        $this->qdrant = $qdrant;
    }

    public function processQuestion(ChatSession $session, string $question): array
    {
        $startTime = microtime(true);

        // 0. Check conversation context: what was the last bot topic?
        $lastBotTopic = $this->detectBotTopic($session);

        // 1. Fast keyword match on FAQs
        $faqResult = $this->matchFaqKeywords($question, $lastBotTopic);
        if ($faqResult) {
            return [
                'answer' => $faqResult['answer'],
                'sources' => [['title' => $faqResult['question'], 'url' => null, 'score' => round($faqResult['score'], 2)]],
                'confidence' => 0.9,
                'latency_ms' => (int)((microtime(true) - $startTime) * 1000),
                'type' => 'faq',
            ];
        }

        // 2. Search context for RAG — only include relevant matches
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
            'score' => $c['score'] ?? 1,
        ], $context);

        return [
            'answer' => $answer,
            'sources' => $sources,
            'confidence' => !empty($context) ? 0.8 : 0.5,
            'latency_ms' => $latency,
            'type' => !empty($context) ? 'rag' : 'no_result',
        ];
    }

    protected function detectBotTopic(ChatSession $session): ?string
    {
        $lastBotMsg = $session->messages()
            ->where('role', 'assistant')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$lastBotMsg) return null;

        $text = mb_strtolower($lastBotMsg->content ?? '');

        $topics = [
            'lactancia' => ['lactancia','lactante','amamantar','subsidio por lactancia','cero tramite'],
            'maternidad' => ['maternidad','embarazo','gestante','prenatal','postnatal','subsidio por maternidad'],
            'afiliacion' => ['afiliar','afiliacion','inscribir','derechohabiente'],
            'incapacidad' => ['incapacidad','itt','enfermedad','accidente'],
            'cita' => ['cita','consultorio','medico','consulta','atencion'],
            'reembolso' => ['reembolso','devolucion','comprobante'],
            'subsanacion' => ['subsanacion','subsanar','observaciones','corregir'],
        ];

        foreach ($topics as $topic => $keywords) {
            foreach ($keywords as $kw) {
                if (mb_strpos($text, $kw) !== false) {
                    return $topic;
                }
            }
        }

        return null;
    }

    protected function matchFaqKeywords(string $question, ?string $botTopic = null): ?array
    {
        $questionLower = $this->normalizeAccents(mb_strtolower($question));
        $questionWords = $this->extractWords($questionLower);

        if (empty($questionWords)) {
            return null;
        }

        // Expand question with synonyms
        $expandedWords = $questionWords;
        foreach ($this->synonyms as $synGroup) {
            foreach ($synGroup as $syn) {
                if (mb_strpos($questionLower, $syn) !== false) {
                    $expandedWords = array_merge($expandedWords, $synGroup);
                    break;
                }
            }
        }

        $expandedWords = array_values(array_unique($expandedWords));

        $faqs = Cache::remember('active_faqs_keywords', 300, function () {
            return Faq::where('is_active', true)
                ->whereNotNull('keywords')
                ->where('keywords', '!=', '')
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

            $faqQuestionLower = $this->normalizeAccents(mb_strtolower($faq->question));

            // A) Keywords → Question: how many keywords match the user question
            $kwMatches = 0;
            foreach ($keywords as $keyword) {
                $kw = $this->normalizeAccents(mb_strtolower($keyword));
                if (mb_strpos($questionLower, $kw) !== false) {
                    $kwMatches++;
                    continue;
                }
                foreach ($expandedWords as $ew) {
                    if ($ew === $kw || (mb_strlen($kw) >= 4 && mb_strpos($ew, $kw) !== false)) {
                        $kwMatches++;
                        break;
                    }
                }
            }

            // B) Question words → FAQ question: how many user words appear in the FAQ question
            $qMatches = 0;
            foreach ($expandedWords as $w) {
                if (mb_strpos($faqQuestionLower, $w) !== false) {
                    $qMatches++;
                }
            }

                // Score: combine both directions, weight keyword matches higher
            $kwTotal = count($keywords);
            // Penalizar FAQs con pocas keywords (evita matches con keywords genéricas como "subsidio")
            $keywordRichness = min($kwTotal / 3, 1);
            $kwScore = $kwTotal > 0 ? ($kwMatches / $kwTotal) * 0.7 * $keywordRichness : 0;
            $qScore = count($expandedWords) > 0 ? ($qMatches / count($expandedWords)) * 0.3 : 0;
            $score = $kwScore + $qScore;

            // Bonus for topic continuity: boost FAQ if it matches the previous bot topic
            if ($botTopic && mb_strpos($faqQuestionLower, $botTopic) !== false) {
                $score *= 1.3;
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestFaq = [
                    'question' => $faq->question,
                    'answer' => $faq->answer,
                    'score' => $score,
                ];
            }
        }

        // Raise threshold for short questions (<= 3 words) to avoid false positives
        $threshold = count($questionWords) <= 3 ? 0.55 : 0.35;

        if ($bestFaq && $bestFaq['score'] >= $threshold) {
            return $bestFaq;
        }

        return null;
    }

    protected function normalizeAccents(string $text): string
    {
        $from = ['á','é','í','ó','ú','ü','ñ','Á','É','Í','Ó','Ú','Ü','Ñ'];
        $to   = ['a','e','i','o','u','u','n','A','E','I','O','U','U','N'];
        return str_replace($from, $to, $text);
    }

    protected function extractWords(string $text): array
    {
        preg_match_all('/[a-záéíóúñ]{3,}/u', $text, $matches);
        return array_values(array_unique(array_diff($matches[0] ?? [], $this->stopwords)));
    }

    protected function searchContext(string $question): array
    {
        $results = [];
        $questionLower = $this->normalizeAccents(mb_strtolower($question));
        $questionWords = $this->extractWords($questionLower);

        // Expand with synonyms
        $expandedWords = $questionWords;
        foreach ($this->synonyms as $synGroup) {
            foreach ($synGroup as $syn) {
                if (mb_strpos($questionLower, $syn) !== false) {
                    $expandedWords = array_merge($expandedWords, $synGroup);
                    break;
                }
            }
        }

        // FAQ search — only include if at least 3 words match
        $faqs = Faq::where('is_active', true)->get(['id','question','answer','keywords']);

        $scoredFaqs = [];
        foreach ($faqs as $faq) {
            $matches = 0;
            $faqLower = $this->normalizeAccents(mb_strtolower($faq->question));
            foreach ($expandedWords as $w) {
                if (mb_strpos($faqLower, $w) !== false) {
                    $matches++;
                }
            }
            if ($matches >= 3) {
                $scoredFaqs[] = [
                    'faq' => $faq,
                    'matches' => $matches,
                ];
            }
        }

        // Sort by matches desc, take top 3
        usort($scoredFaqs, fn($a, $b) => $b['matches'] <=> $a['matches']);
        $topFaqs = array_slice($scoredFaqs, 0, 3);

        foreach ($topFaqs as $scored) {
            $faq = $scored['faq'];
            $relevance = $scored['matches'] / max(count($expandedWords), 1);
            if ($relevance < 0.15) continue;
            $results[] = [
                'content' => "Pregunta: {$faq->question}\nRespuesta: {$faq->answer}",
                'title' => $faq->question,
                'url' => null,
                'score' => $relevance,
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
                        'score' => $hit['score'] ?? 0.5,
                    ];
                }
            }
        } catch (\Exception $e) {
        }

        return array_slice($results, 0, 4);
    }
}
