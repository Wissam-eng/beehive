<?php

namespace App\Http\Controllers;

use App\Models\admins;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\services_client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class HomeController extends Controller
{

    public function index()
    {
        $profits = services_client::where('status', 'active')->where('payment_status', 'paid')->sum('service_cost');
        return view('home.index')->with('profits', $profits);
    }


    public function orders()
    {
        $users = User::all();
        return view('orders.index')->with('users', $users);
    }

    public function users()
    {
        $users = admins::all();
        return view('users.index', compact('users'));
    }


    public function profile()
    {
        $profile = admins::find(auth()->user()->id);

        return view('profile.index', compact('profile'));
    }


    public function update_profile(Request $request)
    {
        try {

            $user = admins::find($request->id);

            if (!$user) {
                return redirect()->back()->with('error', 'البيانات غير موجودة');
            }


            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'img' => 'image|mimes:jpeg,svg,jpg,webp,png,jpg,gif|max:2048',
                'email' => 'sometimes|email',
                'password' => 'nullable|string|min:1',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->with('error', 'حدث خطاء اثناء التسجيل: ' . $validator->errors());
            }


            if ($request->has('password')) {
                $user->password = Hash::make($request->password);
            }

            if ($request->hasFile('img')) {
                $imagePath = $request->file('img')->store('images/users', 'public');
                $imagePath = 'storage/app/public/' . $imagePath;
                $user->img = $imagePath;
            }

            $user->name = $request->name;
            $user->email = $request->email;
            $user->save();
            return redirect()->back()->with([
                'success' => 'تم تحديث البيانات بنجاح'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطاء اثناء تحديث البيانات: ' . $e->getMessage());
        }
    }



    public function register()
    {
        return view('profile.create');
    }


    public function store_user(Request $request)
    {

        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:admins',
                'password' => 'required|string|min:1|confirmed',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->with('error', 'حدث خطاء اثناء التسجيل: ' . $validator->errors());
            }


            $user = admins::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return redirect()->back()->with([
                'success' => 'تم التسجيل بنجاح user :' . $user->email . ' password :' . $request->password

            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطاء اثناء التسجيل: ' . $e->getMessage());
        }
    }
}
