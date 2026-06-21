<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $categories = FaqCategory::orderBy('sort_order')->with(['faqs' => function ($q) {
            $q->where('is_active', true)->orderBy('view_count', 'desc');
        }])->get();

        if ($request->filled('search')) {
            $search = $request->search;
            $faqs = Faq::where('is_active', true)
                ->where(function ($q) use ($search) {
                    $q->where('question', 'like', "%{$search}%")
                      ->orWhere('answer', 'like', "%{$search}%")
                      ->orWhere('keywords', 'like', "%{$search}%");
                })
                ->with('category')
                ->paginate(15);

            return view('faq.index', compact('faqs', 'categories', 'search'));
        }

        $faqs = null;
        return view('faq.index', compact('faqs', 'categories'));
    }

    public function view(Faq $faq)
    {
        $faq->increment('view_count');
        $faq->load('category');
        return response()->json($faq);
    }

    public function helpful(Faq $faq)
    {
        $faq->increment('helpful_count');
        return response()->json(['message' => 'Gracias por tu feedback.']);
    }

    public function notHelpful(Faq $faq)
    {
        $faq->increment('not_helpful_count');
        return response()->json(['message' => 'Gracias por tu feedback.']);
    }

    public function apiIndex(Request $request)
    {
        $query = Faq::with('category')->where('is_active', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                  ->orWhere('answer', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $faqs = $query->latest()->paginate($request->per_page ?? 20);
        return response()->json($faqs);
    }

    public function apiCategories()
    {
        $categories = FaqCategory::orderBy('sort_order')->withCount('faqs')->get();
        return response()->json($categories);
    }

    public function apiShow(Faq $faq)
    {
        $faq->increment('view_count');
        $faq->load('category');
        return response()->json($faq);
    }
}
