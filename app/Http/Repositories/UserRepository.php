<?php

namespace App\Http\Repositories;

use App\Models\User;

class UserRepository
{
    public function findByFullPhone(string $fullPhone): ?User
    {
        return User::where('full_phone', $fullPhone)->first();
    }

    public function createByPhone(string $countryCode, string $phoneNumber, ?string $name = null): User
    {
        $full = (strpos($countryCode, '+') === 0 ? $countryCode : '+' . $countryCode) . $phoneNumber;

        // أول create بدون الاسم النهائي
        $user = User::create([
            'phone_number' => $phoneNumber,
            'country_code' => $countryCode,
            'full_phone' => $full,
            'name' => $name ?? 'temp',
        ]);

        if (!$name) {
            $user->name = "user" . $user->id;
            $user->save();
        }

        return $user;
    }


    public function save(User $user): User
    {
        $user->save();
        return $user;
    }
}
