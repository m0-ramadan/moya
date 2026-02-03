<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendNotificationRequest extends FormRequest
{
    public function authorize()
    {
        // يمكنك تعديل الصلاحيات هنا
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'nullable|exists:users,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'nullable|string|in:info,warning,success,error,system',
            'data' => 'nullable|array',
            'send_to_firebase' => 'nullable|boolean',
            'send_as_broadcast' => 'nullable|boolean', // إذا أردت إرسال لجميع أجهزة المستخدم
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => 'معرف المستخدم مطلوب',
            'user_id.exists' => 'المستخدم غير موجود',
            'title.required' => 'عنوان الإشعار مطلوب',
            'title.max' => 'العنوان يجب ألا يتجاوز 255 حرفاً',
            'message.required' => 'نص الإشعار مطلوب',
        ];
    }
}
