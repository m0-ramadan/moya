<?php

namespace App\Http\Requests\Website;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|string|email|max:255|unique:users',
            'password'              => 'nullable|string|min:6|confirmed',
            'google_id'             => 'nullable|string',
            'facebook_id'           => 'nullable|string',
            'apple_id'              => 'nullable|string',
        ];
    }
}
