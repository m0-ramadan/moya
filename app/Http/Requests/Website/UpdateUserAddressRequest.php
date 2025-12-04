<?php

namespace App\Http\Requests\Website;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserAddressRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'building' => 'nullable|string|max:255',
            'floor' => 'nullable|string|max:255',
            'apartment_number' => 'nullable|string|max:255',
            'address_details' => 'nullable|string',
            'label' => 'nullable|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'city' => 'sometimes|string|max:255',
            'area' => 'sometimes|string|max:255',
            'type' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ];
    }
}
