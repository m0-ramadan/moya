<?php

namespace App\Http\Controllers\Api\Website;

use App\Models\UserAddress;
use Illuminate\Http\Request;
use App\Models\SavedLocation;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Website\UserAddressResource;
use App\Http\Requests\Website\StoreUserAddressRequest;
use App\Http\Requests\Website\UpdateUserAddressRequest;
use App\Http\Resources\WebsiteUser\SavedLocationResource;


class UserAddressController extends Controller
{
    use ApiResponseTrait;

    /**
     * كل عناوين المستخدم
     */
    public function index()
    {
        $addresses = SavedLocation::where('user_id', Auth::id())
            ->latest()
            ->get();

        return $this->success(
            SavedLocationResource::collection($addresses),
            'تم جلب العناوين بنجاح'
        );
    }

    /**
     * عنوان واحد
     */
    public function show($id)
    {
        $address = SavedLocation::where('user_id', Auth::id())
            ->find($id);

        return $address
            ? $this->success(new SavedLocationResource($address), 'تم جلب العنوان بنجاح')
            : $this->error('العنوان غير موجود', 404);
    }

    /**
     * إضافة عنوان
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'nullable|string|max:255',
            'address'    => 'nullable|string',
            'city'       => 'nullable|string|max:255',
            'area'       => 'nullable|string|max:255',
            'latitude'   => 'nullable|numeric',
            'longitude'  => 'nullable|numeric',
            'type'       => 'nullable|string', // home, work
            'is_favorite' => 'boolean',
            'additional_info' => 'nullable|string',
        ]);

        $address = SavedLocation::create([
            'user_id' => Auth::id(),
            ...$validated,
        ]);

        return $this->success(
            new SavedLocationResource($address),
            'تم إضافة العنوان بنجاح',
            201
        );
    }

    /**
     * تحديث عنوان
     */
    public function update(Request $request, $id)
    {
        $address = SavedLocation::where('user_id', Auth::id())
            ->find($id);

        if (!$address) {
            return $this->error('العنوان غير موجود', 404);
        }

        $validated = $request->validate([
            'name'       => 'nullable|string|max:255',
            'address'    => 'nullable|string',
            'city'       => 'nullable|string|max:255',
            'area'       => 'nullable|string|max:255',
            'latitude'   => 'nullable|numeric',
            'longitude'  => 'nullable|numeric',
            'type'       => 'nullable|string',
            'is_favorite' => 'boolean',
            'additional_info' => 'nullable|string',
        ]);

        $address->update($validated);

        return $this->success(
            new SavedLocationResource($address),
            'تم تحديث العنوان بنجاح'
        );
    }

    /**
     * حذف عنوان
     */
    public function destroy($id)
    {
        $address = SavedLocation::find($id);

        if (!$address) {
            return $this->error('العنوان غير موجود', 404);
        }

        $address->delete();

        return $this->success(null, 'تم حذف العنوان بنجاح');
    }
}
