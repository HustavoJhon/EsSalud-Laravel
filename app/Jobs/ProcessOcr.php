<?php

namespace App\Jobs;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
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
        $mime = $document->mime_type;
        $ocrText = '';

        if (str_contains($mime, 'pdf')) {
            $cmd = "pdftotext -layout " . escapeshellarg($path) . " -";
            $ocrText = shell_exec($cmd) ?? '';
        } elseif (str_contains($mime, 'image')) {
            $cmd = "tesseract " . escapeshellarg($path) . " stdout 2>/dev/null";
            $ocrText = shell_exec($cmd) ?? '';
        } elseif (str_contains($mime, 'text')) {
            $ocrText = file_get_contents($path) ?: '';
        }

        $ocrText = trim($ocrText);

        if (!empty($ocrText)) {
            $document->update(['ocr_text' => $ocrText]);

            GenerateEmbeddings::dispatch($document);
        }
    }
}
