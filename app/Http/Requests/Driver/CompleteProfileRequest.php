<?php

namespace App\Http\Requests\Driver;

use Illuminate\Foundation\Http\FormRequest;

class CompleteProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            // معلومات قابلة للتحديث
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'id_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'id_image_back' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'license_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'license_image_back' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'vehicle_registration_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],

            // معلومات المركبة
            'vehicle_plate_number' => ['nullable', 'string', 'max:20'],
            'vehicle_model' => ['nullable', 'string', 'max:100'],
            'vehicle_year' => ['nullable', 'integer', 'min:2000', 'max:' . date('Y')],
            'vehicle_color' => ['nullable', 'string', 'max:50'],
            'vehicle_type' => ['nullable', 'in:truck,van,pickup'],
            'capacity_liters' => ['nullable', 'integer', 'min:1000', 'max:50000'],

            // معلومات الاتصال الطارئ
            'emergency_contact_name' => ['nullable', 'string', 'max:100'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],

            // تفضيلات العمل
            'preferred_working_hours' => ['nullable', 'string'],
            'max_daily_orders' => ['nullable', 'integer', 'min:1', 'max:20'],
            'radius_km' => ['nullable', 'integer', 'min:5', 'max:100'],

            // معلومات البنك
            'bank_name' => ['nullable', 'string', 'max:100'],
            'iban_number' => ['nullable', 'string', 'max:34'],
        ];
    }
}
