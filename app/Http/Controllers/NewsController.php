<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $query = News::with(['category', 'author'])
            ->where('is_active', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());

        if ($request->filled('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('excerpt', 'like', "%{$request->search}%");
            });
        }

        $news = $query->latest('published_at')->paginate(12);
        $categories = NewsCategory::where('is_active', true)->get();

        return view('news.index', compact('news', 'categories'));
    }

    public function show(News $news)
    {
        $news->load(['category', 'author']);
        return view('news.show', compact('news'));
    }

    public function create()
    {
        $categories = NewsCategory::where('is_active', true)->get();
        return view('news.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'image_url' => 'nullable|url|max:512',
            'category_id' => 'nullable|exists:news_categories,id',
            'published_at' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        $news = News::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'excerpt' => $validated['excerpt'] ?? Str::limit(strip_tags($validated['content']), 200),
            'image_url' => $validated['image_url'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'author_id' => Auth::id(),
            'published_at' => $validated['published_at'] ?? now(),
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('news.show', $news)
            ->with('status', 'Noticia publicada exitosamente.');
    }

    public function edit(News $news)
    {
        $categories = NewsCategory::where('is_active', true)->get();
        return view('news.edit', compact('news', 'categories'));
    }

    public function update(Request $request, News $news)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'image_url' => 'nullable|url|max:512',
            'category_id' => 'nullable|exists:news_categories,id',
            'published_at' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        $news->update($validated);

        return redirect()->route('news.show', $news)
            ->with('status', 'Noticia actualizada.');
    }

    public function destroy(News $news)
    {
        $news->update(['is_active' => false]);
        return back()->with('status', 'Noticia desactivada.');
    }

    public function apiIndex(Request $request)
    {
        $query = News::with(['category', 'author'])
            ->where('is_active', true)
            ->whereNotNull('published_at');

        if ($request->filled('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }

        $news = $query->latest('published_at')->paginate($request->per_page ?? 12);
        return response()->json($news);
    }

    public function apiShow(News $news)
    {
        $news->load(['category', 'author']);
        return response()->json($news);
    }
}
