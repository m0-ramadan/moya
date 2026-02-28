<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    use ApiResponseTrait;
    public function available()
    {
        $supports = User::limit(3)->get();
        // أو
        // $supports = Support::where('status', 'available')->get();

        return $this->success($supports);
    }
    public function getApps()
    {
        $apps = [
            [
                'name' => 'driver_google_play',
                'url' => 'https://play.google.com/store/apps/details?id=com.moya.delivery',
            ],
            [
                'name' => 'driver_apple_store',
                'url' => 'https://play.google.com/store/apps/details?id=com.app2',
            ],
            [
                'name' => 'user_google_play',
                'url' => 'https://play.google.com/store/apps/details?id=com.moya.user',
            ],
            [
                'name' => 'user_apple_store',
                'url' => 'https://play.google.com/store/apps/details?id=com.app2',
            ],
        ];

        return $this->success($apps);
    }
}
