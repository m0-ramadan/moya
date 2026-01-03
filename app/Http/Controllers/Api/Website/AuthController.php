<?php

namespace App\Http\Controllers\Api\Website;

use App\Models\User;
use App\Models\DeviceToken;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Website\LoginRequest;
use App\Http\Requests\Website\RegisterRequest;
use App\Http\Requests\Website\SocialMediaLoginRequest;

class AuthController extends Controller
{
    use ApiResponseTrait;

    /**
     * 🔹 تسجيل مستخدم جديد
     */
    public function register(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'name'        => $request->name,
                'email'       => $request->email,
                'password'    => Hash::make($request->password),
                'google_id'   => $request->google_id,
                'facebook_id' => $request->facebook_id,
                'apple_id'    => $request->apple_id,
            ]);

            $token = $user->createToken('api_token')->plainTextToken;

            return $this->success([
                'user'  => $user,
                'token' => $token,
            ], 'تم إنشاء الحساب بنجاح');
        } catch (\Exception $e) {
            return $this->error('حدث خطأ أثناء إنشاء الحساب', 500, ['exception' => $e->getMessage()]);
        }
    }

    /**
     * 🔹 تسجيل الدخول
     */
    public function login(LoginRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return $this->error('بيانات الدخول غير صحيحة', 401);
            }

            if ($request->has('device_token')) {
                DeviceToken::whereNull('user_id')
                    ->where('token', $request->device_token)
                    ->update([
                        'user_id' => auth()->id()
                    ]);
            }

            $token = $user->createToken('api_token')->plainTextToken;

            return $this->success([
                'user'  => $user,
                'token' => $token,
            ], 'تم تسجيل الدخول بنجاح');
        } catch (\Exception $e) {
            return $this->error('حدث خطأ أثناء تسجيل الدخول', 500, ['exception' => $e->getMessage()]);
        }
    }

    /**
     * 🔹 تسجيل الدخول أو إنشاء حساب عبر Google / Facebook / Apple
     */
    public function socialLogin(SocialMediaLoginRequest $request)
    {

        try {
            $column = "{$request->provider}_id";

            $user = User::where($column, $request->provider_id)
                ->orWhere('email', $request->email)
                ->first();

            if (!$user) {
                $user = User::create([
                    'name'        => $request->name ?? 'User',
                    'email'       => $request->email,
                    $column       => $request->provider_id,
                    'password'    => Hash::make(uniqid()), // كلمة مرور عشوائية
                ]);
            }

            $token = $user->createToken('api_token')->plainTextToken;

            return $this->success([
                'user'  => $user,
                'token' => $token,
            ], 'تم تسجيل الدخول بنجاح عبر ' . ucfirst($request->provider));
        } catch (\Exception $e) {
            return $this->error('حدث خطأ أثناء تسجيل الدخول الاجتماعي', 500, ['exception' => $e->getMessage()]);
        }
    }

    /**
     * 🔹 تسجيل الخروج
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return $this->success(null, 'تم تسجيل الخروج بنجاح');
    }

    /**
     * 🔹 عرض بيانات المستخدم الحالي
     */
    public function profile(Request $request)
    {
        return $this->success($request->user(), 'تم جلب بيانات المستخدم');
    }
}
