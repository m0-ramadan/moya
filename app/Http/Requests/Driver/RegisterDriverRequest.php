<?php

namespace App\Http\Requests\Driver;

use Illuminate\Foundation\Http\FormRequest;

class RegisterDriverRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            // المعلومات الشخصية
            'name' => ['required', 'string', 'max:100'],

            'date_of_birth' => ['required', 'date', 'before:' . now()->subYears(18)->format('Y-m-d')],

            'national_id' => ['required', 'string', 'max:20'],
            // الجنسية
            'citizenship' => ['required', 'in:saudi,resident'],
            'country_id' => ['required_if:citizenship,resident', 'exists:countries,id'],

            // الهوية
            'id_number' => ['required', 'string', 'max:20'],
            'id_image' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'], // 5MB
            'id_image_back' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],

            // رخصة القيادة
            'license_number' => ['required', 'string', 'max:50'],
            'issue_date' => ['required', 'date'],
            'expiry_date' => ['required', 'date', 'after:today'],
            'license_image' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'license_image_back' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],

            // الصورة الشخصية
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],

            // معلومات المركبة
            'vehicle_size' => ['required'],
            'is_vehicle_owner' => ['required'],
            'vehicle_plate_number' => ['required', 'string', 'max:20'],
            'vehicle_registration_number' => ['nullable', 'string', 'max:50'],
            'vehicle_registration_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            // 'vehicle_model' => ['nullable', 'string', 'max:100'],
            // 'vehicle_year' => ['nullable', 'integer', 'min:2000', 'max:' . date('Y')],
            // 'vehicle_color' => ['nullable', 'string', 'max:50'],
            // 'vehicle_type' => ['nullable', 'in:truck,van,pickup'],

            // معلومات إضافية
            'blood_type' => ['nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'driving_experience_years' => ['nullable', 'integer', 'min:1', 'max:50'],
            'is_smoker' => ['nullable', 'boolean'],
            'has_helper' => ['nullable', 'boolean'],
            'helper_count' => ['nullable', 'integer', 'min:0', 'max:5'],

            // معلومات الاتصال الطارئ
            'emergency_contact_name' => ['nullable', 'string', 'max:100'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],

            // تفضيلات العمل
            'preferred_working_hours' => ['nullable', 'string'],
            'max_daily_orders' => ['nullable', 'integer', 'min:1', 'max:20'],
            'radius_km' => ['nullable', 'integer', 'min:5', 'max:100'],

            // معلومات البنك (اختياري)
            'bank_name' => ['nullable', 'string', 'max:100'],
            'iban_number' => ['nullable', 'string', 'max:34'],
        ];
    }

    public function messages()
    {
        return [
            'date_of_birth.before' => 'يجب أن يكون عمر السائق 18 سنة على الأقل',
            'expiry_date.after' => 'تاريخ انتهاء رخصة القيادة يجب أن يكون في المستقبل',
            'country_id.required_if' => 'يرجى اختيار الدولة عند اختيار مقيم',
            'id_image.required' => 'صورة الهوية الأمامية مطلوبة',
            'id_image_back.required' => 'صورة الهوية الخلفية مطلوبة',
            'license_image.required' => 'صورة رخصة القيادة الأمامية مطلوبة',
            'license_image_back.required' => 'صورة رخصة القيادة الخلفية مطلوبة',
            'vehicle_registration_image.required' => 'صورة رخصة السيارة مطلوبة',
            'photo.required' => 'الصورة الشخصية مطلوبة',
        ];
    }
}
