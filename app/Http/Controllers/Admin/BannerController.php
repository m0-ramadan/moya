<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\BannerType;
use App\Models\Category;
use App\Models\Product;
use App\Models\PromoCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Services\BannerService;

class BannerController extends Controller
{
    protected $bannerService;

    public function __construct(BannerService $bannerService)
    {
        $this->bannerService = $bannerService;
    }

    /**
     * Display a listing of banners with filters
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            $banners = $this->bannerService->getFilteredBanners($request);
            $bannerTypes = BannerType::all();

            return view('Admin.banners.index', compact('banners', 'bannerTypes'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ في جلب البنرات: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new banner
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        try {
            $data = $this->prepareCreateData();
            
            return view('Admin.banners.create', $data);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ في تحضير النموذج: ' . $e->getMessage());
        }
    }
    /**
     * Store a newly created banner in storage
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validatedData = $this->validateBannerRequest($request);
            
            $banner = Banner::create($validatedData);
            
            $this->handleBannerSettings($banner, $request);
            
            DB::commit();

            return redirect()
                ->route('admin.banners.edit', $banner)
                ->with('success', 'تم إنشاء البانر بنجاح. يمكنك الآن إضافة العناصر.');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ أثناء إنشاء البانر: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified banner
     *
     * @param Banner $banner
     * @return \Illuminate\View\View
     */
    public function show(Banner $banner)
    {
        try {
            $banner->load(['type', 'items', 'gridLayout', 'sliderSetting']);
            
            return view('Admin.banners.show', compact('banner'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ في عرض البانر: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified banner
     *
     * @param Banner $banner
     * @return \Illuminate\View\View
     */
    public function edit(Banner $banner)
    {
        try {
            $banner->load(['items', 'gridLayout', 'sliderSetting']);
            $data = $this->prepareEditData($banner);
            
            return view('Admin.banners.edit', array_merge($data, compact('banner')));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ في تحضير نموذج التعديل: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified banner in storage
     *
     * @param Request $request
     * @param Banner $banner
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Banner $banner)
    {
        DB::beginTransaction();

        try {
            $validatedData = $this->validateBannerRequest($request);
            
            $banner->update($validatedData);
            
            $this->handleBannerSettings($banner, $request);
            
            DB::commit();

            return redirect()
                ->route('admin.banners.index')
                ->with('success', 'تم تحديث البانر بنجاح.');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ أثناء تحديث البانر: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified banner from storage
     *
     * @param Banner $banner
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Banner $banner)
    {
        DB::beginTransaction();

        try {
            $this->deleteBannerAssets($banner);
            $banner->delete();
            
            DB::commit();

            return redirect()
                ->route('admin.banners.index')
                ->with('success', 'تم حذف البانر بنجاح.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ أثناء حذف البانر: ' . $e->getMessage());
        }
    }

    /**
     * Toggle banner active status
     *
     * @param Banner $banner
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleStatus(Banner $banner)
    {
        try {
            $banner->update(['is_active' => !$banner->is_active]);

            return response()->json([
                'success' => true,
                'is_active' => $banner->is_active,
                'message' => 'تم تغيير حالة البانر بنجاح.'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تغيير الحالة.'
            ], 500);
        }
    }

    /**
     * Update banners order
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOrder(Request $request)
    {
        try {
            $this->validate($request, [
                'banners' => 'required|array',
                'banners.*.id' => 'required|exists:banners,id',
                'banners.*.order' => 'required|integer'
            ]);

            foreach ($request->banners as $item) {
                Banner::where('id', $item['id'])->update(['section_order' => $item['order']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الترتيب بنجاح.'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث الترتيب.'
            ], 500);
        }
    }

    /**
     * Prepare data for create form
     *
     * @return array
     */
    private function prepareCreateData()
    {
        return [
            'bannerTypes' => BannerType::all(),
            'categories' => Category::where('status_id',1)->get(),
            'products' => Product::where('status_id',1)->limit(100)->get(),
            'promoCodes' => PromoCode::activeAndValid()->get(),
        ];
    }

    /**
     * Prepare data for edit form
     *
     * @param Banner $banner
     * @return array
     */
    private function prepareEditData(Banner $banner)
    {
        return [
            'bannerTypes' => BannerType::all(),
            'categories' => Category::where('status_id',1)->get(),
            'products' => Product::where('status_id',1)->limit(100)->get(),
            'promoCodes' => PromoCode::activeAndValid()->get(),
        ];
    }

    /**
     * Validate banner request data
     *
     * @param Request $request
     * @return array
     */
    private function validateBannerRequest(Request $request)
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'banner_type_id' => 'required|exists:banner_types,id',
            'section_order' => 'required|integer|min:1',
            'is_active' => 'required|boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'category_id' => 'nullable|exists:categories,id',
        ]);
    }

    /**
     * Handle banner type-specific settings
     *
     * @param Banner $banner
     * @param Request $request
     * @return void
     */
    private function handleBannerSettings(Banner $banner, Request $request)
    {
        switch ($banner->banner_type_id) {
            case 1: // Slider
                $this->handleSliderSettings($banner, $request);
                break;
            case 2: // Grid
                $this->handleGridLayout($banner, $request);
                break;
        }
    }

    /**
     * Handle slider settings
     *
     * @param Banner $banner
     * @param Request $request
     * @return void
     */
    private function handleSliderSettings(Banner $banner, Request $request)
    {
        $settings = $request->only([
            // 'autoplay',
             'autoplay_speed', 
            'arrows', 'dots', 'infinite'
        ]);

        if ($this->hasSliderSettings($settings)) {
            $sliderSetting = $banner->sliderSetting()->firstOrNew();
            $sliderSetting->fill($settings);
            $sliderSetting->save();
        }
    }

    /**
     * Handle grid layout
     *
     * @param Banner $banner
     * @param Request $request
     * @return void
     */
    private function handleGridLayout(Banner $banner, Request $request)
    {
        $layoutData = $request->only([
            'grid_type', 'desktop_columns', 'tablet_columns',
            'mobile_columns', 'row_gap', 'column_gap'
        ]);

        if ($this->hasGridSettings($layoutData)) {
            $gridLayout = $banner->gridLayout()->firstOrNew();
            $gridLayout->fill($layoutData);
            $gridLayout->save();
        }
    }

    /**
     * Check if slider settings exist
     *
     * @param array $settings
     * @return bool
     */
    private function hasSliderSettings(array $settings)
    {
        return !empty(array_filter($settings, function ($value) {
            return !is_null($value);
        }));
    }

    /**
     * Check if grid settings exist
     *
     * @param array $settings
     * @return bool
     */
    private function hasGridSettings(array $settings)
    {
        return !empty(array_filter($settings, function ($value) {
            return !is_null($value);
        }));
    }

    /**
     * Delete all banner related assets
     *
     * @param Banner $banner
     * @return void
     */
    private function deleteBannerAssets(Banner $banner)
    {
        $banner->load('items');
        
        foreach ($banner->items as $item) {
            $this->deleteItemImages($item);
        }
    }

    /**
     * Delete item images from storage
     *
     * @param mixed $item
     * @return void
     */
    private function deleteItemImages($item)
    {
        $images = [
            $item->image_url,
            $item->mobile_image_url,
        ];

        foreach ($images as $image) {
            if ($image && Storage::exists($image)) {
                Storage::delete($image);
            }
        }
    }

    /**
     * Export banners to CSV
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function export(Request $request)
    {
        try {
            $banners = $this->bannerService->getFilteredBanners($request, false);
            
            $fileName = 'banners-' . now()->format('Y-m-d-H-i-s') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$fileName\"",
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ];

            $callback = function () use ($banners) {
                $file = fopen('php://output', 'w');
                fputcsv($file, [
                    'ID', 'العنوان', 'النوع', 'الترتيب', 
                    'الحالة', 'تاريخ البدء', 'تاريخ الانتهاء'
                ]);

                foreach ($banners as $banner) {
                    fputcsv($file, [
                        $banner->id,
                        $banner->title,
                        $banner->type->name,
                        $banner->section_order,
                        $banner->is_active ? 'نشط' : 'غير نشط',
                        $banner->start_date ? $banner->start_date->format('Y-m-d') : '',
                        $banner->end_date ? $banner->end_date->format('Y-m-d') : '',
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء التصدير: ' . $e->getMessage());
        }
    }

    /**
     * Get banner statistics
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistics()
    {
        try {
            $totalBanners = Banner::count();
            $activeBanners = Banner::where('is_active', true)->count();
            $expiredBanners = Banner::where('end_date', '<', now())->count();
            $upcomingBanners = Banner::where('start_date', '>', now())->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total' => $totalBanners,
                    'active' => $activeBanners,
                    'expired' => $expiredBanners,
                    'upcoming' => $upcomingBanners,
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في جلب الإحصائيات.'
            ], 500);
        }
    }

    /**
     * Bulk actions on banners
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkActions(Request $request)
    {
        try {
            $this->validate($request, [
                'action' => 'required|in:activate,deactivate,delete',
                'ids' => 'required|array',
                'ids.*' => 'exists:banners,id'
            ]);

            $banners = Banner::whereIn('id', $request->ids);

            switch ($request->action) {
                case 'activate':
                    $banners->update(['is_active' => true]);
                    $message = 'تم تفعيل البنرات المحددة.';
                    break;
                    
                case 'deactivate':
                    $banners->update(['is_active' => false]);
                    $message = 'تم إلغاء تفعيل البنرات المحددة.';
                    break;
                    
                case 'delete':
                    $banners->each(function ($banner) {
                        $this->deleteBannerAssets($banner);
                    });
                    $banners->delete();
                    $message = 'تم حذف البنرات المحددة.';
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'count' => $banners->count()
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صالحة.',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء المعالجة.'
            ], 500);
        }
    }
}