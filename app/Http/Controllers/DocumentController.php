<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\Procedure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = Document::with(['user', 'category', 'procedure']);

        if (Auth::user()->hasRole('ASEG')) {
            $query->where('user_id', Auth::id());
        }

        if ($request->filled('procedure_id')) {
            $query->where('procedure_id', $request->procedure_id);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $documents = $query->latest()->paginate(20);
        $categories = DocumentCategory::where('is_active', true)->get();
        $procedures = Procedure::where('user_id', Auth::id())->get();

        return view('documents.index', compact('documents', 'categories', 'procedures'));
    }

    public function show(Document $document)
    {
        $document->load(['user', 'category', 'procedure', 'validatedBy', 'embeddings']);
        return view('documents.show', compact('document'));
    }

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
            'user_id' => Auth::id(),
            'procedure_id' => $validated['procedure_id'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'original_name' => $file->getClientOriginalName(),
            'stored_path' => $storedPath,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'version' => 1,
            'minio_path' => null,
        ]);

        if ($request->wantsJson()) {
            return response()->json($document->load('category'), 201);
        }

        return back()->with('status', 'Documento subido exitosamente.');
    }

    public function destroy(Document $document)
    {
        if ($document->user_id !== Auth::id() && !Auth::user()->hasRole('SADM')) {
            abort(403);
        }

        Storage::disk('local')->delete($document->stored_path);
        $document->delete();

        return back()->with('status', 'Documento eliminado.');
    }

    public function validateDoc(Document $document)
    {
        if (!Auth::user()->can('document.validate')) {
            abort(403);
        }

        $document->update([
            'is_validated' => true,
            'validated_by' => Auth::id(),
            'validated_at' => now(),
        ]);

        return back()->with('status', 'Documento validado.');
    }
}
