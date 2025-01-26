<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login'); // تأكد من وجود هذا الملف في مجلد view
    }

    /**
     * Handle a login request.
     */
    public function login(Request $request)
    {


        // Validate the request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);


        if (Auth::guard('web')->attempt($request->only('email', 'password'))) {

            return redirect()->route('home')->with('success', 'تم تسجيل الدخول بنجاح.');
        }


        return redirect('/login')->with('error', 'بيانات الاعتماد غير صحيحة.');
    }


    public function loginWithJWT(Request $request)
    {
        // تحقق من بيانات الاعتماد باستخدام الحارس 'admins' (JWT)
        if (Auth::guard('admins')->attempt($request->only('email', 'password'))) {
            $admin = Auth::guard('admins')->user();
            $token = Auth::guard('admins')->login($admin); // إنشاء التوكن
            return response()->json(['token' => $token]);
        }

        return response()->json(['error' => 'بيانات الاعتماد غير صحيحة.'], 401);
    }


    /**
     * Logout the user.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Redirect to login page after logout
        return redirect('/login')->with('success', 'تم تسجيل الخروج بنجاح.');
    }
}
