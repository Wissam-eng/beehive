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


        $data = $request->all();


        if ($data['statusCode'] = 200 && $data['orderStatus'] = "PAID") {

            $order_id = $data['merchantRefNumber'];

            $order = services_client::where('order_id', $order_id)->first();



            if (!$order) {
                return response()->json(['message' => 'Order not found'], 404);
            }



            $new_user_id = clients::find($order->client_id);

            if (!$new_user_id) {

                return response()->json(['message' => 'User not found'], 404);
            }

            if ($new_user_id->status == "active") {

                $order->update([
                    'status' => "active",
                    'payment_status' => "paid",
                    'referenceNumber' => $data['referenceNumber']
                ]);

                return redirect('thankyou')->with('success', $data['statusDescription'] . 'Payment Successfull');
            } else {



                $new_user_id->update([
                    'status' => "active"
                ]);

                $order->update([
                    'status' => "active",
                    'payment_status' => "paid",
                    'referenceNumber' => $data['referenceNumber']
                ]);

                return redirect('thankyou')->with('success', $data['statusDescription'] . 'Payment Successfull ... you can login now');
            }
        } else {
            return redirect('/thankyou')->with('error', 'Something Went Wrong Please Try Again' . $data['statusDescription']);
        }
    }


    public function getcallback()
    {
        // return redirect()->route('thankyou')->with('success', 'Payment Successfull ... you can login now');
        return redirect()->route('thankyou')->with('error', 'Something Went Wrong Please Try Again');
    }





    //fawry payment

    private $merchantCode;
    private $securityKey;
    private $apiUrl;

    public function __construct()
    {
        $this->merchantCode = env('FAWRY_MERCHANT_CODE');
        $this->securityKey = env('FAWRY_SECRET_KEY');
        $this->apiUrl = env('FAWRY_API_URL');
    }


    /**
     * إنشاء طلب دفع عبر فوري
     */
    public function processPayment(array $orderData, array $userData)
    {




        $new_client = clients::find($userData['user_id']);



        $amount = $orderData['quantity'];




        $newservice = services_client::create([
            'service_name' => $orderData['service_name'],
            'service_cost' => $orderData['service_cost'],
            'client_id' => $userData['user_id'],
            'payment_status' => 'pending',
        ]);




        if (!$newservice) {

            $new_client->forceDelete();

            throw new \Exception("Failed to create order.");
        }


        $merchantRefNum = uniqid();
        $currency = "EGP"; // الجنيه المصري
        // $returnUrl = route('fawry.callback');
        // $returnUrl = 'https://highleveltecknology.com/beehive/callback';
        $returnUrl = 'https://2c34-156-205-75-139.ngrok-free.app/highlevel/beehive/callback';

        $itemId = $newservice->id;
        $service_cost = number_format(100, 2, '.', '');


        $signatureString = $this->merchantCode . $merchantRefNum . ""  . $returnUrl . $itemId . $amount . $service_cost  . $this->securityKey;
        $signature = hash('sha256', $signatureString);


        $newservice->update([
            'order_id' => $merchantRefNum
        ]);

        // تجهيز بيانات الطلب
        $data = [
            "merchantCode" => $this->merchantCode,
            "merchantRefNum" => $merchantRefNum,
            "customerMobile" => $userData['phone_number'],
            "language" => "en-gb",
            "chargeItems" => [


                [
                    "itemId" => $itemId,
                    "price" => $service_cost,
                    "quantity" => $amount
                ]
            ],
            "governorate" => "GIZA",
            "city" => "MOHANDESSIN",
            "area" => "GAMETDEWAL",
            "address" => "9th 90 Street, apartment number 8, 4th floor",
            "receiverName" => "Receiver Name",
            "returnUrl" => $returnUrl,
            "signature" => $signature,
        ];


        // إرسال الطلب إلى API فوري
        $response = Http::withHeaders(['Content-Type' => 'application/json'])->post($this->apiUrl, $data);

        $result = $response->json();


        if ($response->successful()) {
            // return $result;
            if ($response->body()) {


                $paymentUrl =  $response->body();


                return ['status' => 'success', 'payment_url' => $paymentUrl, "merchantRefNum" => $merchantRefNum];
            }
        } else {

            $new_client->forceDelete();
            $newservice->forceDelete();
            return $result;
        }


        return null;
    }



    public function processPayment_new_order(array $orderData, array $userData)
    {

        $new_client = clients::find($userData['user_id']);

        $amount = $orderData['quantity'];

        $newservice = services_client::create([
            'service_name' => $orderData['service_name'],
            'service_cost' => $orderData['service_cost'],
            'client_id' => $userData['user_id'],
            'payment_status' => 'pending',
        ]);


        if (!$newservice) {

            throw new \Exception("Failed to create order.");
        }


        $merchantRefNum = uniqid();
        $currency = "EGP"; // الجنيه المصري
        // $returnUrl = route('fawry.callback');
        // $returnUrl = 'https://highleveltecknology.com/beehive/callback';
        $returnUrl = 'https://2c34-156-205-75-139.ngrok-free.app/highlevel/beehive/callback';

        $itemId = $newservice->id;
        $service_cost = number_format(100, 2, '.', '');


        $signatureString = $this->merchantCode . $merchantRefNum . ""  . $returnUrl . $itemId . $amount . $service_cost  . $this->securityKey;
        $signature = hash('sha256', $signatureString);


        $newservice->update([
            'order_id' => $merchantRefNum
        ]);

        // تجهيز بيانات الطلب
        $data = [
            "merchantCode" => $this->merchantCode,
            "merchantRefNum" => $merchantRefNum,
            "customerMobile" => $userData['phone_number'],
            "language" => "en-gb",
            "chargeItems" => [


                [
                    "itemId" => $itemId,
                    "price" => $service_cost,
                    "quantity" => $amount
                ]
            ],
            "governorate" => "GIZA",
            "city" => "MOHANDESSIN",
            "area" => "GAMETDEWAL",
            "address" => "9th 90 Street, apartment number 8, 4th floor",
            "receiverName" => "Receiver Name",
            "returnUrl" => $returnUrl,
            "signature" => $signature,
        ];


        // إرسال الطلب إلى API فوري
        $response = Http::withHeaders(['Content-Type' => 'application/json'])->post($this->apiUrl, $data);

        $result = $response->json();


        if ($response->successful()) {
            // return $result;
            if ($response->body()) {


                $paymentUrl =  $response->body();


                return ['status' => 'success', 'payment_url' => $paymentUrl, "merchantRefNum" => $merchantRefNum];
            }
        } else {


            $newservice->forceDelete();
            return $result;
        }


        return null;
    }


    // public function processPayment_new_order(array $orderData, array $userData)
    // {
    //     try {


    //         $new_client = clients::find($userData['user_id']);




    //         // Step 1: Get API Token from Paymob
    //         $tokenResponse = Http::post('https://accept.paymob.com/api/auth/tokens', [
    //             'api_key' => env('PAYMOB_API_KEY')
    //         ]);

    //         if (!$tokenResponse->successful()) {




    //             Log::error('Paymob Token Request Failed', [
    //                 'status_code' => $tokenResponse->status(),
    //                 'response_body' => $tokenResponse->body()
    //             ]);
    //             throw new \Exception("Failed to retrieve authentication token.");
    //         }


    //         $authToken = $tokenResponse->object()->token;

    //         // Step 2: Create Order on Paymob
    //         $orderPayload = [
    //             "auth_token" => $authToken,
    //             "delivery_needed" => "false",
    //             "amount_cents" => floatval($orderData['total']) * 100,
    //             "currency" => $orderData['currency'] ?? "EGP",
    //             "items" => $orderData['items'] ?? []
    //         ];


    //         $orderResponse = Http::post('https://accept.paymob.com/api/ecommerce/orders', $orderPayload);

    //         if (!$orderResponse->successful()) {





    //             Log::error('Paymob Order Request Failed', [
    //                 'status_code' => $orderResponse->status(),
    //                 'response_body' => $orderResponse->body()
    //             ]);
    //             throw new \Exception("Failed to create order.");
    //         }


    //         $order = $orderResponse->object();



    //         foreach ($orderData['services'] as $index => $serv) {
    //             $serviceCost = $orderData['services_all_cost'][$index] ?? 0; // الحصول على تكلفة الخدمة باستخدام المفتاح المناسب

    //             $newservice = services_client::create([
    //                 'service_name' => $serv,
    //                 'service_cost' => $serviceCost,
    //                 'client_id' => $userData['user_id'],
    //                 'payment_status' => 'pending',
    //                 'order_id' => $order->id,
    //             ]);
    //         }




    //         if (!$newservice) {



    //             throw new \Exception("Failed to create order.");
    //         }

    //         // Step 3: Generate Payment Token
    //         $billingData = [
    //             "apartment" => $userData['apartment'] ?? 'N/A',
    //             "email" => $userData['email'],
    //             "floor" => $userData['floor'] ?? 'N/A',
    //             "first_name" => $userData['first_name'],
    //             "street" => $userData['street'] ?? 'N/A',
    //             "building" => $userData['building'] ?? 'N/A',
    //             "phone_number" => $userData['phone_number'],
    //             "shipping_method" => $userData['shipping_method'] ?? 'N/A',
    //             "postal_code" => $userData['postal_code'] ?? 'N/A',
    //             "city" => $userData['city'] ?? 'N/A',
    //             "country" => $userData['country'] ?? 'N/A',
    //             "last_name" => $userData['last_name'],
    //             "state" => $userData['state'] ?? 'N/A'
    //         ];

    //         $paymentPayload = [
    //             "auth_token" => $authToken,
    //             "amount_cents" => floatval($orderData['total']) * 100,
    //             "expiration" => 3600,
    //             "order_id" => $order->id,
    //             "billing_data" => $billingData,
    //             "currency" => $orderData['currency'] ?? "EGP",
    //             "integration_id" => env('PAYMOB_INTEGRATION_ID')
    //         ];

    //         // $paymentResponse = Http::post('https://accept.paymob.com/api/acceptance/payment_keys', $paymentPayload);




    //         $paymentResponse = Http::post('https://accept.paymob.com/api/acceptance/payment_keys', [
    //             "source" => [
    //                 // "identifier" => env('identifier') ,
    //                 "identifier" => 01010101010,
    //                 "subtype" => "WALLET"
    //             ],
    //             "auth_token" => $authToken,
    //             "amount_cents" => floatval($orderData['total']) * 100,
    //             "expiration" => 3600,
    //             "order_id" => $order->id,
    //             "billing_data" => $billingData,
    //             "currency" => $orderData['currency'] ?? "EGP",
    //             "integration_id" => env('PAYMOB_INTEGRATION_ID')
    //         ]);





    //         if (!$paymentResponse->successful()) {

    //             foreach ($newservice as $index => $service) {

    //                 $service->forceDelete();
    //             }

    //             Log::error('Paymob Payment Key Request Failed', [
    //                 'status_code' => $paymentResponse->status(),
    //                 'response_body' => $paymentResponse->body()
    //             ]);
    //             throw new \Exception("Failed to generate payment token.");
    //         }

    //         $paymentToken = $paymentResponse->object()->token;
    //         $paymentUrl = 'https://accept.paymob.com/api/acceptance/iframes/' . env('PAYMOB_IFRAME_ID') . '?payment_token=' . $paymentToken;

    //         // Return payment result or redirect
    //         return ['status' => 'success', 'payment_url' => $paymentUrl];
    //     } catch (\Exception $e) {
    //         return ['status' => 'error', 'message' => $e->getMessage()];
    //     }
    // }
}
