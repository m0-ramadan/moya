<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Article::with(['category', 'author']);

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
                $query->where('status', 'published');
            } elseif ($request->status === 'inactive') {
                $query->where('status', 'unpublished');
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
        $authors = Admin::all();

        // الإحصائيات
        $stats = [
            'total' => Article::count(),
            'active' => Article::where('status', 'published')->count(),
            'inactive' => Article::where('status', 'unpublished')->count(),
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
       // $tags = Tag::all();
        $authors = Admin::all();

        return view('Admin.articles.create', compact('categories',  'authors'));
    }
// في ArticleController (الـ Frontend)
public function byTag($tag)
{
    $articles = Article::where('status', 'published')
        ->where('published_at', '<=', now())
        ->whereJsonContains('tags', $tag)
        ->orderBy('published_at', 'desc')
        ->paginate(12);

    return view('frontend.articles.by-tag', compact('articles', 'tag'));
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
        'author_id' => 'required|exists:admins,id',
        'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        'meta_title' => 'nullable|string|max:70',
        'meta_description' => 'nullable|string|max:160',
        'meta_keywords' => 'nullable|string',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_sponsored' => 'boolean',
        'allow_comments' => 'boolean',
        'published_at' => 'nullable|date',
        'tags' => 'nullable|string',
    ]);

    // إنشاء slug
    $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(6);

    // رفع الصورة
    if ($request->hasFile('featured_image')) {
        $validated['featured_image'] = $request->file('featured_image')->store('articles', 'public');
    }

    // معالجة الـ Tags
    if (isset($validated['tags'])) {
        $tags = array_map('trim', explode(',', $validated['tags']));
        $tags = array_filter($tags);
        $tags = array_unique($tags);
        $validated['tags'] = array_values($tags);
    } else {
        $validated['tags'] = [];
    }

    // معالجة الكلمات المفتاحية
    if (isset($validated['meta_keywords'])) {
        $keywords = array_map('trim', explode(',', $validated['meta_keywords']));
        $validated['meta_keywords'] = array_filter($keywords);
    }

    // حساب وقت القراءة
    $wordCount = str_word_count(strip_tags($validated['content']));
    $validated['reading_time'] = ceil($wordCount / 200);

    // إنشاء المقال
    $article = Article::create($validated);

    return redirect()->route('admin.articles.index')
        ->with('success', 'تم إنشاء المقال بنجاح');
}
    /**
     * Display the specified resource.
     */
    public function show( $article)
    {
     
        $article = Article::where('id', $article)->firstOrFail();
        $article->load(['category', 'author', 'comments.user']);
        return view('Admin.articles.show', compact('article'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit( $article)
    {
       $article = Article::where('id', $article)->firstOrFail();
        $categories = ArticleCategory::active()->get();
       // $tags = Tag::all();
        $authors = Admin::all();
  

        return view('Admin.articles.edit', compact('article', 'categories', 'authors'));
    }

    /**
     * Update the specified resource in storage.
     */
public function update(Request $request, Article $article)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'slug' => 'nullable|string|unique:articles,slug,' . $article->id,
        'content' => 'required|string',
        'excerpt' => 'nullable|string|max:500',
        'category_id' => 'required|exists:article_categories,id',
        'subcategory_id' => 'nullable|exists:article_categories,id',
        'author_id' => 'required|exists:admins,id',
        'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        'meta_title' => 'nullable|string|max:70',
        'meta_description' => 'nullable|string|max:160',
        'meta_keywords' => 'nullable|string',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_sponsored' => 'boolean',
        'allow_comments' => 'boolean',
        'published_at' => 'nullable|date',
        'reading_time' => 'nullable|integer|min:1',
        'tags' => 'nullable|string', // تأكد من أنها string
        'related_articles' => 'nullable|array',
    ]);

    // إنشاء slug إذا كان فارغاً
    if (empty($validated['slug'])) {
        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(6);
    }

    // معالجة الصورة
    if ($request->hasFile('featured_image')) {
        if ($article->featured_image) {
            Storage::disk('public')->delete($article->featured_image);
        }
        $validated['featured_image'] = $request->file('featured_image')->store('articles', 'public');
    }

    // معالجة الـ Tags (تحويل من string إلى array)
    if (isset($validated['tags'])) {
        // تنظيف وتقسيم الـ tags
        $tags = array_map('trim', explode(',', $validated['tags']));
        $tags = array_filter($tags); // إزالة القيم الفارغة
        $tags = array_unique($tags); // إزالة التكرار
        $validated['tags'] = array_values($tags); // إعادة ترتيب المفاتيح
    } else {
        $validated['tags'] = [];
    }

    // معالجة الكلمات المفتاحية
    if (isset($validated['meta_keywords'])) {
        $keywords = array_map('trim', explode(',', $validated['meta_keywords']));
        $validated['meta_keywords'] = array_filter($keywords);
    }

    // حساب وقت القراءة
    if (empty($validated['reading_time'])) {
        $wordCount = str_word_count(strip_tags($validated['content']));
        $validated['reading_time'] = ceil($wordCount / 200);
    }

    // تحديث المقال
    $article->update($validated);

    if ($request->action === 'save_and_continue') {
        return redirect()->route('admin.articles.edit', $article)
            ->with('success', 'تم تحديث المقال بنجاح');
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
public function toggleStatus($article)
{
    $article = Article::findOrFail($article);

    $article->status = $article->status === 'published'
        ? 'unpublished'
        : 'published';

    $article->save();

    return response()->json([
        'success' => true,
        'message' => 'تم تغيير حالة المقال بنجاح',
        'status' => $article->status
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
