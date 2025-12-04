<?php

namespace App\Http\Requests\Website;

use Illuminate\Foundation\Http\FormRequest;

class SocialMediaLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'provider'  => 'required|in:google,facebook,apple',
            'provider_id' => 'required|string',
            'email' => 'nullable|email',
            'name' => 'nullable|string|max:255',
        ];
    }
}
