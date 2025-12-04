<?php

namespace App\DataTransferObjects;

final class PhoneLoginData
{
    public string $country_code;
    public string $phone_number;
    public string $full_phone;

    public function __construct(string $country_code, string $phone_number)
    {
        $this->country_code = $country_code;
        $this->phone_number = $phone_number;
        $this->full_phone = (strpos($country_code, '+') === 0 ? $country_code : '+' . $country_code) . $phone_number;
    }

    public static function fromRequest($request): self
    {
        return new self($request->input('country_code', '+966'), $request->input('phone_number'));
    }
}
