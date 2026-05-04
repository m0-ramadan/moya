<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    /**
     * Display a listing of sliders with filters.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            $sliders = Slider::query()
                ->when($request->filled('search'), function ($query) use ($request) {
                    $query->where('title', 'like', '%' . $request->search . '%');
                })
                ->when($request->filled('type'), function ($query) use ($request) {
                    $query->where('type', $request->type);
                })
                ->when($request->filled('is_active') && $request->is_active != '', function ($query) use ($request) {
                    $query->where('is_active', $request->is_active);
                })
                ->orderBy('type')
                ->orderBy('order')
                ->paginate(15)
                ->appends($request->all());

            return view('Admin.sliders.index', compact('sliders'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ في جلب السلايدرات: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new slider.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('Admin.sliders.create');
    }

    /**
     * Store a newly created slider in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'link' => 'nullable|string|max:255',
            'type' => 'required|in:driver,user',
            'order' => 'nullable|integer|min:1',
            'is_active' => 'required|boolean',
        ], [
            'title.required' => 'حقل العنوان مطلوب.',
            'image.required' => 'يجب رفع صورة للسلايدر.',
            'image.image' => 'الملف المرفوع يجب أن يكون صورة.',
            'image.mimes' => 'الصورة يجب أن تكون من نوع: jpeg, png, jpg, gif, webp.',
            'image.max' => 'حجم الصورة يجب ألا يتجاوز 2 ميجابايت.',
            'type.required' => 'يجب تحديد نوع السلايدر.',
            'type.in' => 'نوع السلايدر يجب أن يكون سائق أو مستخدم.',
        ]);

        DB::beginTransaction();

        
            // رفع الصورة وتخزينها
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('sliders', 'public');
                $validatedData['image'] = $imagePath;
            }

        // تعيين قيمة افتراضية للترتيب إذا لم يتم إرساله
            if (!isset($validatedData['order']) || empty($validatedData['order'])) {
                $validatedData['order'] = Slider::where('type', $validatedData['type'])->max('order') + 1;
            }

            Slider::create($validatedData);

            DB::commit();

            return redirect()
                ->route('admin.sliders.index')
                ->with('success', 'تم إنشاء السلايدر بنجاح.');

        } catch (\Exception $e) {
            DB::rollBack();
            // حذف الصورة المرفوعة في حال فشل العملية
            if (isset($imagePath) && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            return redirect()->route('admin.banners.index')->with('error', 'حدث خطأ أثناء إنشاء السلايدر: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified slider.
     *
     * @param Slider $slider
     * @return \Illuminate\View\View
     */
    public function edit($slider)
    {
        $slider = Slider::findOrFail($slider);
        return view('Admin.sliders.edit', compact('slider'));
    }

    /**
     * Update the specified slider in storage.
     *
     * @param Request $request
     * @param Slider $slider
     * @return \Illuminate\Http\RedirectResponse
     */
public function update(Request $request, $id)
{
    try {
    
    $slider = Slider::findOrFail($id);

    $validatedData = $request->validate([
        'title' => 'required|string|max:255',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        'link' => 'nullable|string|max:255',
        'type' => 'required|in:driver,user',
        'order' => 'nullable|integer|min:1',
        'is_active' => 'required|boolean',
    ]);

    $oldImage = $slider->image;


        // رفع الصورة الجديدة
        if ($request->hasFile('image')) {
            $validatedData['image'] = $request->file('image')->store('sliders', 'public');
        }

        $slider->update($validatedData);

        // حذف القديمة بعد النجاح
        if ($request->hasFile('image') && $oldImage && Storage::disk('public')->exists($oldImage)) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect()
            ->route('admin.sliders.index')
            ->with('success', 'تم تحديث السلايدر بنجاح.');

    } catch (\Exception $e) {

        // حذف الجديدة لو فشل
        if (isset($validatedData['image']) && Storage::disk('public')->exists($validatedData['image'])) {
            Storage::disk('public')->delete($validatedData['image']);
        }

        \Log::error($e);

        return back()
            ->with('error', 'حدث خطأ أثناء تحديث السلايدر.')
            ->withInput();
    }
}
    /**
     * Remove the specified slider from storage.
     *
     * @param Slider $slider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Slider $slider)
    {
        DB::beginTransaction();

        try {
            // حذف الصورة من التخزين
            if ($slider->image && Storage::disk('public')->exists($slider->image)) {
                Storage::disk('public')->delete($slider->image);
            }

            $slider->delete();

            DB::commit();

            return redirect()
                ->route('admin.sliders.index')
                ->with('success', 'تم حذف السلايدر بنجاح.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ أثناء حذف السلايدر: ' . $e->getMessage());
        }
    }

    /**
     * Toggle slider active status via AJAX.
     *
     * @param Slider $slider
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleStatus(Slider $slider)
    {
        try {
            $slider->update(['is_active' => !$slider->is_active]);

            return response()->json([
                'success' => true,
                'is_active' => $slider->is_active,
                'message' => $slider->is_active ? 'تم تفعيل السلايدر بنجاح.' : 'تم إلغاء تفعيل السلايدر بنجاح.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تغيير الحالة.'
            ], 500);
        }
    }

    /**
     * Update sliders order via AJAX.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|integer|exists:sliders,id',
            'orders.*.order' => 'required|integer|min:1',
        ]);

        try {
            foreach ($request->orders as $item) {
                Slider::where('id', $item['id'])->update(['order' => $item['order']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث ترتيب السلايدرات بنجاح.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث الترتيب.'
            ], 500);
        }
    }

    /**
     * Bulk delete sliders via AJAX.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:sliders,id',
        ]);

        DB::beginTransaction();

        try {
            $sliders = Slider::whereIn('id', $request->ids)->get();

            foreach ($sliders as $slider) {
                // حذف الصورة من التخزين
                if ($slider->image && Storage::disk('public')->exists($slider->image)) {
                    Storage::disk('public')->delete($slider->image);
                }
                $slider->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف السلايدرات المحددة بنجاح.',
                'count' => $sliders->count()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف السلايدرات.'
            ], 500);
        }
    }

    /**
     * Bulk toggle status via AJAX.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkToggleStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:sliders,id',
            'is_active' => 'required|boolean',
        ]);

        try {
            Slider::whereIn('id', $request->ids)->update(['is_active' => $request->is_active]);

            $actionText = $request->is_active ? 'تفعيل' : 'إلغاء تفعيل';

            return response()->json([
                'success' => true,
                'message' => "تم {$actionText} السلايدرات المحددة بنجاح.",
                'count' => count($request->ids)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء معالجة الطلب.'
            ], 500);
        }
    }
}