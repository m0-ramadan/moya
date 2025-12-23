<?php
// app/Http/Controllers/Api/AuthController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PhoneLoginRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\DataTransferObjects\PhoneLoginData;
use App\Services\AuthService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use App\Models\User;

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

            $res = $this->authService->sendOtp($dto);
            return $this->successResponse([
                'phone' => $res['phone'],
                'method' => $res['method']
            ], 'OTP sent successfully');
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
            ], 'OTP verified successfully');
        } catch (\Exception $e) {
            return $this->validationError(['otp' => [$e->getMessage()]]);
        }
    }

    // Complete profile
    public function completeProfile(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'unique:users,email']
        ]);

        $user = $request->user();
        $user->update($request->only(['name', 'email']));

        return $this->successResponse([
            'user' => $user->only(['id', 'name', 'email', 'full_phone'])
        ], 'Profile completed successfully');
    }

    // Logout (current token)
    public function logout(Request $request)
    {
        //  $request->user()->currentAccessToken()?->delete();
        return $this->successResponse(null, 'Logged out successfully');
    }

    // Current user
    public function user(Request $request)
    {
        return $this->successResponse(['user' => $request->user()]);
    }

    // Resend OTP
    public function resendOtp(Request $request)
    {
        $request->validate(['phone_number' => ['required', 'string']]);
        $fullPhone = $request->input('phone_number');
        if (!$this->authService->canResend($fullPhone)) {
            return $this->errorResponse('Too many OTP requests. Please try again later.', 429);
        }

        // reuse sendOtp logic: create a fake Request DTO from number
        // parse country code if needed - here we assume fullPhone includes code
        // We'll call Twilio fallback path by generating OTP locally
        try {
            $parts = $this->splitFullPhone($fullPhone);
            $dto = new PhoneLoginData($parts['country_code'], $parts['phone_number']);
            $res = $this->authService->sendOtp($dto);
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