<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Models\ArticleCategory;
use App\Http\Controllers\Controller;
use App\Http\Resources\WebsiteUser\ArticleResource;
use App\Http\Resources\WebsiteUser\ArticleCategoryResource;

class ArticleCategoryController extends Controller
{
    /**
     * عرض جميع الأقسام
     */
    public function index()
    {
        $categories = ArticleCategory::active()
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => ArticleCategoryResource::collection($categories),
            'meta' => [
                'count' => $categories->count()
            ]
        ]);
    }

    /**
     * عرض قسم محدد
     */
    public function show($slug)
    {
        $category = ArticleCategory::where('slug', $slug)
            ->active()
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => new ArticleCategoryResource($category)
        ]);
    }

    /**
     * عرض مقالات قسم محدد
     */
    public function articles($slug, Request $request)
    {
        $category = ArticleCategory::where('slug', $slug)
            ->active()
            ->firstOrFail();

        $query = $category->articles()
            ->with(['author', 'category'])
            ->published()
            ->orderBy('published_at', 'desc');

        // الباجينيشين
        $perPage = $request->get('per_page', 12);
        $articles = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'category' => new ArticleCategoryResource($category),
            'data' => ArticleResource::collection($articles),
            'meta' => [
                'total' => $articles->total(),
                'per_page' => $articles->perPage(),
                'current_page' => $articles->currentPage(),
                'last_page' => $articles->lastPage(),
            ]
        ]);
    }
}
