<?php

namespace App\Jobs;

use App\Models\Document;
use App\Models\DocumentEmbedding;
use App\Models\RagSource;
use App\Services\OpenAIService;
use App\Services\QdrantService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GenerateEmbeddings implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Document $document;

    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    public function handle(OpenAIService $openai, QdrantService $qdrant): void
    {
        $document = $this->document->fresh();
        $text = $document->ocr_text ?? '';

        if (empty(trim($text))) {
            Log::info('GenerateEmbeddings skipped — empty OCR text', ['document_id' => $document->id]);
            return;
        }

        try {
            $qdrant->ensureCollection();
        } catch (\Exception $e) {
            Log::error('Failed to ensure Qdrant collection', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
            ]);
            $this->release(30);
            return;
        }

        $chunks = $this->chunkText($text, 1000);
        $totalChunks = count($chunks);

        if ($totalChunks === 0) {
            Log::info('GenerateEmbeddings — no chunks generated', ['document_id' => $document->id]);
            return;
        }

        $ragSource = RagSource::updateOrCreate(
            ['document_id' => $document->id],
            [
                'title' => $document->original_name,
                'category' => $document->category?->name ?? 'general',
                'is_active' => true,
                'total_chunks' => $totalChunks,
                'last_indexed_at' => now(),
            ]
        );

        $points = [];

        foreach ($chunks as $index => $chunk) {
            try {
                $embedding = $openai->embedding($chunk);
            } catch (\Exception $e) {
                Log::error('Embedding generation failed', [
                    'document_id' => $document->id,
                    'chunk_index' => $index,
                    'error' => $e->getMessage(),
                ]);
                continue;
            }

            $pointId = (string) Str::uuid();

            try {
                DocumentEmbedding::create([
                    'document_id' => $document->id,
                    'chunk_index' => $index,
                    'content' => $chunk,
                    'embedding' => $embedding,
                    'qdrant_point_id' => $pointId,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to store DocumentEmbedding', [
                    'document_id' => $document->id,
                    'error' => $e->getMessage(),
                ]);
                continue;
            }

            $points[] = [
                'id' => $pointId,
                'vector' => $embedding,
                'payload' => [
                    'document_id' => $document->id,
                    'title' => $document->original_name,
                    'content' => $chunk,
                    'chunk_index' => $index,
                    'url' => $ragSource->source_url,
                ],
            ];
        }

        if (!empty($points)) {
            $qdrant->upsertPoints($points);
        }
    }

    protected function chunkText(string $text, int $maxLength = 1000): array
    {
        $chunks = [];
        $sentences = preg_split('/(?<=[.!?])\s+/', $text);
        $currentChunk = '';

        foreach ($sentences as $sentence) {
            if (mb_strlen($currentChunk . ' ' . $sentence) > $maxLength && !empty($currentChunk)) {
                $chunks[] = trim($currentChunk);
                $currentChunk = $sentence;
            } else {
                $currentChunk .= ' ' . $sentence;
            }
        }

        if (!empty($currentChunk)) {
            $chunks[] = trim($currentChunk);
        }

        return $chunks;
    }
}
