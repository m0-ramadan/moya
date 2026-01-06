<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Article::with(['category', 'author', 'tags']);

        // البحث
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        // التصنيف
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // الحالة
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'featured') {
                $query->where('is_featured', true);
            } elseif ($request->status === 'published') {
                $query->where('published_at', '<=', now());
            } elseif ($request->status === 'draft') {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '>', now());
            }
        }

        // الترتيب
        $sortBy = $request->get('sort_by', 'published_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // التاريخ من وإلى
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('published_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('published_at', '<=', $request->date_to);
        }

        // العدد من وإلى
        if ($request->has('views_from') && $request->views_from) {
            $query->where('views_count', '>=', $request->views_from);
        }
        if ($request->has('views_to') && $request->views_to) {
            $query->where('views_count', '<=', $request->views_to);
        }

        $articles = $query->paginate(15);
        $categories = ArticleCategory::active()->get();
        $authors = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['admin', 'author', 'editor']);
        })->get();

        // الإحصائيات
        $stats = [
            'total' => Article::count(),
            'active' => Article::where('is_active', true)->count(),
            'inactive' => Article::where('is_active', false)->count(),
            'featured' => Article::where('is_featured', true)->count(),
            'total_views' => Article::sum('views_count'),
            'draft' => Article::whereNull('published_at')
                ->orWhere('published_at', '>', now())
                ->count(),
        ];

        return view('Admin.articles.index', compact('articles', 'categories', 'authors', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = ArticleCategory::active()->get();
        $tags = Tag::all();
        $authors = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['admin', 'author', 'editor']);
        })->get();

        return view('Admin.articles.create', compact('categories', 'tags', 'authors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'category_id' => 'required|exists:article_categories,id',
            'author_id' => 'required|exists:users,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'image_alt' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        // إنشاء slug
        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(6);

        // رفع الصورة إذا وجدت
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('articles', 'public');
            $validated['image'] = $imagePath;
        }

        // حساب وقت القراءة
        $validated['reading_time'] = $this->calculateReadingTime($validated['content']);

        // إنشاء المقال
        $article = Article::create($validated);

        // إضافة التاغات
        if ($request->has('tags')) {
            $article->tags()->sync($request->tags);
        }

        return redirect()->route('admin.articles.index')
            ->with('success', 'تم إنشاء المقال بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        $article->load(['category', 'author', 'tags', 'comments.user']);
        return view('Admin.articles.show', compact('article'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article)
    {
        $categories = ArticleCategory::active()->get();
        $tags = Tag::all();
        $authors = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['admin', 'author', 'editor']);
        })->get();
        $article->load('tags');

        return view('Admin.articles.edit', compact('article', 'categories', 'tags', 'authors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'category_id' => 'required|exists:article_categories,id',
            'author_id' => 'required|exists:users,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'image_alt' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        // تحديث slug إذا تغير العنوان
        if ($validated['title'] !== $article->title) {
            $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(6);
        }

        // رفع صورة جديدة إذا وجدت
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة
            if ($article->image) {
                Storage::disk('public')->delete($article->image);
            }

            $imagePath = $request->file('image')->store('articles', 'public');
            $validated['image'] = $imagePath;
        }

        // حساب وقت القراءة
        $validated['reading_time'] = $this->calculateReadingTime($validated['content']);

        // تحديث المقال
        $article->update($validated);

        // تحديث التاغات
        if ($request->has('tags')) {
            $article->tags()->sync($request->tags);
        } else {
            $article->tags()->detach();
        }

        return redirect()->route('admin.articles.index')
            ->with('success', 'تم تحديث المقال بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        // حذف الصورة
        if ($article->image) {
            Storage::disk('public')->delete($article->image);
        }

        // حذف العلاقات
        $article->tags()->detach();
        $article->comments()->delete();

        // حذف المقال
        $article->delete();

        return response()->json(['success' => 'تم حذف المقال بنجاح']);
    }

    /**
     * Toggle article status.
     */
    public function toggleStatus(Article $article)
    {
        $article->update(['is_active' => !$article->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'تم تغيير حالة المقال بنجاح',
            'is_active' => $article->is_active
        ]);
    }

    /**
     * Toggle featured status.
     */
    public function toggleFeatured(Article $article)
    {
        $article->update(['is_featured' => !$article->is_featured]);

        return response()->json([
            'success' => true,
            'message' => 'تم تغيير حالة التمييز بنجاح',
            'is_featured' => $article->is_featured
        ]);
    }

    /**
     * Calculate reading time.
     */
    private function calculateReadingTime($content)
    {
        $wordCount = str_word_count(strip_tags($content));
        $readingTime = ceil($wordCount / 200); // 200 كلمة في الدقيقة
        return $readingTime > 0 ? $readingTime : 1;
    }

    /**
     * Bulk actions.
     */
    public function bulkActions(Request $request)
    {
        $action = $request->action;
        $ids = $request->ids;

        if (!$ids) {
            return back()->with('error', 'لم يتم تحديد أي مقالات');
        }

        switch ($action) {
            case 'activate':
                Article::whereIn('id', $ids)->update(['is_active' => true]);
                $message = 'تم تفعيل المقالات المحددة';
                break;

            case 'deactivate':
                Article::whereIn('id', $ids)->update(['is_active' => false]);
                $message = 'تم تعطيل المقالات المحددة';
                break;

            case 'feature':
                Article::whereIn('id', $ids)->update(['is_featured' => true]);
                $message = 'تم تمييز المقالات المحددة';
                break;

            case 'unfeature':
                Article::whereIn('id', $ids)->update(['is_featured' => false]);
                $message = 'تم إلغاء تمييز المقالات المحددة';
                break;

            case 'delete':
                $articles = Article::whereIn('id', $ids)->get();
                foreach ($articles as $article) {
                    if ($article->image) {
                        Storage::disk('public')->delete($article->image);
                    }
                    $article->tags()->detach();
                    $article->comments()->delete();
                    $article->delete();
                }
                $message = 'تم حذف المقالات المحددة';
                break;

            default:
                return back()->with('error', 'الإجراء غير معروف');
        }

        return back()->with('success', $message);
    }
}
