<?php

namespace Tests\Unit;

use App\Models\ChatSession;
use App\Models\Faq;
use App\Services\ChatService;
use App\Services\OpenAIService;
use App\Services\QdrantService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ChatServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ChatService $chatService;

    protected function setUp(): void
    {
        parent::setUp();

        $openai = $this->createMock(OpenAIService::class);
        $openai->method('embedding')->willReturn(array_fill(0, 1536, 0.1));
        $openai->method('chatCompletion')->willReturn('Respuesta de prueba.');

        $qdrant = $this->createMock(QdrantService::class);
        $qdrant->method('search')->willReturn([]);

        $this->chatService = new ChatService($openai, $qdrant);
    }

    public function test_match_faq_keywords_returns_null_when_no_faqs(): void
    {
        Cache::shouldReceive('remember')
            ->once()
            ->andReturn(collect([]));

        $result = $this->invokeMethod($this->chatService, 'matchFaqKeywords', ['consulta de prueba']);
        $this->assertNull($result);
    }

    public function test_match_faq_keywords_returns_best_match(): void
    {
        $faq = new Faq([
            'id' => 1,
            'question' => '¿Cómo afiliar a mi cónyuge?',
            'answer' => 'Debe presentar DNI y partida de matrimonio.',
            'keywords' => json_encode(['afiliar', 'conyuge', 'matrimonio']),
        ]);

        Cache::shouldReceive('remember')
            ->once()
            ->andReturn(collect([$faq]));

        $result = $this->invokeMethod($this->chatService, 'matchFaqKeywords', ['quiero afiliar a mi conyuge']);
        $this->assertNotNull($result);
        $this->assertArrayHasKey('question', $result);
        $this->assertArrayHasKey('score', $result);
        $this->assertGreaterThan(0, $result['score']);
    }

    public function test_match_faq_keywords_early_return_on_empty_question(): void
    {
        $result = $this->invokeMethod($this->chatService, 'matchFaqKeywords', ['de la el']);
        $this->assertNull($result);
    }

    public function test_process_question_returns_faq_result_when_high_confidence(): void
    {
        $session = ChatSession::factory()->create();

        $faq = new Faq([
            'id' => 1,
            'question' => '¿Cómo afiliar a mi cónyuge?',
            'answer' => 'Debe presentar DNI y partida de matrimonio.',
            'keywords' => json_encode(['afiliar', 'conyuge', 'matrimonio']),
        ]);

        Cache::shouldReceive('remember')
            ->once()
            ->andReturn(collect([$faq]));

        $result = $this->chatService->processQuestion($session, 'quiero afiliar a mi conyuge');

        $this->assertArrayHasKey('type', $result);
        $this->assertEquals('faq', $result['type']);
        $this->assertArrayHasKey('answer', $result);
        $this->assertArrayHasKey('confidence', $result);
        $this->assertGreaterThanOrEqual(0.8, $result['confidence']);
    }

    public function test_normalize_accents(): void
    {
        $result = $this->invokeMethod($this->chatService, 'normalizeAccents', ['cónyuge afiliación']);
        $this->assertEquals('conyuge afiliacion', $result);
    }

    public function test_extract_words_filters_stopwords(): void
    {
        $result = $this->invokeMethod($this->chatService, 'extractWords', ['el y la consulta de prueba']);
        $this->assertContains('consulta', $result);
        $this->assertContains('prueba', $result);
        $this->assertNotContains('el', $result);
        $this->assertNotContains('y', $result);
        $this->assertNotContains('la', $result);
        $this->assertNotContains('de', $result);
    }

    protected function invokeMethod(object $object, string $methodName, array $parameters = []): mixed
    {
        $reflection = new \ReflectionClass($object);
        $method = $reflection->getMethod($methodName);
        return $method->invokeArgs($object, $parameters);
    }
}
