<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Admin;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\Website\OtpMail;
use App\Models\OtpVerification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use App\Http\Requests\Admin\AdminLoginRequest;
use App\Http\Requests\Admin\Auth\SendOtpRequest;

class AdminAuthController extends Controller
{
  public function __construct()
  {
    // No middleware applied here to allow access to login and password reset pages
  }

  // public function register()
  // {
  //   return $this->adminAuthInterface->register();
  // }

  public function loginPage()
  {
    if (auth('admin')->check()) {
      return redirect('/admin');
    }
    return view('Admin.auth.login');
  }

  public function login(AdminLoginRequest $request)
  {
    $credentials = $request->only('email', 'password');

    if (Auth::guard('admin')->attempt($credentials)) {
      $request->session()->regenerate();
      return redirect()->intended('/admin');
    }

    return back()->withErrors(['email' => 'يرجى إدخال بريد إلكتروني أو كلمة مرور صحيحة']);
  }

  public function logout(Request $request)
  {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('admin.login.page');
  }

  public function showForgotPasswordForm()
  {
    return view('Admin.auth.forgot-password');
  }

  public function showResetPasswordForm($token, Request $request)
  {
    return view('Admin.auth.reset-password', ['token' => $token, 'email' => $request->email]);
  }




  public function sendResetOtp(SendOtpRequest $request)
  {
    try {

      $email = $request->email;


      OtpVerification::where('email', $email)->delete();

      $otp = (string) random_int(100000, 999999);

      OtpVerification::create([
        'email'      => $email,
        'otp'        => Hash::make($otp),
        'expires_at' => Carbon::now()->addMinutes(5),
      ]);

      Mail::to($email)->send(new OtpMail($otp));

      return back()->with('status', 'تم إرسال رمز التحقق (OTP) إلى بريدك الإلكتروني بنجاح ✔');
    } catch (\Exception $e) {
      return back()->withErrors([
        'email' => 'حدث خطأ أثناء إرسال OTP: ' . $e->getMessage()
      ]);
    }
  }


  public function resetPassword(Request $request)
  {

    $request->validate([
      'token' => 'required',
      'email' => 'required|email|exists:admins,email',
      'password' => 'required|min:8|confirmed',
    ]);

    $reset = DB::table('password_resets')
      ->where('email', $request->email)
      ->where('token', $request->token)
      ->first();

    if (!$reset) {
      return back()->withErrors(['email' => 'الرمز غير صالح أو منتهي']);
    }

    // تحديث الباسورد
    $admin = Admin::where('email', $request->email)->first();
    $admin->password = $request->password;
    $admin->save();
    // امسح التوكن بعد الاستخدام
    DB::table('password_resets')->where('email', $request->email)->delete();

    return redirect()->route('admin.login')->with('status', 'تم تغيير كلمة المرور بنجاح ✅');
  }
}
