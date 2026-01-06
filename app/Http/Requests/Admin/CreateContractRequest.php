<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateContractRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'contract_type' => 'required|in:individual,company',
            'company_name' => 'required_if:contract_type,company',
            'applicant_name' => 'required|string|max:255',
            'duration_type' => 'required|in:monthly,quarterly,semi_annual,annual',
            'total_orders_limit' => 'required|integer|min:1',
            'total_amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'delivery_locations' => 'required|array|min:1',
            'delivery_locations.*.saved_location_id' => 'required|exists:saved_locations,id',
            'delivery_locations.*.priority' => 'integer|min:1',
            'notes' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
        ];
    }

    public function messages()
    {
        return [
            'company_name.required_if' => 'اسم الشركة مطلوب عند اختيار نوع العقد كمؤسسة',
            'delivery_locations.required' => 'يجب تحديد موقع توصيل واحد على الأقل',
            'delivery_locations.*.saved_location_id.exists' => 'الموقع المحدد غير موجود',
        ];
    }
}
