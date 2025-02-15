<?php

namespace App\Http\Controllers;

use App\Models\clients;
use App\Models\services_client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\log;

class PaymobController extends Controller
{



    //return the data from paymob and we show the full response and we checked if hmac is correct means successfull payment

    public function callback(Request $request)
    {
        // dd($request->all());
        //this call back function its return the data from paymob and we show the full response and we checked if hmac is correct means successfull payment

        $data = $request->all();

        ksort($data);
        $hmac = $data['hmac'];
        $array = [
            'amount_cents',
            'created_at',
            'currency',
            'error_occured',
            'has_parent_transaction',
            'id',
            'integration_id',
            'is_3d_secure',
            'is_auth',
            'is_capture',
            'is_refunded',
            'is_standalone_payment',
            'is_voided',
            'order',
            'owner',
            'pending',
            'source_data_pan',
            'source_data_sub_type',
            'source_data_type',
            'success',
        ];
        $connectedString = '';
        foreach ($data as $key => $element) {
            if (in_array($key, $array)) {
                $connectedString .= $element;
            }
        }
        $secret = env('PAYMOB_HMAC');
        $hased = hash_hmac('sha512', $connectedString, $secret);
        if ($hased == $hmac) {

            $status = $data['success'];


            $order_id = $data['order'];

            $order = services_client::where('order_id', $order_id)->get();

            if ($order->isEmpty()) {
                return response()->json(['message' => 'Order not found'], 404);
            }



            $new_user_id = clients::find(optional($order->first())->client_id);

            if (!$new_user_id) {
                dd(session('new_client_id'));
                return response()->json(['message' => 'User not found'], 404);
            }

            if ($status == "true") {


                $new_user_id->update([
                    'status' => "active"
                ]);

                foreach ($order as $od) {
                    $od->update([
                        'status' => "active",
                        'payment_status' => "paid",
                        'trans_id' => $data['id']
                    ]);
                }

                return redirect('thankyou')->with('success', 'Payment Successfull ... you can login now');
            } else {

                $new_user_id->forcedelete();
                foreach ($order as $od) {
                    $od->forceDelete();
                }



                return redirect('/thankyou')->with('message', 'Something Went Wrong Please Try Again');
            }
        } else {
            return redirect('/thankyou')->with('message', 'Something Went Wrong Please Try Again');
        }
    }




    public function processPayment(array $orderData, array $userData)
    {
        try {


            $new_client = clients::find($userData['user_id']);




            // Step 1: Get API Token from Paymob
            $tokenResponse = Http::post('https://accept.paymob.com/api/auth/tokens', [
                'api_key' => env('PAYMOB_API_KEY')
            ]);

            if (!$tokenResponse->successful()) {

                $new_client->forceDelete();


                Log::error('Paymob Token Request Failed', [
                    'status_code' => $tokenResponse->status(),
                    'response_body' => $tokenResponse->body()
                ]);
                throw new \Exception("Failed to retrieve authentication token.");
            }

            // if (!$tokenResponse->ok()) {
            //     throw new \Exception("Failed to retrieve authentication token.");
            // }




            $authToken = $tokenResponse->object()->token;

            // Step 2: Create Order on Paymob
            $orderPayload = [
                "auth_token" => $authToken,
                "delivery_needed" => "false",
                "amount_cents" => floatval($orderData['total']) * 100,
                "currency" => $orderData['currency'] ?? "EGP",
                "items" => $orderData['items'] ?? []
            ];


            $orderResponse = Http::post('https://accept.paymob.com/api/ecommerce/orders', $orderPayload);
            if (!$orderResponse->successful()) {


                $new_client->forceDelete();


                Log::error('Paymob Order Request Failed', [
                    'status_code' => $orderResponse->status(),
                    'response_body' => $orderResponse->body()
                ]);
                throw new \Exception("Failed to create order.");
            }


            $order = $orderResponse->object();




            foreach ($orderData['services'] as $index => $serv) {
                $serviceCost = $orderData['services_all_cost'][$index] ?? 0; // الحصول على تكلفة الخدمة باستخدام المفتاح المناسب

                $newservice = services_client::create([
                    'service_name' => $serv,
                    'service_cost' => $serviceCost,
                    'client_id' => $userData['user_id'],
                    'payment_status' => 'pending',
                    'order_id' => $order->id,
                ]);
            }




            if (!$newservice) {

                $new_client->forceDelete();

                throw new \Exception("Failed to create order.");
            }

            // Step 3: Generate Payment Token
            $billingData = [
                "apartment" => $userData['apartment'] ?? 'N/A',
                "email" => $userData['email'],
                "floor" => $userData['floor'] ?? 'N/A',
                "first_name" => $userData['first_name'],
                "street" => $userData['street'] ?? 'N/A',
                "building" => $userData['building'] ?? 'N/A',
                "phone_number" => $userData['phone_number'],
                "shipping_method" => $userData['shipping_method'] ?? 'N/A',
                "postal_code" => $userData['postal_code'] ?? 'N/A',
                "city" => $userData['city'] ?? 'N/A',
                "country" => $userData['country'] ?? 'N/A',
                "last_name" => $userData['last_name'],
                "state" => $userData['state'] ?? 'N/A'
            ];

            $paymentPayload = [
                "auth_token" => $authToken,
                "amount_cents" => floatval($orderData['total']) * 100,
                "expiration" => 3600,
                "order_id" => $order->id,
                "billing_data" => $billingData,
                "currency" => $orderData['currency'] ?? "EGP",
                "integration_id" => env('PAYMOB_INTEGRATION_ID')
            ];

            $paymentResponse = Http::post('https://accept.paymob.com/api/acceptance/payment_keys', $paymentPayload);

            if (!$paymentResponse->successful()) {
                $new_client->forceDelete();

                foreach ($newservice as $index => $service) {

                    $service->forceDelete();
                }

                Log::error('Paymob Payment Key Request Failed', [
                    'status_code' => $paymentResponse->status(),
                    'response_body' => $paymentResponse->body()
                ]);
                throw new \Exception("Failed to generate payment token.");
            }

            $paymentToken = $paymentResponse->object()->token;
            $paymentUrl = 'https://accept.paymob.com/api/acceptance/iframes/' . env('PAYMOB_IFRAME_ID') . '?payment_token=' . $paymentToken;

            // Return payment result or redirect
            return ['status' => 'success', 'payment_url' => $paymentUrl];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
