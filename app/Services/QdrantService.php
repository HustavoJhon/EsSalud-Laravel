<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class QdrantService
{
    protected string $baseUrl;
    protected string $collection;
    protected \GuzzleHttp\Client $client;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.qdrant.url', 'http://qdrant:6333'), '/');
        $this->collection = config('services.qdrant.collection', 'essalud_docs');
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 10,
            'connect_timeout' => 5,
        ]);
    }

    public function ensureCollection(): void
    {
        try {
            $this->client->get("/collections/{$this->collection}");
        } catch (\Exception $e) {
            try {
                $this->client->put("/collections/{$this->collection}", [
                    'json' => [
                        'vectors' => [
                            'size' => 1536,
                            'distance' => 'Cosine',
                        ],
                    ],
                ]);
                Log::info('Qdrant collection created', ['collection' => $this->collection]);
            } catch (\Exception $createException) {
                Log::error('Failed to create Qdrant collection', [
                    'collection' => $this->collection,
                    'error' => $createException->getMessage(),
                ]);
            }
        }
    }

    public function upsertPoints(array $points): void
    {
        try {
            $this->client->put("/collections/{$this->collection}/points", [
                'json' => ['points' => $points],
            ]);
        } catch (\Exception $e) {
            Log::error('Qdrant upsert failed', [
                'points_count' => count($points),
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function search(array $vector, int $limit = 5): array
    {
        try {
            $response = $this->client->post("/collections/{$this->collection}/points/search", [
                'json' => [
                    'vector' => $vector,
                    'limit' => $limit,
                    'with_payload' => true,
                ],
            ]);

            $body = json_decode($response->getBody(), true);
            return $body['result'] ?? [];
        } catch (\Exception $e) {
            Log::warning('Qdrant search failed', [
                'error' => $e->getMessage(),
                'limit' => $limit,
            ]);
            return [];
        }
    }

    public function deletePoint(string $pointId): void
    {
        try {
            $this->client->delete("/collections/{$this->collection}/points", [
                'json' => ['points' => [$pointId]],
            ]);
        } catch (\Exception $e) {
            Log::warning('Qdrant deletePoint failed', [
                'point_id' => $pointId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
