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
    public function credit()
    {
        //this fucntion that send all below function data to paymob and use it for routes;


        $tokens = $this->getToken();
        $order = $this->createOrder($tokens);
        $paymentToken = $this->getPaymentToken($order, $tokens);
        return Redirect::away('https://accept.paymob.com/api/acceptance/iframes/' . env('PAYMOB_IFRAME_ID') . '?payment_token=' . $paymentToken);
    }

    public function getToken()
    {
        //this function takes api key from env.file and get token from paymob accept


        $response = Http::post('https://accept.paymob.com/api/auth/tokens', [
            'api_key' => env('PAYMOB_API_KEY')
        ]);
        return $response->object()->token;
    }

    public function createOrder($tokens)
    {
        //this function takes last step token and send new order to paymob dashboard



        // $amount = new Checkoutshow; here you add your checkout controller
        // $total = $amount->totalProductAmount(); total amount function from checkout controller
        //here we add example for test only
        $total = 100;
        $items = [
            [
                "name" => "ASC1515",
                "amount_cents" => "500000",
                "description" => "Smart Watch",
                "quantity" => "1"
            ],
            [
                "name" => "ERT6565",
                "amount_cents" => "200000",
                "description" => "Power Bank",
                "quantity" => "1"
            ]
        ];

        $data = [
            "auth_token" =>   $tokens,
            "delivery_needed" => "false",
            "amount_cents" => $total * 100,
            "currency" => "EGP",
            "items" => $items,

        ];
        $response = Http::post('https://accept.paymob.com/api/ecommerce/orders', $data);
        return $response->object();
    }

    public function getPaymentToken($order, $token)
    {
        //this function to add details to paymob order dashboard and you can fill this data from your Model Class as below


        // $amountt = new Checkoutshow;
        // $totall = $amountt->totalProductAmount();
        // $todayDate = Carbon::now();
        // $dataa = Order::where('user_id',Auth::user()->id)->whereDate('created_at',$todayDate)->orderBy('created_at','desc')->first();

        //we just added dummy data for test
        //all data we fill is required for paymob
        $billingData = [
            "apartment" => '45', //example $dataa->appartment
            "email" => "newmail@gmai.com", //example $dataa->email
            "floor" => '5',
            "first_name" => 'maher',
            "street" => "NA",
            "building" => "NA",
            "phone_number" => '0123456789',
            "shipping_method" => "NA",
            "postal_code" => "NA",
            "city" => "cairo",
            "country" => "NA",
            "last_name" => "fared",
            "state" => "NA"
        ];
        $data = [
            "auth_token" => $token,
            "amount_cents" => 100 * 100,
            "expiration" => 3600,
            "order_id" => $order->id, // this order id created by paymob
            "billing_data" => $billingData,
            "currency" => "EGP",
            "integration_id" => env('PAYMOB_INTEGRATION_ID')
        ];
        $response = Http::post('https://accept.paymob.com/api/acceptance/payment_keys', $data);
        return $response->object()->token;
    }























    public function callback(Request $request)
    {

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
            //this below data used to get the last order created by the customer and check if its exists to
            // $todayDate = Carbon::now();
            // $datas = Order::where('user_id',Auth::user()->id)->whereDate('created_at',$todayDate)->orderBy('created_at','desc')->first();
            $status = $data['success'];


            $order_id = $data['order'];

            $order = services_client::where('order_id', $order_id)->first();

            if (!$order) {
                return response()->json(['message' => 'Order not found'], 404);
            }



            $new_user_id = clients::find($order->client_id);

            if (!$new_user_id) {
                dd(session('new_client_id'));
                return response()->json(['message' => 'User not found'], 404);
            }
            // $pending = $data['pending'];

            if ($status == "true") {

                //here we checked that the success payment is true and we updated the data base and empty the cart and redirct the customer to thankyou page

                // Cart::where('user_id',auth()->user()->id)->delete();
                // $datas->update([
                //     'payment_id' => $data['id'],
                //     'payment_status' => "Compeleted"
                // ]);
                // try {
                //     $order = Order::find($datas->id);
                //     Mail::to('maherfared@gmail.com')->send(new PlaceOrderMailable($order));
                // }catch(\Exception $e){

                // }
                // return redirect('thankyou');

                $new_user_id->update([
                    'status' => "active"
                ]);

                $order->update([
                    'payment_status' => "paid"
                ]);

                return redirect('thankyou')->with('success', 'Payment Successfull ... you can login now');
            } else {

                $new_user_id->forcedelete();
                // $datas->update([
                //     'payment_id' => $data['id'],
                //     'payment_status' => "Failed"
                // ]);


                return redirect('/thankyou')->with('message', 'Something Went Wrong Please Try Again');
            }
        } else {
            return redirect('/thankyou')->with('message', 'Something Went Wrong Please Try Again');
        }
    }











    public function processPayment(array $orderData, array $userData)
    {
        try {
            // Step 1: Get API Token from Paymob
            $tokenResponse = Http::post('https://accept.paymob.com/api/auth/tokens', [
                'api_key' => env('PAYMOB_API_KEY')
            ]);

            if (!$tokenResponse->successful()) {
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
                "amount_cents" => $orderData['total'] * 100,
                "currency" => $orderData['currency'] ?? "EGP",
                "items" => $orderData['items'] ?? []
            ];

            $orderResponse = Http::post('https://accept.paymob.com/api/ecommerce/orders', $orderPayload);
            if (!$orderResponse->successful()) {
                Log::error('Paymob Order Request Failed', [
                    'status_code' => $orderResponse->status(),
                    'response_body' => $orderResponse->body()
                ]);
                throw new \Exception("Failed to create order.");
            }


            $order = $orderResponse->object();


            $newservice = services_client::create([
                'service_name' => $orderData['service_name'],
                'service_cost' => $orderData['total'],
                'client_id' => $userData['user_id'],
                'payment_status' => 'pending',
                'order_id' => $order->id,
            ]);

            if (!$newservice) {
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
                "amount_cents" => $orderData['total'] * 100,
                "expiration" => 3600,
                "order_id" => $order->id,
                "billing_data" => $billingData,
                "currency" => $orderData['currency'] ?? "EGP",
                "integration_id" => env('PAYMOB_INTEGRATION_ID')
            ];

            $paymentResponse = Http::post('https://accept.paymob.com/api/acceptance/payment_keys', $paymentPayload);
            if (!$paymentResponse->successful()) {
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
