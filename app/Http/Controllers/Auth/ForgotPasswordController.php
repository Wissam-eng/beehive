<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\admins;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;


    public function email()
    {
        return view('auth.passwords.email');
    }


    public function sendOtp(Request $request)
    {
        // dd('sendOtp');

        $user = admins::where('email', $request->email)->first();

        if (!$user) {
            return view('auth.passwords.email')->with('error', 'البريد الالكتروني غير صحيح');
        }

        $otp = rand(1000, 9999);
        $data['otp'] = $otp;

        session()->put('otp', $otp);
        session()->put('user_id', $user->id);



        Mail::to($request->email)->send(new \App\Mail\hellomail($otp));
        // return redirect()->route('otp')->with('email', $request->email);
        return view('auth.passwords.otp')->with('success', 'تم ارسال الكود بنجاح');
    }


    public function receiveOtp(Request $request)
    {
        // dd('receiveOtp');

        $otp_user = $request->otp;
        $otp = session()->get('otp');
        if ($otp_user == $otp) {
            return view('auth.passwords.reset');
        }
        dd(($otp_user));
        return view('auth.passwords.otp')->with('error', 'الكود غير صحيح');
    }


    public function resetpassword(Request $request)
    {

        // dd($request->all());

        $user_id = session()->get('user_id');
        $user = admins::find($user_id);
        $validator = validator($request->all(), [
            'password' => ['required', 'string', 'min:1', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user->password = Hash::make($request->password);
        $user->save();
        return redirect()->route('login')->with('success', 'تم تغيير كلمة المرور بنجاح');
    }
}
