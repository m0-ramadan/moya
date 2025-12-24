<?php
// app/Http/Controllers/Api/AuthController.php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\DataTransferObjects\PhoneLoginData;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Resources\AppUser\UserResource;
use App\Http\Requests\Auth\PhoneLoginRequest;

class AuthController extends Controller
{
    use ApiResponseTrait;

    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    // Send OTP
    public function sendOtp(PhoneLoginRequest $request)
    {
        try {
            $dto = PhoneLoginData::fromRequest($request);

            $res = $this->authService->sendOtp($dto, $request);
            return $this->successResponse([
                'phone' => $res['phone'],
                'method' => $res['method'],
                'otp' => $res['otp'],
            ], 'تم إرسال رمز التحقق بنجاح');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    // Verify
    public function verifyOtp(VerifyOtpRequest $request)
    {
        try {
            $fullPhone = $request->input('phone_number'); // expects full (with country code) per request rules
            $otp = $request->input('otp');

            $res = $this->authService->verifyOtp($fullPhone, $otp);

            return $this->successResponse([
                'user' => [
                    'id' => $res['user']->id,
                    'phone' => $res['user']->full_phone,
                    'name' => $res['user']->name,
                    'is_verified' => true,
                ],
                'token' => $res['token'],
                'token_type' => 'Bearer'
            ], 'تم التحقق من رمز OTP بنجاح');
        } catch (\Exception $e) {
            return $this->validationError(['otp' => [$e->getMessage()]]);
        }
    }

    // Complete profile
    public function completeProfile(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'], // 2MB max
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        $user = $request->user();

        $data = $request->only(['name', 'email']);

        // لو فيه صورة
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $avatarPath;
        }

        $user->update($data);

        return $this->successResponse([
            'user' => new UserResource($user)
        ], 'تم إكمال الملف الشخصي بنجاح');
    }


    // Logout (current token)
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();
        return $this->successResponse(null, 'تم تسجيل الخروج بنجاح');
    }

    // Current user
    public function user(Request $request)
    {
        return $this->successResponse(new UserResource($request->user()));
    }

    // Resend OTP
    public function resendOtp(Request $request)
    {
        $request->validate(['phone_number' => ['required', 'string']]);
        $fullPhone = $request->input('phone_number');
        if (!$this->authService->canResend($fullPhone)) {
            return $this->errorResponse('طلبات رمز التحقق كثيرة جدًا. الرجاء المحاولة لاحقًا .', 429);
        }

        // reuse sendOtp logic: create a fake Request DTO from number
        // parse country code if needed - here we assume fullPhone includes code
        // We'll call Twilio fallback path by generating OTP locally
        try {
            $parts = $this->splitFullPhone($fullPhone);
            $dto = new PhoneLoginData($parts['country_code'], $parts['phone_number']);
            $res = $this->authService->sendOtp($dto, $request);
            return $this->successResponse(['phone' => $res['phone'], 'method' => $res['method']], 'OTP resent');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    private function splitFullPhone(string $full): array
    {
        // very naive split: +country rest
        if (strpos($full, '+') === 0) {
            // try +CCC + rest
            // For simplicity assume country code is up to 4 chars after +
            $cc = substr($full, 0, 4);
            $rest = ltrim(substr($full, 4), '+');
            // fallback: if rest too short, take 3 digits cc
            if (strlen($rest) < 6) {
                $cc = substr($full, 0, 3);
                $rest = ltrim(substr($full, 3), '+');
            }
            return ['country_code' => $cc, 'phone_number' => $rest];
        }
        return ['country_code' => '+966', 'phone_number' => $full];
    }
}
