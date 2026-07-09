<?php

namespace App\Http\Controllers;

use App\Models\HelpArticle;
use App\Models\HelpCategory;
use Illuminate\Http\Request;

class HelpController extends Controller
{
    /** Help center homepage */
    public function index()
    {
        $categories = HelpCategory::active()
            ->withCount(['publishedArticles'])
            ->with(['publishedArticles' => fn($q) => $q->orderBy('sort_order')->limit(5)])
            ->get();

        $featured = HelpArticle::published()->orderByDesc('views')->limit(6)->with('category')->get();

        return view('help.index', compact('categories', 'featured'));
    }

    /** Category listing */
    public function category(string $slug)
    {
        $category = HelpCategory::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $articles  = HelpArticle::published()->ofType('article')
            ->where('help_category_id', $category->id)->orderBy('sort_order')->get();
        $tutorials = HelpArticle::published()->ofType('tutorial')
            ->where('help_category_id', $category->id)->orderBy('sort_order')->get();
        $guides    = HelpArticle::published()->ofType('guide')
            ->where('help_category_id', $category->id)->orderBy('sort_order')->get();

        $allCategories = HelpCategory::active()->get();

        return view('help.category', compact('category', 'articles', 'tutorials', 'guides', 'allCategories'));
    }

    /** Single article/tutorial/guide */
    public function show(string $slug)
    {
        $article = HelpArticle::where('slug', $slug)->published()->with('category')->firstOrFail();

        // Increment views
        $article->increment('views');

        // Related — same category, same type, exclude current
        $related = HelpArticle::published()
            ->where('help_category_id', $article->help_category_id)
            ->where('id', '!=', $article->id)
            ->orderBy('sort_order')
            ->limit(4)
            ->get();

        $allCategories = HelpCategory::active()->get();

        return view('help.show', compact('article', 'related', 'allCategories'));
    }

    /** Search */
    public function search(Request $request)
    {
        $q       = trim($request->input('q', ''));
        $results = collect();

        if (strlen($q) >= 2) {
            $results = HelpArticle::published()
                ->where(function ($query) use ($q) {
                    $query->where('title_fr', 'like', "%{$q}%")
                          ->orWhere('title_en', 'like', "%{$q}%")
                          ->orWhere('content_fr', 'like', "%{$q}%")
                          ->orWhere('content_en', 'like', "%{$q}%")
                          ->orWhere('excerpt_fr', 'like', "%{$q}%")
                          ->orWhere('excerpt_en', 'like', "%{$q}%");
                })
                ->with('category')
                ->orderByDesc('views')
                ->limit(30)
                ->get();
        }

        $allCategories = HelpCategory::active()->get();

        return view('help.search', compact('q', 'results', 'allCategories'));
    }
}
