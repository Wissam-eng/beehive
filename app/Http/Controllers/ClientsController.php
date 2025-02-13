<?php

namespace App\Http\Controllers;

use App\Models\clients;
use App\Http\Controllers\PaymobController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ClientsController extends Controller
{



    public function index()
    {
        $clients = clients::where('status', 'active')->get();
        return view('clients.index', compact('clients'));
    }





    public function ClientsInActive()
    {

        $clients = clients::where('status', 'inactive')->get();
        return view('clients.ClientsInActive', compact('clients'));
    }


    public function store(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'service_name' => 'required|array|max:255',
                'service_cost' => 'required|array|max:255',
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


            // $input['service_cost'] = array_sum($request->service_cost);
            $service_cost = array_sum(array_map('floatval', $request->service_cost));


            if (is_array($request->service_name)) {

                $service_name = implode('_', $request->service_name);
            } else {

                $service_name = $request->service_name;
            }



            $input = $request->except(['img']);
            if ($request->hasFile('img')) {
                $file = $request->file('img');
                $path = $file->store('images/clients', 'public');
                $input['img'] = 'storage/app/public/' . $path;
            }

            $input['password'] = Hash::make($request->password);


            // dd($input['service_name']);



            // Save the client
            $new_client = clients::create($input);

            $userData = [
                'first_name' => $request->name,
                'last_name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'phone_number' => $request->mobile,
                'user_id' => $new_client->id
            ];

            // بيانات الطلب الخاص برسوم التسجيل
            $orderData = [
                // 'total' => 100,
                'total' =>  $service_cost, // رسوم اشتراك على سبيل المثال
                'currency' => 'EGP',
                'service_name' =>  $service_name,
                'services' => $input['service_name'],
                'services_all_cost' => $input['service_cost'],

                'items' => [
                    [
                        // "name" => "User Registration Fee",
                        'name' =>   $service_name,
                        "amount_cents" => $service_cost * 100,
                        "description" => "beehive for services",
                        "quantity" => 1
                    ]
                ]
            ];




            // استدعاء كنترولر الدفع
            $paymobController = new PaymobController();
            $response = $paymobController->processPayment($orderData, $userData);

            if ($response['status'] === 'success') {





                return response()->json([
                    'success' => true,
                    'payment_url' => $response['payment_url'],  // رابط الدفع
                    'data' => $new_client,
                ], 201); // Created

            } else {

                $new_client->forceDelete();

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
            ], 500); // Internal Server Error
        }
    }



    public function add_service(Request $request, $id)
    {
        try {



            $validator = Validator::make($request->all(), [
                'service_name' => 'required|array|max:255',
                'service_cost' => 'required|array|max:255',
            ]);



            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422); // Unprocessable Entity
            }


            $client_id = clients::find($id);

            if (!$client_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client not found',
                ], 404); // Not Found
            }

            $service_cost = array_sum(array_map('floatval', $request->service_cost));


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

            // بيانات الطلب الخاص برسوم التسجيل
            $orderData = [
                // 'total' => 100,
                'total' =>  $service_cost, // رسوم اشتراك على سبيل المثال
                'currency' => 'EGP',
                'service_name' =>  $service_name,
                'services' => $request->service_name,
                'services_all_cost' => $request->service_cost,

                'items' => [
                    [
                        // "name" => "User Registration Fee",
                        'name' =>   $service_name,
                        "amount_cents" => $service_cost * 100,
                        "description" => "beehive for services",
                        "quantity" => 1
                    ]
                ]
            ];





            // استدعاء كنترولر الدفع
            $paymobController = new PaymobController();
            $response = $paymobController->processPayment($orderData, $userData);

            if ($response['status'] === 'success') {





                return response()->json([
                    'success' => true,
                    'payment_url' => $response['payment_url'],  // رابط الدفع
                ], 201); // Created

            } else {

                $client_id->forceDelete();

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


    public function show_my_account($id)
    {
        $clients = clients::find($id);


        if (!$clients) {
            return response()->json(['error' => 'Client not found'], 404);
        }


        return response()->json([
            'success' => true,
            'my_account' => $clients
        ]);
    }


    public function edit(clients $clients)
    {
        //
    }




    public function inactive_client(Request $request, $id)
    {
        $client = Clients::find($id);
        if (!$client) {
            return redirect()->back()->with('error', 'Client not found');
        }

        $client->status = 'inactive';
        $client->save();
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



    public function update(Request $request, clients $clients)
    {
        //
    }


    public function destroy(clients $clients)
    {
        //
    }
}
