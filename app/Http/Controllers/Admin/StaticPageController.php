<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaticPage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class StaticPageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // البحث والفلترة
        $query = StaticPage::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // الترتيب
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $pages = $query->paginate(10);

        return view('Admin.static-pages.index', compact('pages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Admin.static-pages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'slug' => 'nullable|string|max:255|unique:static_pages,slug',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // إنشاء slug إذا لم يتم توفيره
        $slug = $request->slug;
        if (empty($slug)) {
            $slug = Str::slug($request->title, '-', 'ar');
        }

        // التأكد من أن الـ slug فريد
        $slug = $this->makeUniqueSlug($slug);

        StaticPage::create([
            'title' => $request->title,
            'slug' => $slug,
            'content' => $request->content,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.static-pages.index')
            ->with('success', 'تم إنشاء الصفحة الثابتة بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(StaticPage $staticPage)
    {
        return view('Admin.static-pages.show', compact('staticPage'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StaticPage $staticPage)
    {
        return view('Admin.static-pages.edit', compact('staticPage'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StaticPage $staticPage)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'slug' => 'required|string|max:255|unique:static_pages,slug,' . $staticPage->id,
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $staticPage->update([
            'title' => $request->title,
            'slug' => Str::slug($request->slug, '-', 'ar'),
            'content' => $request->content,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.static-pages.index')
            ->with('success', 'تم تحديث الصفحة الثابتة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StaticPage $staticPage)
    {
        // منع حذف الصفحات الأساسية
        $protectedSlugs = ['syas-alkhsosy', 'syas-alastrgaaa', 'aldman', 'mn-nhn', 'alshrot-oalahkam'];

        if (in_array($staticPage->slug, $protectedSlugs)) {
            return redirect()->route('admin.static-pages.index')
                ->with('error', 'لا يمكن حذف هذه الصفحة الأساسية');
        }

        $staticPage->delete();

        return redirect()->route('admin.static-pages.index')
            ->with('success', 'تم حذف الصفحة الثابتة بنجاح');
    }

    /**
     * Make unique slug
     */
    private function makeUniqueSlug($slug)
    {
        $originalSlug = $slug;
        $count = 1;

        while (StaticPage::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $action = $request->action;
        $ids = json_decode($request->ids);

        if (!$ids) {
            return redirect()->back()->with('error', 'لم يتم تحديد أي صفحات');
        }

        switch ($action) {
            case 'activate':
                StaticPage::whereIn('id', $ids)->update(['status' => 'active']);
                $message = 'تم تفعيل الصفحات المحددة';
                break;

            case 'deactivate':
                StaticPage::whereIn('id', $ids)->update(['status' => 'inactive']);
                $message = 'تم تعطيل الصفحات المحددة';
                break;

            case 'delete':
                // منع حذف الصفحات المحمية
                $protectedSlugs = ['syas-alkhsosy', 'syas-alastrgaaa', 'aldman', 'mn-nhn', 'alshrot-oalahkam'];
                StaticPage::whereIn('id', $ids)
                    ->whereNotIn('slug', $protectedSlugs)
                    ->delete();
                $message = 'تم حذف الصفحات المحددة';
                break;

            default:
                return redirect()->back()->with('error', 'الإجراء غير صالح');
        }

        return redirect()->route('admin.static-pages.index')->with('success', $message);
    }
}