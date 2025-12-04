<?php

namespace App\Http\Requests\Website;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserAddressRequest extends FormRequest
{
    public function authorize()
    {
        return true; // لأن المستخدم Auth
    }

    public function rules()
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'building' => 'nullable|string|max:255',
            'floor' => 'nullable|string|max:255',
            'apartment_number' => 'nullable|string|max:255',
            'address_details' => 'nullable|string',
            'label' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'city' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ];
    }
}
