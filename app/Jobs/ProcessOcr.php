<?php

namespace App\Jobs;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessOcr implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Document $document;

    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    public function handle(): void
    {
        $document = $this->document;
        $path = Storage::disk('local')->path($document->stored_path);

        if (!file_exists($path)) {
            Log::warning('OCR file not found', ['document_id' => $document->id, 'path' => $path]);
            return;
        }

        $mime = $document->mime_type;
        $ocrText = '';

        try {
            if (str_contains($mime, 'pdf')) {
                $cmd = "pdftotext -layout " . escapeshellarg($path) . " -";
                $ocrText = shell_exec($cmd) ?? '';
                if ($ocrText === null) {
                    Log::error('pdftotext command failed', ['document_id' => $document->id]);
                    $ocrText = '';
                }
            } elseif (str_contains($mime, 'image')) {
                $cmd = "tesseract " . escapeshellarg($path) . " stdout 2>/dev/null";
                $ocrText = shell_exec($cmd) ?? '';
                if ($ocrText === null) {
                    Log::error('tesseract command failed', ['document_id' => $document->id]);
                    $ocrText = '';
                }
            } elseif (str_contains($mime, 'text')) {
                $ocrText = file_get_contents($path) ?: '';
            } else {
                Log::warning('Unsupported MIME type for OCR', [
                    'document_id' => $document->id,
                    'mime_type' => $mime,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('OCR processing error', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
            ]);
            return;
        }

        $ocrText = trim($ocrText);

        if (!empty($ocrText)) {
            $document->update(['ocr_text' => $ocrText]);
            GenerateEmbeddings::dispatch($document);
        } else {
            Log::info('OCR produced empty text', ['document_id' => $document->id]);
        }
    }
}
