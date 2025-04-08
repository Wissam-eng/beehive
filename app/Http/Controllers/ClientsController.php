<?php

namespace App\Http\Controllers;

use App\Models\clients;
use App\Models\clients_cancel;
use App\Models\services_client;
use App\Http\Controllers\PaymobController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Mail\OTPMail;
use Illuminate\Support\Facades\Mail;

use Illuminate\Validation\Rule;


class ClientsController extends Controller
{



    public function index()
    {
        $clients = clients::where('status', 'active')->orderBy('created_at', 'desc')->get();
        return view('clients.index', compact('clients'));
    }





    public function ClientsInActive()
    {

        $clients = clients::where('status', 'inactive')->orderBy('created_at', 'desc')->get();
        return view('clients.ClientsInActive', compact('clients'));
    }








    public function store(Request $request)
    {
        try {


            $last_clint = Clients::where('email', $request->email)->whereNull('email_verified_at')->first();

            if ($last_clint) {

                $last_clint->forceDelete();
            }

            $last_clint = Clients::where('email', $request->email)->where('status', 'inactive')->first();

            if ($last_clint) {

                $last_clint->forceDelete();
            }

            // Validate the request
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                // 'service_name' => 'nullable|array|max:255',
                // 'service_cost' => 'nullable|max:255',
                'email' => 'required|string|email|unique:clients|max:255',
                'password' => 'required|string|min:8|confirmed',
                'mobile' => 'required|string|max:255|unique:clients',
                'gender' => 'required|in:male,female',
                'birth_date' => 'required|date',
                'age' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'work' => 'nullable|string|max:255',
                'center' => 'nullable|string|max:255',
                'landline' => 'nullable|string|max:255',
                'na_number' => 'nullable|string|max:255',
                'governorate' => 'nullable|string|max:255',
                'Village_Street' => 'nullable|string|max:255',
                'another_mobile' => 'nullable|string|max:255|unique:clients',
                'num_of_children' => 'nullable|string|max:255',
                'marital_status' => 'nullable|in:single,married',
                'Academic_qualification' => 'nullable|string|max:255',
                'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422); // Unprocessable Entity
            }


            $input = $request->except(['img']);
            if ($request->hasFile('img')) {
                $file = $request->file('img');
                $path = $file->store('images/clients', 'public');
                $input['img'] = 'storage/app/public/' . $path;
            }

            $input['password'] = Hash::make($request->password);






            // Save the client
            $new_client = clients::create($input);

            $otp = rand(1000, 9999);

            $new_client->update(['otp' => $otp]);

            $verificationLink = env('APP_URL') . '/getotp';

            Mail::to($request->email)->send(new OTPMail($otp, $verificationLink));

            return response()->json([
                'success' => true,
                'data' => 'Client created successfully... but we sent you an OTP to your email .. verify it first'

            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500); // Internal Server Error
        }
    }




