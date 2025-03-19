<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\admins;
use App\Models\clients;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Mail\OTPMail;


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
        $otp = rand(1000, 9999);


        $user = admins::where('email', $request->email)->first();

        if (!$user) {

            $user = clients::where('email', $request->email)->first();
            $user->update(['otp' => $otp]);
            if (!$user) {

                return view('auth.passwords.email')->with('error', 'البريد الالكتروني غير صحيح');
            }
        }


        $data['otp'] = $otp;

        session()->put('otp', $otp);
        session()->put('user_id', $user->id);

        Mail::to($request->email)->send(new OTPMail($otp, 'test'));
        return view('auth.passwords.otp')->with('success', 'تم ارسال الكود بنجاح');
    }


    public function receiveOtp(Request $request)
    {

        $otp_user = $request->otp;
        $otp = session()->get('otp');
        if ($otp_user == $otp) {
            return view('auth.passwords.reset');
        }

        return view('auth.passwords.otp')->with('error', 'الكود غير صحيح');
    }


    public function resetpassword(Request $request)
    {
        $user_id = session()->get('user_id');

        $user = admins::find($user_id);

        if (!$user) {
            $user = clients::find($user_id);
        }


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

    public function verfiy_email(Request $request)
    {

        $otp_user = $request->otp;
        $user = clients::where('otp', $otp_user)->first();

        $otp = $user->otp;

        if ($otp_user == $otp) {
            $user->update(['email_verified_at' => now()]);

            return view('auth.verifed');
        }

        return view('auth.passwords.otp')->with('error', 'الكود غير صحيح');
    }


    public function getotp()
    {
        return view('auth.verify');
    }


    //---------------------------API RESET PASSWORD & VERIFY EMAIL----------------------------------------

    public function sendOtp_api(Request $request)
    {
        $otp = rand(1000, 9999);

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'حدث خطاء اثناء التسجيل: ' . $validator->errors(),
                'status' => false
            ]);
        }



        $user = clients::where('email', $request->email)->first();


        if (!$user) {

            return response()->json([
                'message' => 'البريد الالكتروني غير صحيح',
                'status' => false
            ]);
        }

        $user->update(['otp' => $otp]);


        $data['otp'] = $otp;

        session()->put('otp', $otp);
        session()->put('user_id', $user->id);

        Mail::to($request->email)->send(new OTPMail($otp, 'test'));
        return response()->json([
            'message' => 'تم ارسال الكود بنجاح',
            'status' => true
        ]);
    }


    public function receiveOtp_api(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'otp' => 'required|digits:4',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'حدث خطاء اثناء التسجيل: ' . $validator->errors(),
                'status' => false
            ]);
        }

        $otp_user = $request->otp;

        $user = clients::where('otp', $otp_user)->first();

        if ($user) {

            $otp = rand(1000, 9999);

            $user->update(['otp' => $otp]);

            return response()->json([
                'message' => 'تم التحقق من الكود بنجاح',
                'otp' => $otp,
                'status' => true
            ]);
        }

        return response()->json([
            'message' => 'الكود غير صحيح',
            'status' => false
        ]);
    }


    public function resetpassword_api(Request $request)
    {



        $validator = validator($request->all(), [
            'password' => ['required', 'string', 'min:1', 'confirmed'],
            'otp' => 'required|digits:4',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'حدث خطاء اثناء التسجيل: ' . $validator->errors(),
                'status' => false
            ]);
        }


        $user = clients::where('otp', $request->otp)->first();

        if (!$user) {
            return response()->json([
                'message' => 'الكود غير صحيح',
                'status' => false
            ]);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'message' => 'تم تغيير كلمة المرور بنجاح',
            'status' => true
        ]);
    }

    public function verfiy_email_api(Request $request)
    {

        $otp_user = $request->otp;
        $user = clients::where('otp', $otp_user)->first();

        if ($user) {
            $user->update(['email_verified_at' => now()]);

            return response()->json([
                'message' => 'تم التحقق من البريد الالكتروني بنجاح',
                'status' => true
            ]);
        }

        return response()->json([
            'message' => 'الكود غير صحيح',
            'status' => false
        ]);
    }
}
