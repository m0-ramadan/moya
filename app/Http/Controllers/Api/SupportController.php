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
}
