<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\ImageOptimizer\OptimizerChainFactory;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {

            $query = Category::with(['parent', 'children'])
                ->when($request->filled('search'), function ($q) use ($request) {
                    $search = $request->search;
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                })
                ->when($request->filled('parent_id'), function ($q) use ($request) {
                    $q->where('parent_id', $request->parent_id);
                })
                ->when($request->filled('status_id'), function ($q) use ($request) {
                    $q->where('status_id', $request->status_id);
                });

            // Return JSON for API requests
            if ($request->wantsJson() || $request->is('api/*')) {
                $categories = $query->get();
                return response()->json([
                    'success' => true,
                    'data' => $categories
                ]);
            }
            // $categories = $query->withCount(['products', 'children'])
            //     ->orderBy($request->order_by ?? 'order')
            //     ->paginate(20)
            //     ->withQueryString();
            // Return view for web requests
            $categories = $query->orderBy('parent_id')
                ->orderBy('order')
                ->paginate(20)
                ->withQueryString();

            $parentCategories = Category::whereNull('parent_id')->get();

            return view('Admin.category.index', compact('categories', 'parentCategories'));
        } catch (\Exception $e) {
            Log::error('Error fetching categories: ' . $e->getMessage());

            return $this->handleError($e, 'Failed to fetch categories');
        }
    }

    /**
     * Show the form for creating a new category.
     *
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $parentCategories = Category::whereNull('parent_id')
            ->orderBy('order')
            ->withCount('children')
            ->get();

        // Get recent categories for sidebar
        $recentCategories = Category::latest()
            ->take(5)
            ->get();

        // Check if coming from parent category
        $parentId = $request->get('parent_id');
        if ($parentId) {
            $parentCategory = Category::find($parentId);
            return view('Admin.category.create', compact('parentCategories', 'recentCategories', 'parentId', 'parentCategory'));
        }

        return view('Admin.category.create', compact('parentCategories', 'recentCategories'));
    }

    /**
     * Store a newly created category.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);

        try {
            DB::beginTransaction();

            $category = new Category();
            $this->fillCategoryData($category, $validated, $request);

            // Generate slug if not provided
            if (empty($category->slug)) {
                $category->slug = $this->generateUniqueSlug($validated['name']);
            }

            $category->save();

            DB::commit();

            $message = 'تم إضافة القسم بنجاح';

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $category->load(['parent', 'children'])
                ], 201);
            }

            // Check if user wants to add another
            if ($request->has('add_another')) {
                return redirect()->route('admin.categories.create')
                    ->with('success', $message)
                    ->with('add_another', true);
            }

            return redirect()->route('admin.categories.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating category: ' . $e->getMessage());

            return $this->handleError($e, 'Failed to create category');
        }
    }

    /**
     * Display the specified category.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\View\View
     */
    public function show(Category $category)
    {
        // Load all necessary relationships with counts
        $category->load([
            'parent',
            'children' => function ($query) {
                $query->orderBy('order')
                    ->withCount('products');
            }
        ]);

        // Load counts
        $category->loadCount(['products', 'children']);

        return view('Admin.category.show', compact('category'));
    }

    /**
     * Show the form for editing the specified category.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $parentCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->orderBy('order')
            ->get();

        // Load counts
        $category->loadCount(['products', 'children']);
        $category->load(['children' => function ($query) {
            $query->orderBy('order');
        }]);

        return view('Admin.category.edit', compact('category', 'parentCategories'));
    }
    /**
     * Update the specified category.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Category $category)
    {
        $validated = $this->validateRequest($request, $category);

        try {
            DB::beginTransaction();

            $this->fillCategoryData($category, $validated, $request);

            // Update slug if name changed
            if ($category->isDirty('name') && empty($validated['slug'])) {
                $category->slug = $this->generateUniqueSlug($validated['name'], $category->id);
            }

            $category->save();

            DB::commit();

            $message = 'Category updated successfully';

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $category->fresh(['parent', 'children'])
                ]);
            }

            return redirect()->route('admin.categories.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating category: ' . $e->getMessage());

            return $this->handleError($e, 'Failed to update category');
        }
    }

    /**
     * Remove the specified category.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function destroy(Category $category)
    {
        try {
            // Check if category has products
            if ($category->products()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن حذف القسم لأنه يحتوي على منتجات.'
                ], 400);
            }

            // Check if category has children
            if ($category->children()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن حذف القسم لأنه يحتوي على أقسام فرعية.'
                ], 400);
            }

            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف القسم بنجاح.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء الحذف: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Update category order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:categories,id',
            'categories.*.order' => 'required|integer',
            'categories.*.parent_id' => 'nullable|exists:categories,id'
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->categories as $item) {
                Category::where('id', $item['id'])
                    ->update([
                        'order' => $item['order'],
                        'parent_id' => $item['parent_id'] ?? null
                    ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Category order updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating category order: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update category order'
            ], 500);
        }
    }

    /**
     * Get categories tree for dropdowns.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function getTree()
    {
        try {
            // Load categories with their children recursively
            $categories = Category::whereNull('parent_id')
                ->with(['children' => function ($query) {
                    $query->orderBy('order');
                }])
                ->orderBy('order')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في تحميل البيانات: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Validate the request data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category|null  $category
     * @return array
     */
    private function validateRequest(Request $request, ?Category $category = null)
    {
        $categoryId = $category ? $category->id : null;

        return $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $categoryId,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'order' => 'nullable|integer|min:0',
            'status_id' => 'required|integer|exists:statuses,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'sub_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048'
        ]);
    }

    /**
     * Fill category with validated data.
     *
     * @param  \App\Models\Category  $category
     * @param  array  $data
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    private function fillCategoryData(Category $category, array $data, Request $request)
    {
        $category->name = $data['name'];
        $category->slug = $data['slug'] ?? null;
        $category->description = $data['description'] ?? null;
        $category->parent_id = $data['parent_id'] ?? null;
        $category->order = $data['order'] ?? 0;
        $category->status_id = $data['status_id'];

        // Handle image uploads
        if ($request->hasFile('image')) {
            $category->image = $this->uploadImage($request->file('image'), 'categories', $category->image);
        }

        if ($request->hasFile('sub_image')) {
            $category->sub_image = $this->uploadImage($request->file('sub_image'), 'categories/sub', $category->sub_image);
        }
    }

    /**
     * Upload an image.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @param  string  $folder
     * @param  string|null  $oldImage
     * @return string
     */
    // private function uploadImage($file, string $folder, ?string $oldImage = null)
    // {
    //     // Delete old image if exists
    //     if ($oldImage && Storage::disk('public')->exists($oldImage)) {
    //         Storage::disk('public')->delete($oldImage);
    //     }

    //     $path = $file->store($folder, 'public');
    //     return $path;
    // }
