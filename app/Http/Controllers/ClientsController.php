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
                'email' => 'required|string|email|unique:clients|max:255',
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
                'total' => 100, // رسوم اشتراك على سبيل المثال
                'currency' => 'EGP',
                'service_name' => 'برمجة',

                'items' => [
                    [
                        "name" => "User Registration Fee",
                        "amount_cents" => 10000,
                        "description" => "Fee for new user registration",
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





    // public function callback(Request $request)
    // {
    //     //this call back function its return the data from paymob and we show the full response and we checked if hmac is correct means successfull payment

    //     $data = $request->all();
    //     ksort($data);
    //     $hmac = $data['hmac'];
    //     $array = [
    //         'amount_cents',
    //         'created_at',
    //         'currency',
    //         'error_occured',
    //         'has_parent_transaction',
    //         'id',
    //         'integration_id',
    //         'is_3d_secure',
    //         'is_auth',
    //         'is_capture',
    //         'is_refunded',
    //         'is_standalone_payment',
    //         'is_voided',
    //         'order',
    //         'owner',
    //         'pending',
    //         'source_data_pan',
    //         'source_data_sub_type',
    //         'source_data_type',
    //         'success',
    //     ];
    //     $connectedString = '';
    //     foreach ($data as $key => $element) {
    //         if (in_array($key, $array)) {
    //             $connectedString .= $element;
    //         }
    //     }
    //     $secret = env('PAYMOB_HMAC');
    //     $hased = hash_hmac('sha512', $connectedString, $secret);
    //     if ($hased == $hmac) {
    //         //this below data used to get the last order created by the customer and check if its exists to
    //         // $todayDate = Carbon::now();
    //         // $datas = Order::where('user_id',Auth::user()->id)->whereDate('created_at',$todayDate)->orderBy('created_at','desc')->first();
    //         $status = $data['success'];
    //         // $pending = $data['pending'];

    //         if ($status == "true") {

    //             //here we checked that the success payment is true and we updated the data base and empty the cart and redirct the customer to thankyou page

    //             // Cart::where('user_id',auth()->user()->id)->delete();
    //             // $datas->update([
    //             //     'payment_id' => $data['id'],
    //             //     'payment_status' => "Compeleted"
    //             // ]);
    //             // try {
    //             //     $order = Order::find($datas->id);
    //             //     Mail::to('maherfared@gmail.com')->send(new PlaceOrderMailable($order));
    //             // }catch(\Exception $e){

    //             // }
    //             return redirect('thankyou');
    //         } else {
    //             // $datas->update([
    //             //     'payment_id' => $data['id'],
    //             //     'payment_status' => "Failed"
    //             // ]);


    //             return redirect('/checkout')->with('message', 'Something Went Wrong Please Try Again');
    //         }
    //     } else {
    //         return redirect('/checkout')->with('message', 'Something Went Wrong Please Try Again');
    //     }
    // }





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




    public function inactive_client($id)
    {
        $client = Clients::find($id);
        if (!$client) {
            return redirect()->back()->with('error', 'Client not found');
        }
        $client->status = 'inactive';
        $client->save();
        return redirect()->back()->with('success', 'Client Inactive');
    }



    public function inactive_my_account($id)
    {
        $client = Clients::find($id);
        if (!$client) {
            return response()->json(['error' => 'Client not found'], 404);
        }
        $client->status = 'inactive';
        $client->save();
        return response()->json(['message' => 'your account Inactive'], 200);
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
