<?php


namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class PhoneLoginRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'country_code' => ['required', 'string'],
            'phone_number' => ['required', 'string'],
        ];
    }

    // Helper to get normalized full phone
    public function getFullPhone(): string
    {
        $cc = $this->input('country_code', '+966');
        $pn = $this->input('phone_number');
        // ensure leading +
        if (strpos($cc, '+') !== 0) $cc = '+' . $cc;
        return $cc . $pn;
    }
}
