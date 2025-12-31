<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;

use Illuminate\Http\Request;
use App\Models\ArticleCategory;
use App\Http\Controllers\Controller;
use App\Http\Resources\WebsiteUser\ArticleResource;

class ArticleController extends Controller
{
    /**
     * عرض قائمة المقالات
     */
    public function index(Request $request)
    {
        $query = Article::with(['author', 'category'])
            ->published()
            ->orderBy('published_at', 'desc');

        // البحث
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // التصفية حسب القسم
        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // التصفية حسب الكاتب
        if ($request->has('author')) {
            $query->whereHas('author', function ($q) use ($request) {
                $q->where('id', $request->author);
            });
        }

        // المقالات المميزة فقط
        if ($request->boolean('featured')) {
            $query->featured();
        }

        // الباجينيشين
        $perPage = $request->get('per_page', 12);
        $articles = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => ArticleResource::collection($articles),
            'meta' => [
                'total' => $articles->total(),
                'per_page' => $articles->perPage(),
                'current_page' => $articles->currentPage(),
                'last_page' => $articles->lastPage(),
            ]
        ]);
    }

    /**
     * عرض مقالة محددة
     */
    public function show($slug)
    {
        $article = Article::with(['author', 'category', 'sections'])
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        // زيادة عدد المشاهدات
        $article->increaseViewCount();

        return response()->json([
            'success' => true,
            'data' => new ArticleResource($article)
        ]);
    }

    /**
     * المقالات المميزة
     */
    public function featured()
    {
        $articles = Article::with(['author', 'category'])
            ->published()
            ->featured()
            ->orderBy('published_at', 'desc')
            ->limit(6)
            ->get();

        return response()->json([
            'success' => true,
            'data' => ArticleResource::collection($articles)
        ]);
    }

    /**
     * أحدث المقالات
     */
    public function latest()
    {
        $articles = Article::with(['author', 'category'])
            ->published()
            ->orderBy('published_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => ArticleResource::collection($articles)
        ]);
    }

    /**
     * مقالات عشوائية
     */
    public function random()
    {
        $articles = Article::with(['author', 'category'])
            ->published()
            ->inRandomOrder()
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => ArticleResource::collection($articles)
        ]);
    }

    /**
     * البحث في المقالات
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:1'
        ]);

        $articles = Article::with(['author', 'category'])
            ->published()
            ->where(function ($query) use ($request) {
                $query->where('title', 'like', "%{$request->q}%")
                    ->orWhere('excerpt', 'like', "%{$request->q}%")
                    ->orWhere('content', 'like', "%{$request->q}%");
            })
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return response()->json([
            'success' => true,
            'data' => ArticleResource::collection($articles),
            'meta' => [
                'total' => $articles->total(),
                'search_term' => $request->q
            ]
        ]);
    }
}