private function uploadImage($file,$directory,?string $oldImage = null) {

    // Delete old image
    if ($oldImage && Storage::disk('public')->exists($oldImage)) {
        Storage::disk('public')->delete($oldImage);
    }

    // Generate webp filename
    $filename = Str::uuid() . '.webp';
    $path = $directory . '/' . $filename;

    // Store file
    Storage::disk('public')->put($path, file_get_contents($file));

    // Optimize + convert
    $optimizer = OptimizerChainFactory::create();
    $optimizer->optimize(
        Storage::disk('public')->path($path)
    );

    return $path;
}
    /**
     * Delete category images.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    private function deleteCategoryImages(Category $category)
    {
        if ($category->image && Storage::disk('public')->exists($category->image)) {
            Storage::disk('public')->delete($category->image);
        }

        if ($category->sub_image && Storage::disk('public')->exists($category->sub_image)) {
            Storage::disk('public')->delete($category->sub_image);
        }
    }

    /**
     * Generate unique slug for category.
     *
     * @param  string  $name
     * @param  int|null  $excludeId
     * @return string
     */
    private function generateUniqueSlug(string $name, ?int $excludeId = null)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (Category::where('slug', $slug)
            ->when($excludeId, function ($query) use ($excludeId) {
                $query->where('id', '!=', $excludeId);
            })
            ->exists()
        ) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }

    /**
     * Handle errors and return appropriate response.
     *
     * @param  \Exception  $exception
     * @param  string  $message
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    private function handleError(\Exception $exception, string $message)
    {
        if (request()->wantsJson() || request()->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);
        }

        return back()->with('error', $exception->getMessage())
            ->withInput();
    }

    /**
     * Duplicate a category.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function duplicate(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            // Duplicate category
            $newCategory = $category->replicate();
            $newCategory->name = $request->name;
            $newCategory->slug = $this->generateUniqueSlug($request->name);
            $newCategory->save();

            // Duplicate images if they exist
            if ($category->image) {
                $newImagePath = $this->duplicateImage($category->image, 'categories');
                $newCategory->image = $newImagePath;
            }

            if ($category->sub_image) {
                $newSubImagePath = $this->duplicateImage($category->sub_image, 'categories/sub');
                $newCategory->sub_image = $newSubImagePath;
            }

            $newCategory->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم نسخ القسم بنجاح',
                'data' => $newCategory
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error duplicating category: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'فشل في نسخ القسم'
            ], 500);
        }
    }

    /**
     * Duplicate an image file.
     *
     * @param  string  $imagePath
     * @param  string  $folder
     * @return string
     */
    private function duplicateImage(string $imagePath, string $folder)
    {
        if (!Storage::disk('public')->exists($imagePath)) {
            return null;
        }

        $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
        $newFileName = uniqid() . '.' . $extension;
        $newPath = $folder . '/' . $newFileName;

        Storage::disk('public')->copy($imagePath, $newPath);

        return $newPath;
    }
}
