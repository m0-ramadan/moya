<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleBookmark;
use App\Models\ArticleLike;
use App\Models\ArticleShare;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleInteractionController extends Controller
{
    /**
     * إعجاب بمقالة
     */
    public function like($articleId)
    {
        $article = Article::published()->findOrFail($articleId);
        $user = Auth::user();

        $existingLike = ArticleLike::where('article_id', $articleId)
            ->where('user_id', $user->id)
            ->first();

        if ($existingLike) {
            // إزالة الإعجاب
            $existingLike->delete();
            $article->decrement('likes_count');
            $message = 'تم إزالة الإعجاب بالمقالة';
        } else {
            // إضافة إعجاب
            ArticleLike::create([
                'article_id' => $articleId,
                'user_id' => $user->id,
                'type' => 'like'
            ]);
            $article->increment('likes_count');
            $message = 'تم الإعجاب بالمقالة';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'likes_count' => $article->fresh()->likes_count
        ]);
    }

    /**
     * إضافة/إزالة من المفضلة
     */
    public function bookmark(Request $request, $articleId)
    {
        $article = Article::published()->findOrFail($articleId);
        $user = Auth::user();

        $request->validate([
            'folder' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:500'
        ]);

        $existingBookmark = ArticleBookmark::where('article_id', $articleId)
            ->where('user_id', $user->id)
            ->first();

        if ($existingBookmark) {
            // إزالة من المفضلة
            $existingBookmark->delete();
            $article->decrement('bookmarks_count');
            $message = 'تم إزالة المقالة من المفضلة';
        } else {
            // إضافة للمفضلة
            ArticleBookmark::create([
                'article_id' => $articleId,
                'user_id' => $user->id,
                'folder' => $request->folder ?? 'default',
                'notes' => $request->notes,
                'tags' => $request->tags ? explode(',', $request->tags) : null
            ]);
            $article->increment('bookmarks_count');
            $message = 'تم إضافة المقالة إلى المفضلة';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'bookmarks_count' => $article->fresh()->bookmarks_count
        ]);
    }

    /**
     * مشاركة مقالة
     */
    public function share(Request $request, $articleId)
    {
        $article = Article::published()->findOrFail($articleId);

        $request->validate([
            'platform' => 'required|string|in:facebook,twitter,whatsapp,linkedin,email,copy_link',
            'method' => 'required|string'
        ]);

        $shareData = [
            'article_id' => $articleId,
            'platform' => $request->platform,
            'method' => $request->method,
            'ip_address' => $request->ip(),
            'shared_with' => $request->shared_with ? explode(',', $request->shared_with) : null
        ];

        if (Auth::check()) {
            $shareData['user_id'] = Auth::id();
        }

        ArticleShare::create($shareData);
        $article->increment('shares_count');

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل المشاركة بنجاح',
            'shares_count' => $article->fresh()->shares_count,
            'share_url' => route('articles.show', $article->slug)
        ]);
    }
}