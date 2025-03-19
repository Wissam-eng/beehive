<?php

namespace App\Http\Controllers;

use App\Models\orders_cancel;
use App\Models\clients;
use App\Models\services;
use App\Models\services_client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class ServicesClientController extends Controller
{

    public function index()
    {

        $services = services_client::where('status', 'active')->orderBy('created_at', 'desc')->get();
        return view('orders.index', compact('services'));
    }


    public function orders_in_active()
    {

        $services = services_client::where('status', 'inactive')->orderBy('created_at', 'desc')->get();
        return view('orders.inActive_order', compact('services'));
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'img' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'حدث خطاء اثناء التسجيل: ' . $validator->errors());
        }
    }


    public function show($id)
    {
        $client = clients::find($id);
        if (!$client) {
            return redirect()->back()->with('error', 'Client not found');
        }


        $services = services_client::where('client_id', $id)->orderBy('created_at', 'desc')->get();

        return view('clients.show_details', compact('services', 'client'));
    }


    public function show_all_my_order($id)
    {
        $id = auth()->user()->id;
        $client = clients::find($id);
        if (!$client) {
            return response()->json(['error', 'Client not found'], 401);
        }



        $services = services_client::where('client_id', $id)->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'my_orders' => $services
        ]);
    }


    public function refund_service(Request $request, $id)
    {
        $service = services_client::find($id);
        if (!$service) {
            return redirect()->back()->with('error', 'service not found');
        }
        $service->refund = 'paid';
        $service->save();
        return redirect()->back()->with('success', 'service refund Done');
    }



    public function show_my_order(Request $request, $id)
    {
        $data = [
            'id' => $id,
            'order_id' => $request->input('order_id'),
        ];

        $validator = Validator::make($data, [
            'id' => 'required|exists:clients,id',
            'order_id' => 'required|string|exists:services_clients,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422); // Ensure this status code is an integer
        }

        // منطقك هنا بعد التحقق الناجح
        $order = services_client::find($data['order_id'])->where('client_id', $id)->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404); // Ensure this status code is an integer
        }

        return response()->json([
            'success' => true,
            'order' => $order,
        ], 200); // Ensure this status code is an integer
    }






    public function inactive_order(Request $request, $id)
    {
        $services_client = services_client::find($id);

        if (!$services_client) {
            return redirect()->back()->with('error', 'Order not found');
        }


        $services_client->update([
            'status' => 'inactive',
        ]);

        if ($services_client->status == 'inactive') {
            $order_cancel = orders_cancel::where('order_id', $id)->first();

            if ($order_cancel) {
                $order_cancel->update([
                    'status' => 'Done',
                ]);
            }
        }



        return redirect()->back()->with('success', 'Order status updated successfully');
    }
}