    public function update(Request $request)
    {
        try {
            $id = auth()->user()->id;

            // جلب العميل
            $client = Clients::findOrFail($id);

            // التحقق من البيانات المدخلة
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'email' => [
                    'sometimes',
                    'email',
                    Rule::unique('clients', 'email')->ignore($id)
                ],
                'mobile' => [
                    'sometimes',
                    Rule::unique('clients', 'mobile')->ignore($id)
                ],
                'gender' => 'sometimes|in:male,female',
                'birth_date' => 'sometimes|date',
                'age' => 'nullable|integer|min:1|max:120',
                'city' => 'nullable|string|max:255',
                'work' => 'nullable|string|max:255',
                'center' => 'nullable|string|max:255',
                'landline' => 'nullable|string|max:255',
                'na_number' => 'nullable|string|max:255',
                'governorate' => 'nullable|string|max:255',
                'village_street' => 'nullable|string|max:255',
                'another_mobile' => [
                    'nullable',
                    Rule::unique('clients', 'another_mobile')->ignore($id)
                ],
                'num_of_children' => 'nullable|integer|min:0',
                'marital_status' => 'nullable|in:single,married',
                'academic_qualification' => 'nullable|string|max:255',
                'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            // جمع البيانات بدون الصورة وكلمة المرور
            $input = $request->except(['img', 'password']);

            // تحديث الصورة إذا وُجِدت
            if ($request->hasFile('img')) {
                $file = $request->file('img');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('images/clients', $filename, 'public');
                $input['img'] = 'storage/' . $path;
            }

            // تحديث كلمة المرور إذا وُجدت


            // تحديث البيانات
            $client->update($input);

            return response()->json([
                'success' => true,
                'user' => $client,
                'message' => 'Client updated successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }





    public function add_service(Request $request)
    {
        try {



            $validator = Validator::make($request->all(), [
                'service_name' => 'required|array|max:255',
                'service_cost' => 'required',
            ]);





            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422); // Unprocessable Entity
            }


            $client_id = clients::find(auth()->user()->id);

            if (!$client_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client not found',
                ], 404); // Not Found
            }

            $service_cost = 5800;


            if (is_array($request->service_name)) {

                $service_name = implode('_', $request->service_name);
            } else {

                $service_name = $request->service_name;
            }




            $userData = [
                'first_name' => $client_id->name,
                'last_name' => $client_id->name,
                'email' => $client_id->email,
                'phone_number' => $client_id->mobile,
                'user_id' => $client_id->id
            ];




            $orderData = [
                'total' =>  $service_cost, // رسوم اشتراك على سبيل المثال
                'currency' => 'EGP',
                'service_name' =>  $service_name,
                'service_cost' => $request->service_cost,
                "quantity" => 1,
                'items' => [
                    [
                        'name' =>   $service_name,
                        "amount_cents" => floatval($service_cost) * 100,
                        "description" => "beehive for services"
                    ]
                ]
            ];





            // استدعاء كنترولر الدفع
            $paymobController = new PaymobController();
            $response = $paymobController->processPayment_new_order($orderData, $userData);

            // dd($response);

            if ($response) {

                return response()->json([
                    'success' => true,
                    // 'payment_url' => $response['payment_url'],
                    'payment_data' => $response,
                    'email_of_company' => 'lainavacompany@gmail.com',
                ], 201); // Created

            } else {



                // عرض رسالة خطأ
                return response()->json([
                    'success' => false,
                    'message' => $response['message'],  // عرض رسالة خطأ في حالة فشل الدفع
                ], 400); // Bad Request
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }



    public function show_details($id)
    {
        $clients = clients::with('orders')->find($id);

        // dd($clients->orders);
        if (!$clients) {
            return redirect()->back()->with('error', 'Client not found');
        }


        return view('clients.show_details', compact('clients'));
    }





    public function show($id)
    {
        $clients = clients::find($id);


        if (!$clients) {
            return redirect()->back()->with('error', 'Client not found');
        }


        return view('clients.show', compact('clients'));
    }


    public function show_my_account()
    {
        $id = auth()->user()->id;
        $clients = clients::find($id);


        if (!$clients) {
            return response()->json(['error' => 'Client not found'], 404);
        }


        return response()->json([
            'success' => true,
            'my_account' => $clients
        ]);
    }




    public function inactive_client(Request $request, $id)
    {
        $client = Clients::find($id);
        if (!$client) {
            return redirect()->back()->with('error', 'Client not found');
        }




        $client->status = 'inactive';
        $client->save();



        if ($client->status == 'inactive') {

            $oreder = services_client::where('client_id', $id)->orderBy('created_at', 'desc')->get();
            foreach ($oreder as $order) {
                $order->status = 'inactive';
                $order->save();
            }

            $client_cancel = clients_cancel::where(['client_id' => $id, 'status' => 'pending'])->first();



            if ($client_cancel) {

                $client_cancel->update([
                    'status' => 'Done',
                ]);
            }
        }







        return redirect()->back()->with('success', 'Client Inactive');
    }



    public function inactive_my_account(Request $request, $id)
    {
        $client = Clients::find($id);

        if (!$client) {
            return response()->json(['error' => 'Client not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'mobile_wallet' => 'required|string|max:255',
            'account_number_bank' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['error', 'حدث خطاء اثناء التسجيل: ' . $validator->errors()], 422);
        }

        $client->update([
            'mobile_wallet' => $request->mobile_wallet,
            'account_number_bank' => $request->account_number_bank,
            'status' => 'inactive',
        ]);

        return response()->json(['message' => 'your account Inactive'], 200);
    }



    public function refund_account(Request $request, $id)
    {
        $client = Clients::find($id);
        if (!$client) {
            return redirect()->back()->with('error', 'Client not found');
        }
        $client->refund = 'paid';
        $client->save();
        return redirect()->back()->with('success', 'Client refund Done');
    }


    public function active_client($id)
    {
        $client = Clients::find($id);
        if (!$client) {
            return redirect()->back()->with('error', 'Client not found');
        }
        $client->status = 'active';
        $client->save();
        return redirect()->back()->with('success', 'Client active');
    }
}
