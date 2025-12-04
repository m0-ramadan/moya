<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PhoneLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone_number' => ['required', 'string', 'regex:/^[0-9]{9,15}$/'],
            'country_code' => ['sometimes', 'string', 'regex:/^\+\d{1,4}$/']
        ];
    }

    public function messages(): array
    {
        return [
            'phone_number.required' => 'Phone number is required',
            'phone_number.regex' => 'Invalid phone number format',
            'country_code.regex' => 'Invalid country code format'
        ];
    }

    public function getFullPhone(): string
    {
        $countryCode = $this->input('country_code', '+966');
        $phoneNumber = $this->input('phone_number');
        
        // Remove leading zeros from phone number
        $phoneNumber = ltrim($phoneNumber, '0');
        
        return $countryCode . $phoneNumber;
    }
}