<?php

namespace App\Http\Controllers\Api\Website;

use App\Models\Page;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\Website\PageResource;

class PageController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get page by key (home / drivers)
     */
    public function show(string $key)
    {
        $page = Page::where('key', $key)
            ->where('is_active', true)
            ->with([
                'sections' => function ($q) {
                    $q->where('is_active', true)->orderBy('order')->with([
                        'contents' => function ($q) {
                            $q->where('is_active', true)->orderBy('order');
                        }
                    ]);
                }
            ])
            ->first();

        if (!$page) {
            return $this->error('الصفحة غير موجودة', 404);
        }

        return $this->success(
            new PageResource($page),
            'تم جلب بيانات الصفحة بنجاح'
        );
    }
}
