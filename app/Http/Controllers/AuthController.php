<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use App\Models\clients;
use App\Models\admins;
use Illuminate\Support\Facades\Mail;



use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; // أضف هذا لضمان عمل Auth بشكل صحيح


class AuthController extends Controller
{
    /**
     * تسجيل مستخدم جديد
     */

    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'country' => 'nullable|string|max:255',
            'mobile' => 'required|string|max:255|unique:clients|unique:admins',
            'birth_date' => 'required|date',
            'email' => 'required|string|email|max:255|unique:clients|unique:admins',
            'password' => 'required|string|min:1',
            'user_type' => 'required|in:admins,clients',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'بعض البيانات غير مكتملة أو غير صحيحة.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->all();

        // $data['usrer_type'] = 'admins';

        if ($request->hasFile('img')) {
            $image = $request->file('img');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

            $uploadPath = $request->user_type === 'admins' ? 'uploads/admins' : 'uploads/clients';
            $image->move(public_path($uploadPath), $imageName);
            $data['img'] = $uploadPath . '/' . $imageName;
        }

        try {
            if ($request->user_type === 'admins') {
                //create otp
                $otp = rand(1000, 9999);
                $data['otp'] = $otp;
                $user = admins::create($data);
                // Mail::to($request->email)->send(new \App\Mail\hellomail());
                Mail::to($request->email)->send(new \App\Mail\hellomail($otp));
                return redirect()->route('otp')->with('email', $request->email);

                // Mail::to($request->email)->send(new $otp);
            } elseif ($request->user_type === 'clients') {
                $user = clients::create($data);
            } else {
                throw new \Exception("نوع المستخدم غير صالح.");
            }

            $token = JWTAuth::fromUser($user);

            return response()->json([
                'status' => 'success',
                'message' => 'تم تسجيل المستخدم بنجاح.',
                'token' => $token,
                'user' => $user,
            ], 201);
        } catch (QueryException $e) {
            if ($e->getCode() == 23000) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'رقم الهاتف أو البريد الإلكتروني مستخدم مسبقًا.',
                ], 409);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ غير متوقع.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }



    public function signup()
    {
        return view('auth.register_user');
    }



    public function register_api(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'country' => 'nullable|string|max:255',
            'mobile' => 'required|string|max:255|unique:clients|unique:admins',
            'birth_date' => 'required|date',
            'email' => 'required|string|email|max:255|unique:clients|unique:admins',
            'password' => 'required|string|min:1',
            'user_type' => 'required|in:admins,clients',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'بعض البيانات غير مكتملة أو غير صحيحة.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->all();

        if ($request->hasFile('img')) {
            $image = $request->file('img');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

            $uploadPath = $request->user_type === 'admins' ? 'uploads/admins' : 'uploads/clients';
            $image->move(public_path($uploadPath), $imageName);
            $data['img'] = $uploadPath . '/' . $imageName;
        }

        try {
            if ($request->user_type === 'admins') {
                //create otp
                $otp = rand(1000, 9999);
                $data['otp'] = $otp;
                $user = admins::create($data);
                // Mail::to($request->email)->send(new \App\Mail\hellomail());
                Mail::to($request->email)->send(new \App\Mail\hellomail($otp));
                return redirect()->route('otp')->with('email', $request->email);

                // Mail::to($request->email)->send(new $otp);
            } elseif ($request->user_type === 'clients') {
                $user = clients::create($data);
            } else {
                throw new \Exception("نوع المستخدم غير صالح.");
            }

            $token = JWTAuth::fromUser($user);

            return response()->json([
                'status' => 'success',
                'message' => 'تم تسجيل المستخدم بنجاح.',
                'token' => $token,
                'user' => $user,
            ], 201);
        } catch (QueryException $e) {
            if ($e->getCode() == 23000) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'رقم الهاتف أو البريد الإلكتروني مستخدم مسبقًا.',
                ], 409);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ غير متوقع.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * تسجيل الدخول
     */

    public function login(Request $request)
    {

     
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        $user = null;

        if ($token = Auth::guard('admins')->attempt($credentials)) {
            $user = Auth::guard('admins')->user();
        } elseif ($token = Auth::guard('clients')->attempt($credentials)) {
            $user = Auth::guard('clients')->user();
            if ($user->status !== 'active') {
                return response()->json(['error' => 'User is not active'], 401);
            }
        }

        if ($user) {
            return response()->json([
                'token' => $token,
                'user' => $user,
            ]);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }



    public function logout(Request $request)
    {


        Auth::logout();
        return response()->json(['success' => true, 'message' => 'Logout successful']);
    }
}
