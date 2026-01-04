<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Models\ArticleComment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\WebsiteUser\ArticleCommentResource;

class ArticleCommentController extends Controller
{
    /**
     * عرض تعليقات مقالة
     */
    public function index($articleId)
    {
        $article = Article::published()->findOrFail($articleId);

        $comments = $article->comments()
            ->with(['user', 'replies.user'])
            ->approved()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => ArticleCommentResource::collection($comments),
            'meta' => [
                'total' => $comments->total(),
                'article_title' => $article->title
            ]
        ]);
    }

    /**
     * إضافة تعليق جديد
     */
    public function store(Request $request, $articleId)
    {
        $request->validate([
            'content' => 'required|string|min:3|max:1000',
            'parent_id' => 'nullable|exists:article_comments,id'
        ]);

        $article = Article::published()->findOrFail($articleId);

        $commentData = [
            'article_id' => $article->id,
            'content' => $request->content,
            'parent_id' => $request->parent_id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ];

        if (auth()->guard('sanctum')->check()) {
            $commentData['user_id'] = Auth::id();
            $commentData['status'] = 'approved'; // الموافقة التلقائية للمستخدمين المسجلين
        } else {
            $request->validate([
                'guest_name' => 'required|string|min:2|max:100',
                'guest_email' => 'required|email'
            ]);

            $commentData['guest_name'] = $request->guest_name;
            $commentData['guest_email'] = $request->guest_email;
            $commentData['status'] = 'pending'; // تحتاج مراجعة
        }

        $comment = ArticleComment::create($commentData);

        // تحديث عدد التعليقات في المقالة
        $article->increment('comments_count');

        return response()->json([
            'success' => true,
            'message' => Auth::check() ? 'تم إضافة التعليق بنجاح' : 'تم إضافة التعليق وسيتم مراجعته',
            'data' => new ArticleCommentResource($comment)
        ], 201);
    }

    /**
     * إعجاب بتعليق
     */
    public function like($commentId)
    {
        $comment = ArticleComment::approved()->findOrFail($commentId);

        // التحقق إذا كان المستخدم قد أعجب بهذا التعليق مسبقاً
        $existingLike = $comment->likes()
            ->where('user_id', Auth::id())
            ->first();

        if ($existingLike) {
            // إزالة الإعجاب
            $existingLike->delete();
            $comment->decrement('likes_count');
            $message = 'تم إزالة الإعجاب';
        } else {
            // إضافة إعجاب
            $comment->likes()->create(['user_id' => Auth::id()]);
            $comment->increment('likes_count');
            $message = 'تم الإعجاب بالتعليق';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'likes_count' => $comment->fresh()->likes_count
        ]);
    }
}
