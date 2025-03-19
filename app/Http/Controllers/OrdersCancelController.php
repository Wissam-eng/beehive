<?php

namespace App\Http\Controllers;

use App\Models\services_client;
use App\Models\orders_cancel;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

class OrdersCancelController extends Controller
{

    public function index()
    {
        $orders_cancel = orders_cancel::with('client', 'order')->where('status', 'pending')->orderBy('created_at', 'desc')->get();
        return view('orders_cancel.index', compact('orders_cancel'));
    }



    public function index_api()
    {
        $id = auth()->user()->id;
        $orders_cancel = orders_cancel::where('client_id', $id)->with('order')->orderBy('created_at', 'desc')->get();
        return response()->json(['orders_cancel' => $orders_cancel], 200);
    }




    public function store(Request $request, $id)
    {
        try {

            $client = auth()->user()->id;

            $request['client_id'] = $client;
            $request['order_id'] = $id;

            $validator = Validator::make($request->all(), [
                'order_id' => 'required|integer|exists:services_clients,id',
                'client_id' => 'required|integer|exists:clients,id',
                'cancel_reason' => 'nullable|string|max:255',
                'bank_number' => 'nullable|string|max:255',
                'mobile_wallet' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }


            $status = orders_cancel::where('order_id', $id)->first();

            $order = services_client::where('id', $id)->where('client_id', $client)->first();


            if ($status) {
                return response()->json(['error' => 'Order already has a cancellation request'], 401);
            }


            $orders_cancel = orders_cancel::create(request()->all());
            $order->update([
                'status' => 'canceled',
            ]);

            return response()->json(['success', 'تم التسجيل الطلب بنجاح'], 200);
        } catch (\Exception $e) {
            return response()->json(['error', 'حدث خطاء اثناء التسجيل: ' . $e->getMessage()], 401);
        }
    }




    public function update(Request $request, orders_cancel $orders_cancel)
    {
        //
    }

    public function destroy($id)
    {
        $orders_cancel = orders_cancel::find($id);
        $orders_cancel->forceDelete();
        return redirect()->route('orders_cancel.index')->with('success', 'تم الحذف بنجاح');
    }
}
