<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Admin;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
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

class AdminAuthController extends Controller
{
  public function __construct()
  {
    // No middleware applied here to allow access to login and password reset pages
  }

  public function register()
  {
    return $this->adminAuthInterface->register();
  }

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
    return redirect('/admin/login');
  }

  public function showForgotPasswordForm()
  {
    return view('Admin.auth.forgot-password');
  }

  public function showResetPasswordForm($token, Request $request)
  {
    return view('Admin.auth.reset-password', ['token' => $token, 'email' => $request->email]);
  }



  public function sendResetLinkEmail(Request $request)
  {
    $request->validate(['email' => 'required|email|exists:admins,email']);

    $token = Str::random(64);

    DB::table('password_resets')->updateOrInsert(
      ['email' => $request->email],
      [
        'email' => $request->email,
        'token' => $token,
        'created_at' => Carbon::now()
      ]
    );

    $resetLink = url("/admin/reset-password/{$token}?email=" . urlencode($request->email));

    // ابعت ايميل باللينك
    Mail::send('Email.admin_reset_password', ['resetLink' => $resetLink], function ($message) use ($request) {
      $message->to($request->email);
      $message->subject('إعادة تعيين كلمة المرور');
    });

    return back()->with('status', 'تم إرسال رابط إعادة التعيين إلى بريدك الإلكتروني ✅');
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
    $admin = \App\Models\Admin::where('email', $request->email)->first();
    $admin->password = $request->password;
    $admin->save();
    // امسح التوكن بعد الاستخدام
    DB::table('password_resets')->where('email', $request->email)->delete();

    return redirect()->route('admin.login')->with('status', 'تم تغيير كلمة المرور بنجاح ✅');
  }
}
