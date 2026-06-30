<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|max:20480',
            'procedure_id' => 'nullable|exists:procedures,id',
            'category_id' => 'nullable|exists:document_categories,id',
        ]);

        $file = $request->file('file');
        $storedPath = $file->store('documents/' . date('Y/m'), 'local');

        $document = Document::create([
            'user_id' => $request->user()->id,
            'procedure_id' => $validated['procedure_id'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'original_name' => $file->getClientOriginalName(),
            'stored_path' => $storedPath,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'version' => 1,
        ]);

        return response()->json($document->load('category'), 201);
    }

    public function index(Request $request)
    {
        $documents = Document::where('user_id', $request->user()->id)
            ->with(['category', 'procedure'])
            ->latest()
            ->paginate($request->per_page ?? 20);

        return response()->json($documents);
    }

    public function show(Document $document)
    {
        $document->load(['user', 'category', 'procedure', 'validatedBy', 'embeddings']);
        return response()->json($document);
    }
}
