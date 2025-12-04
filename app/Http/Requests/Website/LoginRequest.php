<?php

namespace App\Http\Requests\Website;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'email'    => 'required|email',
            'password' => 'nullable|min:6',
        ];
    }
}
