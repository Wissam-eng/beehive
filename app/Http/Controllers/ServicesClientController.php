<?php

namespace App\Http\Controllers;

use App\Models\clients;
use App\Models\services_client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class ServicesClientController extends Controller
{

    public function index()
    {

        $services = services_client::where('status', 'active')->get();
        return view('orders.index', compact('services'));
    }


    public function orders_in_active()
    {

        $services = services_client::where('status', 'inactive')->get();
        return view('orders.inActive_order', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
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

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $client = clients::find($id);
        if (!$client) {
            return redirect()->back()->with('error', 'Client not found');
        }


        $services = services_client::where('client_id', $id)->get();

        return view('clients.show_details', compact('services', 'client'));
    }


    public function show_all_my_order($id)
    {
        $client = clients::find($id);
        if (!$client) {
            return response()->json(['error', 'Client not found'], 401);
        }

        $services = services_client::where('client_id', $id)->get();

        return response()->json([
            'success' => true,
            'my_orders' => $services
        ]);
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
        $order = services_client::find( $data['order_id'])->where('client_id', $id)->first();

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



    public function edit(services_client $services_client)
    {
        //
    }



    public function inactive_order(Request $request, $id)
    {
        $services_client = services_client::find($id);

        if (!$services_client) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $validator = validator($request->all(), [
            'status' => 'required|string|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation error: ' . $validator->errors()], 422);
        }

        $services_client->update([
            'status' => $request->status
        ]);

        return response()->json(['message' => 'Order status updated successfully'], 200);
    }



    public function destroy(services_client $services_client)
    {
        //
    }
}
