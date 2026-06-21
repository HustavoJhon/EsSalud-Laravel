<?php

namespace App\Services;

class QdrantService
{
    protected string $baseUrl;
    protected string $collection;
    protected \GuzzleHttp\Client $client;

    public function __construct()
    {
        $this->baseUrl = rtrim(env('QDRANT_URL', 'http://qdrant:6333'), '/');
        $this->collection = env('QDRANT_COLLECTION', 'essalud_docs');
        $this->client = new \GuzzleHttp\Client(['base_uri' => $this->baseUrl]);
    }

    public function ensureCollection(): void
    {
        try {
            $this->client->get("/collections/{$this->collection}");
        } catch (\Exception $e) {
            $this->client->put("/collections/{$this->collection}", [
                'json' => [
                    'vectors' => [
                        'size' => 1536,
                        'distance' => 'Cosine',
                    ],
                ],
            ]);
        }
    }

    public function upsertPoints(array $points): void
    {
        $this->client->put("/collections/{$this->collection}/points", [
            'json' => ['points' => $points],
        ]);
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
        }
    }
}
