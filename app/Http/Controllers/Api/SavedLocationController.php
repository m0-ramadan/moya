<?php

namespace App\Http\Controllers\Api;

use App\Models\SavedLocation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;

class SavedLocationController extends Controller
{
    use ApiResponseTrait;

    /**
     * حفظ عنوان جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'address'    => 'required|string',
            'city'       => 'nullable|string',
            'area'       => 'nullable|string',
            'latitude'   => 'required|numeric',
            'longitude'  => 'required|numeric',
            'type'       => 'nullable|string', // home / work
            'is_favorite' => 'boolean',
            'additional_info' => 'nullable|string',
        ]);

        $location = SavedLocation::create([
            'user_id' => auth()->id(),
            ...$validated,
        ]);

        return $this->successResponse(
            $location,
            'تم حفظ العنوان بنجاح',
            201
        );
    }
}
