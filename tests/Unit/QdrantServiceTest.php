<?php

namespace Tests\Unit;

use App\Services\QdrantService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class QdrantServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Config::set('services.qdrant.url', 'http://qdrant:6333');
        Config::set('services.qdrant.collection', 'essalud_docs_test');
    }

    public function test_search_returns_empty_array_on_connection_error(): void
    {
        Log::shouldReceive('warning')
            ->once()
            ->withArgs(fn($message) => str_contains($message, 'Qdrant search failed'));

        $mock = new MockHandler([
            new ConnectException('Connection refused', new Request('POST', 'test')),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $service = $this->getMockBuilder(QdrantService::class)
            ->onlyMethods([])
            ->getMock();

        $reflection = new \ReflectionClass($service);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($service, $client);

        $result = $service->search(array_fill(0, 1536, 0.1));
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_search_returns_results_on_success(): void
    {
        $mockResults = [
            'result' => [
                ['id' => 'abc123', 'score' => 0.95, 'payload' => ['content' => 'Test content']],
            ],
        ];

        $mock = new MockHandler([
            new Response(200, [], json_encode($mockResults)),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $service = new QdrantService();
        $reflection = new \ReflectionClass($service);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($service, $client);

        $result = $service->search(array_fill(0, 1536, 0.1));
        $this->assertCount(1, $result);
        $this->assertEquals('abc123', $result[0]['id']);
        $this->assertEquals('Test content', $result[0]['payload']['content']);
    }

    public function test_upsertPoints_logs_error_on_failure(): void
    {
        Log::shouldReceive('error')
            ->once()
            ->withArgs(fn($message) => str_contains($message, 'Qdrant upsert failed'));

        $mock = new MockHandler([
            new ConnectException('Connection refused', new Request('PUT', 'test')),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $service = new QdrantService();
        $reflection = new \ReflectionClass($service);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($service, $client);

        $service->upsertPoints([['id' => 'test', 'vector' => [0.1]]]);

        $this->assertTrue(true);
    }
}
