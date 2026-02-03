<?php

namespace App\Http\Controllers\Api;

use App\Models\StaticPage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AppUser\StaticPageResource;
use App\Models\Faq;
use App\Traits\ApiResponseTrait;

class StaticPagesController extends Controller
{
    use ApiResponseTrait;
    public function index($slug)
    {
        $page = StaticPage::where('slug', $slug)->first();
        return $this->success(new StaticPageResource($page), 'تم جلب بيانات الصفحة بنجاح');
    }

    public function getFaq()
    {
        $faqs = Faq::where('status', 'active')->orderBy('created_at', 'desc')->get();
        return $this->success($faqs, 'تم جلب البيانات بنجاح');
    }
}
